@props([
    // \Carbon\CarbonInterface|null
    'lastEditedAt' => null,
    // Optional extra classes for the wrapper
    'class' => '',
])

<div {{ $attributes->merge(['class' => $class]) }}>
    <div class="flex items-center">
        <h3 class="font-display flex-1 text-2xl">Map of Biosecurity</h3>
    </div>

    @if ($lastEditedAt)
        <p class="mt-1 text-gray-700">
            Last updated on
            <time
                datetime="{{ $lastEditedAt->toIso8601String() }}"
                title="{{ $lastEditedAt->diffForHumans() }}"
            >
                {{ $lastEditedAt->format('F j, Y') }}
            </time>.
        </p>
    @endif

    <div class="mt-4">
        <h4 class="font-display text-gray-900">Team</h4>
        <p class="flex flex-wrap text-gray-700">
            <a
                href="https://www.linkedin.com/in/alix-pham/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Alix Pham
            </a>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/sofyalebedeva/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Sofya Lebedeva
            </a>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/johantang/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Johan Täng
            </a>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/jeremy-andreoletti-330445216/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Jérémy Andréoletti
            </a>
        </p>
    </div>

    <div class="mt-2 mb-6">
        <h4 class="font-display text-gray-900">Support</h4>
        <p class="flex flex-wrap text-gray-700">
            <a
                href="https://www.linkedin.com/in/linbowkerlonnecker/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Lin Bowker-Lonnecker
            </a>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/will-saunter/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Will Saunter
            </a>
            <span>,&nbsp;</span>
            <a
                href="https://www.linkedin.com/in/dornfelix/"
                class="underline"
                target="_blank"
                rel="noopener noreferrer nofollow"
            >
                Félix Dorn
            </a>
        </p>
    </div>
</div>
