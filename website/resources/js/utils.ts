import {Selection} from "d3"
import type {AppState, AppStateChange, AppStateParameters, ProcessedNode, Sector} from "@/types/index.d.ts"

export function changeAppState<T extends AppState>(state: T, params: AppStateParameters[T]) {
    window.dispatchEvent(
        new CustomEvent<AppStateChange>("appstatechange", {
            /* @ts-ignore */
            detail: {state, params},
        }),
    )
}

export const PI = Math.PI
export const PIPI = PI * 2

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

export function eq(a: number, b: number) {
    if (a === b) {
        return true
    }

    return Math.abs((a - b) / b) < 1e-6
}

export function gte(a: number, b: number) {
    return a > b || eq(a, b)
}

export function lte(a: number, b: number) {
    return a < b || eq(a, b)
}

export function gt(a: number, b: number) {
    return a > b && !eq(a, b)
}

export function lt(a: number, b: number) {
    return a < b && !eq(a, b)
}

export function inIE(x: number, fpA: number, fpB: number) {
    return gte(x, fpA) && lt(x, fpB)
}

export function inEI(x: number, fpA: number, fpB: number) {
    return gt(x, fpA) && lte(x, fpB)
}
export function getQuadrant(angle: number): number {
    if (lt(angle, 0) || gt(angle, PIPI)) {
        throw new Error(`Angle ${angle} is not in the range [0, 2*PI]`)
    }

    if (inIE(angle, 0, PI / 2)) {
        return 1
    }

    if (inIE(angle, PI / 2, PI)) {
        return 2
    }

    if (inIE(angle, PI, PI + PI / 2)) {
        return 3
    }

    return 4
}

export function shortestDistanceBetweenRectangles(
    rect1: [number, number, number, number],
    rect2: [number, number, number, number],
): number {
    let [x1, y1, w1, h1] = rect1
    let [x2, y2, w2, h2] = rect2

    let dx = Math.max(0, Math.abs(x1 - x2) - w1 / 2 - w2 / 2)
    let dy = Math.max(0, Math.abs(y1 - y2) - h1 / 2 - h2 / 2)

    return Math.sqrt(dx * dx + dy * dy)
}

export function getDebugLabel(node: ProcessedNode): string {
    if (node.el.querySelector(".entrygroup") !== null) {
        return "Entrygroup"
    }

    return (node.el.querySelector("div > span") as HTMLSpanElement).innerText
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

export function flip(num: number, n: number): number {
    // This is more semantically correct than ~num.
    return num ^ ((1 << n) - 1)
}
