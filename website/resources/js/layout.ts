import {Filters, Node, ProcessedNode, Sector} from "@/types";
import {
    debug,
    eq,
    getQuadrant,
    gt,
    gte,
    inIE,
    lt,
    PI,
    PI_2,
    PI_4,
    PIPI,
    shortestDistanceBetweenRectangles
} from "@/utils"
import {shouldFilterEntry} from "@/filters";
import {showAppState} from "@/map";


export function updateMap(state: Filters) {
    let nodes: ProcessedNode[] = []
    let stack: ProcessedNode[] = []

    showAppState('loading')
    resetGlobalMapState()

    let maxDepth = 0
    let idToNode: Record<number, ProcessedNode> = {}

    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node & Partial<ProcessedNode> & { el: SVGElement }

        idToNode[node.id] = node

        if (node.depth >= maxDepth) {
            maxDepth = node.depth
        }

        if (node.od === 0) {
            let entryIds = node.entries
            let filteredIds = []

            for (const entryId of entryIds) {
                let elEntry = document.querySelector(`button[data-entrygroup="${node.id}"][data-entry="${entryId}"]`) as HTMLButtonElement

                let shouldFilter = shouldFilterEntry(state, window.filterData[entryId])

                elEntry.classList.toggle("matches-filters", !shouldFilter)

                if (!shouldFilter) {
                    filteredIds.push(entryId)
                }
            }

            node.filtered = filteredIds.length === 0
        }

        if (!(node.el instanceof SVGForeignObjectElement)) {
            throw new Error(`Element for node ${node.id} is not a foreignObject, but a ${node.el.tagName}`)
        }

        // We set the <foreignObject> with a height of 100% and a w of 100%
        // because we don't want to compute the size of the elements server-side
        // but this means that we get the wrong bounds.
        if (node.el.firstElementChild === null) {
            throw new Error(
                "It is expected that the foreignObject representing the node " +
                "has a single child to compute its real bounding box, not " +
                "the advertised (100%, 100%)",
            )
        }

        node.size = [
            // getBoundingClientRect() is transform-aware, so the zoom will mess everything up on subsequent renders.
            // We need to use offsetWidth and offsetHeight instead.
            node.el.firstElementChild!.offsetWidth,
            node.el.firstElementChild!.offsetHeight,
        ]
        node.weight = node.size[0] * node.size[1]

        if (node.od > 0) {
            let children = []

            for (let j = 0; j < node.od; j++) {
                let child = stack.pop()
                if (child.filtered) {
                    continue
                }

                children.push(child)
                node.weight += child.weight
            }

            children.sort((a, b) => a.weight - b.weight)
            for (const child of children) {
                nodes.push(child)
            }

            node.filtered = children.length === 0
        }


        stack.push(node)
    }

    let root = stack.pop()!
    root.sector = [0, PIPI]
    root.position = fitToSector(root, [{position: [0, 0], size: root!.size}])

    if (nodes.length === 0) {
        showAppState('empty')
        return
    }

    showNode(root)

    let deltaFromSiblings: Record<number, number> = {}

    for (let i = nodes.length - 1; i >= 0; i--) {
        let node = nodes[i]

        let parent = idToNode[node.parent]

        if (!deltaFromSiblings[node.parent]) {
            deltaFromSiblings[node.parent] = parent.sector[0]
        }

        let delta = deltaFromSiblings[node.parent]
        let alpha = (node.weight / parent.weight) * (parent.sector[1] - parent.sector[0])
        let theta = delta + alpha
        deltaFromSiblings[node.parent] = theta
        node.sector = [delta, theta]


        node.position = fitToSector(node, node.trail.map((id) => idToNode[id]), 150)

        showNode(node)


        // let r = 2 * window.innerWidth
        // if (node.depth === 1) {
        //     let color = level2NodeCount % 2 === 0 ? "#f3f4f6" : "#f9fafb"
        //
        //     $bg.append("path")
        //         .classed('background-sector', true)
        //         .attr('d', [
        //             `M 0,0`,
        //             `L ${r * Math.cos(delta)} ${r * Math.sin(delta)}`,
        //             `A ${r} ${r} 0 0 1 ${r * Math.cos(theta)} ${r * Math.sin(theta)}`,
        //             `Z`,
        //         ].join(" "))
        //         .attr("fill", color)
        //
        //     level2NodeCount++
        // }
    }

    showAppState('success')
}

export function fitToSector(node: ProcessedNode, trail: Pick<ProcessedNode, 'position' | 'size'>[], spacing: number | null = null): [number, number] {
    if (gt(node.sector[1] - node.sector[0], PI) && !eq(node.sector[0], 0)) {
        throw new Error("Should not happen: sectors were not sorted correctly, alpha >= PI but delta is not 0")
    }

    if (eq(node.sector[0], 0) && eq(node.sector[1], PIPI)) {
        return [-node.size[0] / 2, -node.size[1] / 2]
    }

    let pos = fitRectToSector(node.sector, node.size, node.id)

    if (spacing === null) {
        return pos
    }

    let size: [number, number] = [node.size[0], node.size[1]]
    // Take the closest node in the trail, ensure that there's 100 pixels between this one and the one in the trail.
    let neighbour: Pick<ProcessedNode, "position" | "size"> | null = null
    let distanceToNeighbour = Infinity
    for (const candidate of trail) {
        let candidateDistance = shortestDistanceBetweenRectangles(
            [pos[0], pos[1], node.size[0], node.size[1]],
            [candidate.position[0], candidate.position[1], candidate.size[0], candidate.size[1]]
        )

        if (candidateDistance < distanceToNeighbour) {
            neighbour = candidate
            distanceToNeighbour = candidateDistance
        }
    }

    if (neighbour === null) {
        throw new Error("Should not happen: no neighbour found in the trail")
    }

    let neighbourRect: [number, number, number, number] = [neighbour.position[0], neighbour.position[1], neighbour.size[0], neighbour.size[1]]

    let m = (node.sector[0] + node.sector[1]) / 2

    while (shortestDistanceBetweenRectangles([pos[0], pos[1], node.size[0], node.size[1]], neighbourRect) < spacing) {
        if (inIE(m, PI_4, 3 * PI_4) || inIE(m, 5 * PI_4, 7 * PI_4)) {
            size[0] += 20
        } else {
            size[1] += 20
        }

        pos = fitRectToSector(node.sector, size, node.id)
    }

    let [x, y] = pos

    x += (size[0] - node.size[0]) / 2
    y += (size[1] - node.size[1]) / 2

    return [x, y]
}

function fitRectToSector(sector: Sector, size: [number, number], id: number = null): [number, number] {
    let [od, ot] = sector
    // These checks will save you at some point, don't remove them.
    if (eq(od, ot)) {
        throw new Error(`Sector has a 0 angle: ${sector}`)
    }
    if (gt(od, ot)) {
        throw new Error(`Sector is not correct (delta > theta): ${sector}`)
    }
    if (lt(od, 0) || gt(od, PIPI)) {
        throw new Error(`Sector is not in the range [0, 2*PI]: ${sector}`)
    }

    let [d, t] = getEffectiveSector(sector)
    if (gt(d, t)) {
        throw new Error(`Effective sector is not correct (delta > theta): ${sector} -> ${[d, t]}`)
    }
    if (lt(d, 0) || gt(d, PIPI)) {
        throw new Error(`Effective sector is not in the range [0, 2*PI]: ${sector} -> ${[d, t]}`)
    }
    if (!eq(ot - od, t - d)) {
        throw new Error(`Effective sector is not correct (delta != theta): ${sector} -> ${[d, t]}`)
    }

    let [l, w] = size
    let q_od = getQuadrant(od)
    let q_ot = getQuadrant(ot)
    let q_d = getQuadrant(d)
    let q_t = getQuadrant(t)
    let tan_d = Math.tan(d)
    let tan_t = Math.tan(t)

    if (q_d === q_t) {
        let x = ((w + l * tan_d) / (tan_t - tan_d))
        let y = (tan_d * (x + l))

        if (q_od === 2 || q_od === 3) {
            x = -x - l
        }

        if (q_od > 2) {
            y = -y - w
        }

        return [x, y]
    }

    // This is a special case only because we don't have to deal with
    // its symmetry, q_od=4 and q_ot=1, as our sectors start and end
    // at 0 and 2*PI.
    if (q_od === 2 && q_ot === 3) {
        let x = (w / (Math.tan(od) - Math.tan(ot)) - l)
        let y = Math.tan(ot) * (x + l)

        return [x, y]
    }

    if (q_d + 1 === q_t && !(eq(d, 0) && gt(t, PI_2))) {

        let x = (tan_d * l) / (tan_t - tan_d)
        let y = tan_t * x

        if (q_od === 2 || q_od === 3) {
            x = -x - l
        }

        if (q_od > 2) {
            y = -y - w
        }

        return [x, y]
    }


    if (eq(d, 0) && gt(t, PI_2)) {
        let x = gte(t, PI) ? -l / 2 : 0
        let y = l / 2

        if (q_od > 2) {
            y = -y - w
        }

        return [x, y]
    }

    // This happens only if q_od = 2 and q_ot = 4, where d is close to PI
    // and t close to 3*PI/2, because there is necessarily a bigger sector
    // that precedes this one.
    if (q_d + 2 === q_t) {
        return [-l, -w]
    }

    throw new Error(`Unexpected: could not fit sector ${size} in ${sector} (effective sector: ${[d, t]})`)
}

function getEffectiveSector(sector: Sector): Sector {
    let [od, ot] = sector
    let q_od = getQuadrant(od)
    let q_ot = getQuadrant(ot)

    if (q_ot - q_od >= 2) {
        return [od, ot]
    }

    let d: number, t: number

    if (eq(ot, PIPI)) {
        d = 0
        t = ot - od
    } else {
        d = od % PI
        t = d + (ot - od)
    }

    if (gte(d, PI_2)) {
        if (lt(PI - t, 0)) {
            return [d, t]
        }

        d = PI - d
        t = PI - t

        return [t, d]
    }


    return [d, t]
}

function showNode(node: ProcessedNode) {
    node.el.classList.remove("off-screen")
    node.el.ariaHidden = "false"
    node.el.style.transform = `translate(${node.position[0]}px, ${node.position[1]}px)`

    if (node.id === node.parent) {
        return
    }
    debug().point({
        p: node.position,
        label: node.id,
    })
}

function resetGlobalMapState() {
    for (let i = 0; i < window.nodes.length; i++) {
        let node = window.nodes[i] as Node & Partial<ProcessedNode> & { el: SVGElement }

        node.el.classList.add("off-screen")
        node.el.ariaHidden = "true"
        node.el.style.transform = ""
    }
}
