import {D3ZoomEvent, map, select, zoom, zoomIdentity} from "d3"
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
    const elStateContainer = document.querySelector("[data-state='error']")
    if (!elStateContainer) {
        throw new Error("No error state container found")
    }

    let reason = elStateContainer.querySelector(
        ".reason",
    ) as HTMLParagraphElement

    reason.innerHTML = message

    showAppState("error")
}

window.persistedMapState = new MapStateStore()
window.persistedMapState.sync()

const filtersStore = new FiltersStateStore()

document
    .getElementById("filters-reset")!
    .addEventListener("click", () => filtersStore.reset())
// Handle the 'Focus on' filter
for (const lens of ["lens_technical", "lens_governance"]) {
    let elLens = document.querySelector(
        `input[name="${lens}"]`,
    ) as HTMLInputElement
    filtersStore.persist(
        lens,
        () => `${+elLens.checked}`,
        (checked: string) => (elLens.checked = checked === "1"),
        "0",
    )

    elLens.addEventListener("change", () => filtersStore.syncOnly([lens]))
}

// Handle the 'By activity' filter
let activityInputs = document.querySelectorAll(`input[name^="activity_"]`) as NodeListOf<HTMLInputElement>
filtersStore.persist(
    "activities",
    () => {
        let mask = 0
        activityInputs.forEach((el: HTMLInputElement) => {
            if (!el.checked) {
                return;
            }

            mask |= 1 << parseInt(el.dataset.offset)
        })

        return mask.toString(2)
    },
    (value: string) => {
        let mask = parseInt(value, 2)

        activityInputs.forEach((el: HTMLInputElement) => {
                el.checked = (mask & (1 << parseInt(el.dataset.offset))) !== 0

                document
                    .querySelector(`label[for="${el.id}"]`)!
                    .classList.toggle("inactive", !el.checked)
            })
    },
    "1".repeat(activityInputs.length)
)

activityInputs.forEach((el: HTMLInputElement) => el.addEventListener("change", () => filtersStore.syncOnly(["activities"])))

document.getElementById("toggle-all-activities")!.addEventListener('click', () => {
    let reversed = filtersStore.getState('activities').padStart(activityInputs.length, '0').split('').map((bit: string) => bit === '0' ? '1' : '0').join('')
    filtersStore.setState('activities', reversed)
})


let elEntrygroupContainer = document.getElementById("entrygroups")!

// Handle highlights based on sum.
let elsEntryButtons = document.querySelectorAll("button[data-sum]") as NodeListOf<HTMLButtonElement>
function highlightEntriesWithSum(sum: number) {
    let instances = 0

    elsEntryButtons.forEach((btn: HTMLButtonElement) => {
        let isActive = btn.dataset.sum === sum.toString()
        btn.classList.toggle("active", isActive)
        if (isActive) {
            instances++
        }
    })

    if (instances > 1) {
        elEntrygroupContainer.classList.add("hovered")
    }
}
function removeHighlight() {
    elEntrygroupContainer.classList.remove("hovered")
}
elsEntryButtons.forEach((el: HTMLButtonElement) => {
    el.addEventListener("mouseenter", () =>
        highlightEntriesWithSum(+el.dataset.sum),
    )
    el.addEventListener("focus", (e: FocusEvent) =>
        highlightEntriesWithSum(+el.dataset.sum),
    )
    el.addEventListener("mouseleave", (e: MouseEvent) => removeHighlight())
    el.addEventListener("blur", (e: FocusEvent) => removeHighlight())
})

// Handle hiding entries that do not match current filters

let elEntryLoader = document.getElementById("entry-loader")
let elEntryWrapper = document.getElementById("entry-wrapper")!
function openEntry(url: string) {
    elEntryLoader.classList.add("loading-entry")

    let el = document.querySelector(
        `button[data-entry-url="${url}"]`,
    ) as HTMLButtonElement
    fetch(el.dataset.entryUrl!, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
        },
    })
        .then((response: Response) => response.text())
        .then((html: string) => {
            elEntryWrapper.innerHTML = html

            window.persistedMapState.setFocusedEntry(
                +el.dataset.entrygroup,
                +el.dataset.entry,
            )

            elEntryWrapper
                .querySelector("button.close-entry")!
                .addEventListener("click", () => closeEntry())
        })
        .catch((err: unknown) =>
            showError(
                "An error occurred while loading the entry. Please try again later.",
                err,
            ),
        )
        .finally(() => {
            elEntryLoader.classList.remove("loading-entry")
        })
}

function closeEntry() {
    elEntryWrapper.innerHTML = ""
    window.persistedMapState.resetFocusedEntry()
}

document
    .querySelectorAll("button[data-entry-url]")
    .forEach((el: HTMLButtonElement) => {
        el.addEventListener("click", (e: MouseEvent) =>
            openEntry(el.dataset.entryUrl!),
        )
    })


if (window.persistedMapState.focusedEntry) {
    let [entrygroup, entry] = window.persistedMapState.focusedEntry
    let el = document.querySelector(
        `button[data-entrygroup="${entrygroup}"][data-entry="${entry}"]`,
    ) as HTMLButtonElement
    openEntry(el.dataset.entryUrl!)
}

try {
    let elMapWrapper = document.getElementById('map-wrapper')!
    let $map = select<SVGElement,  any>("#map")
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

    window.zoomIn = () => zoomHandler.scaleBy($map, 1.2)
    window.zoomOut = () => zoomHandler.scaleBy($map, 0.8)

    for (const node of window.nodes as (Node & Partial<ProcessedNode>)[]) {
        let el = document.querySelector(
            `[data-node="${node.id}"]`,
        ) as SVGElement | null
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
    // if (IN_PRODUCTION) {
        // Sentry?
    // }

    console.error(err)

    showError(
        "An error occurred while loading the map. Please try again later.",
        err,
    )
}

function drawSectorArea(node: {
    size: [number, number];
    position?: [number, number];
    sector: Sector;
} & Node) {
    // TODO
}

function renderMap() {
    showAppState("loading")

    let nodes: ProcessedNode[] = []
    let stack = []

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node &
            Partial<ProcessedNode> & {el: SVGElement}

        node.el.classList.add("invisible")
        node.el.ariaHidden = "true"
        node.el.style.transform = ""
    }

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node &
            Partial<ProcessedNode> & {el: SVGElement}

        if (node.od === 0) {
            let entryIds = window.lookup.entrygroups[node.id].entries
            let filteredIds = []

            for (const entryId of entryIds) {
                let entry = window.lookup.entries[entryId]
                let elEntry = document.querySelector(`button[data-entrygroup="${node.id}"][data-entry="${entryId}"]`) as HTMLButtonElement

                let shouldFilter = shouldFilterEntry({
                    activities: filtersStore.getState('activities'),
                    lens_technical: filtersStore.getState('lens_technical'),
                    lens_governance: filtersStore.getState('lens_governance'),
                    activityCount: activityInputs.length
                }, entry)


                elEntry.classList.toggle("matches-filters", !shouldFilter)

                if (!shouldFilter) {
                    filteredIds.push(entryId)
                }
            }

            node.filtered = filteredIds.length === 0
        }

        if (!(node.el instanceof SVGForeignObjectElement)) {
            throw new Error(
                `Element for node ${node.id} is not a foreignObject, but a ${node.el.tagName}`,
            )
        }

        // We set the <foreignObject> with a height of 100% and a w of 100%
        // because we don't want to compute the size of the elements server-side
        // but this means that we get the wrong bounds.
        if (node.el.firstElementChild === null) {
            throw new Error(
                "It is expected that the foreignObject representing the node has a single child to compute its real bounding box, not the advertised (100%, 100%)",
            )
        }

        // getBoundingClientRect() is transform-aware, so the zoom will mess everything up on subsequent renders.
        // We need to use offsetWidth and offsetHeight instead.
        node.size = [
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

    let parentIdToNode: Record<number, ProcessedNode> = {[root.id]: root}
    let deltaFromSiblings: Record<number, number> = {}

    if (nodes.length === 0) {
        showAppState("empty")
        return
    }

    for (let i = nodes.length - 1; i >= 0; i--) {
        let node = nodes[i]

        if (!deltaFromSiblings[node.parentId]) {
            deltaFromSiblings[node.parentId] = 0
        }

        let parent = parentIdToNode[node.parentId]
        if (!parent) {
            console.log(node)
            continue
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

        node.el.classList.remove("invisible")
        node.el.ariaHidden = "false"
        node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`
    }

    showAppState("success")
}
