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

it('filters out entries correctly', () => {
    let state = (activities, count = null) => ({ activities, lens_technical: '0', lens_governance: '0', activityCount: count ?? activities.length })
    expect(shouldFilterEntry(
        state('11111111'),
        { activities: 0b10010011, lenses: 0b0 }
    )).toBe(false)


    expect(shouldFilterEntry(
        state('00000000'),
        { activities: 0b10010011, lenses: 0b00 }
    )).toBe(true)

    expect(shouldFilterEntry(
        state('01000000'),
        { activities: 0b10010011, lenses: 0b00 }
    )).toBe(true)

    expect(shouldFilterEntry(
        state('01000000'),
        { activities: 0b11010011, lenses: 0b00 }
    )).toBe(false)

    expect(shouldFilterEntry(
        state('1000000', 8),
        { activities: 0b10000000, lenses: 0b00 }
    )).toBe(true)

    expect(shouldFilterEntry(
        state('1000000', 8),
        { activities: 0b01000000, lenses: 0b00 }
    )).toBe(false)
})
