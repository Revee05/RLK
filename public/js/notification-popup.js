let currentNotificationId = null;
let shownNotificationIds = new Set();

/* ================= SAFE URL ================= */
function safeUrl(url) {
    try {
        const parsed = new URL(url, window.location.origin);
        if (parsed.origin !== window.location.origin) return "/lelang";
        return parsed.href;
    } catch {
        return "/lelang";
    }
}

/* ================== SHOW POPUP ================== */
function showAuctionPopup(data) {
    if (!data || shownNotificationIds.has(data.id)) return;

    const popup = document.getElementById("winner-popup");
    const title = document.getElementById("popup-title");
    const desc = document.getElementById("popup-desc");
    const icon = document.getElementById("popup-icon");
    const btn = document.getElementById("winner-btn");

    if (!popup || !title || !desc || !icon || !btn) return;

    shownNotificationIds.add(data.id);
    currentNotificationId = Number(data.id) || null;

    title.textContent = "";
    desc.textContent = "";
    icon.textContent = "";

    if (data.type === "winner") {
        icon.textContent = "🎉";
        icon.className = "popup-icon-winner";

        const bold = document.createElement("b");
        bold.textContent = data.title || "";

        title.append("🎉 SELAMAT! ANDA TERPILIH SEBAGAI PEMENANG LELANG ");
        title.appendChild(bold);

        desc.textContent =
            "Penawaran Anda dikonfirmasi sebagai yang tertinggi. Selesaikan pembayaran Anda dalam waktu 24 jam agar karya aman.";

        btn.textContent = "Proses Pembayaranmu Sekarang";
        btn.href = safeUrl(data.checkout_url);
    } else {
        icon.textContent = "😢";
        icon.className = "popup-icon-loser";

        const bold = document.createElement("b");
        bold.textContent = data.title || "";

        title.append("😢 Mohon maaf penawaranmu untuk ");
        title.appendChild(bold);
        title.append(" belum berhasil.");

        desc.textContent =
            "Penawaran terakhir Anda dikalahkan saat penutupan lelang. Kami memiliki banyak karya serupa lainnya.";

        btn.textContent = "Lihat Karya Lelang Tersedia";
        btn.href = "/lelang";
    }

    popup.style.display = "flex";
}

/* ================== POLLING DB ================== */
function fetchBannerNotifications() {
    fetch('/notifications/banner')
        .then(res => res.json())
        .then(list => list.forEach(showAuctionPopup))
        .catch(() => {});
}
setInterval(fetchBannerNotifications, 10000);

/* ================== CLOSE POPUP ================== */
function closeWinnerPopup() {
    const popup = document.getElementById("winner-popup");
    if (popup) popup.style.display = "none";

    if (!currentNotificationId) return;

    fetch(`/notifications/${currentNotificationId}/read`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": window.Laravel.csrfToken,
            "Content-Type": "application/json",
        },
        credentials: "same-origin",
    });
}

/* ================== BUTTON ================== */
document.addEventListener("DOMContentLoaded", function () {
    const winnerBtn = document.getElementById("winner-btn");
    if (!winnerBtn) return;

    winnerBtn.addEventListener("click", function (e) {
        e.preventDefault();
        const url = this.href;
        closeWinnerPopup();
        setTimeout(() => window.location.href = url, 120);
    });
});

/* ================== INITIAL LOAD ================== */
document.addEventListener("DOMContentLoaded", function () {
    if (Array.isArray(window.initialBannerNotifications)) {
        window.initialBannerNotifications.forEach(showAuctionPopup);
    }
});

if (window.Laravel?.userId && window.Echo) {
    Echo.private(`auction-result.${window.Laravel.userId}`)
        .listen('.auction.winner', (e) => {
            console.log('[Realtime Winner]', e.notification);
            showAuctionPopup(e.notification);
        });
}
