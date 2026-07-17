---
name: inclusion-evaluator
description: Evaluate whether a candidate organization should be included in the biosecurity.world database. Use when the user asks to assess, screen, vet, or decide on a new org — phrasings like "should we include X?", "est-ce que X coche les critères ?", "evaluate X for inclusion", or pastes a candidate org name/URL. Produces a calibrated Include/Exclude verdict scored against the four project criteria (Focus, Output, Active, Top) and anchored to the 143 orgs already in the database.
---

# Inclusion evaluator for biosecurity.world

You are screening a candidate organization for inclusion in the biosecurity.world database. Apply the project's four criteria, calibrate against the existing 143 entries, and return a structured verdict.

## The four criteria

Score each as **Yes / No / Unsure**:

1. **Focus — scale-sensitive, GCBR-aligned.** Does preventing **large-scale / catastrophic** pandemics (in the effective-altruism / GCBR sense — civilization-scale, worst-case, deliberate or engineered pandemics) constitute a primary mission of all or a substantial part of the organisation? This is **not** "do they work on pandemics or biosecurity" — generic biopreparedness, public-health response, infectious-disease research, or all-hazards readiness all score **No** on Focus, even if excellent. The bar is whether worst-case / civilization-scale biological risk explicitly drives their agenda.

   Calibration from the existing DB (this maps 1-to-1 with the `focuses_on_gcbrs` flag):

   - **Focus = Yes** examples: SecureBio, NTI.bio, Nolan Center @ CSR, IBBIS, SecureDNA, Blueprint Biosecurity, Sentinel, Sentinel Bio, Cambridge Biosecurity Hub, CSER, GCRI, CLTR, CSET, Centre for Future Generations, Esvelt Lab, ALLFED, ORCG, ALTER, JHU Center for Health Security, Asia Centre for Health Security, Convergent Research, IPPS, Coefficient Giving Bio team, Longview, EA LTFF, Survival & Flourishing Fund, IGSC, Mirror Biology Dialogues Fund, Pivotal Research, Oxford Biosecurity Group, BlueDot Impact, ELBI Fellowship, Rosetta Commons Fellowship, X-Lab @ Chicago, Stanford Existential Risk Initiative, Texas A&M Scowcroft Institute, RAND (bio program), Deloitte/Gryphon, Aclid, Ginkgo Biosecurity, Cavendish Labs, Australia Group, UNIDIR.
   - **Focus = No** examples (still often included via Top): CDC, ECDC, CEPI, Gavi, BioNTech, Broad Institute, EMBL, Francis Crick Institute, CIDRAP, Bipartisan Commission on Biodefense, Battelle, Gates Foundation, Bulletin of the Atomic Scientists, Center for Global Health Science and Security @ Georgetown, IARPA, HERA, EBSA, Global Health Security Network, IPPPR, Asimov Press, Argonne / LANL / LLNL / Sandia / MIT Lincoln, RTX BBN, SIPRI, Biosafety Now, CBWNet.
   - **Heuristic**: if the org's own framing centers "high-consequence infectious diseases", "all-hazards", "global health security", "public health preparedness", or "biodefense" without explicit GCBR/x-risk/catastrophic-pandemic framing → Focus = **No**. If they cite GCBRs, engineered pandemics, civilizational resilience, deliberate misuse, x-risk, or longtermism → Focus = **Yes**.
   - **EA-adjacent qualifier**: organisations operating within or directly adjacent to the effective-altruism / longtermist / x-risk ecosystem qualify as Focus = Yes even when their own copy doesn't use GCBR vocabulary, provided they treat biosecurity as one of their priority cause areas. The EA framing is itself scale-sensitive, so cause-prioritisation by EA actors operationalises GCBR concern. Examples: 80,000 Hours, EA Long Term Future Fund, Longview Philanthropy, Survival & Flourishing Fund, Open Philanthropy, Institute for Progress (when the bio program is EA-adjacent), High Impact Engineers, High Impact Medicine, The Unjournal, Ergo Impact, Coefficient Giving, Convergent Research. Note this in the justification ("EA-adjacent qualifier").

2. **Output** — Have they actually produced relevant research, policy work, a product/device, or a published funding decision? Mission statements alone do not count. Look for publications, products, grant announcements, completed programs.

3. **Active** — Currently operating and likely to produce more outputs. Defunct projects, sunset programs, dormant initiatives, or orgs with no activity in the last ~2 years fail this.

4. **Top** — Among the top organisations worldwide (or top in their region/niche) in a key intervention focus: vaccines, therapeutics, pathogen surveillance, rapid diagnostics, DNA synthesis screening, BWC/governance, dual-use research oversight, indoor air, GCBR research, AI×Bio, lab biosafety, biosecurity policy, etc. **Independent evaluation required** — do not score Top = Yes solely on the basis of the org's self-description ("premier", "leading", "world-class" claims by the org itself are not evidence). Look for:
   - External recognition: cited by other top orgs (NTI, JHU CHS, RAND, Nuffield, WHO), funded by major bio funders (Open Phil, Gates, Wellcome, ARPA-H), staffed by widely-cited researchers, or mentioned as a top institution in independent landscape reviews.
   - Concrete differentiators: a unique facility (e.g. only BSL-4 in country), a flagship product widely adopted, policy outputs that shaped legislation, repeated coverage in major bio-policy press.
   - Comparable scale/influence to existing DB anchors in the same intervention focus.
   If the only evidence is the org's own marketing, score **Unsure** and say so explicitly.

## Decision rule (calibrated from existing entries)

**Required**: `Output = Yes` **AND** `Active = Yes`. Without both, recommend **Exclude**.

**Sufficient (given the required pair)**: at least one of `Focus = Yes` **OR** `Top = Yes`. `Unsure` on both is a borderline → **Include with caveats** if the org clearly does biosecurity-adjacent work; otherwise lean Exclude.

Examples from the existing dataset that anchor this rule:
- **National labs** (Sandia, LANL, LLNL, Argonne, MIT Lincoln) — `Focus=No` but included because `Top=Yes` for technical biodefense work.
- **SIPRI, ECDC** — `Focus=No`, included because they are top governance/surveillance bodies.
- **Sentinel Bio** — `Top=No` but included because `Focus=Yes` (mission-aligned).
- **Sabeti Lab, Observatorio de Riesgos Catastróficos Globales** — `Focus=Unsure`, included because clearly top in their niche.

The bar is **moderately inclusive** (143 orgs, broad taxonomy spanning Prevention / Detection / Response / Transversal) — do not be overly strict. But require demonstrated output and current activity.

## Process

1. **Gather information.** If the user provided a URL, use WebFetch on it (and 1-2 deeper pages like /about, /research, /publications if needed). If only a name, search via WebFetch on a likely URL or ask the user for the URL. Never invent facts about the org.

2. **Check for duplicates.** Before evaluating, grep the existing dataset to make sure the org isn't already in:
   ```
   grep -i "<org name>" public/data/entries.csv
   ```
   Also try variants and acronyms. If found, report that it's already included and stop.

3. **Score the four criteria** with one-sentence justifications each, citing what you found.

4. **Apply the decision rule** above and produce a verdict: **Include**, **Include with caveats**, or **Exclude**.

5. **Pre-fill the Notion submission form** if Including. The output must mirror the exact structure of the public form at `https://biosecurityworld.notion.site/33a4061a75b7806fad1dee0fcd2e921a` so the curator can copy values straight in. The form has 9 fields, in this order:

   1. **Name** — text. *"Add the associated acronym in parenthesis, when relevant. E.g. 'Nuclear Threat Initiative (NTI)'."*
   2. **Link** — URL. The org's primary website.
   3. **Description** — text. *"Brief summary (2-3 sentences) of what this organization does in the biosecurity space."* Write a neutral, factual 2-3 sentence description in the same register as existing DB entries (action-oriented, names concrete programs/products, no marketing language).
   4. **Organization Type** — radio (pick exactly one). Closed enum: `Research institute / lab / network`, `For-profit company`, `Think tank`, `Government`, `Intergovernmental entity`, `National non-profit organization`, `International non-profit organization`, `Media`. (Ignore the bracketed `[Category]/[Sub-category]/[Sub-sub-category]` radio options — those are administrative and not for new submissions.)
   5. **Activity Type** — checkboxes (multi). *"What does this organization **primarily** do? Select all that apply."* Closed enum: `Research`, `Technology development`, `Policy development`, `Outreach & Advocacy`, `Education & Career`, `Funding`, `Coordination`. **Be strict — only check an activity if it is a defining/primary function of the org, not a peripheral capability.** Calibration:
      - `Education & Career` → only for orgs whose core mission is training/courses/fellowships/career pipelines (e.g. BlueDot Impact, 80,000 Hours, ELBI Fellowship, Rosetta Commons, Pivotal Research, OBG). A research center that *also* runs trainings does **not** check this.
      - `Coordination` → only for orgs whose core function is bringing actors together (e.g. Australia Group, IGSC, EBSA, HERA, Gavi, Mirror Biology Dialogues Fund). Having partnerships ≠ coordinating.
      - `Policy development` → only if they actively draft, advocate for, or shape policy/regulation as a primary output. Sitting on an advisory committee occasionally does not qualify.
      - `Outreach & Advocacy` → only if public communication / advocacy is a defining activity (media campaigns, lobbying, public letters), not just having a website.
      - `Funding` → only for actual funders/grantmakers, not grant recipients.
      - `Technology development` → building products, devices, software, or infrastructure that ships, not just doing R&D.
      - `Research` → producing original research outputs (papers, reports, datasets).
      
      A typical org checks 1–3 activities. If you find yourself checking 4+, reconsider whether you're capturing primary functions or just listing everything they do. List all considered-but-not-selected activities with a one-line reason, so the curator can override.
   6. **Intervention Focus** — checkboxes (multi, **no cap**). *"First select TECHNICAL and/or GOVERNANCE. Then add relevant topics."* Always start by selecting `[TECHNICAL]` and/or `[GOVERNANCE]`. These two are **additive** (an org can carry both) and each has a **substantive inclusion threshold** — do not slap both on by default:
      - **`[TECHNICAL]`** — select iff the org **does, builds, or funds** technical work: activity `Technology development`; org type `For-profit company`; or `Funding` **with ≥1 technical topic**. Do **not** select it merely because the org *covers, teaches, or broadcasts* technical topics (a think tank writing about vaccines is not doing vaccine work).
      - **`[GOVERNANCE]`** — select iff the org **does** governance work: activity `Policy development` or `Coordination`; a strong governance topic (`Dual-use research of concern` or `Infohazard policies`); or `Outreach & Advocacy` **when the org type is** Think tank / Government / Intergovernmental entity. Do **not** select it on `Education & Career` alone, nor on the `Biological weapons` topic alone (ambiguous — technical for a detection lab, governance for a BWC/disarmament body).
      - For ambiguous activities (Education, Outreach, Research), follow the org's **actual substance/orientation**, not the activity label — e.g. an engineering-careers org whose only activity is `Education & Career` is `[TECHNICAL]` (it trains for technical work), not `[GOVERNANCE]`.

      Then pick from the topics enum: `Dual-use research of concern`, `Infohazard policies`, `AI x Bio`, `Synthetic biology`, `Biological weapons`, `DNA synthesis`, `Lab biosafety`, `Cyberbiosecurity`, `Pathogen surveillance`, `Digital detection`, `Rapid diagnostics`, `Indoor air quality`, `Personal protective equipment`, `Vaccines`, `Therapeutics`, `Antimicrobial resistance`, `Epidemiology`, `Supply chain disruption`, `Crisis management`. **Reflect the actual scope**: if the org's projects/priorities span many topics, list all of them — don't artificially cap. If the org has no specific topical focus (broad cause-prioritisation, generalist newsletter, transversal funder without a topical angle), leave empty (`(none)`).
   7. **Location (City, Country, Region)** — checkboxes (multi). *"Select most relevant regions, then countries, then cities. Select 'Global' if it operates worldwide."* The form has a predefined list of regions (e.g. `Global`, `Remote`, `Europe (excl. UK)`, `North America (excl. US)`, `South America`, `North Africa`, `Sub-Saharan Africa`, `Middle East`, `East Asia & Pacific`, `Central Asia`, `South Asia`), countries (`USA`, `United Kingdom`, `Canada`, `Australia`, `Germany`, `France`, etc.) and cities (`Boston MA`, `Cambridge MA`, `Washington DC`, `London`, `Geneva`, `Berlin`, `San Francisco CA`, `Stanford CA`, `Pittsburgh PA`, `Atlanta GA`, etc.). **It is OK and expected to suggest a new city/country if the org's location is not in the existing enum** — flag it explicitly as "(new — not in current list)" so the curator knows to add it. Always include the relevant region + country, plus the city if known.
   8. **GCBR focus** — single checkbox. Description: *"Aims to prevent Global Catastrophic Biological Risks (large scale pandemics, bioweapons, etc.) as a primary focus."* This is the **same criterion as Focus** in the rubric above — check it iff Focus = Yes (and explicitly flag if Unsure).
   9. **Notes** — text. *"Why should this organization be included? Mention how it meets the inclusion criteria (Output, Active, Top)."* Draft a short paragraph (~3-5 sentences) summarising how the org meets the four criteria, citing concrete evidence (programs, publications, facilities, funding). This is the field the curator uses to decide approval — make it persuasive but factual.

   For each field provide: the suggested value, 1-2 cited sources or short verbatim quotes from the org's website, and an explicit confidence (high/med/low) with the source of uncertainty.

   Optionally also note a suggested **`category_trail`** (one of `Prevention`, `Prevention > Preventing production & release`, `Prevention > Controlling knowledge`, `Detection`, `Detection > Early warning`, `Detection > Diagnostics`, `Response`, `Response > Medical countermeasures`, `Response > Non-medical countermeasures`, `Transversal > Information`, `Transversal > Education`, `Transversal > Financing`, `Transversal > Coordination`) as a **curator hint** — this is not in the public submission form (curators assign it later) but helps them place the org.

## Output format

Render exactly this Markdown table, then the verdict, then — if Including — the form pre-fill block. Match the user's language (French ↔ English) for prose; keep enum values in English (they are literal form values).

```
| Criterion | Score | Justification |
|---|---|---|
| Focus  | Yes/No/Unsure | … |
| Output | Yes/No/Unsure | … |
| Active | Yes/No/Unsure | … |
| Top    | Yes/No/Unsure | … |

**Verdict: Include / Include with caveats / Exclude** — <one-sentence rationale tying back to the decision rule>.

### Notion form pre-fill (if Including)

Mirror the form field-by-field. Use the exact field names from the form.

**1. Name**
> `<Org name (ACRONYM)>`
- Source: <URL>
- Confidence: high/med/low — <why>

**2. Link**
> `<URL>`
- Confidence: high

**3. Description** *(2-3 sentences, neutral register)*
> <draft description>
- Source: <quote or URL excerpt supporting each factual claim>
- Confidence: high/med/low — <why>

**4. Organization Type** *(pick one)*
> `<value from enum>`
- Source: <evidence>
- Confidence: …

**5. Activity Type** *(select all that apply — only primary/defining activities)*
- `<value>` — <one-line evidence>
- `<value>` — …
- *Considered but not selected:* `<value>` (<one-line reason>); …
- Confidence: …

**6. Intervention Focus** *(select all that apply, starting with TECHNICAL and/or GOVERNANCE)*
- `[TECHNICAL]` and/or `[GOVERNANCE]` — <reason>
- `<topic>` — <evidence>
- `<topic>` — …
- *Considered but not selected:* `<topic>` (<reason>); …
- Confidence: …

**7. Location (City, Country, Region)** *(select all that apply)*
- `<region>` — <reason>
- `<country>` — <reason>
- `<city>` — <reason> *(mark "(new — not in current list)" if applicable)*
- Confidence: …

**8. GCBR focus** *(single checkbox)*
- **Check / Don't check** — mirrors the Focus score above. <one-line justification>

**9. Notes** *(why this org should be included — drafted for the curator)*
> <draft paragraph citing the four criteria and concrete evidence>

---

**Curator hint — suggested category placement** (not in the public form): `<category_trail value>` — <reason>.

**Anchor orgs already in DB** (2–3 closest matches for the curator to compare against): <names with one-line distinction>.

**Open uncertainties for the curator** (bulleted list of items that couldn't be resolved from public sources, with the field they would affect): <…>.
```

Cite sources concretely: prefer direct URL fragments (`/about`, `/research`, `/team`) and short verbatim quotes over paraphrase. If a field is a judgement call rather than a direct quote, say so in the Source column.

## Calibration reminders

- Don't conflate "does public health work" with "Focus" — Focus is the narrow GCBR/scale-sensitive criterion. Most public-health and biopreparedness orgs score Focus = No and enter (if at all) via Top.
- Don't conflate "high-consequence pathogen response" with GCBR Focus. Biocontainment units, BSL-4 labs, federal quarantine units, and Ebola/special-pathogen response programs are valuable but score Focus = No unless they explicitly target catastrophic / engineered / civilization-scale threats. They can still earn Top = Yes for biocontainment.
- Dual-use orgs (national labs, big pharma like BioNTech, generic research universities) get in only when they have a clearly identifiable biosecurity-relevant program → `Top=Yes` for that program.
- For Top: never accept self-claims as sufficient. Require independent corroboration (citations, funder reputation, comparable scale to anchor orgs).
- Conferences and one-off reports are **not** orgs → Exclude.
- Personal blogs / single-author newsletters and podcasts are **accepted** if they have substantial reach in the biosecurity community (e.g. widely cited, large subscriber base, regularly referenced by other DB orgs). Examples already in DB: `Newsletter - Force of Inflection` (Caitlin Rivers), `Newsletter - Something in the air` (Jose-Luis Jimenez), `Newsletter - The Century of Biology`, `Podcast - Hear This Idea`. Score them as `Media` org type. Default to Exclude if no evidence of reach.
- Government agencies count if they have a standing biosecurity mandate (ARPA-H, ARIA, ECDC). Generic ministries of health do not.
- Be willing to return `Unsure` rather than overclaim. The dataset uses `Unsure` liberally.
