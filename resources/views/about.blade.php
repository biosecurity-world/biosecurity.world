<x-layouts.default class="bg-gray-100 antialiased" title="About">
    <x-navbar class="mt-4 md:mt-8 md:bg-white" border />
    <main class="mx-auto mt-8 max-w-7xl px-6 md:mt-12 md:px-14 xl:px-0">
        <div class="md:flex">
            <h2
                class="order-1 flex-1 font-display text-lg text-primary-950 md:mt-2.5 md:text-right md:text-xl lg:text-2xl"
            >
                Who we are
            </h2>
            <p
                class="ml-auto max-w-3xl text-left font-display text-4xl font-bold tracking-tight text-primary-900 sm:text-6xl"
            >
                We are a small team of scientists wanting biosecurity for the world.
            </p>
        </div>

        <div class="mt-12 md:mt-24 md:flex">
            <h2 class="flex-1 font-display text-lg text-primary-950 md:mt-2.5 md:text-left md:text-xl lg:text-2xl">
                Our goal
            </h2>
            <p
                class="mr-auto max-w-3xl font-display text-4xl font-bold tracking-tight text-primary-900 sm:text-6xl md:text-right"
            >
                We aim to enable biosecurity-conscious people to contribute to a biosecure future.
            </p>
        </div>

        <section class="mt-12 md:mt-24">
            <h2 class="text font-display text-2xl font-bold tracking-tight md:text-4xl">Our team</h2>
            <div class="mx-auto mt-4 md:mt-8">
                <ul
                    role="list"
                    class="grid max-w-2xl grid-cols-1 gap-6 sm:grid-cols-2 md:mx-0 md:max-w-none md:grid-cols-3 md:gap-8"
                >
                    @for ($i = 0; $i < 5; $i++)
                        <x-team-member
                            picture="https://images.unsplash.com/photo-1519345182560-3f2917c472ef?ixlib=rb-1.2.1&ixid=eyJhcHBfaWQiOjEyMDd9&auto=format&fit=facearea&facepad=8&w=1024&h=1024&q=80"
                            name="Henri V"
                            title="Member of the Comparative Literature Department"
                            bio="Sagittarius. Master in Esperanto literature, especially the 15th century. Something Alumni '99"
                            linkedin="#"
                        />
                    @endfor
                </ul>
            </div>
        </section>

        <section class="mt-12 md:mt-24">
            <h2 class="font-display text-lg text-primary-950 md:text-2xl">Contact us at</h2>
            <p class="max-w-3xl font-display text-3xl font-bold tracking-tight text-primary-900 md:text-6xl">
                team@biosecurity.world
            </p>
        </section>

        <section
            class="relative isolate mt-12 overflow-hidden rounded-3xl bg-primary-900 px-6 py-24 text-center shadow-2xl sm:px-16 md:mt-24"
        >
            <h2 class="mx-auto max-w-2xl font-display text-3xl font-bold tracking-tight text-white sm:text-4xl">
                Help onboard people to the biosecurity world.
            </h2>
            <div class="mt-10 flex items-center justify-center gap-x-6">
                <a
                    href="{{ route("how-to-contribute", absolute: false) }}"
                    class="rounded-md bg-white px-3.5 py-2.5 text-sm font-semibold text-gray-900 shadow-sm hover:bg-gray-100 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white"
                >
                    Learn how to contribute
                </a>
            </div>
            <svg
                viewBox="0 0 1024 1024"
                class="absolute left-1/2 top-1/3 -z-10 h-[64rem] w-[64rem] -translate-x-1/2 [mask-image:radial-gradient(closest-side,white,transparent)]"
                aria-hidden="true"
            >
                <circle cx="512" cy="512" r="512" fill="white" fill-opacity="0.7" />
            </svg>
        </section>
    </main>
    <x-footer />
</x-layouts.default>
