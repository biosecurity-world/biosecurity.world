<footer aria-labelledby="footer-heading" class="px-6 xl:px-0 max-w-7xl mx-auto">
    <div class="mt-12 border-t border-gray-900/10 py-4">
        <h2 id="footer-heading" class="sr-only">Footer</h2>
        <div class="lg:flex justify-between">
            <p>
                <a href="{{ route('welcome', absolute: false) }}" class="font-display">biosecurity.world</a>

                <span class="text-gray-700">
                        &mdash; Understand the biosecurity landscape.
                    </span>
            </p>

            <ul class="flex space-x-4 overflow-x-scroll mt-2 lg:mt-0">
                <li>
                    <a href="{{ route('privacy-policy', absolute: false) }}" class="inline-flex text-sm underline text-gray-700 whitespace-nowrap">
                        Privacy Policy
                    </a>
                </li>
                <li>
                    <a href="{{ route('terms-of-service', absolute: false) }}" class="inline-flex text-sm underline text-gray-700 whitespace-nowrap">
                        Terms of Service
                    </a>
                </li>
                <li>
                    <a href="https://github.com/biosecurity-world/biosecurity.world"
                       class="inline-flex text-sm underline text-gray-700 whitespace-nowrap">
                        <span class="mr-0.5">GitHub</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-4 text-gray-400 group-hover:text-primary-700 mt-px"
                             aria-label="External link icon">
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                        </svg>
                    </a>
                </li>
                <li>
                    <a href="https://notion.so/{{ config('services.notion.database') }}"
                       class="inline-flex text-sm underline text-gray-700 whitespace-nowrap">
                        <span class="mr-0.5">Notion</span>
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                             class="size-4 text-gray-400 group-hover:text-primary-700 mt-px"
                             aria-label="External link icon">
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"/>
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"/>
                        </svg>
                    </a>
                </li>
            </ul>
        </div>
    </div>
</footer>
