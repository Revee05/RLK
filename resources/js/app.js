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
                        // update highest price UI
                        const highestEl = document.getElementById("highestPrice");
                        if (highestEl) highestEl.innerText = "Rp " + window.formatRp(price);

                        // prefer nominals from event, fallback to step
                        const nominals = e.nominals || e.nextNominals || null;
                        const step = typeof e.step !== "undefined" ? e.step : null;
                        window.updateNominalDropdown(price, nominals, step);
                    }
                })
                // === Listener untuk update riwayat bid ===
                .listen("MessageSent", (e) => {
                    // update dropdown from message event too
                    const bid = Number(e.bid || e.message);
                    const nominals = e.nominals || e.nextNominals || null;
                    const step = typeof e.step !== "undefined" ? e.step : null;
                    if (!isNaN(bid)) window.updateNominalDropdown(bid, nominals, step);

                    // add message to list
                    window.app.messages.unshift({
                        user: e.user,
                        message: e.bid || e.message,
                        tanggal: e.tanggal,
                    });
                });

            // Expose refresh helper globally so other scripts/components can call it
            try {
                window.refreshStateImmediate = this.refreshStateImmediate.bind(this);
            } catch (e) {
                if (isDebugEnv()) console.warn('[created] failed to expose refreshStateImmediate', e);
            }
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

            // === NEW: Segera refresh state (highest + messages) tanpa menunggu polling ===
            refreshStateImmediate() {
                const url = `/bid/state/${window.productSlug}`;
                if (isDebugEnv()) console.log('[refreshStateImmediate] fetch', url);
                axios
                    .get(url)
                    .then((res) => {
                        const data = res.data || {};
                        const highest = Number(data.highest);
                        const msgs = Array.isArray(data.messages) ? data.messages : [];

                        // Update highest UI
                        if (!isNaN(highest)) {
                            const highestEl = document.getElementById('highestPrice');
                            if (highestEl) highestEl.innerText = 'Rp ' + window.formatRp(highest);

                            // call dropdown updater but guard against exceptions so we still sync messages
                            try {
                                if (typeof updateNominalDropdown === 'function') {
                                    updateNominalDropdown(
                                        highest,
                                        data.nextNominals || data.nominals || null,
                                        data.step || null
                                    );
                                }
                            } catch (e) {
                                if (isDebugEnv()) console.error('[refreshStateImmediate] updateNominalDropdown threw', e);
                            }
                        }

                        // If the state endpoint already returned a full messages array, use it directly
                        // to avoid an extra request. Otherwise fall back to fetchMessages().
                        if (msgs.length > 0) {
                            // assume backend returns messages newest-first (same shape as fetchMessages)
                            this.messages = msgs;
                        } else {
                            this.fetchMessages();
                        }

                        if (isDebugEnv()) console.log('[refreshStateImmediate] done', { highest, msgs_count: msgs.length });
                    })
                    .catch((err) => {
                        if (isDebugEnv()) console.warn('[refreshStateImmediate] failed', err);
                        // fallback: pastikan setidaknya ambil messages lagi
                        this.fetchMessages();
                    });
            },

            // === Polling fallback untuk sinkronisasi state jika Echo mati/idle ===
            startStatePolling() {
                const POLL_MS = 10 * 1000; // === Interval polling 5 detik ===
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
                                // pass server nominals / step when available
                                window.updateNominalDropdown(
                                    highest,
                                    data.nextNominals || data.nominals || null,
                                    data.step || null
                                );
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
                            const serverData = res.data.data || {};

                            // === Tambahkan riwayat bid baru (gunakan message dari server) ===
                            this.messages.unshift({
                                user: serverData.user,
                                message: serverData.message,
                                tanggal: serverData.tanggal,
                            });

                            // === Prefer highest dari server jika tersedia ===
                            const highestFromServer =
                                typeof serverData.highest !== "undefined"
                                    ? Number(serverData.highest)
                                    : NaN;

                            const displayPrice = !isNaN(highestFromServer)
                                ? highestFromServer
                                : Number(serverData.message);

                            if (!isNaN(displayPrice)) {
                                const highestEl =
                                    document.getElementById("highestPrice");
                                if (highestEl) {
                                    highestEl.innerText =
                                        "Rp " + window.formatRp(displayPrice);

                                    highestEl.style.transition =
                                        "all 0.3s ease";
                                    highestEl.style.backgroundColor =
                                        "#fef3c7";
                                    setTimeout(() => {
                                        highestEl.style.backgroundColor =
                                            "transparent";
                                    }, 800);
                                }

                                // === Update dropdown kelipatan nominal ===
                                if (
                                    typeof updateNominalDropdown === "function"
                                ) {
                                    // prefer server-provided nominals if present in response.data.data
                                    const sd = (res.data && res.data.data) || {};
                                    updateNominalDropdown(
                                        displayPrice,
                                        sd.nextNominals || sd.nominals || null,
                                        sd.step || null
                                    );
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

                        // Jika collision / sudah diambil user lain -> refresh state segera
                        const msg = err.response && err.response.data && err.response.data.message
                            ? err.response.data.message
                            : "";

                        if (err.response && (err.response.status === 409 || msg.includes("Harga bid ini sudah diambil user lain"))) {
                            if (isDebugEnv()) console.log('[addMessage] Detected bid collision. Refreshing state immediately.');
                            this.refreshStateImmediate();
                        } else if (err.response && err.response.status === 422) {
                            // Validasi (mis: bid lebih kecil atau tidak sesuai kelipatan) -> refresh juga untuk sinkronisasi
                            if (isDebugEnv()) console.log('[addMessage] Validation error. Refreshing state to sync.');
                            this.refreshStateImmediate();
                        }

                        alert("Gagal mengirim bid. Silakan coba lagi.");
                    });
            },
        },
    });
});
