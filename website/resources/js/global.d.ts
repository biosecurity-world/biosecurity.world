import {Vertex} from "@/index";

export {};

declare global {
    interface Window {
        nodes: Vertex[]
    }
}
