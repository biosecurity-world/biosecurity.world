import {describe, expect, it} from "vitest"
import {shouldFilterEntry, type EntryFilterData, type Filters} from "./filters"

const metadata = {activityCount: 2, focusesCount: 2}

const defaultState: Filters = {
    activities: 0b11,
    domains: 0,
    focuses: 0b11,
    locations: 0n,
    gcbrFocus: false,
    showNoFocus: true,
}

const defaultEntry: EntryFilterData = {
    activities: 0b01,
    domains: 0b01,
    focuses: 0b01,
    locations: 1n,
    gcbrFocus: false,
}

describe("shouldFilterEntry", () => {
    it("keeps an entry that matches the active filters", () => {
        expect(shouldFilterEntry(defaultState, defaultEntry, metadata)).toBe(false)
    })

    it("filters entries with no shared activity", () => {
        expect(shouldFilterEntry(defaultState, {...defaultEntry, activities: 0b100}, metadata)).toBe(true)
    })

    it("requires every selected domain", () => {
        const bothDomains = {...defaultState, domains: 0b11}

        expect(shouldFilterEntry(bothDomains, defaultEntry, metadata)).toBe(true)
        expect(shouldFilterEntry(bothDomains, {...defaultEntry, domains: 0b11}, metadata)).toBe(false)
    })

    it("supports location masks beyond the JavaScript 32-bit range", () => {
        const highLocation = 1n << 80n
        const state = {...defaultState, locations: highLocation}

        expect(shouldFilterEntry(state, {...defaultEntry, locations: highLocation}, metadata)).toBe(false)
        expect(shouldFilterEntry(state, {...defaultEntry, locations: 1n}, metadata)).toBe(true)
    })

    it("does not exclude entries without location metadata", () => {
        const state = {...defaultState, locations: 1n << 80n}

        expect(shouldFilterEntry(state, {...defaultEntry, locations: 0n}, metadata)).toBe(false)
    })

    it("uses the dedicated toggle for entries without an intervention focus", () => {
        const entryWithoutFocus = {...defaultEntry, focuses: 0}

        expect(shouldFilterEntry(defaultState, entryWithoutFocus, metadata)).toBe(false)
        expect(shouldFilterEntry({...defaultState, showNoFocus: false}, entryWithoutFocus, metadata)).toBe(true)
    })

    it("filters focused entries with no shared intervention focus", () => {
        expect(shouldFilterEntry(defaultState, {...defaultEntry, focuses: 0b100}, metadata)).toBe(true)
    })

    it("only keeps GCBR entries when the GCBR filter is active", () => {
        const state = {...defaultState, gcbrFocus: true}

        expect(shouldFilterEntry(state, defaultEntry, metadata)).toBe(true)
        expect(shouldFilterEntry(state, {...defaultEntry, gcbrFocus: true}, metadata)).toBe(false)
    })
})
