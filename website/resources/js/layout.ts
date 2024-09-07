import {ProcessedNode, Sector} from "./types";
import {debug, eq, getQuadrant, gt, gte, inEI, inIE, lt, PI, PI_2, PIPI} from "@/utils"


export function fitToSector(node: ProcessedNode): [number, number] {
    if (gt(node.sector[1] - node.sector[0], PI) && !eq(node.sector[0], 0)) {
        throw new Error("Should not happen: sectors were not sorted correctly, alpha >= PI but delta is not 0")
    }

    if (eq(node.sector[0], 0) && eq(node.sector[1], PIPI)) {
        return [-node.size[0] / 2, -node.size[1] / 2]
    }

    let [od, ot] = node.sector
    // These checks will save you at some point, don't remove them.
    if (eq(od, ot)) {
        throw new Error(`Sector has a 0 angle: ${node.sector}`)
    }
    if (gt(od, ot)) {
        throw new Error(`Sector is not correct (delta > theta): ${node.sector}`)
    }
    if (lt(od, 0) || gt(od, PIPI)) {
        throw new Error(`Sector is not in the range [0, 2*PI]: ${node.sector}`)
    }

    let [d, t] = getEffectiveSector(node.sector)
    if (gt(d, t)) {
        throw new Error(`Effective sector is not correct (delta > theta): ${node.sector} -> ${[d, t]}`)
    }
    if (lt(d, 0) || gt(d, PIPI)) {
        throw new Error(`Effective sector is not in the range [0, 2*PI]: ${node.sector} -> ${[d, t]}`)
    }
    if (!eq(ot - od, t - d)) {
        throw new Error(`Effective sector is not correct (delta != theta): ${node.sector} -> ${[d, t]}`)
    }

    let [l, w] = node.size
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
        console.log("here");
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
        let x =  - l / 2
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

    throw new Error(`Could not fit sector ${node.size} in ${node.sector} (effective sector: ${[d, t]})`)
}

export function getEffectiveSector(sector: Sector): Sector {
    let [od, ot] = sector
    let q_od = getQuadrant(od)
    let q_ot = getQuadrant(ot)

    if (q_od + 1 == q_ot || q_od === q_ot) {
        let d = od % PI
        let t = d + (ot - od)

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

    return [od, ot]
}

