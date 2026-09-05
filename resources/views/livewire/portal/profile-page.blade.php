<div>
    <h1 class="font-display text-2xl font-bold text-[var(--brand-ink)] mb-6">Profil</h1>
    @if ($isAlumni)
        <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-6 max-w-lg">
            <dl class="space-y-3 text-sm">
                @foreach ([
        'Nama' => auth()->user()->name,
        'Email' => auth()->user()->email,
        'Tahun Lulus' => $alumniProfile?->tahun_lulus,
        'Program Keahlian' => $alumniProfile?->programKeahlian?->name,
        'Status Verifikasi' => $alumniProfile?->is_verified ? 'Terverifikasi' : 'Menunggu Verifikasi',
    ] as $label => $value)
                    <div class="flex justify-between border-b border-[var(--brand-accent)]/10 pb-2">
                        <dt class="text-[var(--brand-ink)]/50">{{ $label }}</dt>
                        <dd class="text-[var(--brand-ink)] font-medium">{{ $value ?: '—' }}</dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @elseif (!$student)
        <x-portal-empty-student :is-parent="auth()->user()->role->value === 'parent'" />
    @else
        <div class="grid lg:grid-cols-2 gap-6">
            <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-6">
                <h2 class="font-semibold text-[var(--brand-ink)] mb-4">Data Diri</h2>
                <dl class="space-y-3 text-sm">
                    @foreach ([
        'Nama' => $student->user->name,
        'NIS' => $student->nis,
        'NISN' => $student->nisn,
        'Tempat Lahir' => $student->tempat_lahir,
        'Tanggal Lahir' => $student->tanggal_lahir?->translatedFormat('d F Y'),
    ] as $label => $value)
                        <div class="flex justify-between border-b border-[var(--brand-accent)]/10 pb-2">
                            <dt class="text-[var(--brand-ink)]/50">{{ $label }}</dt>
                            <dd class="text-[var(--brand-ink)] font-medium">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-6">
                <h2 class="font-semibold text-[var(--brand-ink)] mb-4">Akademik</h2>
                <dl class="space-y-3 text-sm">
                    @foreach ([
        'Kelas' => $classRoom?->full_name,
        'Program Keahlian' => $classRoom?->programKeahlian?->name,
        'Wali Kelas' => $homeroomTeacher?->name,
    ] as $label => $value)
                        <div class="flex justify-between border-b border-[var(--brand-accent)]/10 pb-2">
                            <dt class="text-[var(--brand-ink)]/50">{{ $label }}</dt>
                            <dd class="text-[var(--brand-ink)] font-medium">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>

            <div class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/25 rounded-xl p-6 lg:col-span-2">
                <h2 class="font-semibold text-[var(--brand-ink)] mb-4">Data Orang Tua/Wali</h2>
                <dl class="grid sm:grid-cols-2 gap-x-8 gap-y-3 text-sm">
                    @foreach ([
        'Nama Ayah' => $student->nama_ayah,
        'Nama Ibu' => $student->nama_ibu,
        'Pekerjaan Orang Tua' => $student->pekerjaan_orang_tua,
        'No. Telepon' => $student->no_telp_orang_tua,
        'Alamat' => $student->alamat_orang_tua,
    ] as $label => $value)
                        <div class="flex justify-between border-b border-[var(--brand-accent)]/10 pb-2">
                            <dt class="text-[var(--brand-ink)]/50">{{ $label }}</dt>
                            <dd class="text-[var(--brand-ink)] font-medium text-right">{{ $value ?: '—' }}</dd>
                        </div>
                    @endforeach
                </dl>
            </div>
        </div>
    @endif
</div>
