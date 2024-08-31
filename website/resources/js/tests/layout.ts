import {getEffectiveSector} from "@/data";
import assert = require("assert");
import {Sector} from "@/index";
import {eq, PI, PI_2, PI_3} from "@/utils";

it('finds the correct effective sector for side-bound sectors' , () => {
    let delta = PI_3
    let theta = 2 * PI_3

    for (let i = 0; i < 3; i++) {
        let sector = [delta + i * PI_2, theta + i * PI_2]
        let effectiveSector = getEffectiveSector(sector)
        assert.ok(eq(effectiveSector[0], delta), `Expected delta in effective sector ${effectiveSector} for ${sector} to be [PI_3, 2 * PI_3]`)
        assert.ok(eq(effectiveSector[1], theta), `Expected theta in effective sector ${effectiveSector} for ${sector} to be [PI_3, 2 * PI_3]`)
    }
})

it('finds the correct effect sector for y-bounded sectors', () => {
    let delta = 0
    let theta = 2 * PI_3

    for (let i = 0; i < 3; i++) {
        let sector: Sector = [delta + i * PI_2, theta + i * PI_2]
        let effectiveSector = getEffectiveSector(sector)
        assert.ok(eq(effectiveSector[0], delta), `Expected delta in effective sector ${effectiveSector} for ${sector} to be [0, 2 * PI_3]`)
        assert.ok(eq(effectiveSector[1], theta), `Expected theta in effective sector ${effectiveSector} for ${sector} to be [0, 2 * PI_3]`)
    }

    for (let i = 0; i < 3; i++) {
        let sector: Sector = [PI_3 + (i*PI_2), PI + (i*PI_2)]
        let effectiveSector = getEffectiveSector(sector)
        assert.ok(eq(effectiveSector[0], 0), `Expected delta in effective sector ${effectiveSector} for ${sector} to be [PI_3, PI]`)
        assert.ok(eq(effectiveSector[1], 2 * PI_3), `Expected theta in effective sector ${effectiveSector} for ${sector} to be [PI_3, PI]`)
    }
})
