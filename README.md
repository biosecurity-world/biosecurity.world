# biosecurity.world

### me
- [ ] [minutes] Highlight the path to the highlighted entry
- [ ] [hour] Plumbing: filter
- [ ] [hour] Plumbing: search
- [ ] [hour] Port SVG support to Logosnatch (fixes all the 'logo not found but logo exists' issues)
  that entry; same thing for entries)
- [ ] [hour] Implement the 'Report error' button on the entry page
- [ ] [hour] color the tree so that it's easy to see the different levels (alternate colors for depth=1? is easiest, see
  then0)
- [ ] [hour] Implement the alternative interactions for mobile (i.e. hover on an entry to highlight them doesn't work)
- [ ] [minutes] Optimize Logosnatch's size picking algorithm
- [ ] [minutes] Make the edges nicer
- [ ] [minutes] Mobile support (move the filter sidebar to a bottom bar that pops out, wrap the navigation bar)
- [ ] [minute] Link Give feedback to feedback form for now

#### done
- [x] [minutes] Add hover/focus states for all the interactive things
- [x] [hour] When hovering an entry, highlight all the places in the map where this entry exists.
- [x] [hour] Sort the groups in the entrygroups by the relevance of each entry (how unique is it compared to the
  others?) and then each groups by its average (?) relevance.
- [x] [hours] Match the app state to the URL (i.e. clicking on an entry changes the URL, and going to that URL reopens
- [x] [minutes] Add a footer!
- [x] [minutes] Plumbing: loading spinner when loading an entry through
- [x] [minutes] Rework the zoom handling to make it easier to click on the entries
- [x] [minutes] Add the 'last updated at' (where? bottom-right? hero?)
- [x] [minutes] Add explicit zoom controls (+ / -)
- [x] [minute] embed htmx
- [x] [minutes???] Fix janky resize behavior

###### unplanned
- [x] added phpstan, pint, and fixed a good amount of issues, cleaned the codebase

#### postponed (needs research)
- [ ] [hour] Add the "changelog" to the navigation bar that lists the new entries since the last visit

### next sprint (ideas)
- [ ] Centralize the manipulation of hard-coded category names (like activty types), never do it in blade templates
- [ ] Fix the remaining PHPStan issues
- [ ] take a look at the TSC issues
- [ ] add CI pipeline for running tests and linters
- [ ] Sprite for favicons?
- [ ] Ditch cdn.forevue.org (more resilient)
- [ ] [hour] Privacy policy (not needed for now)
- [ ] Expand the map+filter to full screen once scrolling 
- [ ] Pressing escape should close the entry
- [ ] Easy way to reset the filters (button?) 
- [ ] Error handling if htmx request to /e/_/_ fails (although this should never happen, right?) 
### Fun ideas
- RSS feed
