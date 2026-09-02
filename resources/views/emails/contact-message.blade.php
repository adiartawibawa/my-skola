<x-mail::message>
    # Pesan Baru dari Formulir Kontak

    **Nama:** {{ $contactMessage->name }}
    **Email:** {{ $contactMessage->email }}
    @if ($contactMessage->subject)
        **Perihal:** {{ $contactMessage->subject }}
    @endif

    {{ $contactMessage->message }}

    <x-mail::button :url="url('/admin')">
        Buka di Panel Admin
    </x-mail::button>

    Diterima {{ $contactMessage->created_at->translatedFormat('d F Y, H:i') }} WITA.
</x-mail::message>
