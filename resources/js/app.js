// === Load konfigurasi awal aplikasi (bootstrap Laravel Mix) ===
require("./bootstrap");

// === Import helper lelang (formatRp, updateNominalDropdown, dll) ===
import "./lelang/helper";

import Vue from "vue";
window.Vue = Vue;

// === Registrasi komponen chat ===
Vue.component("chat-form", require("./components/ChatForm.vue").default);
Vue.component(
    "chat-messages",
    require("./components/ChatMessages.vue").default
);

/**
 * ===============================
 * Highest Price Helper (FINAL)
 * ===============================
 */
function updateHighestPrice(val) {
    if (val === null || typeof val === "undefined" || isNaN(val)) return;

    const els = document.querySelectorAll('[data-role="highest-price"]');
    if (!els.length) return;

    els.forEach((el) => {
        if (el.offsetParent === null) return;

        const newText = "Rp " + window.formatRp(val);

        if (el.innerText.trim() === newText.trim()) return;

        el.innerText = newText;

        // animasi ringan
        el.classList.remove("highest-price-animate");
        void el.offsetWidth;
        el.classList.add("highest-price-animate");
    });
}

function safeUpdateNominalDropdown(highest, nominals, step, stepDefault = 10000) {
    if (typeof window.updateNominalDropdown !== "function") return;

    try {
        window.updateNominalDropdown(
            highest,
            nominals || null,
            step || null,
            stepDefault
        );
    } catch (e) {
        console.error("[updateNominalDropdown] error", e);
    }
}

// === Tunggu slug siap ===
function waitForSlug(cb) {
    let tries = 0;
    const t = setInterval(() => {
        tries++;
        if (window.productSlug) {
            clearInterval(t);
            cb();
        }
        if (tries > 30) {
            clearInterval(t);
            console.error("productSlug tidak tersedia");
        }
    }, 100);
}

function isDebugEnv() {
    const env =
        window.appEnv || process.env.APP_ENV || process.env.NODE_ENV || "";
    return ["local", "dev", "development", "testing"].includes(
        env.toLowerCase()
    );
}

// === Init Vue ===
waitForSlug(() => {
    window.app = new Vue({
        el: "#app",

        data: {
            messages:
                window.existingBids && window.existingBids.length
                    ? window.existingBids
                    : [],
        },

        created() {
            this.fetchMessages();
            this.startStatePolling();

            Echo.private(`product.${window.productId}`)
                // === Highest price realtime ===
                .listen("BidSent", (e) => {
                    const price = Number(e.price);
                    if (!isNaN(price)) {
                        updateHighestPrice(price);
                        safeUpdateNominalDropdown(
                            price,
                            e.nominals || e.nextNominals || null,
                            e.step || null
                        );
                    }
                })

                // === Chat realtime (user lain) ===
                .listen("MessageSent", (e) => {
                    // anti duplikasi
                    if (
                        this.messages.length &&
                        String(this.messages[0].message) ===
                            String(e.bid || e.message)
                    ) {
                        return;
                    }

                    this.messages.unshift({
                        user: e.user,
                        message: e.bid || e.message,
                        tanggal: e.tanggal,
                    });
                });
        },

        methods: {
            fetchMessages() {
                axios
                    .get(`/bid/messages/${window.productSlug}`)
                    .then((res) => {
                        this.messages = res.data;
                    })
                    .catch((err) => {
                        if (isDebugEnv())
                            console.error("fetchMessages error", err);
                    });
            },

            refreshStateImmediate() {
                axios
                    .get(`/bid/state/${window.productSlug}`)
                    .then((res) => {
                        const d = res.data || {};
                        const highest = Number(d.highest);

                        if (!isNaN(highest)) {
                            updateHighestPrice(highest);
                            safeUpdateNominalDropdown(
                                highest,
                                d.nextNominals || d.nominals || null,
                                d.step || null
                            );
                        }

                        if (Array.isArray(d.messages) && d.messages.length) {
                            const latest = d.messages[0];
                            if (
                                !this.messages.length ||
                                this.messages[0].message !== latest.message
                            ) {
                                this.messages.unshift(latest);
                            }
                        }
                    })
                    .catch(() => this.fetchMessages());
            },

            startStatePolling() {
                const POLL_MS = 10000;
                let lastEvent = Date.now();

                Echo.private(`product.${window.productId}`).listen(
                    "MessageSent",
                    () => (lastEvent = Date.now())
                );

                setInterval(() => {
                    if (Date.now() - lastEvent < POLL_MS) return;
                    this.refreshStateImmediate();
                }, POLL_MS);
            },

            /**
             * ===============================
             * KIRIM BID (OPTIMISTIC – FINAL)
             * ===============================
             */
            addMessage(msg) {
                axios
                    .post("/bid/messages", msg)
                    .then((res) => {
                        const sd = res.data?.data || {};

                        // ✅ CHAT LANGSUNG MUNCUL
                        this.messages.unshift({
                            user: sd.user,
                            message: sd.message,
                            tanggal: sd.tanggal,
                        });

                        // ✅ HIGHEST PRICE
                        const highest =
                            typeof sd.highest !== "undefined"
                                ? Number(sd.highest)
                                : Number(sd.message);

                        if (!isNaN(highest)) {
                            updateHighestPrice(highest);
                            safeUpdateNominalDropdown(
                                highest,
                                sd.nextNominals || sd.nominals || null,
                                sd.step || null
                            );
                        }
                    })
                    .catch((err) => {
                        if ([409, 422].includes(err?.response?.status)) {
                            this.refreshStateImmediate();
                        }
                        alert("Gagal mengirim bid. Silakan coba lagi.");
                    });
            },
        },
    });
});
