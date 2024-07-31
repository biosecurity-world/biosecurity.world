export type Entry = {
    id: string
    label: string
    link: string
    description: string
    organizationType: string
    interventionFocuses: {
        id: string
        name: string
        color: string
    }[]
    activityType: {
        id: string,
        name: string
        color: string
    }[]
    locationHints: {
        id: string
        name: string
        color: string
    }[]
    gcbrFocus: boolean
    logo: {
        url: string
        filled: boolean
    }
}

export type Vertex = {
    siblingsCount: number
    index: number
    id: string
    label: string
    parentId: string
}

export type PVertex = Vertex & {
    highlighted: boolean
    filtered: boolean
}

export type Graph = {
    vertices: (Vertex & { type: string, children: string })[],
    lookup: Record<string, Entry>,
}

export type PGraph = Graph & {
    vertices: (PVertex & { type: string, children: string })[]
}
