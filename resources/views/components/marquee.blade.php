@props(['speed' => 35])

<div class="relative overflow-hidden [mask-image:linear-gradient(to_right,transparent,black_5%,black_95%,transparent)]">
    <div class="flex w-max animate-marquee hover:[animation-play-state:paused]"
        style="animation-duration: {{ $speed }}s;">
        <div class="flex gap-5 shrink-0 pr-5">
            {{ $slot }}
        </div>
        <div class="flex gap-5 shrink-0 pr-5" aria-hidden="true">
            {{ $slot }}
        </div>
    </div>
</div>
