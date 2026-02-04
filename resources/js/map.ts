import {select} from "d3"
import Panzoom from "@panzoom/panzoom"
import {changeAppState, debug} from "@/utils"
import type {AppStateChangeEvent, Node, ProcessedNode} from "@/types/index.d.ts"
import {updateMap} from "@/layout"
import FiltersStore, {Filters} from "@/filters"

let $map = select<SVGElement, any>("#map")

/* Open and close a specific entry  */
let elEntryLoader = document.getElementById("entry-loader")!
let elEntryWrapper = document.getElementById("entry-wrapper")!

/* Tooltip (appended to body to avoid overflow clipping from scrollable entry panel) */
let tooltip: HTMLDivElement | null = null
function getTooltip(): HTMLDivElement {
    if (!tooltip) {
        tooltip = document.createElement("div")
        tooltip.style.cssText =
            "position:fixed;padding:2px 8px;border-radius:6px;background:#1f2937;color:white;font-size:0.75rem;white-space:nowrap;pointer-events:none;opacity:0;transition:opacity 0.15s;z-index:50"
        document.body.appendChild(tooltip)
    }
    return tooltip
}
function attachTooltip(el: HTMLElement, text: string) {
    el.addEventListener("mouseenter", () => {
        const tip = getTooltip()
        tip.textContent = text
        tip.style.opacity = "0"
        tip.style.transform = "translateY(-100%)"
        tip.style.left = "0"
        const rect = el.getBoundingClientRect()
        const tipWidth = tip.offsetWidth
        const centered = rect.left + rect.width / 2 - tipWidth / 2
        tip.style.left = Math.max(4, centered) + "px"
        tip.style.top = rect.top - 4 + "px"
        tip.style.opacity = "1"
    })
    el.addEventListener("mouseleave", () => {
        getTooltip().style.opacity = "0"
    })
}
function setupEntryTooltips() {
    elEntryWrapper.querySelectorAll<HTMLElement>("[data-activity-label]").forEach((el) => {
        attachTooltip(el, el.dataset.activityLabel!)
    })
    elEntryWrapper.querySelectorAll<HTMLElement>("[data-tooltip-text]").forEach((el) => {
        attachTooltip(el, el.dataset.tooltipText!)
    })
}

/* Position the entry panel as a fixed overlay aligned with the map */
function updateEntryPanelPosition() {
    const mainEl = document.querySelector("#map-wrapper > main") as HTMLElement
    if (!mainEl) return
    const mainRect = mainEl.getBoundingClientRect()
    const top = Math.max(0, mainRect.top) + "px"
    const left = mainRect.left + "px"

    elEntryWrapper.style.top = top
    elEntryWrapper.style.left = left
    elEntryLoader.style.top = top
    elEntryLoader.style.left = left
}
window.addEventListener("scroll", updateEntryPanelPosition, {passive: true})

async function openEntry(entry: HTMLElement): Promise<void> {
    elEntryLoader.classList.add("loading-entry")
    updateEntryPanelPosition()

    let entrygroup = parseInt(entry.dataset.entrygroup!, 10)
    let entryId = parseInt(entry.dataset.entry!, 10)

    try {
        let entryResponse = await fetch(`/partials/entries/${entrygroup}/${entryId}`, {
            headers: {"X-Requested-With": "XMLHttpRequest"},
        })

        if (!entryResponse.ok) {
            throw new Error(`Failed to load entry: ${entryResponse.status}`)
        }

        let content = await entryResponse.text()
        elEntryWrapper.innerHTML = content
        updateEntryPanelPosition()
        setupEntryTooltips()

        let closeButton = elEntryWrapper.querySelector("button.close-entry")
        if (!closeButton) {
            throw new Error("Entry response did not contain expected content")
        }

        setLastFocusedEntry([entrygroup, entryId])

        closeButton.addEventListener("click", () => closeEntry())
    } catch (err: unknown) {
        changeAppState("error", {
            error: err,
            message: "An error occurred while loading the entry. Please try again later.",
        })
    } finally {
        elEntryLoader.classList.remove("loading-entry")
    }
}
function closeEntry(): void {
    elEntryWrapper.innerHTML = ""
    elEntryWrapper.style.top = ""
    elEntryWrapper.style.left = ""
    setLastFocusedEntry(null)
}

/* Handle app state changes */
let stateElements = document.querySelectorAll(".app-state") as NodeListOf<HTMLElement>
window.addEventListener("appstatechange", (e: AppStateChangeEvent) => {
    const {state, params} = e.detail

    switch (state) {
        case "error":
            let elStateContainer = document.querySelector("[data-state='error']")!
            let reason = elStateContainer.querySelector(".reason") as HTMLParagraphElement

            reason.innerHTML = params.message

            console.error(params.error)
            break
    }

    stateElements.forEach((state: HTMLElement) => {
        let isActive = e.detail.state === state.dataset.state

        state.ariaHidden = isActive ? "false" : "true"
        state.classList.toggle("state-active", isActive)
        state.classList.toggle("state-inactive", !isActive)
    })
})

/* Prepare filters */
let activityInputs = document.querySelectorAll(`.activity-checkbox`) as NodeListOf<HTMLInputElement>
let domainInputs = document.querySelectorAll(".domain-checkbox") as NodeListOf<HTMLInputElement>

let focusInputs = document.querySelectorAll(`.focus-checkbox`) as NodeListOf<HTMLInputElement>
let focusesWrapper = document.querySelectorAll("[id^='focuses_wrapper_']") as NodeListOf<HTMLElement>

let gcbrFocus = document.querySelector(".has-gcbr-focus") as HTMLInputElement
let noFocusCheckbox = document.querySelector(".no-focus-checkbox") as HTMLInputElement
const filtersStore = new FiltersStore<Filters>({
    activities: [
        () => Array.from(activityInputs).reduce((mask, el, k: number) => mask | (+el.checked << k), 0),
        (mask: number) =>
            activityInputs.forEach((activityInput, k) => {
                activityInput.checked = (mask & (1 << k)) !== 0
            }),
    ],
    domains: [
        () => Array.from(domainInputs).reduce((mask, el, k) => mask | (+el.checked << k), 0),
        (mask: number) => domainInputs.forEach((domainInput, k) => (domainInput.checked = (mask & (1 << k)) !== 0)),
    ],
    focuses: [
        () =>
            Array.from(focusInputs).reduce((mask, el) => {
                let k = parseInt(el.dataset.globalOffset!, 10)
                return mask | (+el.checked << k)
            }, 0),
        (mask: number) =>
            focusInputs.forEach((el) => {
                let k = parseInt(el.dataset.globalOffset!, 10)
                el.checked = (mask & (1 << k)) !== 0
            }),
    ],
    gcbrFocus: [() => gcbrFocus.checked, (checked: boolean) => (gcbrFocus.checked = checked)],
    showNoFocus: [() => noFocusCheckbox.checked, (checked: boolean) => (noFocusCheckbox.checked = checked)],
})

for (const el of document.querySelectorAll("button.resets-filters")) {
    el.addEventListener("click", (e) => {
        e.stopImmediatePropagation()
        return filtersStore.reset()
    })
}
for (const el of activityInputs) {
    let label = document.querySelector(`label[for="${el.id}"]`)! as HTMLLabelElement

    label.addEventListener("click", (e) => {
        e.preventDefault()
        let mask = 1 << Array.from(activityInputs).indexOf(el)
        const allMask = (1 << activityInputs.length) - 1

        // If this activity is already the only one active, re-enable all
        if (filtersStore.getState("activities") === mask) {
            mask = allMask
        }

        filtersStore.setState("activities", mask)
    })
}
for (const el of domainInputs) {
    el.addEventListener("change", () => filtersStore.syncFilter("domains"))
}
gcbrFocus.addEventListener("change", () => filtersStore.syncFilter("gcbrFocus"))

// The no-focus checkbox label uses exclusive click like other pills
const noFocusLabel = document.querySelector(`label[for="${noFocusCheckbox.id}"]`) as HTMLLabelElement
noFocusLabel.addEventListener("click", (e) => {
    e.preventDefault()
    filtersStore.setState("showNoFocus", !filtersStore.getState("showNoFocus"))
})

let groupedFocuses: Map<HTMLElement, NodeListOf<HTMLInputElement>> = new Map()
let masterCheckboxes: Record<string, HTMLInputElement> = {}
let focusesLabels: Record<string, HTMLLabelElement> = {}
let previousFocuses: Record<string, boolean[]> = {}

focusesWrapper.forEach((focusWrapper) => {
    let focuses = focusWrapper.querySelectorAll(".focus-checkbox") as NodeListOf<HTMLInputElement>

    for (const focus of focuses) {
        focusesLabels[focus.id] = focusWrapper.querySelector(`label[for="${focus.id}"]`) as HTMLLabelElement
    }

    groupedFocuses.set(focusWrapper, focuses)
    previousFocuses[focusWrapper.id] = Array.from(focuses).map((focus) => focus.checked)
    masterCheckboxes[focusWrapper.id] = focusWrapper.querySelector(".focuses-master-checkbox") as HTMLInputElement
})

function toggleGroupedFocus(focusesWrapper: HTMLElement, force: boolean | null = null): void {
    let focuses = groupedFocuses.get(focusesWrapper)!
    if (force === null) {
        force = getGroupOffsets(focuses).length === 0
    }

    if (force) {
        focuses.forEach((focus, k) => (focus.checked = previousFocuses[focusesWrapper.id][k]))
    } else {
        focuses.forEach((focus) => (focus.checked = false))
    }
}
function getGroupOffsets(focuses: NodeListOf<HTMLInputElement>): number[] {
    let offsets = []
    for (const focus of focuses) {
        if (!focus.checked) {
            continue
        }

        offsets.push(parseInt(focus.dataset.globalOffset!, 10))
    }

    return offsets
}

// Exclusive focus selection: clicking a focus pill selects only that one
for (const focus of focusInputs) {
    const focusLabel = document.querySelector(`label[for="${focus.id}"]`) as HTMLLabelElement
    if (!focusLabel) continue

    focusLabel.addEventListener("click", (e) => {
        e.preventDefault()
        const k = parseInt(focus.dataset.globalOffset!, 10)
        const thisMask = 1 << k
        const currentMask = filtersStore.getState("focuses")

        // If already the only one selected (and no-focus hidden), re-enable all
        if (currentMask === thisMask && !filtersStore.getState("showNoFocus")) {
            const allMask = Array.from(focusInputs).reduce((mask, el) => {
                const offset = parseInt(el.dataset.globalOffset!, 10)
                return mask | (1 << offset)
            }, 0)
            filtersStore.setState("showNoFocus", true)
            filtersStore.setState("focuses", allMask)
        } else {
            filtersStore.setState("showNoFocus", false)
            filtersStore.setState("focuses", thisMask)
        }
    })
}

for (let [focusWrapper, focuses] of groupedFocuses) {
    masterCheckboxes[focusWrapper.id].addEventListener("click", (e) => {
        e.stopPropagation()
        toggleGroupedFocus(focusWrapper)
        filtersStore.syncFilter("focuses")
    })

    // Double-click on wrapper header toggles entire group exclusively
    const wrapperHeader = focusWrapper.querySelector(".flex.w-full.items-center") as HTMLElement
    if (wrapperHeader) {
        wrapperHeader.addEventListener("dblclick", (e) => {
            if ((e.target as HTMLElement).closest(".focus-checkbox, .focus-checkbox-label, label")) return
            e.preventDefault()
            for (const [comparisonGroup, _] of groupedFocuses) {
                toggleGroupedFocus(comparisonGroup, comparisonGroup.id === focusWrapper.id)
            }
            filtersStore.syncFilter("focuses")
        })
    }
}

filtersStore.onChange(
    ["focuses"],
    () => {
        for (const [focusesWrapper, focuses] of groupedFocuses) {
            let offsets = getGroupOffsets(focuses)

            if (offsets.length === 0 || offsets.length === focuses.length) {
                masterCheckboxes[focusesWrapper.id].checked = offsets.length === focuses.length
                masterCheckboxes[focusesWrapper.id].indeterminate = false
            } else {
                masterCheckboxes[focusesWrapper.id].checked = true
                masterCheckboxes[focusesWrapper.id].indeterminate = true
            }
        }
    },
    true,
)
;(async function () {
    try {
        let mapContentRes = await fetch("/partials/map-content")
        let mapContent = await mapContentRes.text()

        // Parse the HTML response and extract only the zoom-wrapper element
        let tempDiv = document.createElement("div")
        tempDiv.innerHTML = mapContent
        let zoomWrapper = tempDiv.querySelector("#zoom-wrapper")

        if (zoomWrapper) {
            $map.html(zoomWrapper.outerHTML)
        } else {
            // Fallback to original content if zoom-wrapper not found
            $map.html(mapContent)
        }

        // The map partial contains the entries, so we need to wait for it to load before we can show the entry
        // that was opened on the last visit but never closed.
        let lastFocusedEntry = getRememberedOpenEntry()
        if (lastFocusedEntry !== null) {
            let [entrygroup, entry] = lastFocusedEntry
            let el = document.querySelector(
                `a[data-entrygroup="${entrygroup}"][data-entry="${entry}"]`,
            ) as HTMLButtonElement
            openEntry(el)
        }

        let elsEntryButtons = document.querySelectorAll("a[data-entry]") as NodeListOf<HTMLButtonElement>

        elsEntryButtons.forEach((el: HTMLButtonElement) => {
            el.addEventListener("click", (e) => {
                e.preventDefault()
                openEntry(el)
            })
        })

        // Close entry panel when clicking outside of it
        document.addEventListener("click", (e) => {
            if (!elEntryWrapper.innerHTML.trim()) return
            const target = e.target as HTMLElement
            if (target.closest("#entry-wrapper")) return
            if (target.closest("a[data-entry]")) return
            if (target.closest(".nested-entry")) return
            closeEntry()
        })

        let $centerWrapper = select<SVGGElement, any>("#center-wrapper")
        let zoomWrapperEl = document.getElementById("zoom-wrapper") as SVGGElement

        let mapWidth = $map.node()!.clientWidth
        let mapHeight = $map.node()!.clientHeight

        // No centering needed for horizontal cladistic layout
        $centerWrapper.attr("transform", "")

        // Function to calculate the scale that fits the content
        function calculateFitScale(): number {
            const containerRect = $map.node()!.getBoundingClientRect()
            const containerWidth = containerRect.width
            const containerHeight = containerRect.height

            // Get the bounding box of the actual content
            const nestedLayout = document.getElementById("nested-layout-container")
            if (nestedLayout) {
                const contentWidth = nestedLayout.scrollWidth + 40 // Add padding
                const contentHeight = nestedLayout.scrollHeight + 40

                const scaleX = containerWidth / contentWidth
                const scaleY = containerHeight / contentHeight
                return Math.min(scaleX, scaleY, 1) // Don't exceed 1x
            }

            // Fallback for other layouts - use a reasonable minimum
            return 0.3
        }

        // Check if desktop before initializing panzoom
        const desktopQuery = window.matchMedia("(min-width: 1025px)")

        // Initialize Panzoom on the zoom wrapper
        // On mobile, use noBind to prevent internal handlers that call preventDefault()
        const panzoom = Panzoom(zoomWrapperEl, {
            maxScale: 2,
            minScale: 0.3, // Will be updated dynamically
            startScale: 1,
            startX: 0,
            startY: 0,
            noBind: !desktopQuery.matches,
            touchAction: desktopQuery.matches ? "none" : "auto",
        })

        // Expose panzoom for debugging
        ;(window as any).__panzoom = panzoom

        // Function to center the content
        function centerContent(scale: number, animate = true) {
            const containerRect = $map.node()!.getBoundingClientRect()
            const nestedLayout = document.getElementById("nested-layout-container")

            if (nestedLayout) {
                const contentWidth = (nestedLayout.scrollWidth + 40) * scale
                const contentHeight = (nestedLayout.scrollHeight + 40) * scale

                // Center horizontally and vertically if content is smaller than container
                const x = Math.max(0, (containerRect.width - contentWidth) / 2)
                const y = Math.max(0, (containerRect.height - contentHeight) / 2)

                panzoom.pan(x, y, {animate})
            }
        }

        // Listen for zoom changes to enforce limits and auto-center on zoom out
        zoomWrapperEl.addEventListener("panzoomchange", (e: any) => {
            const scale = e.detail.scale
            const minScale = calculateFitScale()

            // Update minimum scale dynamically
            panzoom.setOptions({minScale})

            // If zooming out to fit scale, center the content
            if (scale <= minScale * 1.1) {
                centerContent(scale)
            }
        })

        // Bind panzoom event handlers only on desktop (mobile gets plain scrolling)
        const parent = $map.node()!

        function togglePanzoom(enabled: boolean) {
            if (enabled) {
                panzoom.bind()
                parent.addEventListener("pointerdown", panzoom.handleDown)
                panzoom.setOptions({disablePan: false, disableZoom: false, touchAction: "none"})
                zoomWrapperEl.style.pointerEvents = "all"
            } else {
                panzoom.destroy()
                parent.removeEventListener("pointerdown", panzoom.handleDown)
                // setOptions({touchAction}) clears touch-action on both elem AND parent
                panzoom.setOptions({disablePan: true, disableZoom: true, touchAction: "auto"})
                panzoom.zoom(1, {animate: false})
                panzoom.pan(0, 0, {animate: false})
                zoomWrapperEl.style.pointerEvents = "none"
            }
        }

        if (desktopQuery.matches) {
            // On desktop, bind the parent SVG handler (internal handlers already bound at init)
            parent.addEventListener("pointerdown", panzoom.handleDown)
        }
        desktopQuery.addEventListener("change", (e) => togglePanzoom(e.matches))

        // Zoom buttons (desktop only, hidden on mobile via CSS)
        const zoomInBtn = document.getElementById("zoom-in")
        const zoomOutBtn = document.getElementById("zoom-out")
        if (zoomInBtn) {
            zoomInBtn.addEventListener("click", (e) => {
                e.stopPropagation()
                panzoom.zoomIn({step: 0.2, animate: true})
            })
        }
        if (zoomOutBtn) {
            zoomOutBtn.addEventListener("click", (e) => {
                e.stopPropagation()
                panzoom.zoomOut({step: 0.2, animate: true})
            })
        }

        // Handle window resize - re-render layout to fit new dimensions
        let resizeTimer: number
        window.addEventListener("resize", () => {
            updateEntryPanelPosition()
            clearTimeout(resizeTimer)
            resizeTimer = window.setTimeout(() => {
                filtersStore.syncFilter("activities")
            }, 150)
        })

        for (const node of window.nodes as (Node & Partial<ProcessedNode>)[]) {
            let el = document.querySelector(`[data-node="${node.id}"]`) as SVGElement | null
            if (!el) {
                throw new Error(`Node with id ${node.id} has no corresponding element in the DOM`)
            }

            node.el = el
        }

        // CRITICAL FIX: Move entrygroup foreignObjects from <g id="entrygroups"> to the
        // categories <g> (unnamed). The entrygroups group has coordinate system issues
        // where foreignObject x/y positioning doesn't work correctly.
        const entrygroupsGroup = document.getElementById("entrygroups")
        const categoriesGroup = entrygroupsGroup?.previousElementSibling // The unnamed <g> before entrygroups
        if (entrygroupsGroup && categoriesGroup) {
            const foreignObjects = Array.from(entrygroupsGroup.querySelectorAll("foreignObject"))
            for (const fo of foreignObjects) {
                categoriesGroup.appendChild(fo)
            }
            // Remove empty entrygroups group
            entrygroupsGroup.remove()
        }

        // Track if this is the initial load
        let isInitialLoad = true

        filtersStore.onChange(
            "*",
            (state) => {
                debug().clear()

                updateMap(state, {
                    activityCount: activityInputs.length,
                    focusesCount: Object.keys(focusesLabels).length,
                })

                debug().flush($centerWrapper)

                // After content renders, resize container to fit content
                // Use requestAnimationFrame chain to ensure layout is fully computed
                requestAnimationFrame(() => {
                    requestAnimationFrame(() => {
                        const mapSvg = $map.node()
                        const sectionEl = mapSvg?.parentElement as HTMLElement
                        const mainEl = sectionEl?.parentElement as HTMLElement

                        if (desktopQuery.matches) {
                            // Desktop: set explicit heights for the SVG foreignObject layout
                            const nestedLayoutFo = document.getElementById("nested-layout-fo")
                            const nestedLayoutContainer = document.getElementById("nested-layout-container")

                            if (nestedLayoutContainer && mainEl) {
                                const contentHeight = nestedLayoutContainer.offsetHeight + 40
                                const newHeight = Math.max(300, contentHeight)

                                if (sectionEl) sectionEl.style.height = `${newHeight}px`
                                mainEl.style.height = `${newHeight}px`

                                if (nestedLayoutFo) {
                                    nestedLayoutFo.setAttribute("height", String(newHeight))
                                }
                            }

                            panzoom.zoom(1, {animate: false})
                            panzoom.pan(0, 0, {animate: false})
                        } else {
                            // Mobile: content is plain HTML, let it flow naturally
                            if (sectionEl) sectionEl.style.height = ""
                            if (mainEl) mainEl.style.height = ""
                        }

                        isInitialLoad = false
                    })
                })
            },
            true,
        )
    } catch (err: unknown) {
        changeAppState("error", {
            error: err,
            message: "An error occurred while loading the map. Please try again later.",
        })
    }
})()

function setLastFocusedEntry(focusedEntry: [number, number] | null) {
    let loc = new URL(window.location.toString())
    loc.hash = focusedEntry ? `/${focusedEntry[0]}:${focusedEntry[1]}/` : ""

    window.history.replaceState({}, "", loc.toString())
}
function getRememberedOpenEntry(): [number, number] | null {
    let entry = new URL(window.location.toString()).hash.slice(1).split("/").filter(Boolean)[0]
    return entry && /(\d+):(\d+)/.test(entry) ?
            (entry.split(":").map((id) => parseInt(id, 10)) as [number, number])
        :   null
}
