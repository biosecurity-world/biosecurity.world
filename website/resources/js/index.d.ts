export type Vertex = {
    id: number
    parentId: number
    children: Vertex[]
}


export type Sector = [number, number]
export type PVertex = {
    id: number
    parentId: number
    depth: number
    weight: number
    od: number
    children: PVertex[]
    el: SVGElement

    edge: [number, number]
    size: [number, number] // length, width
    position?: [number, number]
    sector: Sector
}

export type Root = {
    "@type": NodeType.Root
}
