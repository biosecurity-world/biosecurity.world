import {Selection} from "d3";
import {fitToSector} from "@/data";
import {ProcessedNode} from "@/index";
import {PI, PI_2} from "@/utils";

let _debugInstance: Debug|null = null

export default function debug(): Debug {
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

    flush($svg: Selection<any, {}, HTMLElement, unknown>) {
        this.buffer.forEach((fn) => fn($svg))

        this.buffer = []
    }

    vertex(options: {vertex: ProcessedNode, parent?: ProcessedNode, minDistance?: number, color?: string}) {
        if (!options.vertex.position) {
            options.vertex.position = fitToSector(options.vertex, options.parent ?? null, options.minDistance ?? 0)
        }

        debug().ray({angle: options.vertex.sector[0], color: 'black'})
        debug().ray({angle: options.vertex.sector[1], color: 'black'})
        debug().rect({
            p: options.vertex.position,
            length: options.vertex.size[0],
            width: options.vertex.size[1],
            color: options.color ?? 'red',
            cb: ($rect) => $rect.attr('data-debug-id', options.vertex.id),
        })

        debug().point({p: options.vertex.position, color: options.color ?? 'red'})
    }
}
