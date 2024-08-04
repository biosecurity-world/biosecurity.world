import {PVertex, Sector} from "./index";
import {getQuadrant, eq, gt, gte, inEI, inIE, lt, PI, PI_2, PIPI, shortestDistanceBetweenRectangles} from "@/utils"
import debug from "@/debug";
export enum NodeType {
    Entry = 1,
    Category = 2,
    Entrygroup = 3,
    Root = 4
}

export function prepare(
    vertex: PVertex,
    depth: number = 0
) {
    let el = document.querySelector(`[data-vertex="${vertex.id}"]`) as SVGElement | null
    if (!el) {
        throw new Error(`Vertex with id ${vertex.id} has no corresponding element in the DOM`)
    }
    vertex.el = el

    let bounds: DOMRect
    // We set the <foreignObject> with a height of 100% and a w of 100%
    // because we don't want to compute the size of the elements server-side
    // but this means that if we do vertex.el.getBoundingClientRect()
    // we get the wrong bounds.
    if (vertex.el instanceof SVGForeignObjectElement) {
        if (vertex.el.childElementCount !== 1) {
            throw new Error("It is expected that the foreignObject representing the vertex has a single child to compute its real bounding box, not the advertised (100%, 100%)")
        }

        bounds = vertex.el.firstElementChild!.getBoundingClientRect()
        // We resize the foreignObject to match the bounding box of its child.
        // This is only useful when inspecting the page.
        vertex.el.setAttribute('width', bounds.width + 'px')
        vertex.el.setAttribute('height', bounds.height + 'px')
    } else {
        bounds = vertex.el.getBoundingClientRect()
    }

    vertex.size = [Math.ceil(bounds.width), Math.ceil(bounds.height)]
    vertex.depth = depth
    vertex.weight = vertex.size[0] * vertex.size[1]

    for (const child of vertex.children) {
        vertex.weight += prepare(child as PVertex, depth + 1)
    }

    vertex.children.sort((a, b) => {
        return (b as PVertex).weight - (a as PVertex).weight
    })

    return vertex.weight
}

export function sectorize(
    vertex: PVertex,
    parentSector: [number, number],
    deltaFromSiblings: number,
    siblingsWeight: number = 0
) {
    if (vertex.id === vertex.parentId) {
        vertex.sector = [0, PIPI]
    } else {
        vertex.sector =[
            // deltaFromSiblings + 0.01,
            // deltaFromSiblings + (vertex.weight / siblingsWeight) * (parentSector[1] - parentSector[0]) - 0.01
            deltaFromSiblings,
            deltaFromSiblings + (vertex.weight / siblingsWeight) * (parentSector[1] - parentSector[0])
        ]
    }

    if (lt(vertex.sector[0], 0) || gt(vertex.sector[1], PIPI)) {
        throw new Error(`Sector ${vertex.sector} is not in the range [0, 2*PI]`)
    }

    let sumOfChildrenWeight = vertex.weight - (vertex.size[0] * vertex.size[1])

    let childrenDelta = 0
    for (const child of vertex.children) {
        childrenDelta += sectorize(child as PVertex, vertex.sector, vertex.sector[0] + childrenDelta, sumOfChildrenWeight)
    }

    return vertex.sector[1] - vertex.sector[0]
}


export function fitToSector(vertex: PVertex, parent: PVertex | null, minDistance: number = 0): [number, number] {
    if (gt(vertex.sector[1] - vertex.sector[0], PI) && !eq(vertex.sector[0], 0)) {
        throw new Error("Should not happen: sectors were not sorted correctly, alpha >= PI but delta is not 0")
    }

    if (eq(vertex.sector[0], 0) && eq(vertex.sector[1], PIPI)) {
        return [-vertex.size[0] / 2, -vertex.size[1] / 2]
    }

    let [x, y]: [number, number] = findPositionForRect(vertex)
    let [l, w] = vertex.size

    if (parent === null) {
        return [x, y]
    }

    let [Pl, Pw] = parent.size
    let [Px, Py] = parent.position

    let offset = 1
    while (
        shortestDistanceBetweenRectangles(
            [x, y, l, w],
            [Px, Py, Pl, Pw]
        ) < minDistance
    ) {
        [x, y] = findPositionForRect(vertex, offset)

        offset+= 1;
    }

    return [x, y]
}

export function findPositionForRect(vertex: PVertex, offsetX: number | null = null): [number, number] {
    let [od, ot] = vertex.sector
    let [d, t] = getEffectiveSector(vertex.sector)
    let [l, w] = vertex.size
    let qD = getQuadrant(d)
    let qT = getQuadrant(t)
    let tan_d = Math.tan(d)
    let tan_t = Math.tan(t)

    if (eq(d, 0) && inIE(t, PI, PIPI)) {
        return [-l/2 + offsetX, 0]

    }

    if (qD + 2 === qT) {
        return [-l + offsetX, -w]
    }

    if (inIE(d, 0, t) && inEI(t, d, PI_2)) {
        let x = ((w + l * tan_d) / (tan_t - tan_d)) + offsetX
        let y = (tan_d * (x + l))

        if (getQuadrant(od) === 1 || getQuadrant(od) === 2) {
            x = -x - l
        }

        if (getQuadrant(od) >= 2) {
            y = -y - w
        }

        return [x, y]
    }

    if (qD + 1 === qT && getQuadrant(od) === 1) {
        let x = (w / (Math.tan(od) - Math.tan(ot)) - l) + offsetX

        return [x, Math.tan(ot) * (x + l)]
    }

    if (qD + 1 === qT) {
        let x = (tan_d * l) / (tan_t - tan_d) - offsetX
        let y = tan_t * x

        if (getQuadrant(od) === 1 || getQuadrant(od) === 2) {
            x = -x - l
        }

        if (getQuadrant(od) >= 2) {
            y = -y - w
        }

        return [x, y]
    }

    console.log(vertex);

    throw new Error(`Could not fit sector ${vertex.size} in ${vertex.sector} (effective sector: ${[d, t]}, offsetX: ${offsetX})`)

}

export function getEffectiveSector(sector: Sector): Sector {
    // This could be simplified a lot.
    let [od, ot] = sector

    let d = od % PI_2
    let t = ot % PI_2

    if (lt(ot - od, PI_2) && eq(od, PI_2)) {
        return [PI - ot, PI_2]
    }

    if (lt(ot - od, PI_2) && eq(od, 3 * PI_2)) {
        return [2 * PI - ot, PI_2]
    }

    if (getQuadrant(od) + 1 === getQuadrant(ot) && lt(ot - od, PI) && !eq(ot, PI) && !eq(ot, PIPI)) {
        return [
            -getQuadrant(od) * PI_2 + od,
            -getQuadrant(od) * PI_2 + ot
        ]
    }

    if (getQuadrant(od) === getQuadrant(ot)) {
        d = od % PI
        t = ot % PI

        if (inIE(d, 0, PI_2) && inEI(t, 0, PI_2)) {
            return [d, t]
        }

        return [PI - d, PI - t].sort() as [number, number]
    }

    if (lt(t, d)) {
        let tmp = d
        d = t
        t = tmp
    }

    if (eq(t - d, ot - od)) {
        return [d, t]
    }


    return [d, d + ot - od]

}

