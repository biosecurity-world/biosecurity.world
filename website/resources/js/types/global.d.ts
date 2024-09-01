import {Node} from "@/types/index"
import {MapStateStore} from "@/store";

export {}

declare global {
    interface Window {
        // Set in welcome.blade.php
        nodes: Node[]

        persistedMapState: MapStateStore
    }
}
