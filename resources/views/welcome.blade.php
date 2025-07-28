@php
    use App\Services\NotionData\Enums\FocusCategory;
    /** @var \App\Services\NotionData\Tree\Tree $tree */
@endphp

<x-layouts.default class="w-full bg-white antialiased" title="Understand the biosecurity landscape.">
    <x-slot:head>
        <script>
            {{-- format-ignore-start --}}
            window.nodes = @json($nodes);
            window.filterData = @json($filterData);
            {{-- format-ignore-end --}}
        </script>

        @vite("resources/js/map.ts")
    </x-slot>
    <header class="w-full bg-gradient-to-tl from-primary-600 to-primary-950 pb-36 pt-4 lg:pt-8">
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" class="absolute -mx-8" viewBox="0 0 8505 4061">
            <defs>
                <filter id="a" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse">
                    <feGaussianBlur stdDeviation="5.6" />
                </filter>
                <filter id="b" color-interpolation-filters="sRGB" filterUnits="userSpaceOnUse">
                    <feGaussianBlur stdDeviation="15" />
                </filter>
            </defs>
            <g stroke="#fff" stroke-opacity=".9" stroke-width="6" filter="url(#a)">
                <path
                    d="M44 155c510 44 1693 296 2341 956 792 832 2046 1110 3144 914 880-167 1736 516 2489 762 106 82 363 251 453 511"
                />
                <path
                    d="M43 271c460 82 1545 386 2212 952 796 722 1992 1076 3007 953 816-117 1543 540 2654 656 115 67 401 222 555 531"
                />
                <path
                    d="M42 382c412 117 1405 470 2089 947 801 618 1941 1044 2877 991 755-70 1359 562 2811 556 123 51 438 193 651 548"
                />
                <path
                    d="M41 487c366 151 1272 551 1972 943 805 519 1893 1014 2754 1027 697-26 1185 583 2960 460 130 37 473 167 743 565"
                />
                <path
                    d="M40 586c323 183 1146 627 1861 939 810 426 1848 986 2638 1061 642 16 1020 604 3101 370 137 24 507 141 830 581"
                />
                <path
                    d="M40 679c281 213 1027 699 1756 936 813 337 1804 959 2529 1093 589 56 863 623 3233 284 144 11 538 118 911 597"
                />
                <path
                    d="M39 767c243 242 915 767 1659 933 816 254 1763 933 2425 1123 541 93 717 640 3358 204 150-1 567 95 988 611"
                />
                <path
                    d="M38 849c207 268 811 830 1567 930 820 176 1726 909 2329 1151 495 128 581 657 3475 129 156-12 594 75 1060 625"
                />
                g filter=&quot;url(#i)&quot;&gt;
                <path
                    d="M37 925c174 294 714 889 1483 928 822 104 1690 886 2238 1176 453 161 454 673 3584 60 162-22 620 55 1126 637"
                />
                <path
                    d="M36 1061c115 337 542 994 1331 922 828-24 1627 848 2079 1224 377 218 227 700 3777-65 171-41 665 21 1245 660"
                />
                <path
                    d="M36 1121c88 356 465 1039 1264 920 831-81 1600 830 2009 1243 344 244 129 713 3861-118 176-49 686 5 1298 669"
                />
                <path
                    d="M35 1175c65 373 398 1080 1205 917 832-131 1575 815 1946 1262 314 267 38 724 3937-167 180-57 703-8 1344 678"
                />
                <path
                    d="M35 1223c44 389 336 1117 1150 916 835-177 1554 800 1890 1278 287 287-41 733 4006-211 183-63 719-21 1386 685"
                />
                <path
                    d="M34 1302c10 415 236 1179 1062 913 838-251 1517 778 1797 1305 243 321-174 750 4118-283 189-74 746-41 1456 698"
                />
                <path
                    d="M34 1333c-4 425 196 1203 1027 912 840-281 1503 769 1760 1316 226 334-225 756 4163-312 191-78 756-49 1483 704"
                />
                <path
                    d="M34 1359c-15 433 164 1222 1000 911 840-305 1490 761 1729 1324 212 345-267 761 4200-335 193-81 764-55 1505 708"
                />
                <path
                    d="M35 1378c-23 440 139 1238 978 911 841-324 1481 755 1706 1331 201 353-300 765 4228-353 194-84 771-60 1522 711"
                />
                <path
                    d="M37 1393c-30 444 120 1248 961 909 842-337 1475 752 1690 1336 193 359-324 768 4247-366 196-86 776-63 1535 714"
                />
            </g>
            <g stroke="#fff" stroke-width="6" filter="url(#b)">
                <path
                    d="M46 34c563 4 1846 202 2476 960 787 947 2102 1145 3287 872 948-218 1939 491 2316 873 97 99 322 282 347 492M38 1401c-34 447 110 1255 952 910 842-346 1470 748 1680 1339 188 362-339 769 4259-374 196-87 779-66 1542 715"
                />
                <path
                    d="M45 38c562 6 1842 206 2472 960 787 943 2100 1144 3282 874 946-217 1932 491 2322 869 98 98 323 281 350 492"
                />
                t&gt;
                <path
                    d="M45 45c559 8 1833 211 2464 959 788 937 2097 1143 3274 877 942-214 1920 493 2332 862 98 98 326 280 356 494"
                />
                <path
                    d="M45 54c554 11 1821 219 2454 960 787 927 2092 1139 3262 879 936-209 1904 495 2345 854 99 96 329 277 365 495"
                />
                <path
                    d="M45 67c549 15 1805 227 2440 958 788 916 2087 1136 3248 884 929-204 1883 498 2362 843 100 94 334 274 376 497"
                />
                <path
                    d="M45 82c542 19 1786 239 2423 958 789 901 2080 1131 3231 889 921-198 1858 500 2383 829 101 92 339 270 389 499"
                />
                <path
                    d="M45 99c534 26 1763 253 2403 958 790 885 2072 1126 3210 895 912-191 1830 504 2409 813 102 90 344 265 404 502"
                />
                <path
                    d="M45 119c525 32 1738 269 2381 957 790 866 2062 1121 3186 902 900-182 1795 508 2437 795 104 87 351 260 422 505"
                />
                <path
                    d="M45 142c515 40 1708 286 2355 956 791 844 2052 1114 3159 910 888-172 1758 513 2470 774 106 84 359 254 442 509"
                />
                <path
                    d="M44 168c505 48 1677 305 2327 955 793 820 2040 1106 3129 918 874-161 1716 518 2507 751 107 80 367 248 464 513"
                />
                <path
                    d="M44 196c492 57 1641 327 2295 954 794 793 2028 1098 3097 928 857-150 1668 524 2546 725 109 77 376 241 489 518"
                />
                <path
                    d="M44 227c479 67 1601 351 2261 953 795 764 2013 1089 3060 938 840-136 1617 530 2590 697 111 73 387 233 516 523"
                />
                <path
                    d="M43 260c465 78 1559 377 2224 952 796 733 1998 1080 3021 950 822-122 1561 537 2638 666 114 68 397 225 545 529"
                />
                <path
                    d="M43 296c449 90 1513 405 2184 951 797 698 1980 1069 2978 962 802-107 1501 544 2689 633 116 63 410 216 577 535"
                />
                <path
                    d="M43 335c431 102 1463 435 2140 949 799 662 1963 1058 2932 975 781-90 1437 553 2745 598 119 58 423 206 610 541"
                />
                <path
                    d="M42 377c414 115 1411 466 2094 947 801 623 1944 1046 2884 990 757-73 1367 560 2803 560 123 52 437 195 647 547"
                />
                <path
                    d="M42 421c394 129 1355 500 2045 945 802 582 1923 1034 2832 1005 733-54 1294 570 2866 520 125 46 451 184 685 555"
                />
                <path
                    d="M42 468c373 144 1295 536 1992 944 805 536 1902 1019 2777 1020 707-34 1216 580 2933 477 129 40 467 172 726 563"
                />
                <path
                    d="M41 517c352 161 1233 574 1938 942 806 490 1879 1005 2719 1038 679-14 1133 589 3002 432 133 33 484 159 770 570"
                />
                <path
                    d="M41 569c329 178 1166 615 1879 940 809 441 1855 990 2658 1055 650 10 1047 601 3077 385 136 26 501 146 815 579"
                />
                <path
                    d="M40 624c306 195 1097 656 1819 938 810 389 1829 974 2593 1074 620 32 956 611 3155 335 140 18 519 132 862 588"
                />
                <path
                    d="M40 681c280 214 1024 701 1754 936 813 335 1803 958 2526 1094 588 56 860 623 3236 282 144 11 539 118 913 597"
                />
                <path
                    d="M39 741c254 234 948 747 1688 934 815 278 1775 940 2455 1114 555 82 760 635 3322 228 148 2 558 102 965 607"
                />
                <path
                    d="M39 804c227 254 868 795 1617 931 818 219 1747 922 2382 1136 520 109 656 648 3411 170 153-6 579 86 1020 618"
                />
                <path
                    d="M38 869c198 275 785 846 1545 929 820 158 1716 903 2305 1158 483 137 547 661 3503 111 158-15 601 69 1078 628"
                />
                <path
                    d="M37 937c169 297 699 898 1469 927 824 93 1685 883 2225 1181 446 165 434 675 3601 49 162-24 623 52 1136 639"
                />
                <path
                    d="M37 1008c138 320 609 952 1390 924 826 26 1652 863 2142 1205 407 195 316 689 3701-16 167-33 647 35 1198 651"
                />
                <path
                    d="M36 1081c106 344 516 1009 1309 922 829-44 1618 841 2055 1229 366 227 195 705 3805-82 173-43 672 16 1263 663"
                />
                <path
                    d="M35 1157c73 368 420 1067 1224 919 832-115 1584 819 1967 1255 324 259 68 720 3913-151 178-54 697-4 1328 675"
                />
                <path
                    d="M35 1236c38 393 319 1127 1136 915 835-189 1547 797 1874 1283 280 292-63 736 4025-223 184-65 723-24 1397 688"
                />
                <path
                    d="M34 1317c3 420 216 1190 1045 913 839-266 1510 773 1779 1310 235 327-198 753 4140-297 190-76 751-45 1469 701"
                />
            </g>
        </svg>
        <x-navbar class="md:bg-white/20 md:shadow-inner md:shadow-white/30" invert />

        <h1
            class="mx-auto mt-6 max-w-3xl px-6 font-display text-3xl font-bold tracking-tight text-white sm:mt-16 md:text-center lg:mt-24 lg:text-center lg:text-6xl"
        >
            Understand the biosecurity landscape.
        </h1>

        <ul
            class="mx-auto mt-6 max-w-7xl space-y-6 px-6 md:mt-16 md:grid md:grid-cols-3 md:gap-x-8 md:space-y-0 lg:mt-20 xl:gap-x-12 xl:px-0"
        >
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Up-to-date</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    Humans update regularly the map and our team monitors privileged channels for announcements about
                    new organizations.
                </p>
            </li>
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Crowd-sourced</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    We encourage submissions and corrections, which are individually reviewed by researchers from
                    <a href="https://www.ens.psl.eu" class="text-white underline hover:text-white/70">ENS</a>
                    and
                    <a href="https://ox.ac.uk" class="text-white underline hover:text-white/70">Oxford University</a>
                    .
                </p>
            </li>
            <li>
                <h2 class="font-display text-white lg:text-lg xl:text-2xl">Transparent</h2>
                <hr class="mt-1.5 hidden w-8 border-white/40 lg:block" />

                <p class="mt-1 max-w-[65ch] text-white/95 lg:mt-2 xl:text-lg">
                    As an
                    <a href="https://github.com/biosecurity-world/biosecurity.world" class="underline">open-source</a>
                    and
                    ,
                    <a class="underline" href="{{ $databaseUrl }}">open-data</a>
                    , scientific project, we keep a record of our inclusion decisions for people to challenge.
                </p>
            </li>
        </ul>
    </header>
    <div
        class="fullscreen mx-auto flex h-screen w-full rounded-3xl shadow-lg duration-1000 motion-safe:transition-[transform,order]"
        id="map-wrapper"
    >
        <aside
            class="hidden h-full w-full max-w-md overflow-y-scroll rounded-l-3xl border-y border-l border-r bg-white lg:flex lg:flex-col"
        >
            <header class="border-b">
                <div class="px-6 pt-4">
                    <div class="flex items-center">
                        <h3 class="flex-1 font-display text-2xl">Map of Biosecurity</h3>

                        <button
                            title="Toggle fullscreen (shortcut: F)"
                            id="toggle-fullscreen"
                            class="-m-2 rounded-full border border-transparent p-2 transition hover:border-gray-200 hover:bg-gray-100 hover:shadow-inner"
                        >
                            <span class="sr-only">Toggle fullscreen</span>
                            <x-heroicon-o-arrows-pointing-out
                                id="not-fullscreen"
                                aria-hidden="true"
                                class="size-5 text-gray-700"
                            />
                            <x-heroicon-o-arrows-pointing-in
                                id="is-fullscreen"
                                aria-hidden="true"
                                class="hidden size-5 text-gray-700"
                            />
                        </button>
                    </div>
                    <p class="mt-& text-gray-700">
                        Last updated on
                        <time
                            datetime="{{ $lastEditedAt->toIso8601String() }}"
                            title="{{ $lastEditedAt->diffForHumans() }}"
                        >
                            {{ $lastEditedAt->format("F j, Y") }}
                        </time>
                        .
                    </p>
                </div>

                <ul class="mt-4 flex space-x-4 px-6 pb-2">
                    <li>
                        <a href="#" class="inline-flex whitespace-nowrap text-sm">
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px text-gray-700 underline">Inclusion criteria</span>
                        </a>
                    </li>
                    <li>
                        <a href="#" class="inline-flex whitespace-nowrap text-sm">
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px text-gray-700 underline">Rejected entries</span>
                        </a>
                    </li>
                    <li>
                        <a
                            href="https://notion.so/{{ config("services.notion.database") }}"
                            class="inline-flex whitespace-nowrap text-sm"
                        >
                            <span class="mt-px text-gray-400">&bull;&nbsp;</span>
                            <span class="-mt-px mr-0.5 text-gray-700 underline">Notion</span>
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                viewBox="0 0 16 16"
                                fill="currentColor"
                                class="-mt-px size-4 text-gray-400 group-hover:text-primary-700"
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
            </header>
            <div class="bg-gray-50 px-6 py-4 lg:flex-1">
                <h4 class="flex-1 font-display text-lg">Filters</h4>

                <fieldset class="mt-2">
                    <legend class="font-display leading-6 text-gray-900">Domain</legend>

                    <div class="mt-0.5 rounded-xl bg-white shadow-sm">
                        <div>
                            <input
                                type="checkbox"
                                name="domain_technical"
                                id="domain_technical"
                                value="technical"
                                class="domain-checkbox peer sr-only"
                            />

                            <label
                                for="domain_technical"
                                class="flex cursor-pointer items-center rounded-t-xl border px-4 py-1.5 transition hover:bg-gray-50 peer-checked:border-technical peer-checked:bg-technical peer-focus:border-technical peer-focus:ring-2 peer-focus:ring-technical"
                            >
                                <x-at-technical class="flex-grow" />
                                <x-heroicon-m-check class="check size-5 text-white" />
                            </label>
                        </div>
                        <div>
                            <input
                                type="checkbox"
                                name="domain_governance"
                                id="domain_governance"
                                value="governance"
                                class="domain-checkbox peer sr-only"
                            />
                            <label
                                for="domain_governance"
                                class="domain-checkbox-label flex cursor-pointer items-center rounded-b-xl border !border-t-0 px-4 py-1.5 transition hover:bg-gray-50 peer-checked:border-governance peer-checked:bg-governance peer-focus:border-governance peer-focus:ring-2 peer-focus:ring-governance"
                            >
                                <x-at-governance class="flex-grow" />
                                <x-heroicon-m-check class="check size-5 text-white" />
                            </label>
                        </div>
                    </div>
                </fieldset>
                <div class="mt-6 flex items-center justify-between">
                    <span class="flex flex-grow flex-col">
                        <span class="font-display leading-6 text-gray-900">Focus on GCBRs prevention</span>
                        <span class="text-sm text-gray-500">
                            Include only organizations focused on large-scale pandemics prevention.
                        </span>
                    </span>
                    <x-big-toggle name="has_gcbr_focus" kind="has-gcbr-focus" />
                </div>
                <fieldset class="mt-6">
                    <legend class="inline font-display leading-6 text-gray-900">Activities</legend>

                    <ul class="clear-both mt-1 flex flex-wrap gap-x-2 gap-y-2">
                        @foreach ($tree->activities() as $activity)
                            @php($fg = $activity->color->foreground())
                            @php($bg = $activity->color->background())
                            <li>
                                <x-checkbox-as-pill
                                    name="activity_{{ $activity->id }}"
                                    value="{{ $activity->id }}"
                                    kind="activity-checkbox"
                                    style="--fg: hsl({{ $fg->hue }} {{ $fg->saturation }}% {{ $fg->lightness }}% / var(--tw-text-opacity, 100));
                                           --bg: hsl({{ $bg->hue }} {{ $bg->saturation }}% {{ $bg->lightness }}% / var(--tw-bg-opacity, 100));
                                           --border: hsl({{ $fg->hue }} {{ $fg->saturation }}% {{ $fg->lightness }} / var(--tw-border-opacity, 100));
                                    "
                                    class="hover:border-primary-700 hover:bg-white hover:text-[--fg] transition bg-white text-gray-700 peer-checked:bg-[--bg] border peer-checked:border-[--border] peer-checked:border-opacity-20 peer-checked:text-[--fg]"
                                >
                                    <span class="sr-only">Toggle activity</span>
                                    <x-activity-icon
                                        :activity="$activity"
                                        aria-hidden="true"
                                        class="size-[1.125rem] opacity-75 group-hover:opacity-100 transition"
                                    />
                                    <span class="ml-1.5 font-bold select-none">
                                        {{ $activity->label }}
                                    </span>
                                </x-checkbox-as-pill>
                            </li>
                        @endforeach
                    </ul>
                </fieldset>
                <div class="mt-6">
                    <h4 class="flex-1 font-display leading-6 text-gray-900">Intervention focuses</h4>

                    <div class="mt-4 space-y-8">
                        @foreach ($categorizedFocuses as $category => $focuses)
                            <div id="focuses_wrapper_{{ $category }}">
                                <div class="flex w-full items-center">
                                    <label
                                        title="Toggle all in this group"
                                        class="flex-1 cursor-pointer rounded-full font-bold"
                                        for="focuses_master_checkbox_{{ $category }}"
                                    >
                                        {{ FocusCategory::from($category)->label() }}
                                    </label>
                                    <x-checkbox
                                        name="focuses_master_checkbox_{{ $category }}"
                                        checked
                                        class="focuses-master-checkbox"
                                    />
                                </div>

                                <ul
                                    class="focuses-list -mx-6 mt-0.5 flex cursor-pointer flex-wrap gap-x-2 gap-y-2 rounded-xl border border-transparent bg-white px-4 py-4 shadow-sm transition"
                                >
                                    @foreach ($focuses as $focus)
                                        <li>
                                            <x-checkbox-as-pill
                                                name="focus_{{ $focus->id }}"
                                                value="{{ $focus->id }}"
                                                kind="focus-checkbox"
                                                data-global-offset="{{ $focus->globalSortOrder() }}"
                                                class="bg-white text-gray-700 border hover:border-primary-700 peer-checked:bg-primary-50 peer-checked:border-primary-100 peer-checked:text-primary-800"
                                            >
                                                <span class="ml-1.5 select-none leading-none group-hover:opacity-75">
                                                    {{ $focus->label }}
                                                </span>
                                            </x-checkbox-as-pill>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </aside>
        <main
            class="relative h-full w-full rounded-l-3xl rounded-r-3xl border-b border-r bg-gray-100 lg:rounded-l-none"
        >
            <section data-state="error" aria-hidden="true" class="app-state state-inactive">
                <div class="text-center">
                    <h3 class="text-xl">An error has occurred.</h3>
                    <p class="reason mt-1"></p>
                    <p>
                        You can try reloading the page or checking the
                        <a
                            href="{{ $databaseUrl }}"
                            rel="noopener noreferrer nofollow"
                            class="text-primary-700 underline"
                        >
                            Notion table
                        </a>
                        directly.
                    </p>
                    <a
                        href="javascript:window.location.reload();"
                        class="focusable mt-4 inline-flex items-center space-x-2 rounded-md border bg-white px-4 py-1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="size-4">
                            <path
                                fill-rule="evenodd"
                                d="M15.312 11.424a5.5 5.5 0 0 1-9.201 2.466l-.312-.311h2.433a.75.75 0 0 0 0-1.5H3.989a.75.75 0 0 0-.75.75v4.242a.75.75 0 0 0 1.5 0v-2.43l.31.31a7 7 0 0 0 11.712-3.138.75.75 0 0 0-1.449-.39Zm1.23-3.723a.75.75 0 0 0 .219-.53V2.929a.75.75 0 0 0-1.5 0V5.36l-.31-.31A7 7 0 0 0 3.239 8.188a.75.75 0 1 0 1.448.389A5.5 5.5 0 0 1 13.89 6.11l.311.31h-2.432a.75.75 0 0 0 0 1.5h4.243a.75.75 0 0 0 .53-.219Z"
                                clip-rule="evenodd"
                            />
                        </svg>
                        <span class="-mt-px">Reload</span>
                    </a>
                </div>
            </section>
            <section data-state="loading" aria-hidden="false" class="app-state state-active">
                <div>
                    <svg
                        class="inline h-8 w-8 animate-spin fill-primary-600 text-gray-200"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        ></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        ></path>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>
            </section>
            <section data-state="empty" class="app-state state-inactive" aria-hidden="true">
                <div class="flex flex-col items-center justify-center text-center">
                    <h3 class="font-display text-xl">There are no entries matching your filters.</h3>
                    <button
                        class="resets-filters focusable mt-4 flex items-center space-x-2 rounded-md border bg-white px-4 py-1 hover:bg-gray-50"
                    >
                        Reset the filters
                    </button>
                </div>
            </section>
            <section data-state="success" class="app-state state-inactive" aria-hidden="true">
                <div class="absolute inset-0 z-20 h-full w-full max-w-md border-y" id="entry-wrapper"></div>
                <div
                    class="pointer-events-none absolute inset-0 z-20 flex h-full w-full max-w-md justify-center rounded-r-3xl border-y border-r bg-gray-50 pt-16 opacity-0 transition-[opacity]"
                    id="entry-loader"
                >
                    <svg
                        class="inline h-8 w-8 animate-spin fill-primary-600 text-gray-200"
                        viewBox="0 0 100 101"
                        fill="none"
                        xmlns="http://www.w3.org/2000/svg"
                        aria-hidden="true"
                    >
                        <path
                            d="M100 50.5908C100 78.2051 77.6142 100.591 50 100.591C22.3858 100.591 0 78.2051 0 50.5908C0 22.9766 22.3858 0.59082 50 0.59082C77.6142 0.59082 100 22.9766 100 50.5908ZM9.08144 50.5908C9.08144 73.1895 27.4013 91.5094 50 91.5094C72.5987 91.5094 90.9186 73.1895 90.9186 50.5908C90.9186 27.9921 72.5987 9.67226 50 9.67226C27.4013 9.67226 9.08144 27.9921 9.08144 50.5908Z"
                            fill="currentColor"
                        ></path>
                        <path
                            d="M93.9676 39.0409C96.393 38.4038 97.8624 35.9116 97.0079 33.5539C95.2932 28.8227 92.871 24.3692 89.8167 20.348C85.8452 15.1192 80.8826 10.7238 75.2124 7.41289C69.5422 4.10194 63.2754 1.94025 56.7698 1.05124C51.7666 0.367541 46.6976 0.446843 41.7345 1.27873C39.2613 1.69328 37.813 4.19778 38.4501 6.62326C39.0873 9.04874 41.5694 10.4717 44.0505 10.1071C47.8511 9.54855 51.7191 9.52689 55.5402 10.0491C60.8642 10.7766 65.9928 12.5457 70.6331 15.2552C75.2735 17.9648 79.3347 21.5619 82.5849 25.841C84.9175 28.9121 86.7997 32.2913 88.1811 35.8758C89.083 38.2158 91.5421 39.6781 93.9676 39.0409Z"
                            fill="currentFill"
                        ></path>
                    </svg>
                    <span class="sr-only">Loading...</span>
                </div>

                <div id="above-map"></div>
                <svg id="map" width="100%" height="100%" class="rounded-tr-3xl">
                    <!-- The map will be dynamically inserted here -->
                </svg>
                <div class="absolute bottom-6 right-6">
                    <div class="flex flex-col divide-y rounded-lg bg-white shadow">
                        <button class="focusable rounded-t-lg p-2 hover:bg-gray-50" id="zoom-in">
                            <x-heroicon-s-plus class="size-5 text-gray-700" />
                        </button>
                        <button class="focusable rounded-b-lg p-2 hover:bg-gray-50" id="zoom-out">
                            <x-heroicon-s-minus class="size-5 text-gray-700" />
                        </button>
                    </div>
                </div>
            </section>
        </main>
    </div>
    <x-footer />
    <a href="/_/entries" class="hidden" aria-hidden="true">entries</a>
    <a href="/_/m" class="hidden" aria-hidden="true">map</a>
</x-layouts.default>
