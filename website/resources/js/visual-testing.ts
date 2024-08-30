import {D3ZoomEvent, select, zoom} from "d3";
import debug from "@/debug";
import {PVertex} from "@/index";

const colors = [
    "rebeccapurple",

    "olive", "yellow", "teal", "navy", "mediumturquoise", "orangered", "peru", "crimson", "saddlebrown", "darkgoldenrod", "goldenrod", "dodgerblue", "deeppink", "cyan", "green", "lightcoral", "maroon", "darkgreen", "darkorange", "blue", "red", "darkseagreen", "palegreen", "mediumvioletred", "sienna", "hotpink", "tan", "purple", "gold", "darkslategray", "chocolate"
];

(async () => {
    try {
        const $map = select('#map')
        const $centerWrapper = select('#center-wrapper')
        const $zoomWrapper = select('#zoom-wrapper')
        const $root = select('#cartesian-flip')

        let mapWidth = $map.node().clientWidth
        let mapHeight = $map.node().clientHeight
        let computeMapCenter = () => [mapWidth / 2, mapHeight / 2]

        $centerWrapper.attr('transform', `translate(${computeMapCenter()})`)

        const zoomHandler = zoom().on('zoom', (e: D3ZoomEvent<SVGGElement, unknown>) => {
            $zoomWrapper.attr('transform', e.transform.toString());
        })
            .scaleExtent([0.1, 10])

        window.addEventListener('resize', () => {
            mapWidth = $map.node().clientWidth
            mapHeight = $map.node().clientHeight
            let mapCenter = computeMapCenter()

            $centerWrapper.attr('transform', `translate(${mapCenter})`)
        })

        $map.call(zoomHandler)

        let defaultMinX0 = 0

        console.table(window.testCase.map(box => {
            return {
                delta: box.sector[0],
                theta: box.sector[1],
                width: box.width,
                length: box.length,
                minX0: box.minX0 ?? defaultMinX0
            }
        }))

        console.log("=== CONSOLE OUTPUT BELOW ===")

        for (let i = 0; i < window.testCase.length; i++) {
            let box = window.testCase[i]
            let color = colors[i % colors.length]

            debug().vertex({
                vertex: {sector: box.sector, size: [box.length, box.width]} as PVertex,
                color
            })
        }

        debug().flush($root)

    } catch (e) {
        console.error(e)
        document.body.innerHTML = `<pre>${e.message}
${e.stack}</pre>`
    }


    // Puppeteer waits for this to be true (with a 30s timeout) to take a screenshot.
    window.visualDiffReady = true
})()


