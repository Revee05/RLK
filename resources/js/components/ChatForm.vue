<template>
    <!-- Form bid tersembunyi, hanya digunakan sebagai logic component -->
    <div style="display:none;"></div>
</template>

<script>
export default {
    props: ["user", "produk", "kelipatan", "price"],

    data() {
        return {
            newMessage: Number(this.price)
        };
    },

    created() {
        this.fetchMessages();

        // Listen realtime highest bid
        Echo.private(`product.${this.produk}`)
            .listen("BidSent", (e) => {
                let highest = Number(e.price);
                this.newMessage = highest + Number(this.kelipatan);
            });
    },

    methods: {
        fetchMessages() {
            axios.get(`/bid/messages/${window.productSlug}`).then((res) => {
                if (res.data.length > 0) {
                    let last = res.data[res.data.length - 1];
                    this.newMessage =
                        Number(last.message) + Number(this.kelipatan);
                } else {
                    this.newMessage = Number(this.price);
                }
            });
        },

        /* DIPANGGIL DARI TOMBOL BID SEKARANG */
        sendBidFromButton(val) {
            this.newMessage = Number(val);
            this.sendMessage();
        },

        /* KIRIM BID */
        sendMessage() {
            const today = new Date();
            const timestamp =
                today.getFullYear() +
                "-" +
                String(today.getMonth() + 1).padStart(2, "0") +
                "-" +
                String(today.getDate()).padStart(2, "0") +
                " " +
                today.getHours() +
                ":" +
                today.getMinutes() +
                ":" +
                today.getSeconds();

            this.$emit("messagesent", {
                user: this.user,
                message: this.newMessage,
                produk: this.produk,
                tanggal: timestamp,
            });
        }
    }
};
</script>
