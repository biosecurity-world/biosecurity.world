<footer aria-labelledby="footer-heading" class="mx-auto max-w-7xl px-6 xl:px-0">
    <div class="border-sand-400 mt-12 border-t py-8">
        <h2 id="footer-heading" class="sr-only">Footer</h2>

        <div class="justify-between lg:flex">
            <p>
                <a href="{{ route("welcome", absolute: false) }}" class="font-display">biosecurity.world</a>

                <span class="text-ink-muted">&mdash; Understand the biosecurity landscape.</span>
            </p>

            <ul class="mt-2 flex space-x-4 overflow-x-scroll lg:mt-0">
                <li>
                    <a
                        href="https://foreview.org"
                        class="text-ink-muted inline-flex text-sm whitespace-nowrap underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <span class="mr-0.5">Built by Foreview</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="group-hover:text-primary-700 mt-px size-4 text-gray-400"
                            aria-label="External link icon"
                        >
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                            />
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                            />
                        </svg>
                    </a>
                </li>
                <li>
                    <a
                        href="https://github.com/biosecurity-world/biosecurity.world"
                        class="text-ink-muted inline-flex text-sm whitespace-nowrap underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <span class="mr-0.5">GitHub</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="group-hover:text-primary-700 mt-px size-4 text-gray-400"
                            aria-label="External link icon"
                        >
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                            />
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                            />
                        </svg>
                    </a>
                </li>
                <li>
                    <a
                        href="/data/entries.csv"
                        class="text-ink-muted inline-flex text-sm whitespace-nowrap underline"
                        download
                    >
                        <span class="mr-0.5">Download data (CSV)</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="group-hover:text-primary-700 mt-px size-4 text-gray-400"
                            aria-label="Download icon"
                        >
                            <path
                                d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z"
                            />
                            <path
                                d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z"
                            />
                        </svg>
                    </a>
                </li>
                <li>
                    <a
                        href="https://notion.so/{{ config("services.notion.database") }}"
                        class="text-ink-muted inline-flex text-sm whitespace-nowrap underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <span class="mr-0.5">Notion</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="group-hover:text-primary-700 mt-px size-4 text-gray-400"
                            aria-label="External link icon"
                        >
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                            />
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                            />
                        </svg>
                    </a>
                </li>
                <li>
                    <a
                        href="https://logo.dev"
                        class="text-ink-muted inline-flex text-sm whitespace-nowrap underline"
                        target="_blank"
                        rel="noopener noreferrer"
                    >
                        <span class="mr-0.5">Logos by Logo.dev</span>
                        <svg
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 16 16"
                            fill="currentColor"
                            class="group-hover:text-primary-700 mt-px size-4 text-gray-400"
                            aria-label="External link icon"
                        >
                            <path
                                d="M6.22 8.72a.75.75 0 0 0 1.06 1.06l5.22-5.22v1.69a.75.75 0 0 0 1.5 0v-3.5a.75.75 0 0 0-.75-.75h-3.5a.75.75 0 0 0 0 1.5h1.69L6.22 8.72Z"
                            />
                            <path
                                d="M3.5 6.75c0-.69.56-1.25 1.25-1.25H7A.75.75 0 0 0 7 4H4.75A2.75 2.75 0 0 0 2 6.75v4.5A2.75 2.75 0 0 0 4.75 14h4.5A2.75 2.75 0 0 0 12 11.25V9a.75.75 0 0 0-1.5 0v2.25c0 .69-.56 1.25-1.25 1.25h-4.5c-.69 0-1.25-.56-1.25-1.25v-4.5Z"
                            />
                        </svg>
                    </a>
                </li>
            </ul>
        </div>

        <p class="text-ink-muted mt-4 flex max-w-lg flex-wrap">
            <a
                href="https://www.linkedin.com/in/alix-pham/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Alix Pham
            </a>
            <sup class="text-xs">1</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/sofyalebedeva/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Sofya Lebedeva
            </a>
            <sup class="text-xs">1</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/johantang/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Johan Täng
            </a>
            <sup class="text-xs">1</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/jeremy-andreoletti-330445216/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Jérémy Andréoletti
            </a>
            <sup class="text-xs">1,3</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/linbowkerlonnecker/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Lin Bowker-Lonnecker
            </a>
            <sup class="text-xs">2</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/will-saunter/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Will Saunter
            </a>
            <sup class="text-xs">2</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/dornfelix/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Félix Dorn
            </a>
            <sup class="text-xs">2</sup>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/till-funke-05648290/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Till Funke
            </a>
            <sup class="text-xs">2</sup>
        </p>
        <p class="text-ink-muted mt-1 text-sm opacity-80">
            <sup>1</sup>
            Team,
            <sup>2</sup>
            Support,
            <sup>3</sup>
            Maintainer
        </p>
    </div>
</footer>
