<x-layouts.guest title="Tentang Kami" description="Profil, sejarah, visi misi, dan fasilitas {{ config('app.name') }}.">
    {{-- Intro --}}
    <section class="max-w-4xl mx-auto px-4 pt-16 pb-12 text-center">
        <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-3">Profil Sekolah</p>
        <h1 class="font-display text-4xl font-bold text-[#241512] mb-4">Tentang {{ config('app.name') }}</h1>
        <p class="text-[#241512]/60 leading-relaxed max-w-2xl mx-auto">
            Sekolah Menengah Kejuruan yang berkomitmen mencetak lulusan siap kerja, siap wirausaha,
            dan siap melanjutkan pendidikan tinggi sejak {{ config('school.founded_year') }}.
        </p>
    </section>

    {{-- Sejarah — timeline vertikal --}}
    <section class="max-w-3xl mx-auto px-4 py-16">
        <h2 class="font-display text-2xl font-bold text-[#241512] mb-8">Riwayat Singkat</h2>

        <div class="relative border-l-2 border-[#C89B3C]/30 pl-8 space-y-10">
            @foreach ([['tahun' => config('school.founded_year', '1998'), 'judul' => 'Pendirian Sekolah', 'desc' => 'Berdiri dengan dua program keahlian awal dan puluhan siswa angkatan pertama.'], ['tahun' => '2008', 'judul' => 'Akreditasi A', 'desc' => 'Meraih status akreditasi A dari Badan Akreditasi Nasional.'], ['tahun' => '2015', 'judul' => 'Perluasan Program Keahlian', 'desc' => 'Menambah program keahlian baru mengikuti kebutuhan industri lokal.'], ['tahun' => now()->year, 'judul' => 'Digitalisasi Layanan Akademik', 'desc' => 'Meluncurkan sistem informasi akademik & rapor digital untuk orang tua.']] as $riwayat)
                <div class="relative">
                    <span
                        class="absolute -left-[38px] top-1 w-4 h-4 rounded-full bg-[#6B1220] border-2 border-[#FBF6EE]"></span>
                    <p class="font-mono text-sm font-semibold text-[#8C1F2E] mb-1">{{ $riwayat['tahun'] }}</p>
                    <h3 class="font-display font-semibold text-lg text-[#241512]">{{ $riwayat['judul'] }}</h3>
                    <p class="text-sm text-[#241512]/60 mt-1 leading-relaxed">{{ $riwayat['desc'] }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Visi --}}
    <section class="bg-[#4A0D17] py-20">
        <div class="max-w-2xl mx-auto px-4 text-center">
            <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#E4C878] mb-4">Visi</p>
            <p class="font-display text-2xl sm:text-3xl italic text-[#FBF6EE] leading-snug">
                Menjadi lembaga pendidikan kejuruan unggul yang menghasilkan lulusan kompeten,
                berkarakter, dan berdaya saing di dunia kerja maupun industri.
            </p>
        </div>
    </section>

    {{-- Misi --}}
    <section class="max-w-3xl mx-auto px-4 py-16">
        <h2 class="font-display text-2xl font-bold text-[#241512] mb-8">Misi</h2>
        <div class="divide-y divide-[#C89B3C]/25 border-t border-b border-[#C89B3C]/25">
            @foreach (['Menyelenggarakan pendidikan kejuruan yang selaras dengan kebutuhan dunia usaha dan industri.', 'Membentuk karakter siswa yang disiplin, jujur, dan bertanggung jawab.', 'Meningkatkan kompetensi guru secara berkelanjutan sesuai perkembangan teknologi.', 'Membangun kemitraan strategis dengan industri untuk praktik kerja lapangan dan penyerapan lulusan.'] as $i => $misi)
                <div class="flex gap-5 py-5">
                    <span
                        class="font-mono text-sm text-[#8C1F2E] shrink-0">{{ str_pad($i + 1, 2, '0', STR_PAD_LEFT) }}</span>
                    <p class="text-[#241512]/75 leading-relaxed">{{ $misi }}</p>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Struktur / Pimpinan --}}
    <section class="bg-[#F3EADA] py-20">
        <div class="max-w-6xl mx-auto px-4">
            <h2 class="font-display text-2xl font-bold text-[#241512] mb-10 text-center">Pimpinan Sekolah</h2>

            <div class="grid sm:grid-cols-3 gap-6 max-w-3xl mx-auto">
                @foreach ([['nama' => 'Drs. Wayan Suarta, M.Pd.', 'jabatan' => 'Kepala Sekolah'], ['nama' => 'Ni Made Astuti, S.Pd.', 'jabatan' => 'Wakil Kepala Bidang Kurikulum'], ['nama' => 'I Ketut Adnyana, S.T.', 'jabatan' => 'Wakil Kepala Bidang Hubungan Industri']] as $pimpinan)
                    <div class="text-center">
                        <div
                            class="w-20 h-20 rounded-full bg-[#6B1220] text-[#FBF6EE] flex items-center justify-center font-display text-xl font-bold mx-auto mb-3">
                            {{ collect(explode(' ', $pimpinan['nama']))->map(fn($w) => mb_substr($w, 0, 1))->take(2)->join('') }}
                        </div>
                        <h3 class="font-semibold text-[#241512] text-sm">{{ $pimpinan['nama'] }}</h3>
                        <p class="text-xs text-[#241512]/50 mt-1">{{ $pimpinan['jabatan'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- Fasilitas --}}
    <section class="max-w-6xl mx-auto px-4 py-20">
        <h2 class="font-display text-2xl font-bold text-[#241512] mb-8">Fasilitas</h2>
        <div class="grid sm:grid-cols-2 gap-x-10 gap-y-5">
            @foreach ([['nama' => 'Bengkel Praktik Jurusan', 'desc' => 'Ruang praktik khusus tiap program keahlian dengan alat sesuai standar industri.'], ['nama' => 'Laboratorium Komputer', 'desc' => 'Mendukung mata pelajaran produktif RPL, TKJ, dan simulasi digital.'], ['nama' => 'Perpustakaan', 'desc' => 'Koleksi buku kejuruan dan umum, serta akses e-book.'], ['nama' => 'Ruang BKK (Bursa Kerja Khusus)', 'desc' => 'Menjembatani lulusan dengan lowongan kerja dari mitra industri.'], ['nama' => 'Aula Serbaguna', 'desc' => 'Untuk kegiatan seminar, wisuda, dan acara sekolah.'], ['nama' => 'Lapangan Olahraga', 'desc' => 'Menunjang kegiatan ekstrakurikuler dan pendidikan jasmani.']] as $fasilitas)
                <div class="flex gap-3">
                    <span class="w-1.5 h-1.5 rounded-full bg-[#C89B3C] mt-2 shrink-0"></span>
                    <div>
                        <h3 class="font-semibold text-[#241512] text-sm">{{ $fasilitas['nama'] }}</h3>
                        <p class="text-sm text-[#241512]/55 mt-0.5">{{ $fasilitas['desc'] }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </section>

    {{-- Akreditasi --}}
    <section class="bg-[#FBF6EE] border-t border-[#C89B3C]/25 py-20">
        <div class="max-w-4xl mx-auto px-4 grid sm:grid-cols-[auto_1fr] gap-8 items-center">
            <x-school-seal class="w-32 h-32 mx-auto sm:mx-0" />
            <div>
                <h2 class="font-display text-xl font-bold text-[#241512] mb-2">Legalitas &amp; Akreditasi</h2>
                <p class="text-sm text-[#241512]/60 leading-relaxed">
                    {{ config('app.name') }} terakreditasi <strong>A</strong> oleh Badan Akreditasi Nasional
                    Sekolah/Madrasah (BAN-S/M) dan beroperasi di bawah izin resmi Dinas Pendidikan setempat.
                </p>
            </div>
        </div>
    </section>
</x-layouts.guest>
