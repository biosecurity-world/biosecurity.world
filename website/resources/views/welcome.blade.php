<x-layouts.default title="Understand the biosecurity landscape. - Biosecurity World">
    <main class="h-full">
        <div id="app">
            <section data-state="error" aria-hidden="true" style="opacity: 0" class="flex items-center justify-center">
                <div class="text-center">
                    <h3 class="text-xl">An error has occurred.</h3>
                    <p class="reason mt-1"></p>
                    <p>
                        You can try refreshing the page or checking the <a href="{{ $databaseUrl }}"
                                                                           rel="noopener noreferrer nofollow"
                                                                           class="text-blue-500 underline">Notion
                            table</a> directly.
                    </p>
                    <a href="javascript:window.location.reload();"
                            class="reload-button border px-4 py-1 rounded-md mt-4 bg-white inline-flex items-center space-x-2">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path fill-rule="evenodd"
                                  d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                  clip-rule="evenodd"/>
                        </svg>

                        <span class="-mt-px">Reload</span>
                    </a>
                </div>
            </section>
            <div class="flex h-full">
                <aside class="h-full">
                    <!-- A sidebar for filters would go here -->
                </aside>
                <div>
                    <section data-state="loading" aria-hidden="false" style="opacity: 1" class="flex items-center justify-center">
                        <div>
                            <svg class="inline h-8 w-8 animate-spin text-gray-200 fill-emerald-600" viewBox="0 0 100 101" fill="none"
                                 xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                                <path
                                    d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                                    fill="currentColor"></path>
                                <path
                                    d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                                    fill="currentFill"></path>
                            </svg>
                            <span class="sr-only">Loading...</span>
                        </div>
                    </section>
                    <section  data-state="success" aria-hidden="true" style="opacity: 0">
                        <div id="overlay" class="bg-transparent pointer-events-none h-full w-full absolute inset-0"></div>
                        <svg id="map" width="100%" height="100%">
                            <g id="zoom-wrapper">
                                <g id="pane-wrapper">
                                    <!-- -->
                                </g>
                            </g>
                        </svg>
                    </section>
                </div>
            </div>

            <div>

            </div>
        </div>
    </main>
</x-layouts.default>


