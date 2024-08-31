export type Sector = [number, number]

export type Node = {
    id: number
    od: number
    depth: number
    trail: number[]
    parentId: number
}


export type ProcessedNode = {
    weight: number
    edge: [number, number]
    size: [number, number] // length, width
    position?: [number, number]
    sector: Sector

    el: SVGElement
} & Node

