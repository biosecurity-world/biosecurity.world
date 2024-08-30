import {PVertex, Sector} from "./index";
import {eq, getQuadrant, gt, inEI, inIE, lt, PI, PI_2, PIPI} from "@/utils"

export function fitToSector(vertex: PVertex): [number, number] {
    if (gt(vertex.sector[1] - vertex.sector[0], PI) && !eq(vertex.sector[0], 0)) {
        throw new Error("Should not happen: sectors were not sorted correctly, alpha >= PI but delta is not 0")
    }

    if (eq(vertex.sector[0], 0) && eq(vertex.sector[1], PIPI)) {
        return [-vertex.size[0] / 2, -vertex.size[1] / 2]
    }

    return findPositionForRect(vertex)
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
        return [-l / 2 + offsetX, 0]

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

