import {D3ZoomEvent, select, zoom, zoomIdentity} from "d3"
import {debug, throttle} from "./utils"
import type {Node, ProcessedNode} from "@/types/index.d.ts"
import {updateMap} from "./layout"
import FiltersStore, {Filters} from "@/filters"

type AppState = "error" | "success" | "loading" | "empty"

export function showAppState(newState: AppState): void {
    (document.querySelectorAll(".app-state") as NodeListOf<HTMLElement>).forEach((state: HTMLElement) => {
        let isActive = newState === state.dataset.state

        if (newState === 'empty') {
            // wasEmpty = true
        }

        state.ariaHidden = isActive ? "false" : "true"
        state.classList.toggle("state-active", isActive)
        state.classList.toggle("state-inactive", !isActive)
    })
}

function showError(message: string, err: unknown) {
    console.error(err)
    const elStateContainer = document.querySelector("[data-state='error']")
    if (!elStateContainer) {
        throw new Error("No error state container found")
    }

    let reason = elStateContainer.querySelector(".reason") as HTMLParagraphElement

    reason.innerHTML = message

    showAppState("error")
}

let activityInputs = document.querySelectorAll(`input[name^="activity_"]`) as NodeListOf<HTMLInputElement>
let technicalDomain = document.querySelector(`input[name="lens_technical"]`) as HTMLInputElement
let governanceDomain = document.querySelector(`input[name="lens_governance"]`) as HTMLInputElement
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
            let offset = 0

            for (const activityInput of activityInputs) {
                activityInput.checked = (mask & (1 << offset++)) !== 0
                document.querySelector(`label[for="${activityInput.id}"]`)!.classList.toggle("inactive", !activityInput.checked)
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

document.getElementById("filters-reset")!.addEventListener("click", () => filtersStore.reset())

activityInputs.forEach((el: HTMLInputElement) => el.addEventListener("change", () => filtersStore.syncFilter('activities')))
technicalDomain.addEventListener("change", () => filtersStore.syncFilter('domains'))
governanceDomain.addEventListener("change", () => filtersStore.syncFilter('domains'))
gcbrFocus.addEventListener("change", (e) => filtersStore.syncFilter('gcbrFocus'))

document.getElementById("toggle-all-activities")!.addEventListener('click', () => {
    activityInputs.forEach((el: HTMLInputElement) => {
        el.checked = !el.checked
        document.querySelector(`label[for="${el.id}"]`)!.classList.toggle("inactive", !el.checked)
    })
    //
    // filtersStore.syncFilter('activities')
})


;(async function () {
    try {
        let elMapWrapper = document.getElementById('map-wrapper')!

        let $map = select<SVGElement, any>("#map")
        let mapContentRes = await fetch('/_/m')
        let mapContent = await mapContentRes.text()

        $map.html(mapContent)

        let elEntrygroupContainer = document.getElementById("entrygroups")!
        let elsEntryButtons = document.querySelectorAll("button[data-entry]") as NodeListOf<HTMLButtonElement>

        function highlightEntriesWithId(id: number) {
            let instances = 0

            elsEntryButtons.forEach((btn: HTMLButtonElement) => {
                let isActive = btn.dataset.entry === id.toString()
                btn.classList.toggle("active", isActive)
                instances += isActive ? 1 : 0
            })

            elEntrygroupContainer.classList.toggle("hovered", instances > 1)
        }

        function removeHighlight() {
            elEntrygroupContainer.classList.remove("hovered")
        }

        elsEntryButtons.forEach((el: HTMLButtonElement) => {
            let entryId = parseInt(el.dataset.entry!, 10)

            el.addEventListener("click", () => openEntry(el))
            el.addEventListener("mouseenter", () => highlightEntriesWithId(entryId))
            el.addEventListener("mouseleave", () => removeHighlight())
            el.addEventListener("focus", () => highlightEntriesWithId(entryId))
            el.addEventListener("blur", () => removeHighlight())
        })

        // Handle hiding entries that do not match current filters
        let elEntryLoader = document.getElementById("entry-loader")!
        let elEntryWrapper = document.getElementById("entry-wrapper")!

        function openEntry(entry: HTMLElement) {
            elEntryLoader.classList.add("loading-entry")

            let entrygroup = parseInt(entry.dataset.entrygroup!, 10)
            let entryId = parseInt(entry.dataset.entry!, 10)

            fetch(`/e/${entrygroup}/${entryId}`, {headers: {"X-Requested-With": "XMLHttpRequest"}})
                .then((response: Response) => response.text())
                .then((html: string) => {
                    elEntryWrapper.innerHTML = html

                    setLastFocusedEntry([entrygroup, entryId])

                    elEntryWrapper
                        .querySelector("button.close-entry")!
                        .addEventListener("click", () => closeEntry())
                })
                .catch((err: unknown) => {
                    showError("An error occurred while loading the entry. Please try again later.", err,)
                })
                .finally(() => elEntryLoader.classList.remove("loading-entry"))
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

        let directionalIncrements = 0
        let nextScrollShouldBeIgnored = false
        let comingFromTop = elMapWrapper.getBoundingClientRect().top > 0
        let mapDistanceToTop = window.scrollY + elMapWrapper.getBoundingClientRect().top

        if (elMapWrapper.getBoundingClientRect().top < 0) {
            elMapWrapper.classList.add('fullscreen')
        }

        const handleMovement = (direction: number) => {
            if (!elMapWrapper.classList.contains('fullscreen')) {
                return;
            }

            directionalIncrements += direction

            if (Math.abs(directionalIncrements) !== 2) {
                return;
            }

            elMapWrapper.classList.remove('fullscreen')
            comingFromTop = directionalIncrements < 0
            directionalIncrements = 0

            if (directionalIncrements > 0) {
                nextScrollShouldBeIgnored = true
            }

            window.scrollTo(0, mapDistanceToTop + 10 * Math.sign(directionalIncrements))
        }

        document.addEventListener('scroll', throttle(() => {
            if (nextScrollShouldBeIgnored) {
                nextScrollShouldBeIgnored = false
                return;
            }

            if (elMapWrapper.classList.contains('fullscreen')) {
                return
            }

            if (
                comingFromTop && elMapWrapper.getBoundingClientRect().top < 0 ||
                !comingFromTop && elMapWrapper.getBoundingClientRect().top > 0
            ) {
                elMapWrapper.classList.add('fullscreen')
            }
        }, 100))
        document.addEventListener('wheel', (e: WheelEvent) => handleMovement(e.deltaY > 0 ? 1 : -1))
        document.addEventListener("keydown", (e: KeyboardEvent) => {
            if (e.key === "Escape") {
                closeEntry()
            }

            if (e.key === 'ArrowDown') {
                handleMovement(1)
            }

            if (e.key === 'ArrowUp') {
                handleMovement(-1)
            }
        })
        window.addEventListener('resize', () => {
            mapWidth = $map.node()!.clientWidth
            mapHeight = $map.node()!.clientHeight

            $centerWrapper.attr(
                "transform",
                `translate(${mapWidth / 2}, ${mapHeight / 2})`,
            )

            zoomHandler.translateExtent([
                [-mapWidth * 1.5, -mapHeight * 1.5],
                [mapWidth * 1.5, mapHeight * 1.5],
            ])

            $map.call(zoomHandler.transform, zoomIdentity)

            if (elMapWrapper.getBoundingClientRect().top < 0) {
                elMapWrapper.classList.add('fullscreen')
            }
        })

        for (const node of window.nodes as (Node & Partial<ProcessedNode>)[]) {
            let el = document.querySelector(`[data-node="${node.id}"]`) as SVGElement | null
            if (!el) {
                throw new Error(`Node with id ${node.id} has no corresponding element in the DOM`)
            }

            node.el = el
        }

        filtersStore.onChange((state) => {
            debug().clear()

            updateMap(state)

            debug().flush($centerWrapper)
        }, true)
    } catch (err: unknown) {
        console.error(err)

        showError(
            "An error occurred while loading the map. Please try again later.",
            err,
        )
    }
})()


function setLastFocusedEntry(focusedEntry: [number, number] | null) {
    let loc = new URL(window.location.toString())
    loc.hash = focusedEntry ? `/${focusedEntry[0]}:${focusedEntry[1]}/` : '';

    window.history.replaceState({}, '', loc.toString())
}

function getLastFocusedEntry(): [number, number] | null {
    let focusedEntry = new URL(window.location.toString()).hash.split("/").shift()
    return focusedEntry && /(\d+):(\d+)/.test(focusedEntry) ? focusedEntry.split(":").map(id => parseInt(id, 10)) as [number, number] : null
}
