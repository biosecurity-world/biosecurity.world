import 'htmx.org'
import {D3ZoomEvent, select, zoom} from "d3"
import {gt, IN_PRODUCTION, lt, PIPI} from "./utils";
import {ProcessedNode} from "./types";
import {fitToSector} from "./data";
import debug from "@/debug";

type AppState = "error" | "success" | "loading"

function switchAppState(newState: AppState): void {
    document.querySelectorAll('.app-state').forEach((state: HTMLElement) => {
        let isActive = newState === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.classList.toggle('state-active', isActive)
        state.classList.toggle('state-inactive', !isActive)
    })
}

const router = new Router()

try {
    let $map = select<SVGElement, {}>('#map')
    let $zoomWrapper = select<SVGGElement, {}>('#zoom-wrapper')
    let $centerWrapper = select<SVGGElement, {}>('#center-wrapper')
    let $background = select<SVGGElement, {}>('#background')

    // Resize-, zoom-dependent variables
    let mapWidth = $map.node()!.clientWidth
    let mapHeight = $map.node()!.clientHeight
    let computeMapCenter = () => [mapWidth / 2, mapHeight / 2]

    $centerWrapper.attr('transform', `translate(${computeMapCenter()})`)

    let zoomHandler = zoom()
        .on('zoom', (e: D3ZoomEvent<SVGGElement, unknown>) => $zoomWrapper.attr('transform', e.transform.toString()))
        .scaleExtent([0.5, 2.5])
        .translateExtent([
            [-mapWidth * 2, -mapHeight * 2],
            [mapWidth * 2, mapHeight * 2]
        ])
    $map.call(zoomHandler)

    window.zoomIn = () => zoomHandler.scaleBy($map, 1.2)
    window.zoomOut = () => zoomHandler.scaleBy($map, 0.8)

    window.addEventListener('resize', () => {
        mapWidth = $map.node()!.clientWidth
        mapHeight = $map.node()!.clientHeight
        let mapCenter = computeMapCenter()

        $centerWrapper.attr('transform', `translate(${mapCenter})`)

        zoomHandler.translateTo($map, mapCenter[0], mapCenter[1])
        zoomHandler.translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])
    })

    let nodes: ProcessedNode[] = window.nodes as ProcessedNode[]
    let sortedNodes: ProcessedNode[] = []

    let stack = []

    for (let i = 0; i < nodes.length; i++) {
        let node = nodes[i]

        let el = document.querySelector(`[data-node="${node.id}"]`) as SVGElement | null
        if (!el) {
            throw new Error(`Node with id ${node.id} has no corresponding element in the DOM`)
        }
        node.el = el

        let bounds: DOMRect
        // We set the <foreignObject> with a height of 100% and a w of 100%
        // because we don't want to compute the size of the elements server-side
        // but this means that node.el.getBoundingClientRect() returns the wrong bounds.
        if (node.el instanceof SVGForeignObjectElement) {
            if (node.el.childElementCount !== 1) {
                throw new Error("It is expected that the foreignObject representing the node has a single child to compute its real bounding box, not the advertised (100%, 100%)")
            }

            bounds = node.el.firstElementChild!.getBoundingClientRect()
            // We resize the foreignObject to match the bounding box of its child.
            // This is only useful when inspecting the page.
            node.el.setAttribute('width', bounds.width + 'px')
            node.el.setAttribute('height', bounds.height + 'px')
        } else {
            bounds = node.el.getBoundingClientRect()
        }

        node.size = [Math.ceil(bounds.width), Math.ceil(bounds.height)]
        node.weight = node.size[0] * node.size[1]

        let children = []

        if (node.od > 0) {
            for (let i = 0; i < node.od; i++) {
                let child = stack.pop()
                children.push(child)
                node.weight += child.weight
            }

            children.sort((a, b) => a.weight - b.weight)
            for (const child of children) {
                sortedNodes.push(child)
            }
        }

        stack.push(node)
    }

    let root = stack.pop()
    root.sector = [0, PIPI]

    let parentIdToNode: Record<number, ProcessedNode> = {[root.id]: root}
    let deltaFromSiblings: Record<number, number> = {}

    for (let i = sortedNodes.length - 1; i >= 0; i--) {
        let node = sortedNodes[i]

        if (!deltaFromSiblings[node.parentId]) {
            deltaFromSiblings[node.parentId] = 0
        }

        let parent = parentIdToNode[node.parentId]

        let delta = parent.sector[0] + deltaFromSiblings[parent.id]
        let alpha = (node.weight / parent.weight) * (parent.sector[1] - parent.sector[0])
        node.sector = [delta, delta + alpha]

        parentIdToNode[node.id] = node
        deltaFromSiblings[parent.id] += alpha

        if (lt(node.sector[0], 0) || gt(node.sector[1], PIPI)) {
            throw new Error(`Sector ${node.sector} is not in the range [0, 2*PI]`)
        }

        node.position = fitToSector(node)
        debug().ray({angle: node.sector[0], color: 'blue'})
        debug().ray({angle: node.sector[1], color: 'red'})

        node.el.classList.remove('invisible')
        node.el.ariaHidden = 'false'
        node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`
    }

    debug().flush($background)

    switchAppState('success')
} catch (err: unknown) {
    if (IN_PRODUCTION) {
        // Report the error to the server
    }

    console.error(err)

    const elStateContainer = document.querySelector("[data-state='error']")
    if (!elStateContainer) {
        throw new Error("No error state container found")
    }

    let reason = elStateContainer.querySelector('.reason') as HTMLParagraphElement
    let reloadButton = elStateContainer.querySelector('.reload-button') as HTMLButtonElement
    let debug = elStateContainer.querySelector('.debug') as HTMLPreElement

    if (!IN_PRODUCTION) {
        debug.hidden = false
        debug.textContent = `${err.name}: ${err.message}\n${err.stack}`
    }

    reason.innerHTML = err.message
    reloadButton.hidden = false

    switchAppState('error')
}


