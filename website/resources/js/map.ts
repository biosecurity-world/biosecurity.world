import {D3ZoomEvent, select, zoom} from "d3"
import {changeAppState, debug, flip, trapClickAndDoubleClick} from "./utils"
import type {AppStateChangeEvent, Node, ProcessedNode} from "@/types/index.d.ts"
import {updateMap} from "./layout"
import FiltersStore, {Filters} from "@/filters"

let $map = select<SVGElement, any>("#map")
let elMapWrapper = document.getElementById("map-wrapper")!

/* Open and close a specific entry  */
let elEntryLoader = document.getElementById("entry-loader")!
let elEntryWrapper = document.getElementById("entry-wrapper")!
async function openEntry(entry: HTMLElement): Promise<void> {
    elEntryLoader.classList.add("loading-entry")

    let entrygroup = parseInt(entry.dataset.entrygroup!, 10)
    let entryId = parseInt(entry.dataset.entry!, 10)

    try {
        let entryResponse = await fetch(`/e/${entrygroup}/${entryId}`, {
            headers: {"X-Requested-With": "XMLHttpRequest"},
        })

        elEntryWrapper.innerHTML = await entryResponse.text()

        setLastFocusedEntry([entrygroup, entryId])

        elEntryWrapper.querySelector("button.close-entry")!.addEventListener("click", () => closeEntry())
    } catch (err: unknown) {
        changeAppState("error", {
            error: err,
            message: "An error occurred while loading the entry. Please try again later.",
        })
    } finally {
        elEntryLoader.classList.remove("loading-entry")
    }
}
function closeEntry(): void {
    elEntryWrapper.innerHTML = ""
    setLastFocusedEntry(null)
}
function hasEntryOpen(): boolean {
    return elEntryWrapper.children.length > 0
}

/* Fullscreen mode */
{
    let lastScrollTop = 0

    // element.requestFullscreen() is not available in Safari
    // so we implement a fake fullscreen mode.
    function toggleFullscreen() {
        let isFullscreen = elMapWrapper.classList.contains("fullscreen")

        document.getElementById("not-fullscreen")!.classList.toggle("hidden", !isFullscreen)
        document.getElementById("is-fullscreen")!.classList.toggle("hidden", isFullscreen)

        if (!isFullscreen) {
            lastScrollTop = window.scrollY
            elMapWrapper.classList.add("fullscreen")
        } else {
            elMapWrapper.classList.remove("fullscreen")
            elMapWrapper.scrollIntoView()
            window.scrollTo(0, lastScrollTop)
        }
    }

    document.getElementById("toggle-fullscreen")!.addEventListener("click", toggleFullscreen)
    window.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            if (hasEntryOpen()) {
                closeEntry()
            } else if (elMapWrapper.classList.contains("fullscreen")) {
                e.preventDefault()
                toggleFullscreen()
            }
        }

        if (e.key === "f") {
            e.preventDefault()
            toggleFullscreen()
        }
    })
}

/* Handle app state changes */
let stateElements = document.querySelectorAll(".app-state") as NodeListOf<HTMLElement>
window.addEventListener("appstatechange", (e: AppStateChangeEvent) => {
    const {state, params} = e.detail

    switch (state) {
        case "error":
            let elStateContainer = document.querySelector("[data-state='error']")!
            let reason = elStateContainer.querySelector(".reason") as HTMLParagraphElement

            reason.innerHTML = params.message

            console.error(params.error)
            break
    }

    stateElements.forEach((state: HTMLElement) => {
        let isActive = e.detail.state === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.classList.toggle("state-active", isActive)
        state.classList.toggle("state-inactive", !isActive)
    })
})

/* Prepare filters */
let activityInputs = document.querySelectorAll(`.activity-checkbox`) as NodeListOf<HTMLInputElement>
let domainInputs = document.querySelectorAll(".domain-checkbox") as NodeListOf<HTMLInputElement>

let focusInputs = document.querySelectorAll(`.focus-checkbox`) as NodeListOf<HTMLInputElement>
let focusesWrapper = document.querySelectorAll("[id^='focuses_wrapper_']") as NodeListOf<HTMLElement>

let gcbrFocus = document.querySelector(".has-gcbr-focus") as HTMLInputElement
const filtersStore = new FiltersStore<Filters>({
    activities: [
        () => Array.from(activityInputs).reduce((mask, el, k: number) => mask | (+el.checked << k), 0),
        (mask: number) =>
            activityInputs.forEach((activityInput, k) => {
                activityInput.checked = (mask & (1 << k)) !== 0
            }),
    ],
    domains: [
        () => Array.from(domainInputs).reduce((mask, el, k) => mask | (+el.checked << k), 0),
        (mask: number) => domainInputs.forEach((domainInput, k) => (domainInput.checked = (mask & (1 << k)) !== 0)),
    ],
    focuses: [
        () =>
            Array.from(focusInputs).reduce((mask, el) => {
                let k = parseInt(el.dataset.globalOffset!, 10)
                return mask | (+el.checked << k)
            }, 0),
        (mask: number) =>
            focusInputs.forEach((el) => {
                let k = parseInt(el.dataset.globalOffset!, 10)
                el.checked = (mask & (1 << k)) !== 0
            }),
    ],
    gcbrFocus: [() => gcbrFocus.checked, (checked: boolean) => (gcbrFocus.checked = checked)],
})

for (const el of document.querySelectorAll("button.resets-filters")) {
    el.addEventListener("click", (e) => {
        e.stopImmediatePropagation()
        return filtersStore.reset()
    })
}
for (const el of activityInputs) {
    el.addEventListener("change", () => filtersStore.syncFilter("activities"))
    el.addEventListener(
        "click",
        trapClickAndDoubleClick(
            () => {
                el.checked = !el.checked
                filtersStore.syncFilter("activities")
            },
            () => {
                let mask = 1 << Array.from(activityInputs).indexOf(el)

                // The double-clicked activity is already the only one active, so we flip all of them
                if (filtersStore.getState("activities") === mask) {
                    mask = flip(mask, activityInputs.length)
                }

                filtersStore.setState("activities", mask)
            },
        ),
    )
}
for (const el of domainInputs) {
    el.addEventListener("change", () => filtersStore.syncFilter("domains"))
}
gcbrFocus.addEventListener("change", () => filtersStore.syncFilter("gcbrFocus"))

let masterCheckboxes: Record<string, HTMLInputElement> = {}
let focusesLabels: Record<string, HTMLLabelElement> = {}
let groupedFocuses: Map<HTMLElement, NodeListOf<HTMLInputElement>> = new Map()

focusesWrapper.forEach((focusWrapper) => {
    let focuses = focusWrapper.querySelectorAll(".focus-checkbox") as NodeListOf<HTMLInputElement>

    for (const focus of focuses) {
        focusesLabels[focus.id] = focusWrapper.querySelector(`label[for="${focus.id}"]`) as HTMLLabelElement
    }

    groupedFocuses.set(focusWrapper, focuses)
    masterCheckboxes[focusWrapper.id] = focusWrapper.querySelector(".focuses-master-checkbox") as HTMLInputElement
})

function toggleGroupedFocus(focuses: NodeListOf<HTMLInputElement>, force: boolean | null = null): void {
    if (force === null) {
        force = getGroupOffsets(focuses).length === 0
    }

    for (const focus of focuses) {
        focus.checked = force
    }
}
function getGroupOffsets(focuses: NodeListOf<HTMLInputElement>): number[] {
    let offsets = []
    for (const focus of focuses) {
        if (!focus.checked) {
            continue
        }

        offsets.push(parseInt(focus.dataset.globalOffset!, 10))
    }

    return offsets
}

for (let [focusesWrapper, focuses] of groupedFocuses) {
    for (let focus of focuses) {
        focusesLabels[focus.id].addEventListener(
            "click",
            trapClickAndDoubleClick(
                (e) => {
                    e.stopPropagation()
                    e.stopImmediatePropagation()
                    focus.checked = !focus.checked
                    filtersStore.syncFilter("focuses")
                },
                (e) => {
                    e.stopPropagation()
                    e.stopImmediatePropagation()

                    for (const comparisonFocus of focuses) {
                        comparisonFocus.checked = comparisonFocus.id === focus.id
                    }

                    filtersStore.syncFilter("focuses")
                },
            ),
        )
    }

    masterCheckboxes[focusesWrapper.id].addEventListener("click", () => {
        toggleGroupedFocus(focuses)
        filtersStore.syncFilter("focuses")
    })

    focusesWrapper.addEventListener(
        "click",
        trapClickAndDoubleClick(
            () => {
                toggleGroupedFocus(focuses)
                filtersStore.syncFilter("focuses")
            },
            () => {
                for (const [comparisonGroup, comparisonFocuses] of groupedFocuses) {
                    toggleGroupedFocus(comparisonFocuses, comparisonGroup.id === focusesWrapper.id)
                }
                filtersStore.syncFilter("focuses")
            },
        ),
    )
}

filtersStore.onChange(["focuses"], () => {
    for (const [focusesWrapper, focuses] of groupedFocuses) {
        let offsets = getGroupOffsets(focuses)

        if (offsets.length === 0 || offsets.length === focuses.length) {
            masterCheckboxes[focusesWrapper.id].checked = offsets.length === focuses.length
            masterCheckboxes[focusesWrapper.id].indeterminate = false
        } else {
            masterCheckboxes[focusesWrapper.id].checked = true
            masterCheckboxes[focusesWrapper.id].indeterminate = true
        }
    }
})
;(async function () {
    try {
        let mapContentRes = await fetch("/_/m")
        let mapContent = await mapContentRes.text()

        $map.html(mapContent)

        // The map partial contains the entries, so we need to wait for it to load before we can show the entry
        // that was opened on the last visit but never closed.
        let lastFocusedEntry = getRememberedOpenEntry()
        if (lastFocusedEntry !== null) {
            let [entrygroup, entry] = lastFocusedEntry
            let el = document.querySelector(
                `button[data-entrygroup="${entrygroup}"][data-entry="${entry}"]`,
            ) as HTMLButtonElement
            console.log(el)
            openEntry(el)
        }

        let elEntrygroupContainer = document.getElementById("entrygroups")!
        let elsEntryButtons = document.querySelectorAll("button[data-entry]") as NodeListOf<HTMLButtonElement>

        let highlightEntries = (commonEntryId: number) => {
            let instances = 0

            elsEntryButtons.forEach((btn: HTMLButtonElement) => {
                let isActive = btn.dataset.entry === commonEntryId.toString()
                btn.classList.toggle("active", isActive)
                instances += isActive ? 1 : 0
            })

            elEntrygroupContainer.classList.toggle("hovered", instances > 1)
        }
        let removeHighlight = () => elEntrygroupContainer.classList.remove("hovered")

        elsEntryButtons.forEach((el: HTMLButtonElement) => {
            let entryId = parseInt(el.dataset.entry!, 10)

            el.addEventListener("click", () => openEntry(el))
            el.addEventListener("mouseenter", () => highlightEntries(entryId))
            el.addEventListener("mouseleave", () => removeHighlight())
            el.addEventListener("focus", () => highlightEntries(entryId))
            el.addEventListener("blur", () => removeHighlight())
        })

        let $zoomWrapper = select<SVGGElement, any>("#zoom-wrapper")
        let $centerWrapper = select<SVGGElement, any>("#center-wrapper")

        let mapWidth = $map.node()!.clientWidth
        let mapHeight = $map.node()!.clientHeight

        $centerWrapper.attr("transform", `translate(${mapWidth / 2}, ${mapHeight / 2})`)

        let zoomHandler = zoom<SVGElement, unknown>()
            .on("zoom", (e: D3ZoomEvent<SVGGElement, unknown>) => {
                $zoomWrapper.attr("transform", e.transform.toString())
            })
            .scaleExtent([0.5, 2.5])
            .translateExtent([
                [-mapWidth * 1.5, -mapHeight * 1.5],
                [mapWidth * 1.5, mapHeight * 1.5],
            ])

        $map.call(zoomHandler)

        document.getElementById("zoom-in")!.addEventListener("click", () => zoomHandler.scaleBy($map, 1.2))
        document.getElementById("zoom-out")!.addEventListener("click", () => zoomHandler.scaleBy($map, 0.8))

        for (const node of window.nodes as (Node & Partial<ProcessedNode>)[]) {
            let el = document.querySelector(`[data-node="${node.id}"]`) as SVGElement | null
            if (!el) {
                throw new Error(`Node with id ${node.id} has no corresponding element in the DOM`)
            }

            node.el = el
        }

        filtersStore.onChange(
            "*",
            (state) => {
                debug().clear()

                updateMap(state, {
                    activityCount: activityInputs.length,
                    focusesCount: Object.keys(focusesLabels).length,
                })

                debug().flush($centerWrapper)
            },
            true,
        )
    } catch (err: unknown) {
        changeAppState("error", {
            error: err,
            message: "An error occurred while loading the map. Please try again later.",
        })
    }
})()

function setLastFocusedEntry(focusedEntry: [number, number] | null) {
    let loc = new URL(window.location.toString())
    loc.hash = focusedEntry ? `/${focusedEntry[0]}:${focusedEntry[1]}/` : ""

    window.history.replaceState({}, "", loc.toString())
}
function getRememberedOpenEntry(): [number, number] | null {
    let entry = new URL(window.location.toString()).hash.slice(1).split("/").filter(Boolean)[0]
    return entry && /(\d+):(\d+)/.test(entry) ?
            (entry.split(":").map((id) => parseInt(id, 10)) as [number, number])
        :   null
}

function getLastVisitTime() {
    let lastVisit = localStorage.getItem("startChangelogAt") || "0"
    let timestamp = parseInt(lastVisit, 10)

    return new Date(timestamp)
}
function updateLastVisitTime() {
    localStorage.setItem("startChangelogAt", Date.now().toString())
}
