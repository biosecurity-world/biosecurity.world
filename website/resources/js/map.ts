import {D3ZoomEvent, select, Selection, zoom, zoomIdentity} from "d3"
import {debug, eq, getLabel, gt, lt, PI, PIPI, throttle} from "./utils"
import {Node, ProcessedNode, Sector} from "@/types"
import {fitToSector} from "./layout"
import FiltersStateStore, {MapStateStore} from "@/store"
import {shouldFilterEntry} from "@/filters";

type AppState = "error" | "success" | "loading" | "empty"

let cssColors = [
    "rebeccapurple",

    "peru", "olive", "teal", "navy", "mediumturquoise", "orangered", "crimson", "saddlebrown", "darkgoldenrod", "goldenrod", "dodgerblue", "deeppink", "cyan", "green", "lightcoral", "maroon", "darkgreen", "darkorange", "blue", "red", "darkseagreen", "palegreen", "mediumvioletred", "sienna", "hotpink", "tan", "purple", "gold", "darkslategray", "chocolate"
];

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

function renderMap($svg: Selection<SVGGElement, any, HTMLElement, any>) {
    let nodes: ProcessedNode[] = []
    let stack: ProcessedNode[] = []

    // Reset the map
    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node &
            Partial<ProcessedNode> & { el: SVGElement }

        node.el.classList.add("off-screen")
        node.el.ariaHidden = "true"
        node.el.style.transform = ""
    }

    $svg.selectAll('.layer-bg, .layer-fg').remove()

    let $bg = $svg.append('g').classed('layer-bg', true)
    let $fg = $svg.append('g').classed('layer-fg', true)

    let maxDepth = 0

    let idToNode: Record<number, ProcessedNode> = {}

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node & Partial<ProcessedNode> & { el: SVGElement }

        idToNode[node.id] = node

        if (node.depth >= maxDepth) {
            maxDepth = node.depth
        }

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

    let root = stack.pop()!
    root.sector = [0, PIPI]
    root.position = fitToSector(root, [{ position: [0, 0], size: root!.size}])

    if (nodes.length === 0) {
        return showAppState("empty")
    }

    showNode(root)

    let deltaFromSiblings: Record<number, number> = {}

    let level2NodeCount = 0

    for (let i = nodes.length - 1; i >= 0; i--) {
        let node = nodes[i]

        let parent = idToNode[node.parent]

        if (!deltaFromSiblings[node.parent]) {
            deltaFromSiblings[node.parent] = parent.sector[0]
        }

        let delta = deltaFromSiblings[node.parent]
        let siblingsWeight = parent.weight - (parent.size[0] * parent.size[1])
        let alpha = (node.weight / siblingsWeight) * (parent.sector[1] - parent.sector[0])
        let theta = delta + alpha
        node.sector = [delta, theta]
        node.position = fitToSector(node, node.trail.map((id) => idToNode[id]), 100)

        showNode(node)

        deltaFromSiblings[node.parent] = theta

        let r = 2 * window.innerWidth

        // if (node.depth === 1) {
        //     console.log(node.id, [delta, theta])
        //         $fg.append('line')
        //             .classed('ray', true)
        //             .attr('x1', 0)
        //             .attr('y1', 0)
        //             .attr('x2', Math.cos(theta) * r * 2)
        //             .attr('y2', Math.sin(theta) * r * 2)
        //             .attr('stroke', '#d1d5db')
        // } else
        if (node.depth === 1) {
            let color = level2NodeCount % 2 === 0 ? "#f3f4f6" : "#f9fafb"

            $bg.append("path")
                .classed('background-sector', true)
                .attr('d', [
                    `M 0,0`,
                    `L ${r * Math.cos(delta)} ${r * Math.sin(delta)}`,
                    `A ${r} ${r} 0 0 1 ${r * Math.cos(theta)} ${r * Math.sin(theta)}`,
                    `Z`,
                ].join(" "))
                .attr("fill", color)

            level2NodeCount++
        }
    }
}

function showNode(node: ProcessedNode) {
    node.el.classList.remove("off-screen")
    node.el.ariaHidden = "false"
    node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`

    // if (node.id === node.parent) {
    //     return
    // }
    // debug().point({
    //     p: node.position,
    //     label: node.id,
    // })
}

;(async function () {
    try {
        let elMapWrapper = document.getElementById('map-wrapper')!

        let $map = select<SVGElement, any>("#map")
        let mapContentRes = await fetch('/m')
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

            el.addEventListener("click", () => {
                openEntry(el);
            })
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

        let cb = () => {
            debug().clear()
            showAppState('loading')

            renderMap(select('#background'))

            showAppState('success')
            debug().flush($centerWrapper)
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
})()

