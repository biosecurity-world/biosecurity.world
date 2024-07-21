import * as Sentry from "@sentry/browser";
import { zoom, select } from "d3"

type Layer = "layer-error" | "layer-loading" | "layer-entry" | "layer-entry-groups" | "layer-categories"
const LAYER_ERROR:Layer = "layer-error"
const LAYER_LOADING: Layer = "layer-loading"
const LAYER_ENTRY: Layer = "layer-entry"
const LAYER_ENTRY_GROUPS: Layer = "layer-entry-groups"
const LAYER_CATEGORIES: Layer = "layer-categories"

const IN_PRODUCTION = import.meta.env.PROD === true

if (IN_PRODUCTION) {
    Sentry.init({
        dsn: import.meta.env.VITE_SENTRY_DSN_PUBLIC
    })
}



(async function () {
    let $map = select<SVGElement, {}>('#map')
    let $zoomWrapper = select<SVGGElement, {}>('#zoom-wrapper')
    let $paneWrapper = select<SVGGElement, {}>('#pane-wrapper')
    let $layersWrapper = select<SVGGElement, {}>('#layers-wrapper')

    // Layers
    let layers = $layersWrapper.node().childNodes
    let $layerError = select<SVGGElement, {}>('#layer-error')
    let $layerLoading = select<SVGGElement, {}>('#layer-loading')
    let $layerEntry = select<SVGGElement, {}>('#layer-entry')
    let $layerCategories = select<SVGGElement, {}>('#layer-categories')
    let $layerEntryGroups = select<SVGGElement, {}>('#layer-entry-groups')

    // Resize-, zoom-dependent variables
    let mapWidth = $map.node().clientWidth
    let mapHeight = $map.node().clientHeight
    let computeMapCenter = () => [mapWidth / 2, mapHeight / 2 ]

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

})()
