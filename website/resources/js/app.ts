import {D3ZoomEvent, select, zoom} from "d3"
import {debug, gt, lt, PIPI} from "./utils"
import {Node, ProcessedNode, Sector} from "@/types"
import {fitToSector} from "./layout"
import FiltersStateStore, {MapStateStore} from "@/store"
import {shouldFilterEntry} from "@/filters";

type AppState = "error" | "success" | "loading" | "empty"

function showAppState(newState: AppState): void {
    (document.querySelectorAll(".app-state") as NodeListOf<HTMLElement>).forEach((state: HTMLElement) => {
        let isActive = newState === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.tabIndex = isActive ? 0 : -1
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

window.persistedMapState = new MapStateStore()
window.persistedMapState.sync()

const filtersStore = new FiltersStateStore()

document.getElementById("filters-reset")!.addEventListener("click", () => filtersStore.reset())

// Handle the 'By activity' filter
let activityInputs = document.querySelectorAll(`input[name^="activity_"]`) as NodeListOf<HTMLInputElement>
let technicalLens = document.querySelector(`input[name="lens_technical"]`) as HTMLInputElement
let governanceLens = document.querySelector(`input[name="lens_governance"]`) as HTMLInputElement
let gcbrFocus = document.querySelector(`input[name="gcbr_focus"]`) as HTMLInputElement
filtersStore.persist(
    "mask",
    () => {
        let mask = 0
        let offset = 0

        activityInputs.forEach((el) => mask |= (el.checked ? 1 : 0) << offset++)
        mask |= (technicalLens.checked ? 1 : 0) << offset++
        mask |= (governanceLens.checked ? 1 : 0) << offset++
        mask |= (gcbrFocus.checked ? 1 : 0) << offset

        return mask.toString(2)
    },
    (maskStr: string) => {
        let mask = parseInt(maskStr, 2)
        let offset = 0

        activityInputs.forEach((el: HTMLInputElement) => {
            el.checked = (mask & (1 << offset++)) !== 0

            document
                .querySelector(`label[for="${el.id}"]`)!
                .classList.toggle("inactive", !el.checked)
        })

        technicalLens.checked = (mask & (1 << offset++)) !== 0
        governanceLens.checked = (mask & (1 << offset++)) !== 0
        gcbrFocus.checked = (mask & (1 << offset)) !== 0

        document.querySelector('label[for="gcbr_focus"]')!.dataset.toggle = gcbrFocus.checked ? "on" : "off"
    },
    "0".repeat(window.bitmaskLength),
)

activityInputs.forEach((el: HTMLInputElement) => el.addEventListener("change", () => filtersStore.syncOnly(["mask"])))
technicalLens.addEventListener("change", () => filtersStore.syncOnly(["mask"]))
governanceLens.addEventListener("change", () => filtersStore.syncOnly(["mask"]))
gcbrFocus.addEventListener("change", (e) => {
    document.querySelector('label[for="gcbr_focus"]')!.dataset.toggle = gcbrFocus.checked ? "on" : "off"
    return filtersStore.syncOnly(["mask"]);
})

document.getElementById("toggle-all-activities")!.addEventListener('click', () => {
    activityInputs.forEach((el: HTMLInputElement) => {
        el.checked = !el.checked
        document.querySelector(`label[for="${el.id}"]`)!.classList.toggle("inactive", !el.checked)
    })

    filtersStore.syncOnly(["mask"])
})

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

    fetch(`/e/${entrygroup}/${entryId}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response: Response) => response.text())
        .then((html: string) => {
            elEntryWrapper.innerHTML = html

            window.persistedMapState.setFocusedEntry(entrygroup, entryId)

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
    window.persistedMapState.resetFocusedEntry()
}

if (window.persistedMapState.focusedEntry) {
    let [entrygroup, entry] = window.persistedMapState.focusedEntry
    let el = document.querySelector(`button[data-entrygroup="${entrygroup}"][data-entry="${entry}"]`) as HTMLButtonElement
    openEntry(el)
}

try {
    let elMapWrapper = document.getElementById('map-wrapper')!
    let $map = select<SVGElement, any>("#map")

    let mapContentRes = await fetch('/m')
    let mapContent = await mapContentRes.text()

    $map.html(mapContent)

    let $zoomWrapper = select<SVGGElement, any>("#zoom-wrapper")
    let $centerWrapper = select<SVGGElement, any>("#center-wrapper")
    let $background = select<SVGGElement, any>("#background")

    // Resize-, zoom-dependent variables
    let mapWidth = $map.node()!.clientWidth
    let mapHeight = $map.node()!.clientHeight
    let computeMapCenter = () => [mapWidth / 2, mapHeight / 2]

    $centerWrapper.attr("transform", `translate(${computeMapCenter()})`)

    let isMapFixed = () => elMapWrapper.classList.contains('fullscreen')
    let directionalIncrements = 0
    let nextScrollShouldBeIgnored = false
    let comingFromTop = elMapWrapper.getBoundingClientRect().top > 0
    let mapDistanceToTop = window.scrollY + elMapWrapper.getBoundingClientRect().top

    window.addEventListener('resize', () => {
        comingFromTop = elMapWrapper.getBoundingClientRect().top > 0
        mapDistanceToTop = window.scrollY + elMapWrapper.getBoundingClientRect().top
    })

    if (elMapWrapper.getBoundingClientRect().top < 0) {
        elMapWrapper.classList.add('fullscreen')
    }

    const handleMovement = (direction: number) => {
        if (!isMapFixed()) {
            return
        }

        directionalIncrements += direction

        if (directionalIncrements === -2) {
            elMapWrapper.classList.remove('fullscreen')
            comingFromTop = true
            directionalIncrements = 0
            window.scrollTo(0, mapDistanceToTop - 1 - 1)

            return;
        }

        if (directionalIncrements === 5) {
            elMapWrapper.classList.remove('fullscreen')
            directionalIncrements = 0
            comingFromTop = false
            nextScrollShouldBeIgnored = true
            window.scrollTo(0, mapDistanceToTop + 1 + 1)
        }
    }

    document.addEventListener('scroll', () => {
        if (isMapFixed() || nextScrollShouldBeIgnored) {
            nextScrollShouldBeIgnored = false
            return
        }

        // noinspection PointlessBooleanExpressionJS
        if (comingFromTop === true && elMapWrapper.getBoundingClientRect().top < 0) {
            elMapWrapper.classList.add('fullscreen')
        }

        // noinspection PointlessBooleanExpressionJS
        if (comingFromTop === false && elMapWrapper.getBoundingClientRect().top > 0) {
            elMapWrapper.classList.add('fullscreen')
        }
    })

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

    let zoomHandler = zoom<SVGElement, unknown>()
        .on("zoom", (e: D3ZoomEvent<SVGGElement, unknown>) => $zoomWrapper.attr("transform", e.transform.toString()))
        .scaleExtent([0.5, 2.5])

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

    let cb = () => {
        debug().clear()
        renderMap()
        debug().flush($background)
    }
    filtersStore.sync().onChange(cb)
    cb()
} catch (err: unknown) {
    console.error(err)

    showError(
        "An error occurred while loading the map. Please try again later.",
        err,
    )
}

function renderMap() {
    showAppState("loading")

    let nodes: ProcessedNode[] = []
    let stack = []

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node &
            Partial<ProcessedNode> & { el: SVGElement }

        node.el.classList.add("invisible")
        node.el.ariaHidden = "true"
        node.el.style.transform = ""
    }

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node &
            Partial<ProcessedNode> & { el: SVGElement }

        if (node.od === 0) {
            let entryIds = node.entries
            let filteredIds = []

            for (const entryId of entryIds) {
                let entryMask = window.masks[entryId]
                let elEntry = document.querySelector(`button[data-entrygroup="${node.id}"][data-entry="${entryId}"]`) as HTMLButtonElement

                let shouldFilter = shouldFilterEntry(filtersStore.getState('mask'), entryMask.toString(2))

                elEntry.classList.toggle("matches-filters", !shouldFilter)

                if (!shouldFilter) {
                    filteredIds.push(entryId)
                }
            }

            node.filtered = filteredIds.length === 0
        }

        if (!(node.el instanceof SVGForeignObjectElement)) {
            throw new Error(`Element for node ${node.id} is not a foreignObject, but a ${node.el.tagName}`)
        }

        // We set the <foreignObject> with a height of 100% and a w of 100%
        // because we don't want to compute the size of the elements server-side
        // but this means that we get the wrong bounds.
        if (node.el.firstElementChild === null) {
            throw new Error(
                "It is expected that the foreignObject representing the node " +
                "has a single child to compute its real bounding box, not " +
                "the advertised (100%, 100%)",
            )
        }

        node.size = [
            // getBoundingClientRect() is transform-aware, so the zoom will mess everything up on subsequent renders.
            // We need to use offsetWidth and offsetHeight instead.
            node.el.firstElementChild!.offsetWidth,
            node.el.firstElementChild!.offsetHeight,
        ]

        node.weight = node.size[0] * node.size[1]

        let children = []

        if (node.od > 0) {
            for (let i = 0; i < node.od; i++) {
                let child = stack.pop()
                if (child.filtered) {
                    continue
                }

                children.push(child)
                node.weight += child.weight
            }

            children.sort((a, b) => a.weight - b.weight)
            for (const child of children) {
                nodes.push(child)
            }

            node.filtered = children.length === 0
        }

        stack.push(node)
    }

    let root = stack.pop()
    root.sector = [0, PIPI]
    root.position = fitToSector(root)

    let parentIdToNode: Record<number, ProcessedNode> = {[root.id]: root}
    let deltaFromSiblings: Record<number, number> = {}

    if (nodes.length === 0) {
        return showAppState("empty")
    }

    showNode(root)

    for (let i = nodes.length - 1; i >= 0; i--) {
        let node = nodes[i]

        if (!deltaFromSiblings[node.parent]) {
            deltaFromSiblings[node.parent] = 0
        }

        let parent = parentIdToNode[node.parent]
        if (!parent) {
            throw new Error(`Parent with id ${node.parent} not found for node ${node.id}`)
        }

        let delta = parent.sector[0] + deltaFromSiblings[parent.id]
        let alpha = (node.weight / parent.weight) * (parent.sector[1] - parent.sector[0])
        node.sector = [delta, delta + alpha]

        parentIdToNode[node.id] = node
        deltaFromSiblings[parent.id] += alpha

        if (lt(node.sector[0], 0) || gt(node.sector[1], PIPI)) {
            throw new Error(`Sector ${node.sector} is not in the range [0, 2*PI]`,)
        }

        node.position = fitToSector(node)

        if (node.depth === 1) {
            drawSectorArea(node)
        }

        showNode(node)
    }

    showAppState("success")
}

function drawSectorArea(node: {
    size: [number, number];
    position?: [number, number];
    sector: Sector;
} & Node) {
    // TODO
}

function showNode(node: ProcessedNode) {
    node.el.classList.remove("invisible")
    node.el.ariaHidden = "false"
    node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`
}
