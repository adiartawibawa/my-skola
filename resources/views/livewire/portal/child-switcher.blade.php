<div>
    @if ($children->count() > 1)
        <select wire:model.live="childId"
            class="text-sm rounded-lg border-[var(--brand-accent)]/30 focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]">
            @foreach ($children as $child)
                <option value="{{ $child->id }}">{{ $child->user->name }}</option>
            @endforeach
        </select>
    @elseif ($children->count() === 1)
        <span class="text-sm text-[var(--brand-ink)]/60">{{ $children->first()->user->name }}</span>
    @endif
</div>
