<x-layouts.default class="antialiased bg-gray-100" title="About">
<x-navbar class="mt-4 lg:mt-8 lg:bg-white" border />
<main class="max-w-7xl mx-auto mt-12 px-6 xl:px-0">
    <div class="lg:flex">
        <h2 class="order-1 flex-1 text-lg lg:text-2xl font-display text-primary-950 lg:text-right">Who we are</h2>
        <p class="max-w-3xl ml-auto text-4xl font-bold tracking-tight sm:text-6xl font-display text-primary-900 text-left">
            We are a small team of scientists wanting biosecurity for the world.
        </p>
    </div>

    <div class="lg:flex mt-12 lg:mt-24">
        <h2 class="flex-1 text-lg lg:text-2xl font-display text-primary-950 text-left">Our goal</h2>
        <p class="max-w-3xl mr-auto text-4xl font-bold tracking-tight sm:text-6xl font-display text-primary-900 lg:text-right">
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
        <h2 class="text-lg lg:text-2xl font-display text-primary-950">Contact us at</h2>
        <p class="max-w-3xl  text-3xl font-bold tracking-tight lg:text-6xl font-display text-primary-900">
            team@biosecurity.world
        </p>
    </section>

    <section
        class="mt-12 lg:mt-24 relative isolate overflow-hidden bg-primary-900 px-6 py-24 text-center shadow-2xl rounded-3xl sm:px-16">
        <h2 class="mx-auto max-w-2xl text-3xl font-bold tracking-tight text-white sm:text-4xl font-display">
            Help onboard people to the biosecurity world.
        </h2>
        <div class="mt-10 flex items-center justify-center gap-x-6">
            <a href="{{ route('how-to-contribute', absolute: false) }}"
               class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white">Learn how to contribute</a>
        </div>
        <svg viewBox="0 0 1024 1024"
             class="absolute left-1/2 top-1/3 -z-10 h-[64rem] w-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
             aria-hidden="true">
            <circle cx="512" cy="512" r="512" fill="white" fill-opacity="0.7"/>
        </svg>
    </section>
</main>
<x-footer />
</x-layouts.default>
