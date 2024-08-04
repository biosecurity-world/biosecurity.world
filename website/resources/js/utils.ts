export const IN_PRODUCTION = import.meta.env.PROD === true

export function switchState(newState: string, selector: string, datasetKey: string): void {
    document.querySelectorAll(selector).forEach((state: HTMLElement) => {
        let isActive = newState === state.dataset[datasetKey]

        state.ariaHidden = isActive ? "false" : "true"
        if (isActive) {
            state.classList.add('state-active')
            state.classList.remove('state-inactive')
        } else {
            state.classList.add('state-inactive')
            state.classList.remove('state-active')
        }
    })

}

export const PI = Math.PI
export const PI_4 = PI / 4
export const PI_6 = PI / 6
export const PI_2 = PI / 2
export const PI_3 = PI / 3
export const PIPI = PI * 2

export function eq(a: number, b: number) {
    if (a === b) {
        return true
    }

    return Math.abs((a - b) / b) < 1e-6
}

export function gte(a: number, b: number) {
    return a > b || eq(a, b)
}

export function lte(a: number, b: number) {
    return a < b || eq(a, b)
}

export function gt(a: number, b: number) {
    return a > b && !eq(a, b)
}

export function lt(a: number, b: number) {
    return a < b && !eq(a, b)
}

export function inIE(x: number, fpA: number, fpB: number) {
    return gte(x, fpA) && lt(x, fpB)
}

export function inEI(x: number, fpA: number, fpB: number) {
    return gt(x, fpA) && lte(x, fpB)
}
export function getQuadrant(angle: number): number {
    if (lt(angle, 0) || gt(angle, PIPI)) {
        throw new Error(`Angle ${angle} is not in the range [0, 2*PI]`)
    }

    return Math.floor(angle / PI_2)
}

export function shortestDistanceBetweenRectangles(
    rect1: [number, number, number, number],
    rect2: [number, number, number, number]
): number {
    let [x1, y1, w1, h1] = rect1
    let [x2, y2, w2, h2] = rect2

    let dx = Math.max(0, Math.abs(x1 - x2) - w1 / 2 - w2 / 2)
    let dy = Math.max(0, Math.abs(y1 - y2) - h1 / 2 - h2 / 2)

    return Math.sqrt(dx * dx + dy * dy)
}
