@props(['icon'])
<svg
    {{ $attributes }}
    fill="none"
    stroke="currentColor"
    stroke-linecap="round"
    stroke-linejoin="round"
    stroke-width="2"
    viewBox="0 0 24 24"
>
    @if($icon === 'research')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path
            d="M5 21h14M6 18h2m-1 0v3m2-10 3 3 6-6-3-3zm1.5 1.5L9 14m8-11 3 3"
        />
        <path d="M12 21a6 6 0 0 0 3.71-10.71"/>
    @elseif($icon === 'advocacy')

        <path stroke="none" d="M0 0h24v24H0z"/>
        <path
            d="M18 8a3 3 0 0 1 0 6m-8-6v11a1 1 0 0 1-1 1H8a1 1 0 0 1-1-1v-5"
        />
        <path
            d="M12 8h0l5-4a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1l-5-4H4a1 1 0 0 1-1-1V9a1 1 0 0 1 1-1h8"
        />
    @elseif($icon === 'funding')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path d="M9 14c0 1.7 2.7 3 6 3s6-1.3 6-3-2.7-3-6-3-6 1.3-6 3z"/>
        <path
            d="M9 14v4c0 1.7 2.7 3 6 3s6-1.3 6-3v-4M3 6c0 1 1.1 2 3 2.6s4.1.5 6 0c1.9-.5 3-1.5 3-2.6 0-1-1.1-2-3-2.6s-4.1-.5-6 0C4.1 3.9 3 4.9 3 6z"
        />
        <path d="M3 6v10c0 .9.8 1.4 2 2"/>
        <path d="M3 11c0 .9.8 1.4 2 2"/>
    @elseif($icon === 'education')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path d="M22 9 12 5 2 9l10 4 10-4v6"/>
        <path d="M6 10.6V16a6 3 0 0 0 12 0v-5.4"/>
    @elseif($icon === 'strategy')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path
            d="M3 12h1m8-9v1m8 8h1M5.6 5.6l.7.7m12.1-.7-.7.7M9 16a5 5 0 1 1 6 0 3.5 3.5 0 0 0-1 3 2 2 0 0 1-4 0 3.5 3.5 0 0 0-1-3m.7 1h4.6"
        />
    @elseif($icon === 'policy')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path
            d="M7 20h10M6 6l6-1 6 1m-6-3v17m-3-8L6 6l-3 6a3 3 0 0 0 6 0m12 0-3-6-3 6a3 3 0 0 0 6 0"
        />
    @elseif($icon === 'lobbying')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path d="M19.5 12.6 12 20l-7.5-7.4A5 5 0 1 1 12 6a5 5 0 1 1 7.5 6.6"/>
        <path
            d="M12 6 8.7 9.3a1 1 0 0 0 0 1.4l.6.5c.6.7 1.8.7 2.4 0l1-1a3.2 3.2 0 0 1 4.6 0l2.2 2.3m-7 3 2 2M15 13l2 2"
        />
    @elseif($icon === 'strategy')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path d="M11 12a1 1 0 1 0 2 0 1 1 0 1 0-2 0"/>
        <path d="M12 7a5 5 0 1 0 5 5"/>
        <path d="M13 3a9 9 0 1 0 8 8"/>
        <path d="M15 6v3h3l3-3h-3V3zm0 3-3 3"/>
    @elseif($icon === 'technology')
        <path stroke="none" d="M0 0h24v24H0z"/>
        <path
            d="M5 6a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H6a1 1 0 0 1-1-1z"
        />
        <path
            d="M9 9h6v6H9zm-6 1h2m-2 4h2m5-11v2m4-2v2m7 5h-2m2 4h-2m-5 7v-2m-4 2v-2"
        />
    @endif
</svg>
