import * as Sentry from "@sentry/browser";
import {D3ZoomEvent, select, zoom} from "d3"
import {Graph} from "./index";
import {LAYER_CATEGORY, LayerContext, LayerSwitchEventParams, renderMapLayer} from "./layers";

const IN_PRODUCTION = import.meta.env.PROD === true
if (IN_PRODUCTION) {
    Sentry.init({
        dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC
    })
}


type AppState = "error" | "success" | "loading"
const STATE_SUCCESS: AppState = "success"
const STATE_ERROR: AppState = "error"
const STATE_LOADING: AppState = "loading"


function switchAppState<S extends AppState>(
    newState: S,
    args: (S extends "success" ? {
        rawGraph: Graph
    } : (
        S extends "error" ? {
            message: string,
            showReloadButton: boolean
        } : (
            S extends "loading" ? {
                //
            } : {})))
): void {
    let allStates = document.querySelectorAll("[data-state]")

    switch (newState) {
        case "loading":
            //
            break
        case "error":
            renderErrorState(args.message, args.showReloadButton)
            break
        case "success":
            try {
                renderSuccessState(args.rawGraph)
            } catch (e) {
                if (IN_PRODUCTION) {
                    Sentry.captureException(e)
                }

                console.error(e)

                // We want to be careful not to create an infinite loop here.
                // This is fine for now.
                return switchAppState(STATE_ERROR, {
                    message: "It looks like we are having trouble rendering the map.",
                    showReloadButton: false
                })
            }
            break
    }

    allStates.forEach((state: HTMLElement) => {
        let isActive = newState === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.style.pointerEvents = isActive ? 'initial' : 'none'
        state.style.opacity = newState === state.dataset.state ? '1' : '0'
    })
}

function renderErrorState(message: string, showReloadButton: boolean) {
    const stateEl = document.querySelector("[data-state='error']")

    stateEl.querySelector('.reason').innerHTML = message
    stateEl.querySelector('.reload-button').hidden = showReloadButton === false
}


function renderSuccessState(rawGraph: Graph) {
    let $map = select<SVGElement, {}>('#map')
    let $zoomWrapper = select<SVGGElement, {}>('#zoom-wrapper')
    let $centerWrapper = select<SVGGElement, {}>('#center-wrapper')

    // Resize-, zoom-dependent variables
    let mapWidth = $map.node().clientWidth
    let mapHeight = $map.node().clientHeight
    let computeMapCenter = () => [mapWidth / 2, mapHeight / 2]

    $centerWrapper.attr('transform', `translate(${computeMapCenter()})`)

    let layerCtx: LayerContext = {
        $map,
        $zoomWrapper,
        $centerWrapper: $centerWrapper,

        graph: rawGraph,
        current: LAYER_CATEGORY
    }

    let zoomHandler = zoom()
        .on('zoom', (e: D3ZoomEvent<SVGGElement, unknown>) => {
            $zoomWrapper.attr('transform', e.transform.toString());

            // We'd want to switch layers based on the zoom here ideally.
        })
        .scaleExtent(IN_PRODUCTION ? [0.5, 4] : [0.5, 10])
        .translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])

    $map.call(zoomHandler)
    window.addEventListener('resize', () => {
        mapWidth = $map.node().clientWidth
        mapHeight = $map.node().clientHeight
        let mapCenter = computeMapCenter()

        $centerWrapper.attr('transform', `translate(${mapCenter})`)

        zoomHandler.translateTo($map, mapCenter[0], mapCenter[1])
        zoomHandler.translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])
    })

    let allLayers = document.querySelectorAll("[data-layer]")

    window.addEventListener('layerswitch', (e: CustomEvent<LayerSwitchEventParams>) => {
        layerCtx = renderMapLayer(layerCtx, e.detail.layer, e.detail.args)

        allLayers.forEach((state: SVGGElement) => {
            let isActive = e.detail.layer === state.dataset.layer


            state.ariaHidden = isActive ? "false" : "true"
            state.style.pointerEvents = isActive ? 'initial' : 'none'
            state.style.opacity = isActive ? '1' : '0'
        })
    })

    layerCtx = renderMapLayer(layerCtx, LAYER_CATEGORY)
}


try {
    const rawGraph = await fetch('/data.json').then(res => res.json())

    switchAppState(STATE_SUCCESS, {rawGraph})
} catch (e) {
    if (IN_PRODUCTION) {
        Sentry.captureException(e)
    }

    console.error(e)

    switchAppState(STATE_ERROR, {
        message: "It looks like we are having trouble accessing the map.",
        showReloadButton: true
    })
}
