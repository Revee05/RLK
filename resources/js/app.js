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
            messages: []
        },

        created() {
            this.fetchMessages();

            Echo.private(`product.${window.productId}`)
                .listen("MessageSent", (e) => {
                    this.messages.push({
                        user: e.user,
                        message: e.bid,
                        tanggal: e.tanggal,
                    });

                    this.$nextTick(() => {
                        let el = document.getElementById("chat-container");
                        if (el) el.scrollTop = el.scrollHeight;
                    });
                })
                .listen("BidSent", (e) => {
                    console.log("Realtime BidSent", e);
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
                        console.error("Gagal ambil messages:", err);
                    });
            },

            addMessage(msg) {
                axios.post("/bid/messages", msg).then(() => {
                    this.messages.push(msg);
                });
            },
        },
    });
});
