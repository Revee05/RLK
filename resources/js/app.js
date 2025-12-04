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

            Echo.private(`product.${window.productId}`)
                .listen("MessageSent", (e) => {
                    console.log('[MessageSent] Bid baru diterima:', e);
                    // Bid baru masuk di atas (paling baru di index 0)
                    this.messages.unshift({
                        user: e.user,
                        message: e.bid || e.message,
                        tanggal: e.tanggal,
                    });

                    this.$nextTick(() => {
                        let el = document.getElementById("chat-container");
                        if (el) el.scrollTop = 0;
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
