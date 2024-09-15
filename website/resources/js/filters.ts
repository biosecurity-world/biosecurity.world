export type Filters = {
    activities: number,
    domains: number,
    gcbrFocus: boolean,
}

export type FilterMetadata = {
    activityCount: number,
}

const TECHNICAL_DOMAIN = 1 << 0
const GOVERNANCE_DOMAIN = 1 << 1

export function shouldFilterEntry(state: Filters, meta: FilterMetadata, filterData: [Filters['activities'], Filters['domains'], Filters['gcbrFocus']]): boolean {
    let [activities, domains, gcbrFocus] = filterData

    if (state.gcbrFocus && !gcbrFocus) {
        return true
    }

    if ((state.domains & TECHNICAL_DOMAIN) !== 0 && (domains & TECHNICAL_DOMAIN) === 0) {
        return true
    }

    if ((state.domains & GOVERNANCE_DOMAIN) !== 0 && (domains & GOVERNANCE_DOMAIN) === 0) {
        return true
    }


    let shouldFilter = true

    for (let i = 0; i < meta.activityCount; i++) {
        if ((activities & (1 << i)) === 0) {
            continue
        }

        if ((state.activities & (1 << i)) !== 0) {
            shouldFilter = false
            break
        }
    }

    return shouldFilter
}

export default class FiltersState<S extends Record<string, number|boolean|string>> {
    private changeCallbacks: ((state: S) => void)[] = []

    private getters: { [K in keyof S]: () => S[K] }
    private setters: { [K in keyof S]: (v: S[K]) => void }
    private defaults: { [K in keyof S]: S[K] }

    constructor(gettersSetters: { [K in keyof S]: [() => S[K], (v: S[K]) => void] }) {
        this.getters = {} as any
        this.setters = {} as any
        this.defaults = {} as any

        for (const id in gettersSetters) {
            const [getter, setter] = gettersSetters[id]

            this.getters[id] = getter
            this.setters[id] = setter
            this.defaults[id] = getter()

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

    setState<K extends keyof S>(id: K, value: S[K]): FiltersState<S> {
        this.setters[id](value)
        this.setQueryParam(id, value)

        this.triggerChange(this.changeCallbacks)

        return this
    }

    reset(): FiltersState<S> {
        for (const key in this.defaults) {
            this.setState(key as keyof S, this.defaults[key as keyof S])
        }

        return this
    }

    onChange(callback: (state: S) => void, executeImmediately: boolean = false): FiltersState<S> {
        this.changeCallbacks.push(callback)

        if (executeImmediately) {
            this.triggerChange([callback])
        }

        return this
    }

    private triggerChange(callbacks: typeof this.changeCallbacks) {
        let data: Partial<S> = {}

        for (const id in this.getters) {
            data[id] = this.getters[id]()
        }

        for (const cb of callbacks) {
            cb(data as S)
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
            throw new Error(`Unsupported type conversion for ${key.toString()}`)
        }
    }

    setQueryParam<K extends keyof S>(key: K, value: S[K]) {
        let loc = new URL(window.location.toString())

        loc.searchParams.set(key as string, value.toString())

        window.history.replaceState({}, '', loc.toString())
    }
}

