import {D3ZoomEvent, select, zoom, zoomIdentity} from "d3"
import {debug, changeAppState, throttle, wrapClickAndDoubleClickEvent} from "./utils"
import type {Node, ProcessedNode, AppStateChangeEvent} from "@/types/index.d.ts"
import {updateMap} from "./layout"
import FiltersStore, {Filters} from "@/filters"

let $map = select<SVGElement, any>("#map")
let elMapWrapper = document.getElementById('map-wrapper')!

let lastScrollTop = 0

// element.requestFullscreen() is not available in Safari
// so we implement our own almost "fullscreen" mode.
function toggleFullscreen() {
    let isFullscreen = elMapWrapper.classList.contains('fullscreen')

    document.getElementById('not-fullscreen')!.classList.toggle('hidden', !isFullscreen)
    document.getElementById('is-fullscreen')!.classList.toggle('hidden', isFullscreen)

    if (!isFullscreen) {
        lastScrollTop = window.scrollY
        elMapWrapper.classList.add('fullscreen')
    } else {
        elMapWrapper.classList.remove('fullscreen')
        elMapWrapper.scrollIntoView()
        window.scrollTo(0, lastScrollTop)
    }
}
document.getElementById('toggle-fullscreen')!.addEventListener('click', toggleFullscreen)
window.addEventListener('keydown', (e) => {
    if (
        e.key === "f" ||
        (e.key === "Escape" && elMapWrapper.classList.contains('fullscreen'))
    ) {
        e.preventDefault()
        toggleFullscreen()
    }
})

/* Open/Close an entry, restore last focused entry */
let elEntryLoader = document.getElementById("entry-loader")!
let elEntryWrapper = document.getElementById("entry-wrapper")!

async function openEntry(entry: HTMLElement) {
    elEntryLoader.classList.add("loading-entry")

    let entrygroup = parseInt(entry.dataset.entrygroup!, 10)
    let entryId = parseInt(entry.dataset.entry!, 10)

    try {
        let entryResponse = await fetch(`/e/${entrygroup}/${entryId}`, {headers: {"X-Requested-With": "XMLHttpRequest"}})

        elEntryWrapper.innerHTML = await entryResponse.text()

        setLastFocusedEntry([entrygroup, entryId])

        elEntryWrapper
            .querySelector("button.close-entry")!
            .addEventListener("click", () => closeEntry())
    } catch (err: unknown) {
        changeAppState("error", { error: err, message: "An error occurred while loading the entry. Please try again later." })
    } finally {
        elEntryLoader.classList.remove("loading-entry")
    }
}

function closeEntry() {
    elEntryWrapper.innerHTML = ""
    setLastFocusedEntry(null)
}

let lastFocusedEntry = getLastFocusedEntry()
if (lastFocusedEntry !== null) {
    let [entrygroup, entry] = lastFocusedEntry
    let el = document.querySelector(`button[data-entrygroup="${entrygroup}"][data-entry="${entry}"]`) as HTMLButtonElement
    openEntry(el)
}

// Handle app state changes
let stateElements = document.querySelectorAll(".app-state") as NodeListOf<HTMLElement>
window.addEventListener('appstatechange', (e: AppStateChangeEvent) => {
    const { state, params } = e.detail;

    switch (state) {
        case "error":
            let elStateContainer = document.querySelector("[data-state='error']")!
            let reason = elStateContainer.querySelector(".reason") as HTMLParagraphElement

            reason.innerHTML = params.message

            console.error(params.error)
            break;
    }

    stateElements.forEach((state: HTMLElement) => {
        let isActive = e.detail.state === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.classList.toggle("state-active", isActive)
        state.classList.toggle("state-inactive", !isActive)
    })
});

/* Prepare filters */
let activityInputs = document.querySelectorAll(`input[name^="activity_"]`) as NodeListOf<HTMLInputElement>
let activityInputLabels = document.querySelectorAll(`label[for^="activity_"]`) as NodeListOf<HTMLLabelElement>

let technicalDomain = document.querySelector(`input[name="domain_technical"]`) as HTMLInputElement
let governanceDomain = document.querySelector(`input[name="domain_governance"]`) as HTMLInputElement

let gcbrFocus = document.querySelector(`input[name="gcbr_focus"]`) as HTMLInputElement
let gcbrFocusLabel = document.querySelector('label[for="gcbr_focus"]') as HTMLLabelElement

const filtersStore = new FiltersStore<Filters>({
    activities: [
        () => {
            let mask = 0, offset = 0;

            activityInputs.forEach((el) => mask |= (el.checked ? 1 : 0) << offset++)

            return mask
        },
        (mask: number) => {
            for (let i = 0; i < activityInputs.length; i++) {
                let checked = (mask & (1 << i)) !== 0
                activityInputs[i].checked = checked
                activityInputLabels[i].classList.toggle("inactive", !checked)
            }
        }
    ],
    domains: [
        () => {
            let mask = 0, offset = 0;

            mask |= (technicalDomain.checked ? 1 : 0) << offset++
            mask |= (governanceDomain.checked ? 1 : 0) << offset++

            return mask
        },
        (mask: number) => {
            let offset = 0

            technicalDomain.checked = (mask & (1 << offset++)) !== 0
            governanceDomain.checked = (mask & (1 << offset)) !== 0
        }
    ],
    gcbrFocus: [
        () => gcbrFocus.checked,
        (checked: boolean) => {
            gcbrFocus.checked = checked
            gcbrFocusLabel.dataset.toggle = checked ? "on" : "off"
        }
    ]
})
document.querySelectorAll('button.resets-filters').forEach(btn => {
    btn.addEventListener("click", e => {
        e.stopImmediatePropagation()
        return filtersStore.reset();
    })
})

activityInputs.forEach((el: HTMLInputElement) => {
    el.addEventListener("change", e => filtersStore.syncFilter("activities"))
    el.addEventListener('click', wrapClickAndDoubleClickEvent(
        (preventedEvent) => {
            el.checked = !el.checked
            filtersStore.syncFilter("activities")
        },
        (preventedEvent) => {
            let mask = 1 << Array.from(activityInputs).indexOf(el)

            // The double-clicked activity is already the only one active, so we flip all of them
            if (filtersStore.getState('activities') === mask) {
                mask = ~mask
            }

            filtersStore.setState('activities', mask)
        }
    ))
})

technicalDomain.addEventListener("change", () => filtersStore.syncFilter('domains'))
governanceDomain.addEventListener("change", () => filtersStore.syncFilter('domains'))
gcbrFocus.addEventListener("change", (e) => filtersStore.syncFilter('gcbrFocus'))

;(async function () {
    try {
        let mapContentRes = await fetch('/_/m')
        let mapContent = await mapContentRes.text()

        $map.html(mapContent)

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

        $centerWrapper.attr(
            "transform",
            `translate(${mapWidth / 2}, ${mapHeight / 2})`,
        )

        let zoomHandler = zoom<SVGElement, unknown>()
            .on("zoom", (e: D3ZoomEvent<SVGGElement, unknown>) => {
                $zoomWrapper.attr("transform", e.transform.toString());
            })
            .scaleExtent([0.5, 2.5])
            .translateExtent([
                [-mapWidth * 1.5, -mapHeight * 1.5],
                [mapWidth * 1.5, mapHeight * 1.5],
            ])

        $map.call(zoomHandler)

        document.getElementById('zoom-in')!.addEventListener('click', () => zoomHandler.scaleBy($map, 1.2))
        document.getElementById('zoom-out')!.addEventListener('click', () => zoomHandler.scaleBy($map, 0.8))

        for (const node of window.nodes as (Node & Partial<ProcessedNode>)[]) {
            let el = document.querySelector(`[data-node="${node.id}"]`) as SVGElement | null
            if (!el) {
                throw new Error(`Node with id ${node.id} has no corresponding element in the DOM`)
            }

            node.el = el
        }

        filtersStore.onChange((state) => {
            debug().clear()

            updateMap(state, { activityCount: activityInputs.length })

            debug().flush($centerWrapper)
        }, true)
    } catch (err: unknown) {
        changeAppState("error", { error: err, message: "An error occurred while loading the map. Please try again later." })
    }
})()


function setLastFocusedEntry(focusedEntry: [number, number] | null) {
    let loc = new URL(window.location.toString())
    loc.hash = focusedEntry ? `/${focusedEntry[0]}:${focusedEntry[1]}/` : '';

    window.history.replaceState({}, '', loc.toString())
}

function getLastFocusedEntry(): [number, number] | null {
    let focusedEntry = new URL(window.location.toString()).hash.slice(1).split("/").filter(Boolean)[0]
    return focusedEntry && /(\d+):(\d+)/.test(focusedEntry) ? focusedEntry.split(":").map(id => parseInt(id, 10)) as [number, number] : null
}

function getLastVisitTime() {
    let lastVisit = localStorage.getItem('startChangelogAt') || '0'
    let timestamp = parseInt(lastVisit, 10)

    return new Date(timestamp)
}

function updateLastVisitTime() {
    localStorage.setItem('startChangelogAt', Date.now().toString())
}
