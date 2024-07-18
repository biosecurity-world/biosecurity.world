This is a document intented for current and future developers.

### Architectural choices

#### #0 Why Laravel?
We use a default Laravel app as a static site generator.

This brings many advantages compared to any other static site generator:

* It's not a static site generator. It's a dumb server.
  It can do _anything_ a regular server can do, and we can tap into a huge ecosystem of packages and integrations.
  Traditional SSGs don't make it easy to have complex build phases (like making HTTP requests), at least not without
  screwing up
  caching in development. We have a lot of logic that we can put in the build phase, so it would have been annoying.
* It's stable
  The frontend ecosystem is a mess and changes too frequently to be relied upon for projects meant to last years.
* We get all the features we could possible need without the added annoyances SSGs bring with them:
    * Different pages
    * Components at build time without JS
    * Logic in templates at build time
    * Can cache HTTP requests in development
    * Modern asset bundling
    * It'll be very easy to make the website dynamic if it ever needs to be.
    * Easy scripting using Artisan.

Consider very carefully the advantages before switching to something else. If you are thinking of using something like
N*xt, step away from the keyboard immediately and call me.
