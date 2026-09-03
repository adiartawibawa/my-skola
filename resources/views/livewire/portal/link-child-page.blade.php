<div class="max-w-2xl mx-auto">
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-2">Tautkan Data Anak</h1>
    <p class="text-sm text-[var(--brand-ink)]/50 mb-8">
        Masukkan NISN dan tanggal lahir anak untuk menautkan data akademiknya ke akunmu.
    </p>

    @if (session('link_success'))
        <div class="bg-emerald-50 text-emerald-700 text-sm rounded-lg p-3 mb-6">{{ session('link_success') }}</div>
    @endif

    @if ($linkedStudents->isNotEmpty())
        <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-5 mb-8">
            <h2 class="font-semibold text-[var(--brand-ink)] text-sm mb-3">Anak yang Sudah Tertaut</h2>
            <ul class="space-y-2 text-sm">
                @foreach ($linkedStudents as $student)
                    <li class="flex items-center justify-between">
                        <span class="text-[var(--brand-ink)]/80">{{ $student->user->name }} ({{ $student->nisn }})</span>
                        <span
                            class="text-xs font-mono uppercase text-[var(--brand-primary-light)]">{{ $student->pivot->relationship_type }}</span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif

    <form wire:submit="link"
        class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-6 space-y-4">
        <div>
            <label class="text-sm font-medium text-[var(--brand-ink)]/70">NISN Anak</label>
            <input type="text" wire:model="nisn"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm mt-1 focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
            @error('nisn')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-sm font-medium text-[var(--brand-ink)]/70">Tanggal Lahir Anak</label>
            <input type="date" wire:model="tanggal_lahir"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm mt-1 focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]" />
            @error('tanggal_lahir')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <div>
            <label class="text-sm font-medium text-[var(--brand-ink)]/70">Hubungan</label>
            <select wire:model="relationship_type"
                class="w-full rounded-lg border-[var(--brand-accent)]/30 text-sm mt-1 focus:border-[var(--brand-primary)] focus:ring-[var(--brand-primary)]">
                <option value="">Pilih hubungan</option>
                @foreach (\App\Enums\GuardianRelationshipType::options() as $value => $label)
                    <option value="{{ $value }}">{{ $label }}</option>
                @endforeach
            </select>
            @error('relationship_type')
                <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
            @enderror
        </div>

        <button type="submit"
            class="w-full bg-[var(--brand-primary)] text-[var(--brand-paper)] text-sm font-semibold py-2.5 rounded-lg hover:bg-[var(--brand-primary-light)] transition">
            Tautkan
        </button>
    </form>
</div>
