<link href="{{ asset('css/footer.css') }}" rel="stylesheet">
<footer class="footer">
    <div class="footer__container">
        <div class="footer__left">
            <img src="{{ asset('assets/img/690969f1e0df8.png') }}" class="footer__logo" alt="Logo">
            <div class="footer__copyright">
                © 2025 Rasanya Lelang Karya. All rights reserved.
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
                <a href="mailto:rasanyalelangkarya@gmail.com" class="footer__icon">
                    <i class="fas fa-envelope"></i>
                </a>
                <a href="https://api.whatsapp.com/send/?phone=6285742829289" class="footer__icon">
                    <i class="fab fa-whatsapp"></i>
                </a>
                <a href="{{ $social['instagram'] ?? '#' }}" target="_blank" class="footer__icon"><i
                        class="fab fa-instagram"></i></a>
                <a href="https://youtube.com/{{ $social['youtube'] ?? '#' }}" target="_blank" class="footer__icon"><i
                        class="fab fa-youtube"></i></a>
                <a href="{{ $social['facebook'] ?? '#' }}" target="_blank" class="footer__icon"><i
                        class="fab fa-facebook"></i></a>
                <a href="{{ $social['linkedin'] ?? '#' }}" target="_blank" class="footer__icon"><i
                        class="fab fa-linkedin"></i></a>
            </div>
        </div>
    </div>
</footer>
