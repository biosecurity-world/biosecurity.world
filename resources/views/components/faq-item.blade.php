@props ([
    "title",
])

<details class="group border-sand-300 rounded-xl border bg-white shadow-xs">
    <summary class="text-ink flex cursor-pointer list-none items-center justify-between p-4 font-semibold">
        {{ $title }}
        <div class="ml-2 flex-shrink-0">
            <!-- Down arrow - shown when closed -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="size-6 group-open:hidden"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
            </svg>
            <!-- Up arrow - shown when open -->
            <svg
                xmlns="http://www.w3.org/2000/svg"
                fill="none"
                viewBox="0 0 24 24"
                stroke-width="1.5"
                stroke="currentColor"
                class="hidden size-6 group-open:block"
            >
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
            </svg>
        </div>
    </summary>
    <div class="text-ink-muted px-4 pb-4"> {{ $slot }} </div>
</details>
