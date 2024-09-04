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
    let entry = (activities, lenses = 0) =>({ activities, lenses })

    expect(shouldFilterEntry(
        { activities: '11111111', lens_governance: '0', lens_technical: '1', activityCount: 8 },
        entry(0b1, 0b01)
    )).toBe(false)

    expect(shouldFilterEntry(state('11111111'), entry(0b10010011))).toBe(false)
    expect(shouldFilterEntry(state('00000000'), entry(0b10010011))).toBe(true)
    expect(shouldFilterEntry(state('01000000'), entry(0b10010011))).toBe(true)
    expect(shouldFilterEntry(state('01000000'), entry(0b11010011))).toBe(false)
    expect(shouldFilterEntry(state('1000000', 8), entry(0b10000000))).toBe(true)
    expect(shouldFilterEntry(state('1000000', 8), entry(0b01000000))).toBe(false)

    expect(shouldFilterEntry(
        { activities: '00000000', lens_governance: '0', lens_technical: '0', activityCount: 8 },
        entry(0b0, 0b00)
    )).toBe(true)
    //
    expect(shouldFilterEntry(
        { activities: '11111111', lens_governance: '0', lens_technical: '1', activityCount: 8 },
        entry(0b1, 0b10)
    )).toBe(true)

    expect(shouldFilterEntry(
        { activities: '11111111', lens_governance: '0', lens_technical: '1', activityCount: 8 },
        entry(0b1, 0b01)
    )).toBe(false)

    expect(shouldFilterEntry(
        { activities: '00000000', lens_governance: '1', lens_technical: '0', activityCount: 8 },
        entry(0b0, 0b01)
    )).toBe(true)

    expect(shouldFilterEntry(
        { activities: '11111111', lens_governance: '1', lens_technical: '0', activityCount: 8 },
        entry(0b1, 0b10)
    )).toBe(false)
})
