@extends('web.partials.layout')

@section('title', 'Tentang Kami - Rasanya Lelang Karya')

@section('content')
    <!-- Hero Section -->
    <section class="py-4">
        <div class="container tentang-container">
            <div class="row align-items-center">
                <div class="col-12 text-center pt-5">
                    <h1 class="display-5 fw-bold">
                        Tentang <span class="tentang-brand-color">Rasanya Lelang Karya</span>
                    </h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Introduction Section -->
    <section class="py-4 mb-5">
        <div class="container tentang-container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="pe-lg-4">
                        <p class="mb-4 tentang-text-justify tentang-body-typography">
                            Rasanya Lelang Karya merupakan platform lelang karya seni bagi seniman pemula. Bagian unit usaha
                            dari PT Rasa Kreasi Karya ini berfokus untuk mewadahi para penikmat seni untuk mengoleksi karya.
                            Rasanya Lelang Karya menjadi wadah yang mempertemukan para pemula dengan para pecinta karya seni
                            yang ingin menjadi kolektor muda.
                        </p>
                        <p class="mb-4 tentang-text-justify tentang-body-typography">
                            Di samping menyelenggarakan lelang, unit ini turut memperkenalkan beragam produk merchandise
                            sebagai bentuk dukungan terhadap pertumbuhan kreativitas baru sekaligus membuka ruang bagi
                            publik untuk membawa pulang karya seni dalam bentuk yang lebih personal.
                        </p>
                        <p class="mb-3 tentang-text-justify tentang-body-typography">
                            Rasanya Lelang Karya pertama kali diinisiasi oleh Amanda Rizqyana yang bekerjasama dengan Arief
                            Hadinata dari Hokgstudio yang hampir 2 dekade berkecimpung di dunia kesenian. Berbekal
                            pengetahuan
                            dan pengalaman dalam kegiatan jual beli karya, Rasanya Lelang Karya dihadirkan untuk
                            meningkatkan
                            produktivitas dan kualitas karya seniman muda serta mempermudah proses apresiasi karya seni di
                            Indonesia.
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <img src="{{ asset('assets/img/tentang/tentang-1.webp') }}" alt="RLK Artwork"
                        class="img-fluid rounded shadow-sm mb-3 tentang-img-full tentang-main-photo">
                </div>
            </div>
        </div>

        <!-- Small images row (moved outside the image column) -->
        <div class="container tentang-small-wider mb-5">
            <div class="row g-3 mt-2">
                <div class="col-6">
                    <div class="small-image-wrap">
                        <img src="{{ asset('assets/img/tentang/tentang-2.webp') }}" alt="Studio Workshop"
                            class="img-fluid rounded shadow-sm tentang-img-full">
                    </div>
                </div>
                <div class="col-6">
                    <div class="small-image-wrap">
                        <img src="{{ asset('assets/img/tentang/tentang-3.webp') }}" alt="Artist at Work"
                            class="img-fluid rounded shadow-sm tentang-img-full">
                    </div>
                </div>
            </div>
        </div>

        <div class="row mt-4 tentang-container">
            <div class="col-12">
                <p class="tentang-text-justify tentang-body-typography">
                    Dalam prosesnya, Rasanya Lelang Karya membantu Anda untuk memilih karya seni yang cocok untuk kebutuhan
                    estetika dan hobi koleksi karya. Kami memastikan bahwa Anda dapat dengan mudah melakukan transaksi
                    dengan metode yang aman dan terpercaya. Selain itu, dalam proses penyerahan karya, kami akan mengawal
                    pengiriman karya secara langsung ke tangan kolektor dan memastikan karya diterima dengan aman dan sesuai
                    spesifikasi dan kelengkapan.
                </p>
            </div>
        </div>
        </div>
    </section>

    <!-- Vision & Mission Section -->
    <section class="py-5 mt-3">
        <div class="container tentang-container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">
                    Tim <span class="tentang-brand-color">Rasanya Lelang Karya</span>
                </h2>
                <p class="text-center tentang-body-typography">
                    Kombinasi Visi Tentang Nilai Keberagaman Dan Kreativitas<br>
                    Tercakup Dalam Di Dalam Indah (Visi), Yang Memberikan Dasar Pengembangan
                </p>
            </div>

            <div class="row g-4">
                <!-- Vision/Mission Cards - Placeholder for team members -->
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded mb-3 tentang-team-placeholder">
                                <span class="text-muted">Anjar Andriani</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded mb-3 tentang-team-placeholder">
                                <span class="text-muted">Azzizatul Maghfiroh</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded mb-3 tentang-team-placeholder">
                                <span class="text-muted">Bahtiar Kurniadi</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded mb-3 tentang-team-placeholder">
                                <span class="text-muted">Muhammad Ibnu A.</span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="card h-100 border-0 shadow-sm">
                        <div class="card-body text-center p-4">
                            <div class="bg-light rounded mb-3 tentang-team-placeholder">
                                <span class="text-muted">Noor Thanif</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Members Section -->
    <section class="py-5">
        <div class="container tentang-container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">
                    <span class="tentang-brand-color">Rasanya Lelang Karya</span> Melayani
                </h2>
            </div>

            <div class="row justify-content-center g-4">
                <div class="col-md-4 col-lg-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <img src="https://s3-alpha-sig.figma.com/img/f83c/0fa0/c2bf3a58da8e5ba2f66ee8fa653b6cfb?Expires=1736726400&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=VYhF4dSE2LJwVgCJLpQNT6kl9AJZdkuWFV~fXv5Z8JzS3TfwfVsQkz5cPqKMkZuQE-hHzkPRUJNmYdqEqeBEAJ-MgBwgTjjlJ0CPSIjZE~xOxQdOB8B9ZqG0XbfY~wnF9O5BmE3GyEVSk3JEhUPSdgZt8sXLVOqPy6KogjW5-TmQpVH9Q~vSPNgTqcEJb5lXGS8L-NiaNLXApA7DMTUmPPFcJ1h3yyxlQgwGMpf-nWrVYoA44Xg3Qd2RQoSVSmzYWZ~IEKl5kRKVJSqBdT7C-MJ2e7~kKs43wYQwpzR6Mxk0mD~bk0cPcFdRtMrVOMv16CsNJFHUn9Sop9m3vg__"
                                alt="Aji Azman" class="rounded-circle mb-3 tentang-team-photo">
                        </div>
                        <h5 class="fw-bold mb-2 tentang-brand-color">Aji Azman</h5>
                        <p class="small text-muted mb-0">
                            Dengan saya Kami Bekerja Sama Dengan Kelembagaan Untuk Menjaga Warisan Karyaa Asli Mereka Lelang
                            Karya
                        </p>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <img src="https://s3-alpha-sig.figma.com/img/2b15/f8a0/7bf8d9f99c06c03f64ea02d97c44fe84?Expires=1736726400&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=aSf9gExGnhBzMmTlh1UkR5K7n7g~DWZwvhOMUtRH19rG1u1xGWLRWqvQwlYNAZN5GLPJP14ujjJDEy5~dEtqvpaSRNglxOD1~AczwJNzGNKpDG4a8vp4rJfONnrRYJPGrdjWgiFdHWXwF7RL0z3eLFcgGdO8fhQwAKIUEsgJDfaLrIzzjMkN4lbjm6xrI8vEPEuMGfCpb3CDDG4mMkdwPr8UvUZfEQFCE0tIxqhFNGNXlW~xjcXvOwZjCQG4rkbXzEHqPCTl4C-6GcI2WsDO0Vn3bIkXrYEw8iKPPpVOqNv64MfFKK4xMHLVUa1HYJzUkFxS-EB~cOiPf8lHVS7DPA__"
                                alt="Bang Jo Natsu" class="rounded-circle mb-3 tentang-team-photo">
                        </div>
                        <h5 class="fw-bold mb-2 tentang-brand-color">Bang Jo Natsu</h5>
                        <p class="small text-muted mb-0">
                            Penjualan Oleh Kota Dan Rasanya Lelang Karya & Bisa Mendapatkan Hasil Untuk Karya Seni Mereka
                            Karya Seni Dia
                        </p>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3">
                    <div class="text-center">
                        <div class="mb-3">
                            <img src="https://s3-alpha-sig.figma.com/img/7d95/a1fc/9b84e8da0e85d2cddb14e15fe5ffc20d?Expires=1736726400&Key-Pair-Id=APKAQ4GOSFWCVNEHN3O4&Signature=rKvgcePWe-Mn3gfBDtX8gsCWr8K9JUW7hljZStcZCZvIHe2SsYSXOEWILfPT6CPNrMSN~EfYqzSR9bqZnPf-emGjZk1bCXxOxRHMZhSRyX~kWFY4jU~KlTYZVEhxLDYKD1VRl5Q1hZ4Bc9qNBqwqgGjJJMywBkGi6JOwNNaKG7j2N~6hy8r8V-c00Q5xLPaGvKBd-o2ZwvtfJ0wnhczGpjpTrL5s4f0W3h~BPYyQ50VHD8Jfc5qfk54YItJIRZ0c9BXMLYiNNe5TUzPwbAWStFWWTVUxL6A6TJFSlD~Yg-sjAb8HWY~CQxhAOOc2SX3oRY2S7pKJLqiYD8ZWn3qkXw__"
                                alt="Bahtiar Lumansay" class="rounded-circle mb-3 tentang-team-photo">
                        </div>
                        <h5 class="fw-bold mb-2 tentang-brand-color">Bahtiar Lumansay</h5>
                        <p class="small text-muted mb-0">
                            Membuka Menghargai Dari Pemeriksaan Membuktikan Di Dalam Dan Seni Member Adaam Lelang Karya Yang
                            Karya
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section class="py-5 bg-light">
        <div class="container tentang-container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">
                    Aktivitas <span class="tentang-brand-color">Rasanya Lelang Karya</span>
                </h2>
            </div>

            <div class="accordion" id="activitiesAccordion">
                <!-- Jual Karya -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header" id="headingOne">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Jual Karya
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            Karya Seni Menawarkan Hewan Dan Kota Para Juga Produk Kepada, Antineri Untuk Pembayaran Adanya
                            Semua Alat Penjualan Dari Umum Akhirnya Dari. Agar Rasanya Lelang Karya Dari Lelang Produk
                            Pembayaran Pembayaran. Sistem Jadi Mampu Karya Mereka.
                        </div>
                    </div>
                </div>

                <!-- Sosialisasi Dan Promosi -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header" id="headingTwo">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                            Sosialisasi Dan Promosi
                        </button>
                    </h2>
                    <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            Konten sosialisasi dan promosi akan ditampilkan di sini.
                        </div>
                    </div>
                </div>

                <!-- Melayani Karya -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Melayani Karya
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            Konten melayani karya akan ditampilkan di sini.
                        </div>
                    </div>
                </div>

                <!-- Penyerahan Karya Ke Konsumen/Pemulung Lelang -->
                <div class="accordion-item border-0 shadow-sm">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Penyerahan Karya Ke Konsumen/Pemulung Lelang
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            Konten penyerahan karya akan ditampilkan di sini.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="py-5">
        <div class="container tentang-container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h3 class="fw-bold mb-4">
                        Bergabunglah dengan <span class="tentang-brand-color">Rasanya Lelang Karya</span> hari ini dan
                        rasakan
                        pengalaman berbelanja yang berbeda!
                    </h3>
                    <a href="{{ route('register') }}" class="btn btn-lg px-5 py-3 fw-bold text-white tentang-cta-btn">
                        Daftar Sekarang
                    </a>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tentang/tentang_styles.css') }}">
@endpush
