export default class FiltersStateStore {
    tracked: Record<string, [() => any, (value: any) => void, any]> = {}
    cb: (() => void) | null = null;

    /**
     * @param key A unique key for this state used in the query string and local storage.
     * @param get A function that returns the current value of the state.
     * @param apply A function that applies the state to the UI.
     * @param defaultValue Whatever value returned by get that should be considered as "no change".
     */
    persist<T>(key: string, get: () => T, apply: (value: T) => void, defaultValue: T|null = null){
        if (this.tracked[key]) {
            throw new Error(`Key ${key} is already being tracked.`)
        }

        this.tracked[key] = [get, apply, defaultValue]
    }

    syncOnly(keys: string[]): this {
        let changed = false
        for (const key of keys) {
            if (!this.tracked[key]) {
                throw new Error(`Key [${key}] is not being tracked.`)
            }

            let [check, apply, defaultValue] = this.tracked[key]

            let value = check()
            apply(value)

            if (value === this.getState(key)) {
                continue
            }

            if (defaultValue !== null && value === defaultValue) {
                this.clearState(key)
                if (this.cb) {
                    this.cb()
                }
                continue;
            }

            this.setState(key, value)
            changed = true
        }

        if (changed && this.cb) {
            this.cb()
        }

        return this
    }

    sync(): this {
        this.syncOnly(Object.keys(this.tracked))

        return this
    }

    getState(key: string) {
        if (!this.tracked[key]) {
            throw new Error(`Key [${key}] is being accessed but is not being tracked.`)
        }

        let loc = new URL(window.location.toString())
        if (loc.searchParams.has(key)) {
            return loc.searchParams.get(key)
        }

        let potentialValue = this.getInLocalStorage(key)
        if (potentialValue !== null) {
            return potentialValue
        }

        return this.tracked[key][2]
    }

    setState(key: string, value: string) {
        if (!this.tracked[key]) {
            throw new Error(`Key [${key}] is being set but is not being tracked.`)
        }

        let loc = new URL(window.location.toString())
        if (loc.searchParams.get(key) !== value) {
            loc.searchParams.delete(key)
            loc.searchParams.set(key, value)

            window.history.pushState({}, '', loc.toString())
        }

        let ls = this.getInLocalStorage(key)
        if (ls !== value) {
            this.setInLocalStorage(key, value)
        }

        this.tracked[key][1](value)
        if (this.cb) {
            this.cb()
        }

        return this;
    }

    reset() {
        let loc = new URL(window.location.toString())
        for (const key of Object.keys(this.tracked)) {
            if (loc.searchParams.has(key)) {
                loc.searchParams.delete(key)
            }

            this.removeInLocalStorage(key)
        }

        window.history.pushState({}, '', loc.toString())

        for (const key of Object.keys(this.tracked)) {
            let [_, apply, defaultValue] = this.tracked[key]
            apply(defaultValue)
        }

        this.sync()

        if (this.cb) {
            this.cb()
        }
    }

    clearState(key: string) {
        let loc = new URL(window.location.toString())
        if (loc.searchParams.has(key)) {
            loc.searchParams.delete(key)

            window.history.pushState({}, '', loc.toString())
        }

        this.removeInLocalStorage(key)

        return this;
    }

    private getInLocalStorage(key: string) {
        return JSON.parse(localStorage.getItem(`state-${key}`) ?? '{}')?.value || null
    }

    private setInLocalStorage(key: string, value: any) {
        localStorage.setItem(`state-${key}`, JSON.stringify({value}))

        return this;
    }

    private removeInLocalStorage(key: string) {
        localStorage.removeItem(`state-${key}`)

        return this;
    }

    onChange(cb: () => void) {
        this.cb = cb

        return this
    }
}

export class MapStateStore {
    focusedEntry: [number, number] | null = null

    setFocusedEntry(groupId: number, entryId: number) {
        this.focusedEntry = [groupId, entryId]

        this.updateFragment()
    }

    resetFocusedEntry() {
        this.focusedEntry = null

        this.updateFragment()
    }
    sync() {
        let loc = new URL(window.location.toString())

        for (const part of loc.hash.split("/")) {
            if (part.match(/(\d+):(\d+)/)) {
                this.focusedEntry = [parseInt(part.split(":")[0]), parseInt(part.split(":")[1])]
            }
        }

        this.updateFragment()
    }

    private updateFragment() {
        let loc = new URL(window.location.toString())

        let hash = '/'

        if (this.focusedEntry !== null) {
            hash += `${this.focusedEntry[0]}:${this.focusedEntry[1]}/`
        }

        loc.hash = hash

        window.history.replaceState({}, '', loc.toString())
    }
}
