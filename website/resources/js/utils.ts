import {Selection} from "d3"
import {ProcessedNode} from "@/types"
import {fitToSector} from "@/layout"


export const IN_PRODUCTION = import.meta.env.PROD === true

export const PI = Math.PI
export const PI_4 = PI / 4
export const PI_6 = PI / 6
export const PI_2 = PI / 2
export const PI_3 = PI / 3
export const PIPI = PI * 2

let _debugInstance: Debug|null = null

export function debug(): Debug {
    if (_debugInstance === null) {
        _debugInstance = new Debug()
    }

    return _debugInstance
}

class Debug {
    buffer: (($selection: Selection<SVGElement, {}, HTMLElement, unknown>) => void)[] = []

    point(options: { p: [number, number], color?: string }) {
        this.buffer.push(($svg) => {
            $svg.append('circle')
                .attr('cx', options.p[0])
                .attr('cy', options.p[1])
                .attr('r', 2)
                .attr('fill', options.color || 'red')
        })
    }

    rect(options: { p: [number, number], width: number, length: number, color?: string, cb?: (rect: Selection<SVGRectElement, {}, HTMLElement, unknown>) => void }) {
        this.buffer.push(($svg) => {
            let $rect = $svg.append('rect')
                .attr('x', options.p[0])
                .attr('y', options.p[1])
                .attr('width', options.length)
                .attr('height', options.width)
                .attr('fill', 'none')
                .attr('stroke', options.color || 'red');

            if (options.cb) {
                options.cb($rect)
            }
        })
    }

    cartesianPlane() {
        debug().ray({angle: 0, color: 'gray'})
        debug().ray({angle: PI, color: 'gray'})
        debug().ray({angle: PI_2, color: 'gray'})
        debug().ray({angle: -PI_2, color: 'gray'})
    }

    ray(options: { angle: number, p?: [number, number], length?: number, color?: string }) {
        this.buffer.push(($svg) => {
            const [x, y] = options.p ?? [0, 0]

            let length = options.length ?? window.innerWidth
            let color = options.color ?? 'black'

            $svg.append('line')
                .attr('x1', x)
                .attr('y1', y)
                .attr('x2', x + Math.cos(options.angle) * length)
                .attr('y2', y + Math.sin(options.angle) * length)
                .attr('stroke', color)
        })
    }

    clear() {
        this.buffer = [($svg) => $svg.selectAll('*').remove()]

        return this
    }

    flush($svg: Selection<any, {}, HTMLElement, unknown>) {
        this.buffer.forEach((fn) => fn($svg))

        this.buffer = []
    }

    node(options: {node: ProcessedNode, parent?: ProcessedNode, minDistance?: number, color?: string}) {
        if (!options.node.position) {
            options.node.position = fitToSector(options.node)
        }

        debug().ray({angle: options.node.sector[0], color: 'black'})
        debug().ray({angle: options.node.sector[1], color: 'black'})
        debug().rect({
            p: options.node.position,
            length: options.node.size[0],
            width: options.node.size[1],
            color: options.color ?? 'red',
            cb: ($rect) => $rect.attr('data-debug-id', options.node.id),
        })

        debug().point({p: options.node.position, color: options.color ?? 'red'})
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

    return Math.floor(angle / PI_2)
}

export function shortestDistanceBetweenRectangles(
    rect1: [number, number, number, number],
    rect2: [number, number, number, number]
): number {
    let [x1, y1, w1, h1] = rect1
    let [x2, y2, w2, h2] = rect2

    let dx = Math.max(0, Math.abs(x1 - x2) - w1 / 2 - w2 / 2)
    let dy = Math.max(0, Math.abs(y1 - y2) - h1 / 2 - h2 / 2)

    return Math.sqrt(dx * dx + dy * dy)
}
