export function shouldFilterEntry(state: { activities: string, lens_technical: string, lens_governance: string, activityCount: number}, entry: { activities: number, lenses: number}) {
    let lens = 0
    if (state.lens_technical === "1") {
        lens |= 1 << 0
    }

    if (state.lens_governance === "1") {
        lens |= 1 << 1
    }

    if (lens !== 0 && (entry.lenses & lens) === 0) {
        return true
    }

    let stateActivities = state.activities.padStart(state.activityCount, '0').split("")
    let entryActivities = entry.activities.toString(2).padStart(state.activityCount, '0').split("")

    let shouldFilter = true
    for (let i = 0; i < entryActivities.length; i++) {
        if (stateActivities[i] === "1" && entryActivities[i] === "1") {
            shouldFilter = false
            break
        }
    }

    return shouldFilter
}
