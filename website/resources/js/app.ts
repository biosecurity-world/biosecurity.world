import * as Sentry from "@sentry/browser";
import {select, zoom} from "d3"

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


;(async function () {
    switchAppState(STATE_LOADING, {})

    try {
        const mapDataResponse = await fetch('/data.json')
        const rawMapData = await mapDataResponse.json()

        switchAppState(STATE_SUCCESS, {
            mapData: rawMapData
        })
    } catch (e) {
        if (IN_PRODUCTION) {
            Sentry.captureException(e)
        }

        switchAppState(STATE_ERROR, {
            message: "It looks like we are having trouble accessing the map.",
            showReloadButton: true
        })
    }
})()

function switchAppState<S extends AppState>(
    newState: S,
    args: (S extends "success" ? {
        mapData: any
    } : (
        S extends "error" ? {
            message: string,
            showReloadButton: boolean
        } : (
            S extends "loading" ? {
                //
            } : {})))
): void {
    let allStates = document.querySelectorAll("#app > [data-state]")

    switch (newState) {
        case "loading":
            //
            break
        case "error":
            renderErrorState(args.message, args.showReloadButton)
            break
        case "success":
            try {
                renderSuccessState(args.mapData)
            } catch (e) {
                if (IN_PRODUCTION) {
                    Sentry.captureException(e)
                }

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
        state.style.opacity = newState === state.dataset.state ? '1' : '0'
    })
}

function renderErrorState(message: string, showReloadButton: boolean) {
    const stateEl = document.querySelector("[data-state='error']")

    stateEl.querySelector('.reason').innerHTML = message
    stateEl.querySelector('.reload-button').hidden = showReloadButton === false
}

function renderSuccessState(mapData: any) {
    let $map = select<SVGElement, {}>('#map')
    let $zoomWrapper = select<SVGGElement, {}>('#zoom-wrapper')
    let $paneWrapper = select<SVGGElement, {}>('#pane-wrapper')

    // Resize-, zoom-dependent variables
    let mapWidth = $map.node().clientWidth
    let mapHeight = $map.node().clientHeight
    let computeMapCenter = () => [mapWidth / 2, mapHeight / 2]

    let zoomHandler = zoom()
        .on('zoom', e => $zoomWrapper.attr('transform', e.transform))
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

        $paneWrapper.attr('transform', `translate(${mapCenter})`)

        zoomHandler.translateTo($map, mapCenter[0], mapCenter[1])
        zoomHandler.translateExtent([
            [-mapWidth * 0.5, -mapHeight * 0.5],
            [mapWidth * 1.5, mapHeight * 1.5]
        ])
    })
}
