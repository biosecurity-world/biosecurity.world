import {SectorType, NodeType} from "./data";


export type Entrygroup = {
    "@type": NodeType.Entrygroup
    entries: number[]
}

export type Category = {
    label: string
    "@notionId" : string
    "@type": NodeType.Category
}

export type Entry = {
    id: string
    label: string
    link: string
    description: string
    organizationType: string
    interventionFocuses: string[]
    activityType: string[]
    locationHints: string[]
    gcbrFocus: boolean
    logo: {
        url: string
        filled: boolean
    }
    "@notionId": string
    "@type": NodeType.Entry
}

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
