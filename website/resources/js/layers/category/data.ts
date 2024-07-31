import {Graph, PGraph, PVertex} from "../../index";

export function processRawGraph(graph: Graph): PGraph {
    for (let idx = 0; idx < graph.vertices.length; idx++) {
        let _vertex = graph.vertices[idx]
        let vertex = _vertex as PVertex

        vertex.highlighted = false
        vertex.filtered = false
    }

    return graph as PGraph
}

