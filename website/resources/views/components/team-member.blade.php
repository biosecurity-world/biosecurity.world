@props(['picture', 'name', 'title', 'bio', 'linkedin'])
<li class="rounded-3xl bg-white border py-8 px-6 text-center">
    <img class="mx-auto size-36 rounded-full md:size-48" src="{{ $picture }}" alt="">
    <h3 class="mt-6 text-xl font-display font-semibold leading-7 tracking-tight">{{ $name }}</h3>
    <p class="text-lg px-5 text-gray-900">{{ $title }}</p>
    <p class="mt-6 leading-6 text-gray-700">
        {{ $bio }}
    </p>
    @if($linkedin)
        <ul role="list" class="mt-6 flex justify-center gap-x-6">
            <li>
                <a href="{{ $linkedin }}" class="block rounded-full p-2 -m-2 hover:bg-gray-100 text-emerald-700">
                    <span class="sr-only">LinkedIn</span>
                    <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M16.338 16.338H13.67V12.16c0-.995-.017-2.277-1.387-2.277-1.39 0-1.601 1.086-1.601 2.207v4.248H8.014v-8.59h2.559v1.174h.037c.356-.675 1.227-1.387 2.526-1.387 2.703 0 3.203 1.778 3.203 4.092v4.711zM5.005 6.575a1.548 1.548 0 11-.003-3.096 1.548 1.548 0 01.003 3.096zm-1.337 9.763H6.34v-8.59H3.667v8.59zM17.668 1H2.328C1.595 1 1 1.581 1 2.298v15.403C1 18.418 1.595 19 2.328 19h15.34c.734 0 1.332-.582 1.332-1.299V2.298C19 1.581 18.402 1 17.668 1z" clip-rule="evenodd" />
                    </svg>
                </a>
            </li>
        </ul>
    @endif
</li>
