import {IN_PRODUCTION} from "@/utils"

/**
 * A global, non-reactive, persistent state manager.
 */
export default class PersistentState {
    tracked: Record<string, [() => any, (value: any) => void, any]> = {}

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
        for (const key of keys) {
            if (!this.tracked[key]) {
                throw new Error(`Key [${key}] is not being tracked.`)
            }

            let [check, apply, defaultValue] = this.tracked[key]
            let value = check()

            console.log(value)

            apply(value)

            if (value === this.getState(key)) {
                continue
            }

            if (defaultValue !== null && value === defaultValue) {
                this.clearState(key)
                continue;
            }

            this.setState(key, value)
        }

        return this
    }

    sync(): this {
        this.syncOnly(Object.keys(this.tracked))

        return this
    }

    getState(key: string, fallback: string|null = null) {
        let loc = new URL(window.location.toString())
        if (loc.searchParams.has(key)) {
            return loc.searchParams.get(key)
        }

        let potentialValue = this.getInLocalStorage(key)
        if (key !== null) {
            return potentialValue
        }

        return fallback
    }

    setState(key: string, value: string) {
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

        this.syncOnly([key])

        return this;
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

    getInLocalStorage(key: string) {
        return JSON.parse(localStorage.getItem(`state-${key}`))?.value || null
    }

    setInLocalStorage(key: string, value: any) {
        localStorage.setItem(`state-${key}`, JSON.stringify({value}))

        return this;
    }

    removeInLocalStorage(key: string) {
        localStorage.removeItem(`state-${key}`)

        return this;
    }
}

export class PersistentMapState {
    position: [number, number, number] = [0, 0, 1]
    focusedEntry: [number, number] | null = null

    setPosition(x: number, y: number, k: number) {
        this.position = [x, y, k]

        this.updateFragment()
    }

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
            // TODO: Regexes should never be used, ever. If you have some time, rewrite this.
            if (/^-?[0-9]+(\.[0-9]+)?x-?[0-9]+(\.[0-9]+)?@-?[0-9]+(\.[0-9]+)?$/.test(part)) {
                this.position = [
                    parseFloat(part.split("x")[0]),
                    parseFloat(part.split("x")[1].split("@")[0]),
                    parseFloat(part.split("@")[1])
                ]
            }

            if (part.match(/(\d+):(\d+)/)) {
                this.focusedEntry = [parseInt(part.split(":")[0]), parseInt(part.split(":")[1])]
            }
        }

        this.updateFragment()
    }

    private updateFragment() {
        let loc = new URL(window.location.toString())

        let hash = '/'

        if (this.position !== null) {
            hash += `${this.position[0]}x${this.position[1]}@${this.position[2]}/`
        }

        if (this.focusedEntry !== null) {
            hash += `${this.focusedEntry[0]}:${this.focusedEntry[1]}/`
        }

        loc.hash = hash

        window.history.replaceState({}, '', loc.toString())

    }
}
