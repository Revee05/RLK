require("./bootstrap");

import Vue from "vue";
window.Vue = Vue;

Vue.component("chat-form", require("./components/ChatForm.vue").default);
Vue.component("chat-messages", require("./components/ChatMessages.vue").default);

function waitForSlug(callback) {
    let tries = 0;

    const timer = setInterval(() => {
        tries++;

        if (window.productSlug && window.productSlug !== "undefined") {
            clearInterval(timer);
            callback();
        }

        if (tries > 30) {
            clearInterval(timer);
            console.error("ERROR: productSlug tetap undefined!");
        }
    }, 100);
}

            // Echo listener for history updates

waitForSlug(() => {
    console.log("Vue initialized. slug =", window.productSlug);

    window.app = new Vue({
        el: "#app",

        data: {
            messages: window.existingBids && window.existingBids.length > 0
                ? window.existingBids
                : []
        },

        created() {
            this.fetchMessages();

            // Start fallback polling if Echo becomes idle or disconnected
            this.startStatePolling();

            Echo.private(`product.${window.productId}`)
                .listen("BidSent", (e) => {
                    const price = Number(e.price);
                    if (!isNaN(price)) {
                        // Update harga tertinggi
                        const highestEl = document.getElementById('highestPrice');
                        if (highestEl) highestEl.innerText = 'Rp ' + window.formatRp(price);
                        // Update dropdown kelipatan
                        window.updateNominalDropdown(price);
                    }
                })
                .listen("MessageSent", (e) => {
                    // Update riwayat bid
                    window.app.messages.unshift({
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
                        // Terima data dari backend apa adanya (sudah urut terbaru di atas)
                        this.messages = res.data;
                    })
                    .catch((err) => {
                        console.error("Gagal ambil messages:", err);
                    });
            },

            // Polling fallback: reconcile UI using canonical state
            startStatePolling() {
                const POLL_MS = 10 * 1000; // 10s
                let lastEventTs = Date.now();

                try {
                    const conn = Echo.connector && Echo.connector.pusher && Echo.connector.pusher.connection;
                    if (conn) {
                        conn.bind('connected', () => { console.log('[Poll] Echo connected'); });
                        conn.bind('disconnected', () => { console.warn('[Poll] Echo disconnected'); });
                        conn.bind('error', (err) => { console.error('[Poll] Echo error', err); });
                    }
                } catch (e) { console.warn('[Poll] Echo bind failed', e); }

                // Update lastEvent when MessageSent arrives
                Echo.private(`product.${window.productId}`).listen('MessageSent', () => {
                    lastEventTs = Date.now();
                });

                // Periodic polling
                setInterval(() => {
                    const now = Date.now();
                    const idle = now - lastEventTs > POLL_MS;
                    const url = `/bid/state/${window.productSlug}`;
                    if (!idle) return;

                    axios.get(url).then((res) => {
                        const data = res.data || {};
                        const highest = Number(data.highest);
                        const msgs = Array.isArray(data.messages) ? data.messages : [];

                        if (!isNaN(highest)) {
                            const highestEl = document.getElementById('highestPrice');
                            if (highestEl) highestEl.innerText = 'Rp ' + window.formatRp(highest);
                            window.updateNominalDropdown(highest);
                        }

                        // Update riwayat bid dengan bid terbaru
                        if (msgs.length > 0) {
                            if (!window.app.messages.length || window.app.messages[0].message !== msgs[0].message) {
                                window.app.messages.unshift(msgs[0]);
                            }
                        }
                    }).catch((err) => {
                        console.warn('[Poll] State fetch failed', err);
                    });
                }, POLL_MS);
            },

            addMessage(msg) {
                axios.post("/bid/messages", msg).then((res) => {
                    console.log('[addMessage] Bid berhasil dikirim:', res.data);
                    
                    // Langsung update UI untuk user yang melakukan bid
                    if (res.data.status === 'Message Sent!' && res.data.data) {
                        // Update riwayat bid
                        this.messages.unshift({
                            user: res.data.data.user,
                            message: res.data.data.message,
                            tanggal: res.data.data.tanggal,
                        });
                        
                        // Update harga tertinggi di UI
                        const price = Number(res.data.data.message);
                        if (!isNaN(price)) {
                            const highestEl = document.getElementById('highestPrice');
                            if (highestEl) {
                                highestEl.innerText = 'Rp ' + price.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
                                // Animasi highlight
                                highestEl.style.transition = 'all 0.3s ease';
                                highestEl.style.backgroundColor = '#fef3c7';
                                setTimeout(() => {
                                    highestEl.style.backgroundColor = 'transparent';
                                }, 800);
                            }
                            
                            // Update dropdown
                            if (typeof updateNominalDropdown === 'function') {
                                updateNominalDropdown(price);
                            }
                        }
                        
                        console.log('[addMessage] ✓ UI updated immediately for bidder');
                    }
                    
                    // Echo event akan handle update untuk user lain
                }).catch((err) => {
                    console.error('[addMessage] Bid gagal:', err);
                    alert('Gagal mengirim bid. Silakan coba lagi.');
                });
            },
        },
    });
});
