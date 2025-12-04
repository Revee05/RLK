// === Load konfigurasi awal aplikasi (bootstrap Laravel Mix) ===
require("./bootstrap");

import Vue from "vue";
// === Expose Vue ke window agar komponen dapat diakses global ===
window.Vue = Vue;

// === Registrasi komponen global untuk form chat dan daftar message ===
Vue.component("chat-form", require("./components/ChatForm.vue").default);
Vue.component(
    "chat-messages",
    require("./components/ChatMessages.vue").default
);

// === Menunggu window.productSlug sampai tersedia sebelum inisialisasi Vue ===
function waitForSlug(callback) {
    let tries = 0;

    const timer = setInterval(() => {
        tries++;

        // === Jika slug sudah siap, hentikan timer dan jalankan callback ===
        if (window.productSlug && window.productSlug !== "undefined") {
            clearInterval(timer);
            callback();
        }

        // === Jika slug tidak muncul setelah beberapa percobaan, hentikan dan tampilkan error ===
        if (tries > 30) {
            clearInterval(timer);
            console.error("ERROR: productSlug tetap undefined!");
        }
    }, 100);
}

// === Helper untuk cek mode environment (local, testing, development) ===
function isDebugEnv() {
    const env =
        window.appEnv || process.env.APP_ENV || process.env.NODE_ENV || "";
    return ["local", "testing", "development", "dev"].includes(
        env.toLowerCase()
    );
}

// === Mulai inisialisasi Vue setelah slug terdeteksi ===
waitForSlug(() => {
    if (isDebugEnv())
        console.log("Vue initialized. slug =", window.productSlug);

    window.app = new Vue({
        el: "#app",

        data: {
            // === Set data awal messages berdasarkan existingBids jika tersedia ===
            messages:
                window.existingBids && window.existingBids.length > 0
                    ? window.existingBids
                    : [],
        },

        created() {
            // === Ambil daftar message awal dari backend ===
            this.fetchMessages();

            // === Jalankan polling fallback untuk menjaga state ketika Echo idle ===
            this.startStatePolling();

            // === Listener Laravel Echo pada channel privat produk ===
            Echo.private(`product.${window.productId}`)
                // === Listener untuk update harga tertinggi ===
                .listen("BidSent", (e) => {
                    const price = Number(e.price);
                    if (!isNaN(price)) {
                        // === Update elemen harga tertinggi di UI ===
                        const highestEl =
                            document.getElementById("highestPrice");
                        if (highestEl)
                            highestEl.innerText =
                                "Rp " + window.formatRp(price);

                        // === Update dropdown kelipatan nominal ===
                        window.updateNominalDropdown(price);
                    }
                })
                // === Listener untuk update riwayat bid ===
                .listen("MessageSent", (e) => {
                    // === Tambahkan message baru ke awal daftar ===
                    window.app.messages.unshift({
                        user: e.user,
                        message: e.bid || e.message,
                        tanggal: e.tanggal,
                    });
                });
        },

        methods: {
            // === Mengambil seluruh message untuk productSlug dari server ===
            fetchMessages() {
                axios
                    .get(`/bid/messages/${window.productSlug}`)
                    .then((res) => {
                        // === Backend sudah mengurutkan: terbaru di atas ===
                        this.messages = res.data;
                    })
                    .catch((err) => {
                        if (isDebugEnv())
                            console.error("Gagal ambil messages:", err);
                    });
            },

            // === Polling fallback untuk sinkronisasi state jika Echo mati/idle ===
            startStatePolling() {
                const POLL_MS = 15 * 1000; // === Interval polling 15 detik ===
                let lastEventTs = Date.now();

                // === Bind event status koneksi Echo (opsional, hanya logging) ===
                try {
                    const conn =
                        Echo.connector &&
                        Echo.connector.pusher &&
                        Echo.connector.pusher.connection;
                    if (conn) {
                        if (isDebugEnv())
                            conn.bind("connected", () => {
                                console.log("[Poll] Echo connected");
                            });
                        if (isDebugEnv())
                            conn.bind("disconnected", () => {
                                console.warn("[Poll] Echo disconnected");
                            });
                        if (isDebugEnv())
                            conn.bind("error", (err) => {
                                console.error("[Poll] Echo error", err);
                            });
                    }
                } catch (e) {
                    if (isDebugEnv())
                        console.warn("[Poll] Echo bind failed", e);
                }

                // === Update timestamp ketika event MessageSent masuk ===
                Echo.private(`product.${window.productId}`).listen(
                    "MessageSent",
                    () => {
                        lastEventTs = Date.now();
                    }
                );

                // === Jalankan polling periodik ===
                setInterval(() => {
                    const now = Date.now();
                    const idle = now - lastEventTs > POLL_MS; // === Cek apakah Echo idle ===
                    const url = `/bid/state/${window.productSlug}`;

                    // === Jika tidak idle, polling tidak dijalankan ===
                    if (!idle) return;

                    // === Ambil state terbaru dari server ===
                    axios
                        .get(url)
                        .then((res) => {
                            const data = res.data || {};
                            const highest = Number(data.highest);
                            const msgs = Array.isArray(data.messages)
                                ? data.messages
                                : [];

                            // === Sinkronisasi harga tertinggi ===
                            if (!isNaN(highest)) {
                                const highestEl =
                                    document.getElementById("highestPrice");
                                if (highestEl)
                                    highestEl.innerText =
                                        "Rp " + window.formatRp(highest);
                                window.updateNominalDropdown(highest);
                            }

                            // === Sinkronisasi riwayat bid jika ada yang lebih baru ===
                            if (msgs.length > 0) {
                                if (
                                    !window.app.messages.length ||
                                    window.app.messages[0].message !==
                                        msgs[0].message
                                ) {
                                    window.app.messages.unshift(msgs[0]);
                                }
                            }
                        })
                        .catch((err) => {
                            if (isDebugEnv())
                                console.warn("[Poll] State fetch failed", err);
                        });
                }, POLL_MS);
            },

            // === Mengirim bid baru ke server ===
            addMessage(msg) {
                axios
                    .post("/bid/messages", msg)
                    .then((res) => {
                        if (isDebugEnv())
                            console.log(
                                "[addMessage] Bid berhasil dikirim:",
                                res.data
                            );

                        // === Update UI secara instan untuk pengirim (optimistic update) ===
                        if (
                            res.data.status === "Message Sent!" &&
                            res.data.data
                        ) {
                            // === Tambahkan riwayat bid baru ===
                            this.messages.unshift({
                                user: res.data.data.user,
                                message: res.data.data.message,
                                tanggal: res.data.data.tanggal,
                            });

                            // === Update harga tertinggi ===
                            const price = Number(res.data.data.message);
                            if (!isNaN(price)) {
                                const highestEl =
                                    document.getElementById("highestPrice");
                                if (highestEl) {
                                    // === Format angka ribuan ===
                                    highestEl.innerText =
                                        "Rp " +
                                        price
                                            .toString()
                                            .replace(
                                                /\B(?=(\d{3})+(?!\d))/g,
                                                "."
                                            );

                                    // === Highlight harga untuk memberi efek perubahan ===
                                    highestEl.style.transition =
                                        "all 0.3s ease";
                                    highestEl.style.backgroundColor = "#fef3c7";
                                    setTimeout(() => {
                                        highestEl.style.backgroundColor =
                                            "transparent";
                                    }, 800);
                                }

                                // === Update dropdown kelipatan nominal ===
                                if (
                                    typeof updateNominalDropdown === "function"
                                ) {
                                    updateNominalDropdown(price);
                                }
                            }
                            if (isDebugEnv())
                                console.log(
                                    "[addMessage] ✓ UI updated immediately for bidder"
                                );
                        }

                        // === User lain otomatis update dari Echo ===
                    })
                    .catch((err) => {
                        if (isDebugEnv())
                            console.error("[addMessage] Bid gagal:", err);

                        // Cek jika error karena harga sudah diambil user lain
                        if (
                            err.response &&
                            err.response.data &&
                            err.response.data.message &&
                            err.response.data.message.includes(
                                "Harga bid ini sudah diambil user lain"
                            )
                        ) {
                            // Langsung fetch data terbaru dari server
                            axios
                                .get(`/bid/state/${window.productSlug}`)
                                .then((res) => {
                                    const data = res.data || {};
                                    const highest = Number(data.highest);
                                    const msgs = Array.isArray(data.messages)
                                        ? data.messages
                                        : [];
                                    // Update harga tertinggi dan dropdown
                                    if (!isNaN(highest)) {
                                        const highestEl =
                                            document.getElementById(
                                                "highestPrice"
                                            );
                                        if (highestEl)
                                            highestEl.innerText =
                                                "Rp " +
                                                window.formatRp(highest);
                                        window.updateNominalDropdown(highest);
                                    }
                                    // Update riwayat bid
                                    if (msgs.length > 0) {
                                        if (
                                            !window.app.messages.length ||
                                            window.app.messages[0].message !==
                                                msgs[0].message
                                        ) {
                                            window.app.messages.unshift(
                                                msgs[0]
                                            );
                                        }
                                    }
                                    if (isDebugEnv())
                                        console.log(
                                            "[addMessage] Fetched state after bid collision"
                                        );
                                });
                        }

                        alert("Gagal mengirim bid. Silakan coba lagi.");
                    });
            },
        },
    });
});
