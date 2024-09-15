import {getEffectiveSector} from "@/layout"
import {PI, PI_2, PI_3} from "@/utils"
import {shouldFilterEntry} from "./filters.ts";

it("finds the correct effective sector for side-bound sectors", () => {
    let delta = PI_3
    let theta = 2 * PI_3

    for (let i = 0; i < 3; i++) {
        let sector = [delta + i * PI_2, theta + i * PI_2]
        let effectiveSector = getEffectiveSector(sector)
        expect(effectiveSector[0]).toBeCloseTo(delta, 5)
        expect(effectiveSector[1]).toBeCloseTo(theta, 5)
    }
})

it("finds the correct effect sector for y-bounded sectors", () => {
    let delta = 0
    let theta = 2 * PI_3

    for (let i = 0; i < 3; i++) {
        let sector = [delta + i * PI_2, theta + i * PI_2]
        let effectiveSector = getEffectiveSector(sector)


        expect(effectiveSector[0]).toBeCloseTo(delta, 5)
        expect(effectiveSector[1]).toBeCloseTo(theta, 5)
    }

    for (let i = 0; i < 3; i++) {
        let sector = [PI_3 + i * PI_2, PI + i * PI_2]
        let effectiveSector = getEffectiveSector(sector)

        expect(effectiveSector[0]).toBeCloseTo(0, 5)
        expect(effectiveSector[1]).toBeCloseTo(2 * PI_3, 5)
    }
})
