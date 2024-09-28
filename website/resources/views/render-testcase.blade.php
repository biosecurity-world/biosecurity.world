<x-layouts.default title="Document" class="h-full">
    <x-slot:head>
        <script>
            // There are probably problems with floating-point precision when passing data
            // from PHP -> JSON -> JS. This seems to work for now.
            window.testCase = @js($case)
        </script>

        @vite(["resources/js/render-testcase.ts"])
    </x-slot>

    <svg width="100%" height="100%" id="map">
        <g id="zoom-wrapper">
            <g id="center-wrapper"></g>
        </g>
    </svg>
</x-layouts.default>
