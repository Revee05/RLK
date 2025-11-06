<footer class="main-footer">
    <div class="container footer-container">

        <div class="footer-left-block">
            <a href="{{ route('home') }}">
                <img src="{{ asset('uploads/logos/'.$setting->logo) }}" class="footer-logo" alt="Logo">
            </a>
            <p class="footer-copyright">
                © {{ date('Y') }} Rasanya Lelang Karya. All rights reserved.
            </p>
            <div class="footer-legal-links">
                <a href="#">Terms of Service</a> |
                <a href="#">Privacy Policy</a>
            </div>
        </div>

        <div class="footer-right-block">
            <div class="row">
                
                <div class="col-md-4">
                    <h5 class="footer-heading">Tentang</h5>
                    <ul class="footer-links">
                        <li><a href="#">Tentang Perusahaan</a></li>
                        <li><a href="#">Tentang Tim Kerja</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5 class="footer-heading">Panduan</h5>
                    <ul class="footer-links">
                        <li><a href="#">Panduan Beli</a></li>
                        <li><a href="#">Panduan Jual</a></li>
                    </ul>
                </div>

                <div class="col-md-4">
                    <h5 class="footer-heading">Ikuti kami</h5>
                    <div class="footer-social-icons">
                        <a href="#" aria-label="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" aria-label="LinkedIn"><i class="fab fa-linkedin-in"></i></a>
                        <a href="#" aria-label="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" aria-label="Instagram"><i class="fab fa-instagram"></i></a>
                    </div>
                </div>
                
            </div> 
        </div> 
    </div> 
</footer>