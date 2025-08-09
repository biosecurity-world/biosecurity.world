import {Node, AppStateChangeEvent} from "@/types/index"

declare global {
    interface Window {
        nodes: Node[]
        bitmaskLength: number
        filterData: Record<number, [number, number, number, boolean]>
        filterMetadata: [number]
        andOrMask: number
    }

    interface WindowEventMap {
        appstatechange: AppStateChangeEvent
    }
}
