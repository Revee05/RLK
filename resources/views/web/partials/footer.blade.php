<footer class="py-5" id="footer">
    <div class="section">
        <div class="container">
            <div class="d-flex justify-content-between footer-mobile">
                <div class="d-block">
                    <img src="{{asset('assets/img/logo-lelang.png')}}">
                    <p class="m-0 text-center text-white">Hak Cipta © 2023 Lelang. All rights reserved.</p>
                </div>
                <div class="d-block">
                     <p class="m-0 text-center text-white py-1">Contact Us</p>
                     <div class="d-inline-flex py-1 text-center w-100 justify-content-center">
                         
                        <a href="mailto:rasanyalelangkarya@gmail.com" class="d-block text-decoration-none footer-icon-top">
                            <i class="fas fa-envelope"></i>
                        </a>
                        <a href="https://api.whatsapp.com/send/?phone=6285742829289" class="d-block text-decoration-none footer-icon-top">
                            <i class="fab fa-whatsapp"></i>
                        </a>
                        
                     </div>
                     <p class="m-0 text-center text-white py-1">Official Channel</p>
                     <div class="d-inline-flex text-center w-100 justify-content-center">
                         
                        <a href="https://www.instagram.com/{{$social['instagram'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                            <i class="fab fa-instagram"></i>
                        </a>
                        <a href="{{$social['website'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                            <i class="fas fa-globe"></i>
                        </a>
                        <a href="https://twitter.com/{{$social['twitter'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                            <i class="fab fa-twitter"></i>
                        </a>
                        <a href="https://tiktok.com/{{$social['tiktok'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                            <i class="fab fa-tiktok"></i>
                        </a>
                        <a href="https://youtube.com/{{$social['youtube'] ?? '#'}}" class="d-block text-decoration-none footer-icon-bottom">
                            <i class="fab fa-youtube"></i>
                        </a>
                        
                        
                     </div>
                </div>
            </div>
        </div>
        
    </div>
</footer>