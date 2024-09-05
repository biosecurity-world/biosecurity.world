<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="length=device-length, user-scalable=yes, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>About - Biosecurity World</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=Nunito:400,500,600,700" rel="stylesheet"/>

    @vite('resources/js/app.ts')
    @vite('resources/css/app.css')
</head>
<body class="antialiased bg-gray-100">
<x-navbar class="mt-4 lg:mt-8 lg:bg-white" border />
<main class="max-w-7xl mx-auto mt-12 px-6 xl:px-0">
    <div class="lg:flex">
        <h2 class="order-1 flex-1 text-lg lg:text-2xl font-display text-emerald-950 lg:text-right">Who we are</h2>
        <p class="max-w-3xl ml-auto text-4xl font-bold tracking-tight sm:text-6xl font-display text-emerald-900 text-left">
            We are a small team of scientists wanting biosecurity for the world.
        </p>
    </div>

    <div class="lg:flex mt-12 lg:mt-24">
        <h2 class="flex-1 text-lg lg:text-2xl font-display text-emerald-950 text-left">Our goal</h2>
        <p class="max-w-3xl mr-auto text-4xl font-bold tracking-tight sm:text-6xl font-display text-emerald-900 lg:text-right">
            We aim to enable biosecurity-conscious people to contribute to a biosecure future.
        </p>
    </div>

    <section class="mt-12 lg:mt-24">
        <h2 class="text-2xl lg:text-4xl tracking-tight font-bold font-display text">Our team</h2>
        <div class="mx-auto mt-4 lg:mt-8">
            <ul role="list"
                class="grid max-w-2xl grid-cols-1 gap-6 sm:grid-cols-2 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-8">
                @for($i = 0; $i < 5; $i++)
                    <x-team-member
                        picture="https://images.unsplash.com/photo-1519345182560-3f2917c472ef?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80"
                        name="Félix Dorn"
                        title="Member of the Comparative Literature Department"
                        bio="Sagittarius. Master in Esperanto literature, especially the 15th century. Something Alumni '99"
                        linkedin="#"
                    />
                @endfor
            </ul>
        </div>
    </section>

    <section class="mt-12 lg:mt-24">
        <h2 class="text-lg lg:text-2xl font-display text-emerald-950">Contact us at</h2>
        <p class="max-w-3xl  text-3xl font-bold tracking-tight lg:text-6xl font-display text-emerald-900">
            team@biosecurity.world
        </p>
    </section>

    <section
        class="mt-12 lg:mt-24 relative isolate overflow-hidden bg-emerald-900 px-6 py-24 text-center shadow-2xl rounded-3xl sm:px-16">
        <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl font-display">
            Help onboard people to the biosecurity world.
        </h2>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="#"
               class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Learn how to contribute</a>
        </div>
        <svg viewBox="0 0 1024 1024"
             class="absolute left-1/2 top-1/3 -z-10 h-[64rem] w-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
             aria-hidden="true">
            <circle cx="512" cy="512" r="512" fill="white" fill-opacity="0.7"/>
        </svg>
    </section>
    <footer aria-labelledby="footer-heading" class="">
        <div class="mt-12 border-t border-gray-900/10 py-4">
            <h2 id="footer-heading" class="sr-only">Footer</h2>
            <div class="lg:flex justify-between">
                <p>
                    <a href="{{ route('welcome') }}" class="font-display">biosecurity.world</a>

                    <span class="text-gray-700">
                        &mdash; Understand the biosecurity landscape.
                    </span>
                </p>

                <ul class="flex space-x-4 overflow-x-scroll mt-2 lg:mt-0">
                    <li>
                        <a href="https://github.com/biosecurity-world/biosecurity.world"
                           class="inline-flex text-sm underline text-gray-700 whitespace-nowrap">
                            <span class="mr-0.5">GitHub</span>
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor"
                                 class="size-4 text-gray-400 group-hover:text-emerald-700 mt-px"
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
</main>
</body>
</html>
