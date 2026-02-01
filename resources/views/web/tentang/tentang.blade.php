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
    <section class="pt-5 mb-5">
        <div class="container tentang-container">
            <div class="row g-4">
                <div class="col-md-6">
                    <div class="pe-lg-4">
                        <p class="mb-4 tentang-text-justify tentang-responsive-fs">
                            Rasanya Lelang Karya merupakan platform lelang karya seni bagi seniman pemula. Bagian unit usaha
                            dari PT Rasa Kreasi Karya ini berfokus untuk mewadahi para penikmat seni untuk mengoleksi karya.
                            Rasanya Lelang Karya menjadi wadah yang mempertemukan para pemula dengan para pecinta karya seni
                            yang ingin menjadi kolektor muda.
                        </p>
                        <p class="mb-4 tentang-text-justify tentang-responsive-fs">
                            Di samping menyelenggarakan lelang, unit ini turut memperkenalkan beragam produk merchandise
                            sebagai bentuk dukungan terhadap pertumbuhan kreativitas baru sekaligus membuka ruang bagi
                            publik untuk membawa pulang karya seni dalam bentuk yang lebih personal.
                        </p>
                        <p class="mb-3 tentang-text-justify tentang-responsive-fs">
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
                <p class="tentang-text-justify tentang-responsive-fs">
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
    <section class="py-5 mb-3">
        <div class="container tentang-container">
            <div class="text-center mb-3">
                <h2 class="display-5 fw-bold">
                    Tim <span class="tentang-brand-color">Rasanya Lelang Karya</span>
                </h2>
                <p class="text-center tentang-responsive-fs">
                    Kekuatan kami terletak pada kebersamaan dan kolaborasi.
                    Temukan kisah di balik setiap individu yang berkontribusi dalam perjalanan kami.
                </p>
            </div>

            <div class="row g-4 justify-content-start">
                @forelse($teamMembers as $member)
                    <div class="col-6 col-md-4">
                        <div class="team-card h-100">
                            <div class="team-card-img-wrapper">
                                <img src="{{ $member->avatar ? asset($member->avatar) : asset('assets/img/default.jpg') }}"
                                    alt="{{ $member->name }}">
                            </div>
                            <div class="card-body">
                                <h3 class="team-card-name">{{ $member->name }}</h3>
                                <p class="team-card-role">{{ $member->role }}</p>
                                <div class="team-card-socials">
                                    @if ($member->email)
                                        <a href="mailto:{{ $member->email }}" aria-label="Email"><i
                                                class="fas fa-envelope"></i></a>
                                    @endif
                                    @if ($member->instagram)
                                        <a href="{{ $member->instagram }}" target="_blank" aria-label="Instagram"><i
                                                class="fab fa-instagram"></i></a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <p class="text-center mb-0">Data tim belum tersedia.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <!-- Team Members Section -->
    <section class="py-5 mb-2">
        <div class="container tentang-container">
            <div class="text-center mb-5">
                <h2 class="display-5 fw-bold">
                    <span class="tentang-brand-color">Rasanya Lelang Karya</span> Melayani
                </h2>
            </div>

            <div class="row justify-content-center g-4 mt-5">
                <div class="col-md-4 col-lg-3 mb-sm-down-5">
                    <div class="card h-100 service-card text-center rounded-3 shadow-md px-3 py-1">
                        <img src="{{ asset('assets/img/tentang/tentang-1.webp') }}" alt="Art Auction"
                            class="about-avatar-abs">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2 tentang-brand-color">Art Auction</h5>
                            <hr class="tentang-hr-cyan">
                            <p class="mb-0">
                                Lelang karya seni secara daring untuk para seniman pemula dengan proses transaksi yang
                                mudah.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3 mb-sm-down-5">
                    <div class="card h-100 service-card text-center rounded-3 shadow-md px-3 py-1">
                        <img src="{{ asset('assets/img/tentang/tentang-2.webp') }}" alt="Buy it Now"
                            class="about-avatar-abs">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2 tentang-brand-color">Buy it Now</h5>
                            <hr class="tentang-hr-cyan">
                            <p class="mb-0">
                                Proses jual beli karya seni bagi seminan pemula secara daring.
                            </p>
                        </div>
                    </div>
                </div>

                <div class="col-md-4 col-lg-3 mb-sm-down-5">
                    <div class="card h-100 service-card text-center rounded-3 shadow-md px-1 py-2">
                        <img src="{{ asset('assets/img/tentang/tentang-3.webp') }}" alt="Pricing Consultant"
                            class="about-avatar-abs">
                        <div class="card-body">
                            <h5 class="fw-bold mb-2 tentang-brand-color">Pricing Consultant</h5>
                            <hr class="tentang-hr-cyan">
                            <p class="mb-0">
                                Membantu menghitung atau menafsir harga jual karya seni melalui sistem lelang untuk seniman
                                pemula.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Activities Section -->
    <section class="pt-5 pb-1">
        <div class="container tentang-container">
            <div class="text-center mb-4">
                <h2 class="display-5 fw-bold">
                    Aktivitas <span class="tentang-brand-color">Rasanya Lelang Karya</span>
                </h2>
            </div>

            <div class="accordion" id="activitiesAccordion">
                <!-- Survei Karya -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header text-center" id="headingOne">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseOne" aria-expanded="false" aria-controls="collapseOne">
                            Survei Karya
                        </button>
                    </h2>
                    <div id="collapseOne" class="accordion-collapse collapse" aria-labelledby="headingOne"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            Kami rutin menguniungi pameran seni untuk mendapatkan kabar terbaru dan masukan dari perupa
                            maupun kolekior terkait platform kami, peluang apresiasi, dan selera pasar terhadap karya seni.
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
                            kami juga membuka booth promosi di beberapa event untuk mempromosikan program dan karya-karya
                            yang sedang kami lelang kepada publik.
                        </div>
                    </div>
                </div>

                <!-- Melelang Karya -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header" id="headingFour">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseFour" aria-expanded="false" aria-controls="collapseFour">
                            Melelang Karya
                        </button>
                    </h2>
                    <div id="collapseFour" class="accordion-collapse collapse" aria-labelledby="headingFour"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            kami memposting karya terkurasi untuk dilelang via instagram dan website kami, kami
                            mempersilahkan para audience untuk melakukan bidding pada karya yang disukai secara terbuka
                            dengan durasi open bid 1 minggu untuk setiap karya. </div>
                    </div>
                </div>

                <!-- Penyerahan Karya ke kolektor/pemenang Lelang -->
                <div class="accordion-item border-0 mb-3 shadow-sm">
                    <h2 class="accordion-header" id="headingThree">
                        <button class="accordion-button collapsed fw-bold" type="button" data-bs-toggle="collapse"
                            data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                            Penyerahan Karya ke kolektor/pemenang Lelang
                        </button>
                    </h2>
                    <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree"
                        data-bs-parent="#activitiesAccordion">
                        <div class="accordion-body">
                            kami mengawal untuk pengantaran setiap karya yang terjual dan memastikan karya tersebut sampai
                            di tangan pemenang lelang dengan aman.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="tentang-cta-section">
        <div class="container tentang-container">
            <div class="tentang-cta-card">
                <h2 class="headline">Butuh Respon Cepat</h2>
                <p class="subheadline">
                    Hubungi admin kami langsung melalui via WhatsApp untuk pertanyaan mendesak atau diskusi langsung terkait
                    lelang maupun produk merchandise.
                </p>
                <a href="https://api.whatsapp.com/send/?phone=6282324730989&text=Halo%20admin%20RLK%2C%20saya%20ingin%20bertanya%20tentang%20lelang%20dan%20produk%20merchandise."
                    target="_blank" class="btn tentang-cta-btn-white">
                    Hubungi Sekarang
                </a>
            </div>
        </div>
    </section>
@endsection

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/tentang/tentang_styles.css') }}">
@endpush
