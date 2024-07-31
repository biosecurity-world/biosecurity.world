import {Graph, PGraph} from "./index";
import {select, Selection} from "d3";
import renderCategoryLayer from "./layers/category";

export type MapLayer = "category" | "entrygroup" | "entry"
export const LAYER_CATEGORY: MapLayer = "category"
export const LAYER_ENTRYGROUP: MapLayer = "entrygroup"
export const LAYER_ENTRY: MapLayer = "entry"

export type LayerContext = {
    $map: Selection<SVGElement, {}, HTMLElement, any>;

    $zoomWrapper: Selection<SVGGElement, {}, HTMLElement, any>;
    $centerWrapper: Selection<SVGGElement, {}, HTMLElement, any>;

    $layer?: Selection<SVGGElement, {}, HTMLElement, any>

    graph: Graph,
    current: MapLayer
}


export type LayerSwitchEventParams = { layer: MapLayer, args: any }

export function switchMapLayer(layer: MapLayer, args: any = null) {
    window.dispatchEvent(new CustomEvent<LayerSwitchEventParams>("layerswitch", {
        detail: { layer, args }
    }))
}

export function renderMapLayer(ctx: LayerContext, layer: MapLayer, args: any = null): LayerContext {
    ctx.$layer = select(`[data-layer="${layer}"]`) as Selection<SVGGElement, {}, HTMLElement, any>

    ;({
        [LAYER_CATEGORY]: renderCategoryLayer,
        [LAYER_ENTRYGROUP]: renderEntryGroupsLayer,
        [LAYER_ENTRY]: renderEntryLayer
    }[layer](ctx, args));

    ctx.current = layer

    return ctx
}

export function renderEntryGroupsLayer(ctx: LayerContext, args: null) {
    ctx.$layer
        .append('rect')
        .attr('width', 300)
        .attr('height', 300)
        .attr('fill', 'rebeccapurple')
        .on('click', () => switchMapLayer(LAYER_ENTRY))
}
export function renderEntryLayer(ctx: LayerContext, args: null) {
    ctx.$layer
        .append('rect')
        .attr('width', 500)
        .attr('height', 500)
        .attr('fill', 'red')
        .on('click', () => switchMapLayer(LAYER_CATEGORY))
}
