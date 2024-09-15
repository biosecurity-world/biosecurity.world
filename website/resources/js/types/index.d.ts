export type AppState = "error" | "success" | "loading" | "empty";
export type AppStateParameters = {
    error: { message: string; error: unknown };
    success: { };
    loading: { };
    empty: { };
};
export type AppStateChange = {
    [K in AppState]: { state: K; params: AppStateParameters[K] };
}[AppState];
export type AppStateChangeEvent = CustomEvent<AppStateChange>;


export type Sector = [number, number]

export type Node = {
    id: number
    od: number
    depth: number
    parent: number
    filtered: boolean

    entries?: number[]
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
