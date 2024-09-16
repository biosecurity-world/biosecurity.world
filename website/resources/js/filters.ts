export type Filters = {
    activities: number,
    domains: number,
    focuses: number,
    gcbrFocus: boolean,
}

export type FilterMetadata = {
    activityCount: number,
    focusesCount: number,
}

const TECHNICAL_DOMAIN = 1 << 0 // 1
const GOVERNANCE_DOMAIN = 1 << 1 // 2

export function shouldFilterEntry(state: Filters, entry: Filters, meta: FilterMetadata): boolean {
    if (state.gcbrFocus && !entry.gcbrFocus) {
        return true
    }

    if ((state.domains & TECHNICAL_DOMAIN) !== 0 && (entry.domains & TECHNICAL_DOMAIN) === 0) {
        return true
    }

    if ((state.domains & GOVERNANCE_DOMAIN) !== 0 && (entry.domains & GOVERNANCE_DOMAIN) === 0) {
        return true
    }

    let hasSharedActivities = (state.activities & entry.activities) !== 0
    if (!hasSharedActivities) {
        return true
    }

    let hasSharedFocuses = (state.focuses & entry.focuses) !== 0
    return !hasSharedFocuses
}

type ChangeCallback<S> = (state: S) => void

export default class FiltersState<
    // If you add a new type for a filter's value, you need to update
    // the getQueryParam and setQueryParam methods which handle
    // the serialization and deserialization of the filters.
    S extends Record<string, number|boolean|string>
> {
    private getters: { [K in keyof S]: () => S[K] }
    private setters: { [K in keyof S]: (v: S[K]) => void }
    private defaults: { [K in keyof S]: S[K] }
    private changeCallbacks: { [K in keyof S]: ChangeCallback<S>[] }

    constructor(gettersSetters: { [K in keyof S]: [() => S[K], (v: S[K]) => void] }) {
        this.getters = {} as any
        this.setters = {} as any
        this.defaults = {} as any
        this.changeCallbacks = { } as any

        for (const id in gettersSetters) {
            const [getter, setter] = gettersSetters[id]

            this.getters[id] = getter
            this.setters[id] = setter
            this.defaults[id] = getter()
            this.changeCallbacks[id] = []

            let queryValue = this.getQueryParam(id)

            if (queryValue) {
                this.setState(id, queryValue)
            }
        }
    }

    syncFilter(id: keyof S): FiltersState<S> {
        this.setState(id, this.getters[id]())

        return this
    }

    getState<K extends keyof S>(id: K): S[K] {
        return this.getters[id]()
    }

    setState<K extends keyof S>(id: K, value: S[K]): FiltersState<S> {
        this.setters[id](value)
        this.setQueryParam(id, value)

        this.triggerChange([id])

        return this
    }

    reset(): FiltersState<S> {
        for (const key in this.defaults) {
            this.setState(key as keyof S, this.defaults[key as keyof S])
        }

        return this
    }

    onChange(keys: (keyof S)[] | '*', callback: (state: S) => void, executeImmediately: boolean = false): FiltersState<S> {
        if (keys === '*') {
            keys = Object.keys(this.defaults) as (keyof S)[]
        }

        for (const key of keys) {
            this.changeCallbacks[key].push(callback)
        }

        if (executeImmediately) {
            callback(this.getFullState())
        }

        return this
    }

    private getFullState(): S {
        let data: Partial<S> = {}

        for (const id in this.getters) {
            data[id] = this.getters[id]()
        }

        return data as S
    }

    private triggerChange(keys: (keyof S)[]) {
        let state = this.getFullState()
        for (const key of keys) {
            for (const callback of this.changeCallbacks[key]) {
                callback(state)
            }
        }

        return this
    }

    getQueryParam<K extends keyof S>(key: K): S[K] | null {
        let value = (new URL(window.location.toString())).searchParams.get(key as string)

        if (value === null) {
            return null
        }

        let type = typeof this.defaults[key]

        if (type === 'string') {
            return value as any
        } else if (type === 'number') {
            return parseInt(value, 10) as any
        } else if (type === 'boolean') {
            return (value === 'true') as any
        } else {
            throw new Error(`Could not deserialize key [${key.toString()}]`)
        }
    }

    setQueryParam<K extends keyof S>(key: K, value: S[K]) {
        let loc = new URL(window.location.toString())

        loc.searchParams.set(key as string, value.toString())

        window.history.replaceState({}, '', loc.toString())
    }
}

