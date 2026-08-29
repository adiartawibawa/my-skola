@props(['title' => null, 'description' => null, 'ogImage' => null, 'canonicalUrl' => null])

<x-layouts.guest :title="$title" :description="$description" :og-image="$ogImage" :canonical-url="$canonicalUrl" type="article">
    {{ $slot }}
</x-layouts.guest>
