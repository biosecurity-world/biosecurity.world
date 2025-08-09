import {D3ZoomEvent, select, zoom} from "d3"
import {debug} from "@/utils"
import {fitToSector} from "@/layout"
import type {ProcessedNode} from "@/types/index.d.ts"

declare global {
    interface Window {
        testCase: {
            sector: [number, number]
            width: number
            length: number
            spacing: number
        }[]

        visualDiffReady: boolean
    }
}

const cssColors = [
    "rebeccapurple",
    "peru",
    "olive",
    "teal",
    "navy",
    "mediumturquoise",
    "orangered",
    "crimson",
    "saddlebrown",
    "darkgoldenrod",
    "goldenrod",
    "dodgerblue",
    "deeppink",
    "cyan",
    "green",
    "lightcoral",
    "maroon",
    "darkgreen",
    "darkorange",
    "blue",
    "red",
    "darkseagreen",
    "palegreen",
    "mediumvioletred",
    "sienna",
    "hotpink",
    "tan",
    "purple",
    "gold",
    "darkslategray",
    "chocolate",
]

try {
    const $map = select<SVGElement, {}>("#map")
    const $centerWrapper = select<SVGGElement, {}>("#center-wrapper")
    const $zoomWrapper = select<SVGGElement, {}>("#zoom-wrapper")

    let mapWidth = $map.node()!.clientWidth
    let mapHeight = $map.node()!.clientHeight

    $centerWrapper.attr("transform", `translate(${mapWidth / 2},${mapHeight / 2})`)

    const zoomHandler = zoom().on("zoom", (e: D3ZoomEvent<SVGGElement, unknown>) => {
        $zoomWrapper.attr("transform", e.transform.toString())
    })
    $map.call(zoomHandler as any)

    console.table(window.testCase)
    console.log("=== CONSOLE OUTPUT BELOW ===")

    for (let i = 0; i < window.testCase.length; i++) {
        let box = window.testCase[i]
        let position = fitToSector({sector: box.sector, size: [box.length, box.width]}, [], box.spacing)

        debug().ray({angle: box.sector[0], color: "black"})
        debug().ray({angle: box.sector[1], color: "black"})
        debug().rect({
            p: position,
            length: box.length,
            width: box.width,
            color: cssColors[i % cssColors.length],
        })
        debug().point({p: position})
    }

    debug().flush($centerWrapper)
} catch (e: unknown) {
    if (e instanceof Error) {
        console.error(e)
        document.body.innerHTML = "<pre>" + e.message + "\n" + e.stack + "</pre>"
    } else {
        console.error("An unknown error occurred", e)
        document.body.innerHTML = "<pre>An unknown error occurred: check the console</pre>"
    }
}

// Puppeteer waits for this to be true (with a 5s timeout) to take a screenshot.
window.visualDiffReady = true
