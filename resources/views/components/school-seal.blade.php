@props(['class' => 'w-40 h-40'])

<div {{ $attributes->merge(['class' => $class . ' seal-stamp']) }} style="transform: rotate(-6deg);">
    <svg viewBox="0 0 200 200" class="w-full h-full">
        <defs>
            <path id="sealRingPath" d="M100,100 m-82,0 a82,82 0 1,1 164,0 a82,82 0 1,1 -164,0" />
        </defs>

        <circle cx="100" cy="100" r="96" fill="none" stroke="#C89B3C" stroke-width="1.5" opacity="0.55" />
        <circle cx="100" cy="100" r="70" fill="#6B1220" stroke="#C89B3C" stroke-width="2" />

        <text font-family="JetBrains Mono, monospace" font-size="9.2" fill="#E4C878" letter-spacing="3">
            <textPath href="#sealRingPath" startOffset="0%">
                {{ strtoupper(config('app.name')) }} &#8226; LEMBAGA PENDIDIKAN RESMI &#8226; TERAKREDITASI A &#8226;
            </textPath>
        </text>

        <text x="100" y="96" text-anchor="middle" font-family="Fraunces, serif" font-weight="700" font-size="32"
            fill="#FBF6EE">
            {{ collect(explode(' ', config('app.name')))->map(fn($w) => mb_substr($w, 0, 1))->join('') }}
        </text>
        <text x="100" y="120" text-anchor="middle" font-family="JetBrains Mono, monospace" font-size="8"
            letter-spacing="2" fill="#E4C878">
            EST. {{ config('school.founded_year', '1998') }}
        </text>
    </svg>
</div>
