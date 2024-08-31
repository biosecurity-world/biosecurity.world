import {Node} from "@/index";

export {};

declare global {
    interface Window {
        nodes: Node[]
    }
}
