// public/js/home.js

// Fungsi untuk memformat sisa waktu (HH:MM:SS)
function formatTime(timeInSeconds) {
    const hours = Math.floor(timeInSeconds / 3600);
    const minutes = Math.floor((timeInSeconds % 3600) / 60);
    const seconds = timeInSeconds % 60;

    return [
        hours.toString().padStart(2, '0'),
        minutes.toString().padStart(2, '0'),
        seconds.toString().padStart(2, '0')
    ].join(':');
}

// Fungsi untuk mengupdate semua countdown timer
function updateCountdownTimers() {
    const now = new Date();
    
    document.querySelectorAll('.product-countdown').forEach(timerElement => {
        const endTimeStr = timerElement.dataset.endTime;
        if (!endTimeStr) return;

        const endTime = new Date(endTimeStr);
        const timeLeft = Math.round((endTime.getTime() - now.getTime()) / 1000); // Sisa detik

        const displayElement = timerElement.querySelector('.countdown-timer');

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (24 * 60 * 60));
            const secondsLeftToday = timeLeft % (24 * 60 * 60);
            
            let timeString = '';
            if (days > 0) {
                timeString += `${days} hari `;
            }
            timeString += formatTime(secondsLeftToday); // HH:MM:SS

            displayElement.textContent = timeString;
        } else {
            displayElement.textContent = 'Lelang Berakhir';
            displayElement.style.color = '#777';
        }
    });
}


$(document).ready(function() {

    // 1. Inisialisasi Bootstrap Carousel (Untuk Hero Slider)
    $('#myCarousel').carousel({
        interval: 3000
    });

    // 2. Inisialisasi Owl Carousel Produk (DIHAPUS)
    // Bagian ini sengaja dikosongkan karena kita tidak pakai carousel produk lagi

    // 3. Inisialisasi Infinite Scrolling (jscroll) untuk Artikel
    $('ul.pagination').hide(); 
    
    if ($('.scrolling-pagination').length) {
        $('.scrolling-pagination').jscroll({
            autoTrigger: true,
            padding: 0,
            nextSelector: '.pagination li.active + li a, .pagination li.page-item.active + li.page-item a.page-link',
            contentSelector: 'div.scrolling-pagination',
            callback: function() {
                $('ul.pagination').remove();
            }
        });
    }

    // 4. Inisialisasi Countdown Timers
    updateCountdownTimers(); 
    setInterval(updateCountdownTimers, 1000); 
});