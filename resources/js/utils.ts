import {Selection} from "d3"
import type {AppState, AppStateChange, AppStateParameters, Sector} from "@/types/index.d.ts"

export function changeAppState<T extends AppState>(state: T, params: AppStateParameters[T]) {
    window.dispatchEvent(
        new CustomEvent<AppStateChange>("appstatechange", {
            /* @ts-ignore */
            detail: {state, params},
        }),
    )
}

export const PIPI = Math.PI * 2

let _debugInstance: Debug | null = null

export function debug(): Debug {
    if (_debugInstance === null) {
        _debugInstance = new Debug()
    }

    return _debugInstance
}

class Debug {
    buffer: (($selection: Selection<SVGElement, {}, HTMLElement, unknown>) => void)[] = []

    point(options: {p: [number, number]; color?: string; label?: string}) {
        this.buffer.push(($svg) => {
            $svg.append("circle")
                .classed("debug", true)
                .attr("cx", options.p[0])
                .attr("cy", options.p[1])
                .attr("r", 2)
                .attr("fill", options.color || "red")

            if (options.label) {
                $svg.append("text")
                    .classed("debug", true)
                    .attr("x", options.p[0] + 5)
                    .attr("y", options.p[1] + 5)
                    .attr("fill", "black")
                    .text(options.label)
            }
        })
    }

    rect(options: {p: [number, number]; width: number; length: number; color?: string}) {
        this.buffer.push(($svg) => {
            $svg.append("rect")
                .classed("debug", true)
                .attr("x", options.p[0])
                .attr("y", options.p[1])
                .attr("width", options.length)
                .attr("height", options.width)
                .attr("fill", "none")
                .attr("stroke", options.color || "red")
        })
    }

    sector(sector: Sector, color?: string, p?: [number, number], length?: number) {
        this.ray({angle: sector[0], color, p, length})
        this.ray({angle: sector[1], color, p, length})
    }

    ray(options: {angle: number; p?: [number, number]; length?: number; color?: string}) {
        this.buffer.push(($svg) => {
            const [x, y] = options.p ?? [0, 0]

            let length = options.length ?? window.innerWidth
            let color = options.color ?? "black"

            $svg.append("line")
                .classed("debug", true)
                .attr("x1", x)
                .attr("y1", y)
                .attr("x2", x + Math.cos(options.angle) * length)
                .attr("y2", y + Math.sin(options.angle) * length)
                .attr("stroke", color)
        })
    }

    clear() {
        this.buffer = [($svg) => $svg.selectAll(".debug").remove()]

        return this
    }

    flush($svg: Selection<any, {}, HTMLElement, unknown>) {
        this.buffer.forEach((fn) => fn($svg))

        this.buffer = []
    }
}

export function shortestDistanceBetweenRectangles(
    rect1: [number, number, number, number],
    rect2: [number, number, number, number],
): number {
    let [x1, y1, w1, h1] = rect1
    let [x2, y2, w2, h2] = rect2

    // Calculate centers
    const cx1 = x1 + w1 / 2
    const cy1 = y1 + h1 / 2
    const cx2 = x2 + w2 / 2
    const cy2 = y2 + h2 / 2

    // Calculate the distance between centers
    const cdx = Math.abs(cx1 - cx2)
    const cdy = Math.abs(cy1 - cy2)

    // Calculate the minimum distance between the rectangles' edges
    const dx = Math.max(0, cdx - (w1 + w2) / 2)
    const dy = Math.max(0, cdy - (h1 + h2) / 2)

    return Math.sqrt(dx * dx + dy * dy)
}

export function trapClickAndDoubleClick(
    singleClickHandler: (event: MouseEvent) => void,
    doubleClickHandler: (event: MouseEvent) => void,
) {
    return function (e: MouseEvent) {
        e.preventDefault()
        if (e.detail === 1) {
            singleClickHandler(e)
        } else if (e.detail === 2) {
            singleClickHandler(e)
            doubleClickHandler(e)
        }
    }
}

export function semanticBitFlip(x: number, length: number): number {
    return x ^ ((1 << length) - 1)
}
