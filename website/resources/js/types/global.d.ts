import {Node} from "@/types/index"

export {}

declare global {
    interface Window {
        nodes: Node[]
        bitmaskLength: number
        filterData: Record<number, [number, number, boolean]>
        filterMetadata: [number]
        andOrMask: number
    }
}
