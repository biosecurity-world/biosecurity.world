import type {Node, ProcessedNode, Sector} from "@/types/index.d.ts"
import {
  changeAppState,
  debug,
  eq,
  getQuadrant,
  gt,
  gte,
  inIE,
  lt,
  PI,
  PIPI,
  shortestDistanceBetweenRectangles,
} from "@/utils"
import {FilterMetadata, Filters, shouldFilterEntry} from "@/filters"

type PreparedNode = (typeof window.nodes)[number] & {el: SVGElement} & Partial<ProcessedNode>

export function updateMap(state: Filters, metadata: FilterMetadata) {
  let nodes: PreparedNode[] = []
  let stack: PreparedNode[] = []

  changeAppState("loading", {})
  resetGlobalMapState()
  const background = document.getElementById("background") as SVGGElement | null
  if (background) {
    background.innerHTML = ""
  }
  const topPalette = [
    "#e41a1c",
    "#377eb8",
    "#4daf4a",
    "#984ea3",
    "#ff7f00",
    "#a65628",
    "#f781bf",
    "#999999",
    "#66c2a5",
    "#fc8d62",
    "#8da0cb",
    "#e78ac3",
    "#a6d854",
    "#ffd92f",
    "#e5c494",
    "#b3b3b3",
  ]
  const topColorById: Record<number, string> = {}
  let topColorIndex = 0

  let maxDepth = 0
  let idToNode: Record<number, PreparedNode> = {}

  for (let i = 0; i < window.nodes.length; i++) {
    let node = window.nodes[i] as PreparedNode

    idToNode[node.id] = node

    if (node.depth >= maxDepth) {
      maxDepth = node.depth
    }

    if (node.od === 0) {
      let entryIds = node.entries!
      let matchingEntries = []

      for (const entryId of entryIds) {
        let filterData = window.filterData[entryId]
        let shouldFilter = shouldFilterEntry(
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

    if (!(node.el instanceof SVGForeignObjectElement)) {
      throw new Error(`Element for node ${node.id} is not a foreignObject, but a ${node.el.tagName}`)
    }

    // We set the <foreignObject> with a height of 100% and a w of 100%
    // because we don't want to compute the size of the elements server-side
    // but this means that we get the wrong bounds.
    if (node.el.firstElementChild === null) {
      throw new Error(
        "It is expected that the foreignObject representing the node " +
          "has a single child to compute its real bounding box, not " +
          "the advertised (100%, 100%)",
      )
    }

    node.size = [
      // getBoundingClientRect() is transform-aware, so the zoom will mess everything up on subsequent renders.
      // We need to use offsetWidth and offsetHeight instead.
      (node.el.firstElementChild! as HTMLElement).offsetWidth,
      (node.el.firstElementChild! as HTMLElement).offsetHeight,
    ]
    node.weight = node.size[0] * node.size[1]

    if (node.od > 0) {
      let children = []

      for (let j = 0; j < node.od; j++) {
        let child = stack.pop()!
        if (child.filtered) {
          continue
        }

        children.push(child)
        node.weight += child.weight!
      }

      children.sort((a, b) => a.weight! - b.weight!)
      for (const child of children) {
        nodes.push(child)
      }

      node.filtered = children.length === 0
    }

    stack.push(node)
  }

  let root = stack.pop()!
  root.sector = [0, PIPI]
  root.position = fitToSector(root as Required<PreparedNode>, [{position: [0, 0], size: root.size!}])

  if (nodes.length === 0) {
    changeAppState("empty", {})
    return
  }

  showNode(root)

  const getRectCenter = (n: Required<PreparedNode>): [number, number] => [
    n.position![0] + n.size![0] / 2,
    n.position![1] + n.size![1] / 2,
  ]
  // Compute intersection point between a ray from the center of an axis-aligned
  // rectangle and the rectangle's edge in the ray's direction.
  function rectEdgeIntersection(
    cx: number,
    cy: number,
    w: number,
    h: number,
    dx: number,
    dy: number,
  ): [number, number] {
    const hw = w / 2
    const hh = h / 2
    // Normalize direction
    const L = Math.hypot(dx, dy) || 1
    const ux = dx / L
    const uy = dy / L
    // Candidate hit on vertical sides
    let x: number, y: number
    if (Math.abs(ux) > 1e-8) {
      const tx = (ux > 0 ? hw : -hw) / ux
      const yx = uy * tx
      if (Math.abs(yx) <= hh + 1e-8) {
        x = cx + (ux > 0 ? hw : -hw)
        y = cy + yx
        return [x, y]
      }
    }
    // Otherwise hit on horizontal sides
    const ty =
      (uy > 0 ? hh : -hh) /
      (Math.abs(uy) > 1e-8 ? uy
      : uy >= 0 ? 1
      : -1)
    const xy = ux * ty
    x = cx + xy
    y = cy + (uy > 0 ? hh : -hh)
    return [x, y]
  }
  const getTopAncestorId = (n: Required<PreparedNode>): number => {
    let cur: Required<PreparedNode> = n
    while (cur.depth > 1) {
      cur = idToNode[cur.parent] as Required<PreparedNode>
    }
    return cur.id
  }
  let deltaFromSiblings: Record<number, number> = {}

  for (let i = nodes.length - 1; i >= 0; i--) {
    let node = nodes[i] as Required<PreparedNode>
    let parent = idToNode[node.parent] as Required<PreparedNode>

    if (!deltaFromSiblings[node.parent]) {
      deltaFromSiblings[node.parent] = parent.sector[0]
    }

    let delta = deltaFromSiblings[node.parent]
    let theta = delta + (node.weight / parent.weight) * (parent.sector[1] - parent.sector[0])

    node.sector = [delta, theta]
    node.position = fitToSector(
      node as Required<PreparedNode>,
      node.trail.map((id: number) => idToNode[id] as Required<PreparedNode>),
    )

    deltaFromSiblings[node.parent] = theta

    showNode(node)

    if (background) {
      const p = parent as Required<PreparedNode>

      const [pcx, pcy] = getRectCenter(p)
      const [ccx, ccy] = getRectCenter(node)

      // Direction from parent to child
      const dx = ccx - pcx
      const dy = ccy - pcy
      const len = Math.hypot(dx, dy) || 1
      const ux = dx / len
      const uy = dy / len

      // Attach precisely to rectangle edges along the segment direction
      const start = rectEdgeIntersection(pcx, pcy, p.size![0], p.size![1], ux, uy)
      const end = rectEdgeIntersection(ccx, ccy, node.size![0], node.size![1], -ux, -uy)

      // Control point for curvature
      const mx = (start[0] + end[0]) / 2
      const my = (start[1] + end[1]) / 2
      const nx = -uy
      const ny = ux
      const segLen = Math.hypot(end[0] - start[0], end[1] - start[1])
      const ctrlX = mx + nx * segLen * 0.25
      const ctrlY = my + ny * segLen * 0.25

      const topId = getTopAncestorId(node)
      if (!topColorById[topId]) {
        topColorById[topId] = topPalette[topColorIndex++ % topPalette.length]
      }

      const alpha = Math.max(0, 1 - 0.05 * (node.depth - 1))
      const thickness = Math.max(2, 40 * Math.sqrt((node.weight as number) / (root.weight as number)))

      // Band offsets using normals to the curve at endpoints
      const t0x = ctrlX - start[0]
      const t0y = ctrlY - start[1]
      const t0l = Math.hypot(t0x, t0y) || 1
      const n0x = -t0y / t0l
      const n0y = t0x / t0l

      const t1x = end[0] - ctrlX
      const t1y = end[1] - ctrlY
      const t1l = Math.hypot(t1x, t1y) || 1
      const n1x = -t1y / t1l
      const n1y = t1x / t1l

      const half = thickness / 2

      const startLeftX = start[0] + n0x * half
      const startLeftY = start[1] + n0y * half
      const startRightX = start[0] - n0x * half
      const startRightY = start[1] - n0y * half

      const endLeftX = end[0] + n1x * half
      const endLeftY = end[1] + n1y * half
      const endRightX = end[0] - n1x * half
      const endRightY = end[1] - n1y * half

      // Approximate offset for control point using averaged normals
      const avgNx = n0x + n1x
      const avgNy = n0y + n1y
      const avgNl = Math.hypot(avgNx, avgNy) || 1
      const offX = (avgNx / avgNl) * half
      const offY = (avgNy / avgNl) * half

      const ctrlLeftX = ctrlX + offX
      const ctrlLeftY = ctrlY + offY
      const ctrlRightX = ctrlX - offX
      const ctrlRightY = ctrlY - offY

      const path = document.createElementNS("http://www.w3.org/2000/svg", "path")
      const d =
        `M ${startLeftX} ${startLeftY} ` +
        `Q ${ctrlLeftX} ${ctrlLeftY} ${endLeftX} ${endLeftY} ` +
        `L ${endRightX} ${endRightY} ` +
        `Q ${ctrlRightX} ${ctrlRightY} ${startRightX} ${startRightY} Z`
      path.setAttribute("d", d)
      path.setAttribute("fill", topColorById[topId])
      path.setAttribute("fill-opacity", alpha.toString())
      path.setAttribute("class", "connector")
      background.appendChild(path)
    }
  }

  changeAppState("success", {})
}

export function fitToSector(
  node: Pick<ProcessedNode, "position" | "size" | "sector">,
  trail: Pick<ProcessedNode, "position" | "size">[],
): [number, number] {
  // Radial placement along the sector bisector with clearance from trail rectangles.
  const [w, h] = node.size!
  const [a, b] = node.sector
  const angle = (a + b) / 2
  const ux = Math.cos(angle)
  const uy = Math.sin(angle)

  // Special case root centered at origin (full circle, trail with origin sentinel)
  if (
    trail.length === 1 &&
    Array.isArray(trail[0].position) &&
    trail[0].position![0] === 0 &&
    trail[0].position![1] === 0
  ) {
    return [-w / 2, -h / 2]
  }

  // Compute an automatic spacing based on ancestor average size and local size
  const ancAvg =
    trail.length ? trail.reduce((sum, anc) => sum + Math.min(anc.size![0], anc.size![1]), 0) / trail.length : 0
  const s = Math.max(64, 0.08 * ancAvg + 0.12 * Math.min(w, h))
  const rNode = Math.sqrt(w * w + h * h) / 2

  // Initial radius guess using circle-approx clearance from all ancestors
  let r = 0
  for (const anc of trail) {
    const ax = anc.position![0]
    const ay = anc.position![1]
    const aw = anc.size![0]
    const ah = anc.size![1]
    const acx = ax + aw / 2
    const acy = ay + ah / 2
    const RA = Math.hypot(acx, acy)
    const rA = Math.sqrt(aw * aw + ah * ah) / 2
    r = Math.max(r, RA + rA + rNode + s)
  }

  const candidateRect = (rv: number): [number, number, number, number] => {
    const cx = ux * rv
    const cy = uy * rv
    return [cx - w / 2, cy - h / 2, w, h]
  }

  const getMinDist = (rect: [number, number, number, number]) => {
    let min = Infinity
    for (const anc of trail) {
      const rectA: [number, number, number, number] = [anc.position![0], anc.position![1], anc.size![0], anc.size![1]]
      const d = shortestDistanceBetweenRectangles(rect, rectA)
      if (d < min) min = d
    }
    return min
  }

  let rect = candidateRect(r)
  let minDist = getMinDist(rect)
  if (!isFinite(minDist)) {
    minDist = s
  }

  let iterations = 0
  while (minDist < s && iterations < 1000) {
    const deficit = s - minDist
    r += Math.max(1, deficit * 1.1)
    rect = candidateRect(r)
    minDist = getMinDist(rect)
    iterations++
  }

  // Enforce angular sector boundaries so the rectangle stays within [a, b]
  const phi = Math.max(1e-3, (b - a) / 2)
  const nx = -uy
  const ny = ux
  const extentPerp = 0.5 * (Math.abs(nx) * w + Math.abs(ny) * h)
  const requiredR = extentPerp / Math.tan(phi)
  if (isFinite(requiredR)) {
    r = Math.max(r, requiredR + s)
  }

  const cx = ux * r
  const cy = uy * r
  return [cx - w / 2, cy - h / 2]
}

function showNode(node: Pick<ProcessedNode, "el" | "id" | "parent" | "position">) {
  node.el.classList.remove("off-screen")
  node.el.ariaHidden = "false"
  node.el.style.transform = `translate(${node.position![0]}px, ${node.position![1]}px)`

  if (node.id === node.parent) {
    return
  }
}

function resetGlobalMapState() {
  for (let i = 0; i < window.nodes.length; i++) {
    let node = window.nodes[i] as Node & Partial<ProcessedNode> & {el: SVGElement}

    node.el.classList.add("off-screen")
    node.el.ariaHidden = "true"
    node.el.style.transform = ""
  }
}
