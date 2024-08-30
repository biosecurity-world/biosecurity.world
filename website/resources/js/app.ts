import 'htmx.org'
import {D3ZoomEvent, select, zoom} from "d3"
import {gt, IN_PRODUCTION, lt, PIPI, switchState} from "./utils";
import {PVertex} from "./index";
import {fitToSector} from "./data";
import debug from "@/debug";


type AppState = "error" | "success" | "loading"
const STATE_SUCCESS: AppState = "success"
const STATE_ERROR: AppState = "error"

let switchAppState = (newState: AppState) => switchState(newState, '.app-state', 'state')

const SHOW_RELOAD_BUTTON = 1

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
        .scaleExtent(IN_PRODUCTION ? [0.5, 4] : [0.5, 10])
        .translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])
    $map.call(zoomHandler)

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

    let nodes: PVertex[] = window.nodes as PVertex[]
    let sortedNodes: PVertex[] = []

    let stack = []

    for (let i = 0; i < nodes.length; i++) {
        let node = nodes[i]

        let el = document.querySelector(`[data-vertex="${node.id}"]`) as SVGElement | null
        if (!el) {
            throw new Error(`Vertex with id ${node.id} has no corresponding element in the DOM`)
        }
        node.el = el

        let bounds: DOMRect
        // We set the <foreignObject> with a height of 100% and a w of 100%
        // because we don't want to compute the size of the elements server-side
        // but this means that if we do vertex.el.getBoundingClientRect()
        // we get the wrong bounds.
        if (node.el instanceof SVGForeignObjectElement) {
            if (node.el.childElementCount !== 1) {
                throw new Error("It is expected that the foreignObject representing the vertex has a single child to compute its real bounding box, not the advertised (100%, 100%)")
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

    let parentIdToNode = {[root.id]: root}
    let deltaFromSiblings = {}

    for (let i = sortedNodes.length - 1; i >= 0; i--) {
        let node = sortedNodes[i]

        if (!deltaFromSiblings[node.parentId]) {
            deltaFromSiblings[node.parentId] = 0
        }

        let parent = parentIdToNode[node.parentId]

        let delta = parent.sector[0] + deltaFromSiblings[parent.id]
        let alpha = (node.weight / parent.weight) * (parent.sector[1] - parent.sector[0])
        node.sector = [delta, delta + alpha]

        console.log(node.sector);

        parentIdToNode[node.id] = node

        deltaFromSiblings[parent.id] += node.sector[1] - node.sector[0]

        if (lt(node.sector[0], 0) || gt(node.sector[1], PIPI)) {
            throw new Error(`Sector ${node.sector} is not in the range [0, 2*PI]`)
        }

        debug().ray({angle: node.sector[0]})
        debug().ray({angle: node.sector[1]})
        node.position = fitToSector(node, parent, 0)

        node.el.classList.remove('invisible')
        node.el.ariaHidden = 'false'
        node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`
    }

    debug().flush($background)

    switchAppState(STATE_SUCCESS)
} catch (e) {
    if (IN_PRODUCTION) {
        // Report the error to the server
    }

    console.error(e)

    updateErrorState(e, "It looks like we are having trouble rendering the map.")
    switchAppState(STATE_ERROR)
}


function updateErrorState(
    e: Error,
    message: string,
    flags: number = 0
) {
    const elStateContainer = document.querySelector("#app > [data-state='error']")

    let reason = elStateContainer.querySelector('.reason') as HTMLParagraphElement
    let reloadButton = elStateContainer.querySelector('.reload-button') as HTMLButtonElement
    let debug = elStateContainer.querySelector('.debug') as HTMLPreElement

    if (!IN_PRODUCTION) {
        debug.hidden = false
        debug.textContent = `${e.name}: ${e.message}\n${e.stack}`
    }

    reason.innerHTML = message
    reloadButton.hidden = (flags & SHOW_RELOAD_BUTTON) === 0
}
