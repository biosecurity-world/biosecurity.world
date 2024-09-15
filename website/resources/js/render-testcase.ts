import {select, zoom} from "d3";
import {debug} from "@/utils";

const cssColors = [
    "rebeccapurple",

    "peru", "olive", "teal", "navy", "mediumturquoise", "orangered", "crimson", "saddlebrown", "darkgoldenrod", "goldenrod", "dodgerblue", "deeppink", "cyan", "green", "lightcoral", "maroon", "darkgreen", "darkorange", "blue", "red", "darkseagreen", "palegreen", "mediumvioletred", "sienna", "hotpink", "tan", "purple", "gold", "darkslategray", "chocolate"
];

try {
    const $map = select('#map')
    const $zoomWrapper = select('#zoom-wrapper')
    const $centerWrapper = select('#center-wrapper')

    let mapWidth = $map.node().clientWidth
    let mapHeight = $map.node().clientHeight


    $centerWrapper.attr('transform', `translate(${mapWidth / 2},${mapHeight / 2})`)

    const zoomHandler = zoom().on('zoom', (e) => {
        $zoomWrapper.attr('transform', e.transform.toString());
    })
    $map.call(zoomHandler)

    console.table(window.testCase)

    console.log("=== CONSOLE OUTPUT BELOW ===")
    for (let i = 0; i < window.testCase.length; i++) {
        let box = window.testCase[i]
        let color = cssColors[i % cssColors.length]

        debug().node({
            node: {sector: box.sector, size: [box.length, box.width]},
            color
        })
    }

    debug().flush($centerWrapper)

} catch (e) {
    console.error(e)
    document.body.innerHTML = `<pre>${e.message}
${e.stack}</pre>`
}


// Puppeteer waits for this to be true (with a 5s timeout) to take a screenshot.
window.visualDiffReady = true


