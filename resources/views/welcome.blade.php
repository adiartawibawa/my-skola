<x-layouts.guest title="Beranda"
    description="Sistem informasi akademik sekaligus media resmi sekolah kami — kabar, prestasi, dan proses belajar yang transparan.">

    {{-- HERO --}}
    <section class="relative overflow-hidden border-b-2 border-[#C89B3C]/20 pb-16">
        <div class="max-w-6xl mx-auto px-4 pt-16 pb-28 grid lg:grid-cols-[1fr_auto] gap-12 items-center">
            <div>
                <div class="h-px w-16 bg-[#C89B3C] mb-3"></div>
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-5 fade-up">
                    Sekolah Menengah Kejuruan &middot; Sistem Informasi Akademik &amp; Media Sekolah Resmi
                </p>

                <h1 class="font-display text-4xl sm:text-5xl font-bold text-[#241512] leading-[1.1] mb-6 fade-up"
                    style="animation-delay:.08s">
                    Di Sini, Setiap Keterampilan
                    <span class="italic font-medium text-[#6B1220]">Punya Catatannya Sendiri.</span>
                </h1>

                <p class="text-[#241512]/70 max-w-lg leading-relaxed mb-8 fade-up" style="animation-delay:.16s">
                    Dari kelas praktik hingga uji kompetensi keahlian, kami menyiapkan lulusan yang
                    siap kerja, siap wirausaha, dan siap melanjutkan pendidikan tinggi. SMK Bisa!
                </p>

                <div class="flex flex-wrap gap-3 fade-up" style="animation-delay:.24s">
                    <a href="#"
                        class="bg-[#6B1220] text-[#FBF6EE] text-sm font-semibold px-6 py-3 rounded-full hover:bg-[#8C1F2E] transition">
                        Lihat Jalur PPDB
                    </a>
                    <a href="#program"
                        class="border border-[#241512]/20 text-[#241512] text-sm font-semibold px-6 py-3 rounded-full hover:border-[#6B1220] hover:text-[#6B1220] transition">
                        Jelajahi Program Keahlian
                    </a>
                </div>
            </div>

            {{-- Kartu foto crossfade + cap sekolah menumpang di sudutnya --}}
            <div class="relative w-full max-w-sm mx-auto lg:mx-0 fade-up" style="animation-delay:.3s">
                <div
                    class="relative w-full aspect-[4/5] sm:aspect-square lg:w-80 lg:h-96 rounded-3xl overflow-hidden border-2 border-[#C89B3C]/40 shadow-[0_20px_50px_-15px_rgba(74,13,23,0.3)]">
                    @foreach (['images/hero/praktik-siswa.jpg', 'images/hero/uji-kompetensi.jpg', 'images/hero/kunjungan-industri.jpg', 'images/hero/belajar.jpg', 'images/hero/meja-bangku.jpg', 'images/hero/praktik-siswa.jpg', 'images/hero/ruang-kelas.jpg', 'images/hero/suasana-kelas.jpg'] as $i => $foto)
                        <img src="{{ asset($foto) }}" alt="Kegiatan siswa SMK"
                            class="hero-slide absolute inset-0 w-full h-full object-cover"
                            style="animation-delay: {{ $i * 4 }}s" />
                    @endforeach

                    <div class="absolute inset-0 bg-gradient-to-t from-[#4A0D17]/40 via-transparent to-transparent">
                    </div>
                </div>

                <x-school-seal class="w-28 h-28 absolute -bottom-8 -left-8 z-10" />
            </div>
        </div>

        {{-- Kartu ledger — menumpang di atas batas hero, seperti dokumen fisik --}}
        <div class="max-w-5xl mx-auto px-4 -mt-14 relative z-10">
            <div
                class="bg-[#FBF6EE] border-2 border-[#C89B3C]/40 rounded-2xl shadow-[0_20px_50px_-15px_rgba(74,13,23,0.25)] grid grid-cols-2 sm:grid-cols-4 divide-x divide-[#C89B3C]/25">
                @foreach ([['label' => 'Program Keahlian', 'value' => '6'], ['label' => 'Mitra Industri', 'value' => '42'], ['label' => 'Lulusan Terserap Kerja', 'value' => '89%'], ['label' => 'Akreditasi', 'value' => 'A']] as $stat)
                    <div class="px-4 py-6 text-center">
                        <p class="font-mono text-2xl sm:text-3xl font-semibold text-[#6B1220]">{{ $stat['value'] }}</p>
                        <p class="text-xs text-[#241512]/50 mt-1 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- PROGRAM KEAHLIAN — CAROUSEL --}}
    <section id="program" class="max-w-6xl mx-auto px-4 pt-24 pb-20">
        <div class="mb-10 max-w-xl">
            <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Program Keahlian</p>
            <h2 class="font-display text-3xl font-bold text-[#241512]">Kompetensi yang Disiapkan untuk Dunia Kerja</h2>
        </div>

        <x-carousel>
            @foreach ([['kode' => 'TKJ', 'nama' => 'Teknik Komputer & Jaringan', 'desc' => 'Instalasi jaringan, administrasi server, dan keamanan siber dasar.'], ['kode' => 'RPL', 'nama' => 'Rekayasa Perangkat Lunak', 'desc' => 'Pengembangan aplikasi web & mobile, basis data, dan logika pemrograman.'], ['kode' => 'MM', 'nama' => 'Multimedia', 'desc' => 'Desain grafis, videografi, dan produksi konten digital.'], ['kode' => 'TBSM', 'nama' => 'Teknik & Bisnis Sepeda Motor', 'desc' => 'Perawatan, perbaikan, dan manajemen bengkel sepeda motor.'], ['kode' => 'AKL', 'nama' => 'Akuntansi & Keuangan Lembaga', 'desc' => 'Pencatatan keuangan, perpajakan, dan sistem akuntansi digital.'], ['kode' => 'TB', 'nama' => 'Tata Boga', 'desc' => 'Pengolahan makanan, manajemen dapur, dan kewirausahaan kuliner.']] as $prodi)
                <div
                    class="snap-start shrink-0 w-72 bg-[#FBF6EE] border border-[#C89B3C]/30 rounded-2xl p-6 hover:border-[#C89B3C] hover:shadow-md transition">
                    <div class="font-mono text-xs tracking-widest text-[#8C1F2E] mb-3">{{ $prodi['kode'] }}</div>
                    <h3 class="font-display font-semibold text-lg text-[#241512] mb-2 leading-snug">{{ $prodi['nama'] }}
                    </h3>
                    <p class="text-sm text-[#241512]/60 leading-relaxed">{{ $prodi['desc'] }}</p>
                </div>
            @endforeach
        </x-carousel>
    </section>

    {{-- MITRA INDUSTRI --}}
    <section class="py-16 border-y border-[#C89B3C]/20 bg-[#F3EADA]/50">
        <div class="max-w-6xl mx-auto px-4 mb-8 text-center">
            <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Kerja Sama</p>
            <h2 class="font-display text-2xl font-bold text-[#241512]">Didukung oleh Mitra Industri</h2>
        </div>

        <x-marquee :speed="50">
            @foreach (['PT Sinar Teknologi Nusantara', 'PT Karya Mandiri Otomotif', 'Grup Boga Cipta Rasa', 'PT Cakra Digital Kreasi', 'Koperasi Jasa Keuangan Sejahtera', 'PT Mitra Logistik Bali'] as $mitra)
                <div
                    class="w-56 h-20 shrink-0 flex items-center justify-center px-6 border border-[#C89B3C]/20 rounded-xl bg-[#FBF6EE]">
                    <span
                        class="text-sm font-semibold text-[#241512]/40 hover:text-[#6B1220] transition text-center leading-snug">
                        {{ $mitra }}
                    </span>
                </div>
            @endforeach
        </x-marquee>
    </section>

    {{-- KEUNGGULAN --}}
    <section class="bg-[#F3EADA] py-20">
        <div class="max-w-6xl mx-auto px-4">
            <div class="mb-12 max-w-xl">
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Mengapa Kami</p>
                <h2 class="font-display text-3xl font-bold text-[#241512]">Yang Membedakan Kami</h2>
            </div>

            <div class="divide-y divide-[#C89B3C]/30 border-t border-b border-[#C89B3C]/30">
                @foreach ([['judul' => 'Kurikulum Link and Match dengan Industri', 'desc' => 'Disusun bersama mitra industri agar kompetensi lulusan sesuai kebutuhan dunia kerja terkini.'], ['judul' => 'Guru Produktif Bersertifikasi Kompetensi', 'desc' => 'Setiap pengajar kejuruan memegang sertifikat kompetensi sesuai bidang keahliannya.'], ['judul' => 'Rapor Digital Real-time', 'desc' => 'Orang tua dapat memantau nilai, kehadiran, dan perkembangan anak langsung dari sistem kami — kapan saja.'], ['judul' => 'Praktik Kerja Lapangan di Perusahaan Mitra', 'desc' => 'Setiap siswa menjalani PKL langsung di industri sebelum kelulusan, bukan sekadar simulasi.']] as $item)
                    <div class="grid sm:grid-cols-[1fr_2fr] gap-3 py-6 border-l-2 border-[#6B1220] pl-5">
                        <h3 class="font-display font-semibold text-lg text-[#241512]">{{ $item['judul'] }}</h3>
                        <p class="text-sm text-[#241512]/65 leading-relaxed">{{ $item['desc'] }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    {{-- GALERI KEGIATAN — MARQUEE --}}
    <section class="py-20 overflow-hidden">
        <div class="max-w-6xl mx-auto px-4 mb-10">
            <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Dokumentasi</p>
            <h2 class="font-display text-3xl font-bold text-[#241512]">Momen &amp; Aktivitas Sekolah</h2>
        </div>

        <x-marquee :speed="85">
            @foreach ([['src' => 'images/gallery/belajar.jpg', 'caption' => 'Praktik Kerja Lapangan'], ['src' => 'images/gallery/uji-kompetensi.jpg', 'caption' => 'Uji Kompetensi Keahlian'], ['src' => 'images/gallery/meja-bangku.jpg', 'caption' => 'Lomba Kompetensi Siswa'], ['src' => 'images/gallery/kunjungan-industri.jpg', 'caption' => 'Kunjungan Industri'], ['src' => 'images/gallery/praktik-siswa.jpg', 'caption' => 'Ekstrakurikuler'], ['src' => 'images/gallery/ruang-kelas.jpg', 'caption' => 'Upacara Bendera'], ['src' => 'images/gallery/suasana-kelas.jpg', 'caption' => 'Wisuda Kelulusan']] as $foto)
                <figure class="w-72 shrink-0">
                    <img src="{{ asset($foto['src']) }}" alt="{{ $foto['caption'] }}" loading="lazy"
                        class="w-72 h-48 object-cover rounded-2xl border border-[#C89B3C]/25" />
                    <figcaption class="mt-2 text-xs font-mono uppercase tracking-wide text-[#241512]/50">
                        {{ $foto['caption'] }}
                    </figcaption>
                </figure>
            @endforeach
        </x-marquee>
    </section>

    {{-- TESTIMONI --}}
    <section class="bg-[#4A0D17] py-24">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <span class="font-display text-6xl text-[#C89B3C]/50 leading-none">&ldquo;</span>
            <p class="font-display text-2xl sm:text-3xl text-[#FBF6EE] leading-snug -mt-4 mb-6">
                Berkat pengalaman PKL di bengkel resmi, saya langsung direkrut
                sebagai teknisi sebelum wisuda.
            </p>
            <p class="font-mono text-xs tracking-widest uppercase text-[#E4C878]">
                Dimas &mdash; Alumni Program Keahlian TBSM
            </p>
        </div>
    </section>

    {{-- BLOG --}}
    <section class="max-w-6xl mx-auto px-4 py-20">
        <div class="flex items-end justify-between mb-8">
            <div>
                <p class="font-mono text-xs tracking-[0.2em] uppercase text-[#8C1F2E] mb-2">Blog Sekolah</p>
                <h2 class="font-display text-3xl font-bold text-[#241512]">Kabar &amp; Cerita dari Sekolah Kami</h2>
            </div>
            <a href="{{ route('blog.index') }}"
                class="text-sm font-semibold text-[#6B1220] hover:underline whitespace-nowrap">
                Lihat semua &rarr;
            </a>
        </div>

        <livewire:blog.latest-posts />
    </section>

    {{-- CTA BANNER --}}
    <section class="bg-gradient-to-br from-[#6B1220] to-[#4A0D17] py-20">
        <div class="max-w-3xl mx-auto px-4 text-center">
            <h2 class="font-display text-3xl sm:text-4xl font-bold text-[#FBF6EE] mb-4">
                Siap Menjadi Bagian dari Keluarga Besar Kami?
            </h2>
            <p class="text-[#FBF6EE]/70 mb-8">
                Pendaftaran Peserta Didik Baru (PPDB) dibuka setiap tahun ajaran. Tim kami siap membantu proses
                pendaftaran dari awal hingga selesai.
            </p>
            <div class="flex flex-wrap justify-center gap-3">
                <a href="#"
                    class="bg-[#C89B3C] text-[#4A0D17] text-sm font-semibold px-6 py-3 rounded-full hover:bg-[#E4C878] transition">
                    Daftar Sekarang
                </a>
                <a href="#"
                    class="border border-[#FBF6EE]/30 text-[#FBF6EE] text-sm font-semibold px-6 py-3 rounded-full hover:border-[#FBF6EE] transition">
                    Hubungi Kami
                </a>
            </div>
        </div>
    </section>
</x-layouts.guest>
