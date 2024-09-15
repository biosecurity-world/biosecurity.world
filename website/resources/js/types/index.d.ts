export type Sector = [number, number]

export type Node = {
    id: number
    od: number
    depth: number
    parent: number
    filtered: boolean

    entries?: number[]
    activities?: number
    lenses?: number
}

export type ProcessedNode = {
    weight: number
    childrenWeight: number
    edge: [number, number]
    size: [number, number] // length, width
    position?: [number, number]
    sector: Sector

    el: SVGElement
} & Node
