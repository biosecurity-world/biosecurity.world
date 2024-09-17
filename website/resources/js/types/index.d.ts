export type AppState = "error" | "success" | "loading" | "empty"
export type AppStateParameters = {
    error: {message: string; error: unknown}
    success: {}
    loading: {}
    empty: {}
}
export type AppStateChange = {
    [K in AppState]: {state: K; params: AppStateParameters[K]}
}[AppState]
export type AppStateChangeEvent = CustomEvent<AppStateChange>

export type Sector = [number, number]

export type Node = {
    id: number
    parent: number
    od: number
    depth: number
    trail: number[]
    entries?: number[]

    filtered: boolean
}

export type ProcessedNode = {
    weight: number
    edge: [number, number]
    size: [number, number]
    sector: Sector
    position?: [number, number]

    el: SVGElement
} & Node
