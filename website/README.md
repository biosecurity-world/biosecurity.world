This is a document intended for current and future developers.

### What needs to change when Notion changes.

* **When the database itself changes**

  Properties are matched based on their IDs not their names, these are specific to a particular database,
  duplicating the database doesn't keep the IDs. See [Schema.php](app/Services/NotionData/Schema.php) for more details.

* **When the activity types options change**

  Activity types icons are matched to an icon name based on their **exact**
  name (see [Notion.php](app/Services/NotionData/Notion.php)), then the icon name is matched to the
  actual icon (see [activity-type-icon.blade.php](resources/views/components/activity-icon.blade.php)). 

### Architectural choices

#### #0 Why Laravel?

We use a default Laravel app as a static site generator using the [Laravel Export](https://github.com/spatie/laravel-export) package (it crawls the app then creates a static bundle out of the pages).

This brings many advantages compared to any other static site generator:

* **It's not a static site generator. It's a dumb server.**

  It can do _anything_ a regular server can do, but only once. We can tap into a huge ecosystem of packages and integrations.
  Traditional SSGs don't make it easy to have complex build phases (like making HTTP requests), at least not without
  screwing up
  caching in development. We have a lot of logic that we can put in the build phase, we would have struggled.


* **It's stable.**

  The frontend ecosystem is a mess and changes too frequently for projects meant to last years without regular maintenance.

* **It has all the features that SSGs struggle to add properly because of their focus on bundling files only.**
    * Different pages
    * Components at build time without JS
    * Logic in templates at build time
    * Can cache HTTP requests in development
    * Modern asset bundling
    * It'll be very easy to make the website dynamic if it ever needs to be.
    * Easy scripting using Artisan.

Consider very carefully the advantages before switching to something else. If you are thinking of using something like
N*xt, step away from the keyboard immediately and call me.
