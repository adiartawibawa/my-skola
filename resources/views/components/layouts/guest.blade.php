@props(['title' => null, 'description' => null, 'ogImage' => null, 'canonicalUrl' => null, 'type' => 'website'])

<x-layouts.master :title="$title" :description="$description" :og-image="$ogImage" :canonical-url="$canonicalUrl" :type="$type">
    <x-site-header />

    <main>
        {{ $slot }}
    </main>

    <x-site-footer />
</x-layouts.master>
