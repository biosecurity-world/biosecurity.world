import type {Node, ProcessedNode} from "@/types/index.d.ts"
import {changeAppState} from "@/utils"
import {FilterMetadata, Filters, shouldFilterEntry} from "@/filters"

// Stub export for backwards compatibility with render-testcase.ts
// The old radial/sector layout is no longer used but the test file still imports this
export function fitToSector(
    _params: {sector: [number, number]; size: [number, number]},
    _others: unknown[],
): [number, number] {
    console.warn("fitToSector is deprecated - nested box layout does not use sectors")
    return [0, 0]
}

// Layout mode - can be switched
export type LayoutMode = "nested" | "branching"

// High-level category mapping (using actual category labels from data)
const HIGH_LEVEL_CATEGORIES: Record<string, {order: number; label: string; color: string; fullWidth?: boolean}> = {
    Prevention: {order: 0, label: "Prevention", color: "#10b981"},
    Detection: {order: 1, label: "Detection", color: "#3b82f6"},
    Response: {order: 2, label: "Response", color: "#8b5cf6"},
    Transversal: {order: 3, label: "Transversal", color: "#f59e0b", fullWidth: true},
}

// Darker shades for subcategory backgrounds (matching HIGH_LEVEL_CATEGORIES)
const SUBCATEGORY_COLORS: Record<string, {bg: string; border: string}> = {
    Prevention: {bg: "#10b98125", border: "#10b98150"},
    Detection: {bg: "#3b82f625", border: "#3b82f650"},
    Response: {bg: "#8b5cf625", border: "#8b5cf650"},
    Transversal: {bg: "#f59e0b25", border: "#f59e0b50"},
}

type PreparedNode = (typeof window.nodes)[number] & {el: SVGElement} & Partial<ProcessedNode> & {
        children?: PreparedNode[]
        highLevelCategory?: string
    }

export function updateMap(state: Filters, metadata: FilterMetadata) {
    changeAppState("loading", {})
    resetGlobalMapState()

    const background = document.getElementById("background") as SVGGElement | null
    if (background) {
        background.innerHTML = ""
    }

    // Build node lookup and filter entries
    const idToNode: Record<number, PreparedNode> = {}

    for (let i = 0; i < window.nodes.length; i++) {
        const node = window.nodes[i] as PreparedNode
        idToNode[node.id] = node

        // Reset tree state from previous runs to prevent accumulation
        node.children = undefined
        node.filtered = undefined
        node.highLevelCategory = undefined

        // Filter leaf nodes (entrygroups)
        if (node.od === 0) {
            const entryIds = node.entries!
            let matchingEntries = []

            for (const entryId of entryIds) {
                const filterData = window.filterData[entryId]
                const shouldFilter = shouldFilterEntry(
                    state,
                    {
                        activities: filterData[0],
                        focuses: filterData[1],
                        domains: filterData[2],
                        gcbrFocus: filterData[3],
                    },
                    metadata,
                )

                document
                    .querySelector(`a[data-entrygroup="${node.id}"][data-entry="${entryId}"]`)!
                    .classList.toggle("matches-filters", !shouldFilter)

                if (!shouldFilter) {
                    matchingEntries.push(entryId)
                }
            }

            node.filtered = matchingEntries.length === 0
        }
    }

    // Build tree structure
    let root: PreparedNode | null = null

    for (const node of Object.values(idToNode)) {
        if (node.id === node.parent) {
            root = node
        } else {
            const parent = idToNode[node.parent]
            if (parent) {
                if (!parent.children) parent.children = []
                parent.children.push(node)
            }
        }
    }

    if (!root) {
        changeAppState("error", {error: new Error("No root node"), message: "Could not find root node"})
        return
    }

    // Propagate filtered status
    function propagateFiltered(node: PreparedNode): boolean {
        if (!node.children || node.children.length === 0) {
            return node.filtered || false
        }
        node.children = node.children.filter((child) => !propagateFiltered(child))
        node.filtered = node.children.length === 0
        return node.filtered || false
    }

    propagateFiltered(root)

    if (root.filtered || !root.children || root.children.length === 0) {
        changeAppState("empty", {})
        return
    }

    // Create the nested box layout
    renderNestedBoxes(root, idToNode)

    changeAppState("success", {})
}

function renderNestedBoxes(root: PreparedNode, idToNode: Record<number, PreparedNode>) {
    const centerWrapper = document.getElementById("center-wrapper")
    if (!centerWrapper) return

    // Clear existing content and create HTML container
    const existingFo = document.getElementById("nested-layout-fo")
    if (existingFo) {
        existingFo.remove()
    }

    // Get the map container dimensions to make layout responsive
    const mapEl = document.getElementById("map")
    const containerWidth = mapEl ? mapEl.clientWidth : 1400

    // Create main container as foreignObject - use full width for centering
    const fo = document.createElementNS("http://www.w3.org/2000/svg", "foreignObject")
    fo.setAttribute("x", "0")
    fo.setAttribute("y", "0")
    fo.setAttribute("width", "100%")
    fo.setAttribute("height", "3000") // Will be adjusted after render
    fo.id = "nested-layout-fo"
    fo.style.pointerEvents = "auto"

    // Wrapper div for centering
    const wrapper = document.createElement("div")
    wrapper.id = "nested-layout-wrapper"
    wrapper.style.cssText = `
        display: flex;
        justify-content: center;
        width: 100%;
        padding: 20px;
        box-sizing: border-box;
    `

    const layoutWidth = Math.min(Math.max(containerWidth - 40, 300), 1800)
    const container = document.createElement("div")
    container.id = "nested-layout-container"
    container.className = "nested-layout"
    container.style.cssText = `
        display: flex;
        flex-direction: column;
        gap: 16px;
        width: ${layoutWidth}px;
        max-width: 100%;
    `

    // Top row: Prevention, Detection, Response - width proportional to content, stretch to equal heights
    const topRow = document.createElement("div")
    topRow.className = "top-row"
    topRow.style.cssText = `
        display: flex;
        gap: 12px;
        align-items: stretch;
    `

    // Categorize children into high-level categories
    const categorizedChildren: Record<string, PreparedNode[]> = {
        Prevention: [],
        Detection: [],
        Response: [],
        Transversal: [],
    }

    // Helper function to get label from node
    function getNodeLabel(node: PreparedNode): string {
        if (node.el && node.el.querySelector) {
            const labelEl = node.el.querySelector("span")
            return labelEl?.textContent?.trim() || ""
        }
        return ""
    }

    // Get labels from lookup to categorize
    for (const child of root.children || []) {
        const label = getNodeLabel(child)

        // Match to high-level category by exact name
        if (categorizedChildren[label] !== undefined) {
            categorizedChildren[label].push(child)
            child.highLevelCategory = label
        } else {
            // Default to Transversal if no exact match
            categorizedChildren["Transversal"].push(child)
            child.highLevelCategory = "Transversal"
        }
    }

    // Render top 3 categories
    const topCategories = ["Prevention", "Detection", "Response"]

    for (const catName of topCategories) {
        const catConfig = HIGH_LEVEL_CATEGORIES[catName]
        const children = categorizedChildren[catName]

        const box = createHighLevelBox(catConfig.label, catConfig.color, children, idToNode)
        topRow.appendChild(box)
    }

    container.appendChild(topRow)

    // Bottom row: Transversal (full width)
    const transversalConfig = HIGH_LEVEL_CATEGORIES["Transversal"]
    const transversalChildren = categorizedChildren["Transversal"]

    if (transversalChildren.length > 0) {
        const transversalBox = createHighLevelBox(
            transversalConfig.label,
            transversalConfig.color,
            transversalChildren,
            idToNode,
            true,
        )
        container.appendChild(transversalBox)
    }

    wrapper.appendChild(container)
    fo.appendChild(wrapper)
    centerWrapper.appendChild(fo)

    // Hide all the original SVG foreignObjects (categories and entrygroups)
    hideOriginalElements()
}

// Count total entries in children recursively
function countEntriesInChildren(children: PreparedNode[]): number {
    let total = 0
    for (const child of children) {
        if (child.filtered) continue
        total += countEntries(child)
    }
    return total
}

function createHighLevelBox(
    label: string,
    color: string,
    children: PreparedNode[],
    idToNode: Record<number, PreparedNode>,
    fullWidth = false,
): HTMLElement {
    // Count entries to determine flex-grow (more content = more width = similar height)
    const totalEntries = countEntriesInChildren(children)
    const flexGrow = Math.max(1, totalEntries)

    const box = document.createElement("div")
    box.className = "high-level-box"
    box.style.cssText = `
        background: linear-gradient(135deg, ${color}15 0%, ${color}08 100%);
        border: 2px solid ${color};
        border-radius: 12px;
        padding: 12px;
        display: flex;
        flex-direction: column;
        overflow: hidden;
        min-width: 0;
        ${fullWidth ? "width: 100%;" : `flex: ${flexGrow} 1 0%;`}
    `

    // Header
    const header = document.createElement("div")
    header.className = "box-header"
    header.style.cssText = `
        font-size: 15px;
        font-weight: 700;
        color: ${color};
        margin-bottom: 10px;
        padding-bottom: 6px;
        border-bottom: 1px solid ${color}40;
    `
    header.textContent = label
    box.appendChild(header)

    // Content area for subcategories - single row flexbox, equal heights, width proportional to org count
    const content = document.createElement("div")
    content.className = "box-content"
    content.style.cssText = `
        display: flex;
        gap: 8px;
        align-items: stretch;
        overflow: hidden;
        flex: 1;
    `

    // Flatten children - if a child has the same label as parent, use its children directly
    const flattenedChildren: PreparedNode[] = []
    for (const child of children) {
        if (child.filtered) continue

        // Get child's label
        let childLabel = ""
        if (child.el) {
            const labelEl = child.el.querySelector("span")
            childLabel = labelEl?.textContent?.trim() || ""
        }

        // If child has same name as parent box, flatten its children
        if (childLabel === label && child.children) {
            flattenedChildren.push(...child.children.filter((c) => !c.filtered))
        } else {
            flattenedChildren.push(child)
        }
    }

    // Separate subcategories from direct entries
    const subcategories: PreparedNode[] = []
    const directEntrygroups: PreparedNode[] = []

    for (const child of flattenedChildren) {
        if (child.filtered) continue
        if (child.children && child.children.length > 0) {
            subcategories.push(child)
        } else if (child.od === 0) {
            directEntrygroups.push(child)
        }
    }

    // Render subcategories as boxes
    for (const subcat of subcategories) {
        const subBox = createSubcategoryBox(subcat, label, idToNode)
        content.appendChild(subBox)
    }

    box.appendChild(content)

    // Render direct entries in a simple flex row at the bottom (no boxes)
    if (directEntrygroups.length > 0) {
        const directEntriesRow = document.createElement("div")
        directEntriesRow.className = "direct-entries"
        directEntriesRow.style.cssText = `
            display: flex;
            flex-wrap: wrap;
            gap: 5px;
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px dashed ${color}40;
        `
        for (const eg of directEntrygroups) {
            renderEntriesInContainer(eg, directEntriesRow, true)
        }
        box.appendChild(directEntriesRow)
    }

    return box
}

// Count total entries in a node (recursively)
function countEntries(node: PreparedNode): number {
    if (node.od === 0) {
        // Entrygroup - count matching entries
        const entryIds = node.entries || []
        let count = 0
        for (const entryId of entryIds) {
            const entryLink = document.querySelector(
                `a[data-entrygroup="${node.id}"][data-entry="${entryId}"]`,
            ) as HTMLElement
            if (entryLink?.classList.contains("matches-filters")) {
                count++
            }
        }
        return count
    }
    // Category - sum children
    let total = 0
    for (const child of node.children || []) {
        if (!child.filtered) {
            total += countEntries(child)
        }
    }
    return total
}

function createSubcategoryBox(
    node: PreparedNode,
    parentCategory: string,
    idToNode: Record<number, PreparedNode>,
): HTMLElement {
    // Get colors from parent category
    const categoryColors = SUBCATEGORY_COLORS[parentCategory] || SUBCATEGORY_COLORS["Transversal"]

    // Count entries to determine flex-grow (width proportional to org count)
    const entryCount = countEntries(node)
    const flexGrow = Math.max(1, entryCount) // More entries = proportionally wider

    const box = document.createElement("div")
    box.className = "subcategory-box"
    box.style.cssText = `
        background: ${categoryColors.bg};
        border: 1px solid ${categoryColors.border};
        border-radius: 6px;
        padding: 8px;
        min-width: 100px;
        display: flex;
        flex-direction: column;
        flex: ${flexGrow} 1 0%;
        overflow: hidden;
    `

    // Get label from the node's element
    let label = "Subcategory"
    if (node.el) {
        const labelEl = node.el.querySelector("span")
        if (labelEl) {
            label = labelEl.textContent?.trim() || label
        }
    }

    // Header
    const header = document.createElement("div")
    header.className = "subbox-header"
    header.style.cssText = `
        font-size: 11px;
        font-weight: 600;
        color: #374151;
        margin-bottom: 5px;
        border-bottom: 1px solid ${categoryColors.border};
        padding-bottom: 3px;
    `
    header.textContent = label
    box.appendChild(header)

    // Content - horizontal wrap for entries
    const content = document.createElement("div")
    content.className = "subbox-content"
    content.style.cssText = `
        display: flex;
        flex-wrap: wrap;
        gap: 4px;
        align-items: flex-start;
        align-content: flex-start;
        flex: 1;
    `

    // Render children (could be more subcategories or entrygroups)
    for (const child of node.children || []) {
        if (child.filtered) continue

        if (child.od === 0) {
            // Entrygroup - render entries
            renderEntriesInContainer(child, content, true)
        } else if (child.children) {
            // Deeper subcategory - render recursively
            const deeperBox = createSubcategoryBox(child, parentCategory, idToNode)
            deeperBox.style.flex = "1 1 100%"
            content.appendChild(deeperBox)
        }
    }

    box.appendChild(content)
    return box
}

function renderEntriesInContainer(entryNode: PreparedNode, container: HTMLElement, horizontal = false) {
    const entryIds = entryNode.entries || []

    for (const entryId of entryIds) {
        // Find the entry link element
        const entryLink = document.querySelector(
            `a[data-entrygroup="${entryNode.id}"][data-entry="${entryId}"]`,
        ) as HTMLElement

        if (!entryLink || !entryLink.classList.contains("matches-filters")) continue

        // Clone the entry for display in nested layout
        const entryClone = document.createElement("div")
        entryClone.className = "nested-entry"
        entryClone.style.cssText = `
            display: flex;
            align-items: center;
            gap: 4px;
            padding: 2px 5px;
            background: #f9fafb;
            border-radius: 4px;
            cursor: pointer;
            transition: background 0.15s;
            max-width: 100%;
            min-width: 0;
        `
        entryClone.onmouseenter = () => (entryClone.style.background = "#e5e7eb")
        entryClone.onmouseleave = () => (entryClone.style.background = "#f9fafb")

        // Copy logo if exists (it's in a span.entry-logo, may contain img or svg)
        const logoSpan = entryLink.querySelector(".entry-logo")
        if (logoSpan) {
            const logoClone = logoSpan.cloneNode(true) as HTMLElement
            logoClone.style.cssText =
                "width: 16px; height: 16px; min-width: 16px; border-radius: 2px; overflow: hidden; flex-shrink: 0;"
            const imgInClone = logoClone.querySelector("img")
            if (imgInClone) {
                imgInClone.style.cssText = "width: 100%; height: 100%; object-fit: contain;"
            }
            entryClone.appendChild(logoClone)
        }

        // Copy label (second span with text-sm class)
        const labelSpan = entryLink.querySelector("span.text-sm")
        if (labelSpan && labelSpan.textContent?.trim()) {
            const labelClone = document.createElement("span")
            labelClone.style.cssText =
                "font-size: 10px; color: #374151; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; min-width: 0;"
            labelClone.textContent = labelSpan.textContent.trim()
            entryClone.appendChild(labelClone)
        }

        // Set title for tooltip on hover
        entryClone.title = labelSpan?.textContent?.trim() || ""

        // Make clickable
        entryClone.onclick = () => entryLink.click()

        container.appendChild(entryClone)
    }
}

function hideOriginalElements() {
    // The SVG structure is:
    // #zoom-wrapper > #center-wrapper > #background, foreignObjects, <g> with categories/entrygroups
    // We need to hide everything inside #center-wrapper EXCEPT our nested layout foreignObject

    const centerWrapper = document.getElementById("center-wrapper")
    if (!centerWrapper) return

    // Hide all children of center-wrapper except background and our nested layout
    Array.from(centerWrapper.children).forEach((child) => {
        const el = child as SVGElement
        if (el.id === "background") {
            // Clear background but keep it
            el.innerHTML = ""
        } else if (el.id === "nested-layout-fo") {
            // Keep our nested layout visible
        } else if (el.tagName.toLowerCase() === "g") {
            // Hide the entire group containing categories/entrygroups
            el.style.display = "none"
            el.style.visibility = "hidden"
        } else if (el.tagName.toLowerCase() === "foreignobject") {
            // Hide individual foreignObjects (root node, etc)
            el.setAttribute("x", "-99999")
            el.setAttribute("y", "-99999")
            el.setAttribute("width", "0")
            el.setAttribute("height", "0")
            el.style.display = "none"
        }
    })
}

function resetGlobalMapState() {
    document.querySelectorAll(".debug-rect").forEach((el) => el.remove())

    // Remove nested layout if exists
    const nestedContainer = document.getElementById("nested-layout-fo")
    if (nestedContainer) {
        nestedContainer.remove()
    }

    for (let i = 0; i < window.nodes.length; i++) {
        const node = window.nodes[i] as Node & Partial<ProcessedNode> & {el: SVGElement}

        node.el.style.display = ""
        node.el.classList.add("off-screen")
        node.el.ariaHidden = "true"
        node.el.removeAttribute("transform")
        node.el.removeAttribute("x")
        node.el.removeAttribute("y")
        node.el.setAttribute("width", "100%")
        node.el.setAttribute("height", "100%")
    }
}
