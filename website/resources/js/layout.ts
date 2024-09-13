import {ProcessedNode, Sector} from "./types";
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

export function fitToSector(node: ProcessedNode, trail: Pick<ProcessedNode, 'position' | 'size'>[], spacing: number | null = null): [number, number] {
    if (gt(node.sector[1] - node.sector[0], PI) && !eq(node.sector[0], 0)) {
        throw new Error("Should not happen: sectors were not sorted correctly, alpha >= PI but delta is not 0")
    }

    if (eq(node.sector[0], 0) && eq(node.sector[1], PIPI)) {
        return [-node.size[0] / 2, -node.size[1] / 2]
    }

    let pos = fitRectToSector(node.sector, node.size)

    if (spacing === null) {
        return pos
    }

    let size = [node.size[0], node.size[1]]
    // Take the closest node in the trail, ensure that there's 100 pixels between this one and the one in the trail.
    let neighbour = null
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

    let neighbourRect = [neighbour.position[0], neighbour.position[1], neighbour.size[0], neighbour.size[1]]

    let m = (node.sector[0] + node.sector[1]) /2

    while (shortestDistanceBetweenRectangles([...pos, ...node.size], neighbourRect) < spacing) {
        if (inIE(m, PI_4, 3*PI_4) || inIE(m, 5*PI_4, 7*PI_4)) {
            size[0] += 20
        } else {
            size[1] += 20
        }

        let nextPos = fitRectToSector(node.sector, size)

        if (eq(pos[0], nextPos[0]) && eq(pos[1], nextPos[1])) {
            break
        }

        pos = nextPos
    }

    let [x, y] = pos

    x += (size[0] - node.size[0]) / 2
    y += (size[1] - node.size[1]) / 2

    return [x, y]
}

export function fitRectToSector(sector: Sector, size: [number, number]): [number, number] {
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

    if (q_d + 1 === q_t) {
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

    if (eq(d, 0) && gte(t, PI)) {
        let x = -l / 2
        let y = 0

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

export function getEffectiveSector(sector: Sector): Sector {
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
