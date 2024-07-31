import {LAYER_ENTRYGROUP, LayerContext, switchMapLayer} from "../../layers";
import {processRawGraph} from "./data";

export default function renderCategoryLayer(ctx: LayerContext, args: null) {
    let processedGraph = processRawGraph(ctx.graph)

    ctx.$layer
        .append('rect')
        .attr('width', 100)
        .attr('height', 100)
        .attr('fill', 'black')
        .on('click', () => switchMapLayer(LAYER_ENTRYGROUP))

    console.log("here in category layer");
}
