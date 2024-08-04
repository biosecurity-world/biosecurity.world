import {D3ZoomEvent, Selection, select, zoom} from "d3"
import {IN_PRODUCTION, PI, PI_3, PI_4, PI_6, shortestDistanceBetweenRectangles, switchState} from "./utils";
import {PVertex} from "./index";
import {fitToSector, prepare, sectorize} from "./data";
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

        zoomHandler.translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])
        zoomHandler.translateTo($map, mapCenter[0], mapCenter[1])
    })

    let tree: PVertex = window.rawMap
    prepare(tree)
    sectorize(tree, [0, 2 * PI], 0)
    draw($background, tree)

    // debug().ray({angle: PI_3})
    // debug().ray({angle: PI_6 * 4})
    //
    // let p =  { id: 0, sector: [PI_3, PI_6 * 4], size: [250, 35], children: []} as PVertex
    // debug().vertex({ vertex: p})
    // debug().vertex({
    //     vertex: { id: 1, sector: [PI_3 + 0.1, PI_6 * 4 -0.1], size: [200, 35], children: []} as PVertex,
    //     parent: p,
    //     minDistance: 100
    // })

    // debug().cartesianPlane()
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

function draw(
    $g: Selection<SVGGElement, {}, HTMLElement, unknown>,
    vertex: PVertex,
    parent: PVertex | null = null
) {
    debug().ray({ angle: vertex.sector[0] })
    debug().ray({ angle: vertex.sector[1] })
    vertex.position = fitToSector(vertex, parent, vertex.depth === 1 ? 50 : 0)

    vertex.el.classList.remove('invisible')
    vertex.el.ariaHidden = 'false'
    vertex.el.style.transform = `translate(${vertex.position[0]}px, ${vertex.position[1]}px)`


    if (parent) {
        let [l, w] = vertex.size
        let [x, y] = vertex.position

        let [Pl, Pw] = parent.size
        let [Px, Py] = parent.position

        let closestPoint = [
            [x, y + w/2],
            [x + l, y + w/2],
            [x + l/2, y + w],
            [x + l/2, y]
        ].reduce((prev, curr) => {
            return distance(prev, parent.position) < distance(curr, parent.position) ? prev : curr
        })

        let closestPointToParent = [
            [Px, Py + Pw/2],
            [Px + Pl, Py + Pw/2],
            [Px + Pl/2, Py + Pw],
            [Px + Pl/2, Py]
        ].reduce((prev, curr) => {
            return distance(prev, vertex.position) < distance(curr, vertex.position) ? prev : curr
        })


        vertex.edge = closestPoint

        $g.append('path')
            .attr('d', `M${closestPointToParent} L${closestPoint}`)
            .attr('stroke', '#ddd')
            .attr('stroke-width', 2)
    }

    for (const child of vertex.children) {
        draw($g, child, vertex)
    }
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

function distance(a: [number, number], b: [number, number]) {
    return Math.sqrt((a[0] - b[0]) ** 2 + (a[1] - b[1]) ** 2)
}
