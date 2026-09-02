<x-layouts.guest title="Program Keahlian"
    description="Daftar lengkap program keahlian di {{ config('app.name') }} beserta kompetensi dan prospek kariernya.">
    <section class="max-w-4xl mx-auto px-4 pt-16 pb-12 text-center">
        <p class="font-mono text-xs tracking-[0.2em] uppercase text-[var(--brand-primary-light)] mb-3">Program Keahlian
        </p>
        <h1 class="font-display text-4xl font-bold text-[var(--brand-ink)] mb-4">Kompetensi untuk Dunia Kerja</h1>
        <p class="text-[var(--brand-ink)]/60 leading-relaxed max-w-2xl mx-auto">
            Setiap program keahlian dirancang bersama mitra industri agar lulusan benar-benar siap kerja,
            bukan sekadar lulus ujian.
        </p>
    </section>

    <section class="max-w-4xl mx-auto px-4 pb-24 space-y-10">
        @foreach ([
        [
            'kode' => 'TKJ',
            'nama' => 'Teknik Komputer & Jaringan',
            'desc' => 'Membekali siswa dengan kemampuan instalasi, administrasi jaringan, dan keamanan siber dasar.',
            'kompetensi' => ['Instalasi & konfigurasi jaringan LAN/WAN', 'Administrasi server', 'Troubleshooting perangkat keras & lunak', 'Dasar keamanan siber'],
            'karier' => ['Teknisi Jaringan', 'IT Support', 'Network Administrator'],
        ],
        [
            'kode' => 'RPL',
            'nama' => 'Rekayasa Perangkat Lunak',
            'desc' => 'Fokus pada pengembangan aplikasi web dan mobile, basis data, serta logika pemrograman.',
            'kompetensi' => ['Pemrograman web & mobile', 'Perancangan basis data', 'Pengujian perangkat lunak', 'Version control (Git)'],
            'karier' => ['Junior Web Developer', 'Mobile Developer', 'QA Tester'],
        ],
        [
            'kode' => 'MM',
            'nama' => 'Multimedia',
            'desc' => 'Mengasah kemampuan desain grafis, videografi, dan produksi konten digital.',
            'kompetensi' => ['Desain grafis digital', 'Produksi & editing video', 'Fotografi produk', 'Animasi 2D dasar'],
            'karier' => ['Graphic Designer', 'Video Editor', 'Content Creator'],
        ],
        [
            'kode' => 'TBSM',
            'nama' => 'Teknik & Bisnis Sepeda Motor',
            'desc' => 'Menguasai perawatan, perbaikan, dan manajemen bengkel sepeda motor.',
            'kompetensi' => ['Perawatan mesin sepeda motor', 'Diagnosis kerusakan', 'Manajemen bengkel', 'Sistem injeksi & kelistrikan'],
            'karier' => ['Mekanik Sepeda Motor', 'Kepala Bengkel', 'Wirausaha Bengkel'],
        ],
        [
            'kode' => 'AKL',
            'nama' => 'Akuntansi & Keuangan Lembaga',
            'desc' => 'Mempelajari pencatatan keuangan, perpajakan, dan sistem akuntansi digital.',
            'kompetensi' => ['Siklus akuntansi', 'Perpajakan dasar', 'Aplikasi akuntansi digital', 'Laporan keuangan'],
            'karier' => ['Staf Akuntansi', 'Admin Keuangan', 'Teller Bank'],
        ],
        [
            'kode' => 'TB',
            'nama' => 'Tata Boga',
            'desc' => 'Melatih pengolahan makanan, manajemen dapur, dan kewirausahaan kuliner.',
            'kompetensi' => ['Pengolahan makanan Indonesia & kontinental', 'Manajemen dapur (kitchen management)', 'Food safety & hygiene', 'Kewirausahaan kuliner'],
            'karier' => ['Chef/Cook', 'Pengusaha Kuliner', 'Food Stylist'],
        ],
    ] as $program)
            <article
                class="bg-[var(--brand-paper)] border border-[var(--brand-accent)]/30 rounded-2xl p-6 sm:p-8 border-l-4 border-l-[var(--brand-primary)]">
                <div class="flex flex-wrap items-baseline gap-3 mb-3">
                    <span
                        class="font-mono text-xs tracking-widest text-[var(--brand-primary-light)]">{{ $program['kode'] }}</span>
                    <h2 class="font-display text-xl font-bold text-[var(--brand-ink)]">{{ $program['nama'] }}</h2>
                </div>

                <p class="text-sm text-[var(--brand-ink)]/65 leading-relaxed mb-5">{{ $program['desc'] }}</p>

                <div class="grid sm:grid-cols-2 gap-6">
                    <div>
                        <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-ink)]/40 mb-2">
                            Kompetensi
                            Dipelajari</h3>
                        <ul class="space-y-1.5 text-sm text-[var(--brand-ink)]/75">
                            @foreach ($program['kompetensi'] as $kompetensi)
                                <li class="flex gap-2">
                                    <span class="text-[var(--brand-accent)]">&#8226;</span> {{ $kompetensi }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-mono text-xs tracking-widest uppercase text-[var(--brand-ink)]/40 mb-2">Prospek
                            Karier
                        </h3>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($program['karier'] as $karier)
                                <span
                                    class="text-xs px-3 py-1 rounded-full bg-[#F3EADA] text-[var(--brand-primary)] font-medium">{{ $karier }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </article>
        @endforeach
    </section>

    <section class="bg-gradient-to-br from-[var(--brand-primary)] to-[var(--brand-primary-dark)] py-16">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <h2 class="font-display text-2xl sm:text-3xl font-bold text-[var(--brand-paper)] mb-4">
                Tertarik dengan Salah Satu Program Ini?
            </h2>
            <a href="#"
                class="inline-block bg-[var(--brand-accent)] text-[var(--brand-primary-dark)] text-sm font-semibold px-6 py-3 rounded-full hover:bg-[var(--brand-accent-light)] transition">
                Daftar Sekarang
            </a>
        </div>
    </section>
</x-layouts.guest>
