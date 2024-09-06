// This a bad abstraction. We shouldn't generalize here.
export function shouldFilterEntry(mask: string, cmp: string): boolean {
    let andOrMask = window.andOrMask.toString(2).padStart(window.bitmaskLength, "0")

    mask = mask.padStart(window.bitmaskLength, "0")
    cmp = cmp.padStart(window.bitmaskLength, "0")

    for (let i = 0; i < mask.length; i++) {
        if (andOrMask[i] === '1' && mask[i] === '1' && cmp[i] === '0') {
            return true
        }
    }

    for (let i = 0; i < mask.length; i++) {
        if (andOrMask[i] === '0' && mask[i] === "1" && cmp[i] === "1") {
            return false
        }
    }

    return true
}
