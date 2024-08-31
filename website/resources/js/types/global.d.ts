import {Node} from "@/types/index";

export {};

declare global {
    interface Window {
        nodes: Node[]
    }
}
