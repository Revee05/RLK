<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
<footer class="footer">
    <div class="footer__container">
        <div class="footer__left">
            <img src="{{ asset('uploads/logos/'.$setting->logo) }}" class="footer__logo" alt="Logo">
            <div class="footer__copyright">
                © {{ date('Y') }} Rasanya Lelang Karya. All rights reserved.
            </div>
            <div class="footer__links">
                <a href="#">Terms of Service</a> | <a href="#">Privacy Policy</a>
            </div>
        </div>
        <div class="footer__center">
            <div class="footer__section">
                <div class="footer__title">Tentang</div>
                <a href="#">Tentang Perusahaan</a>
                <a href="#">Tentang Tim Kerja</a>
            </div>
            <div class="footer__section">
                <div class="footer__title">Panduan</div>
                <a href="#">Panduan Beli</a>
                <a href="#">Panduan Jual</a>
            </div>
        </div>
        <div class="footer__right">
            <div class="footer__title">Ikuti kami</div>
            <div class="footer__social">
                @if(!empty($setting->email))
                    <a href="mailto:{{ $setting->email }}" class="footer__icon">
                        <i class="fas fa-envelope"></i>
                    </a>
                @endif
                @if(!empty($setting->wa))
                    <a href="https://api.whatsapp.com/send/?phone={{ $setting->wa }}" class="footer__icon">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                @endif
                @if(!empty($setting->social['instagram']))
                    <a href="{{ $setting->social['instagram'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-instagram"></i>
                    </a>
                @endif
                @if(!empty($setting->social['youtube']))
                    <a href="{{ $setting->social['youtube'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-youtube"></i>
                    </a>
                @endif
                @if(!empty($setting->social['facebook']))
                    <a href="{{ $setting->social['facebook'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-facebook"></i>
                    </a>
                @endif
                @if(!empty($setting->social['tiktok']))
                    <a href="{{ $setting->social['tiktok'] }}" target="_blank" class="footer__icon">
                        <i class="fab fa-tiktok"></i>
                    </a>
                @endif
                @if(!empty($setting->social['threads']))
                    <a href="{{ $setting->social['threads'] }}" target="_blank" class="footer__icon" title="Threads">
                        <img src="{{ asset('assets/img/threads.png') }}" alt="Threads" style="width:20px; height:20px; object-fit:contain;">
                    </a>
                @endif
                @if(!empty($setting->social['twitter']))
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