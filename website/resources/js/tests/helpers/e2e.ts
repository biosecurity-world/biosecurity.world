import {D3ZoomEvent, select, zoom} from "d3";
import {debug} from "@/utils";
import {ProcessedNode} from "@/types";

declare global {
    interface Window {
        testCase: {
            sector: [number, number],
            width: number,
            length: number,
            minX0?: number
        }[]

        visualDiffReady: boolean
    }
}

const cssColors = [
    "rebeccapurple",

    "peru", "olive", "teal", "navy", "mediumturquoise", "orangered", "crimson", "saddlebrown", "darkgoldenrod", "goldenrod", "dodgerblue", "deeppink", "cyan", "green", "lightcoral", "maroon", "darkgreen", "darkorange", "blue", "red", "darkseagreen", "palegreen", "mediumvioletred", "sienna", "hotpink", "tan", "purple", "gold", "darkslategray", "chocolate"
];

(async () => {
    try {
        const $map = select<SVGElement, {}>('#map')
        const $zoomWrapper = select<SVGGElement, {}>('#zoom-wrapper')
        const $centerWrapper = select<SVGGElement, {}>('#center-wrapper')

        let mapWidth = $map.node()!.clientWidth
        let mapHeight = $map.node()!.clientHeight


        $centerWrapper.attr('transform', `translate(${mapWidth / 2},${mapHeight / 2})`)

        const zoomHandler = zoom().on('zoom', (e: D3ZoomEvent<SVGGElement, unknown>) => {
            $zoomWrapper.attr('transform', e.transform.toString());
        })
        $map.call(zoomHandler)

        console.table(window.testCase)

        console.log("=== CONSOLE OUTPUT BELOW ===")
        for (let i = 0; i < window.testCase.length; i++) {
            let box = window.testCase[i]
            let color = cssColors[i % cssColors.length]

            debug().node({
                node: {sector: box.sector, size: [box.length, box.width]} as ProcessedNode,
                color
            })
        }

        debug().flush($centerWrapper)

    } catch (e: unknown) {
        console.error(e)
        document.body.innerHTML = `<pre>${e.message}
${e.stack}</pre>`
    }


    // Puppeteer waits for this to be true (with a 30s timeout) to take a screenshot.
    window.visualDiffReady = true
})()


