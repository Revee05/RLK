{{-- Footer stylesheet for layout and styles --}}
<link href="{{ asset('css/footer.css') }}" rel="stylesheet">

<footer class="footer">
    <div class="footer__container">
        {{-- Footer Left: Logo, copyright, dan legal links --}}
        <div class="footer__left">
            {{-- Logo: sumber di uploads/logos dari $setting->logo --}}
            <img src="{{ asset('uploads/logos/'.$setting->logo) }}" class="footer__logo" alt="Logo">

            {{-- Copyright: tampilkan tahun berjalan --}}
            <div class="footer__copyright">
                © {{ date('Y') }} Rasanya Lelang Karya. All rights reserved.
            </div>

            {{-- Legal / policy links: ubah href jika ada route spesifik --}}
            <div class="footer__links">
                <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms of Service & Privacy Policy</a>
            </div>
        </div>

        {{-- Footer Center: Kolom navigasi singkat (mis. Tentang, Panduan) --}}
        <div class="footer__center">
            {{-- Section: Tentang - links terkait informasi perusahaan/organisasi --}}
            <div class="footer__section">
                <div class="footer__title">Tentang</div>
                <a class="@yield('tentang')" href="{{ route('perusahaan') }}">Tentang Kami</a>
            </div>

            {{-- Section: Panduan - panduan pengguna untuk beli/jual --}}
            <div class="footer__section">
                <div class="footer__title">Panduan</div>
                <a href="{{ route('panduan.index') }}" class="@yield('panduan')">Panduan Jual</a>
                <a href="{{ route('panduan.index') }}" class="@yield('panduan')">Panduan</a>
            </div>
        </div>

        {{-- Footer Right: Ikon sosial dan kontak --}}
        <div class="footer__right">
            <div class="footer__title">Ikuti kami</div>

            {{-- Social icons: tampilkan jika kunci pada $setting ada/tidak kosong
                Dukung: email, wa, instagram, youtube, facebook, tiktok, threads, twitter(X)
                - email: mailto link
                - wa: link API WhatsApp
                - social[]: url eksternal (target _blank)
            --}}
            <div class="footer__social">
                @if(!empty($setting->email))
                    {{-- Email contact --}}
                    <a href="mailto:{{ $setting->email }}" class="footer__icon">
                        <i class="fas fa-envelope"></i>
                    </a>
                @endif

                @if(!empty($setting->wa))
                    {{-- WhatsApp contact (gunakan format internasional tanpa +) --}}
                    <a href="https://api.whatsapp.com/send/?phone={{ $setting->wa }}" class="footer__icon">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif

                @if(!empty($setting->social['instagram']))
                    {{-- Instagram --}}
                    <a href="{{ $setting->social['instagram'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif

                @if(!empty($setting->social['youtube']))
                    {{-- YouTube --}}
                    <a href="{{ $setting->social['youtube'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif

                @if(!empty($setting->social['facebook']))
                    {{-- Facebook --}}
                    <a href="{{ $setting->social['facebook'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-facebook"></i>
                    </a>
                @endif

                @if(!empty($setting->social['tiktok']))
                    {{-- TikTok --}}
                    <a href="{{ $setting->social['tiktok'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-tiktok"></i>
                    </a>
                @endif

                @if(!empty($setting->social['threads']))
                    {{-- Threads (custom image icon) --}}
                    <a href="{{ $setting->social['threads'] }}" target="_blank" class="footer__icon" title="Threads">
                        <img src="{{ asset('assets/img/threads.png') }}" alt="Threads" style="width:20px; height:20px; object-fit:contain;">
                    </a>
                @endif

                @if(!empty($setting->social['twitter']))
                    {{-- X (Twitter) - menggunakan SVG sederhana sebagai ikon --}}
                    <a href="{{ $setting->social['twitter'] }}" target="_blank" class="footer__icon" title="X (Twitter)">
                        <!-- SVG X (Twitter) -->
                        <svg width="20" height="20" viewBox="0 0 512 512" fill="currentColor"
                            xmlns="http://www.w3.org/2000/svg">
                            <path
                                d="M370.6 64H464l-192 192 192 192h-93.4L256 337.9 141.4 448H48l192-192L48 64h93.4L256 174.1 370.6 64z" />
                        </svg>
                    </a>
                @endif
            </div>
        </div>

    </div>
</footer>
