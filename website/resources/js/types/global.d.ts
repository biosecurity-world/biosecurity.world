import {Node} from "@/types/index"
import {MapStateStore} from "@/store"

export {}

declare global {
    interface Window {
        nodes: Node[]
        bitmaskLength: number
        masks: Record<number, number>
        andOrMask: number

        persistedMapState: MapStateStore
    }
}
