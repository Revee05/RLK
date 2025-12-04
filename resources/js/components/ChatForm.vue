<template>
    <!-- === Komponen ini tidak memiliki UI, hanya logic untuk mengirim bid === -->
    <div style="display:none;"></div>
</template>

<script>
export default {
    // === Props yang dikirim dari parent: user, id produk, kelipatan bid, harga awal ===
    props: ["user", "produk", "kelipatan", "price"],

    data() {
        return {
            // === Menyimpan nilai bid yang akan dikirim, default = harga awal ===
            newMessage: Number(this.price)
        };
    },

    created() {
        // === Ambil riwayat bid dan tentukan bid berikutnya ===
        this.fetchMessages();

        // === Mendengarkan update realtime harga tertinggi via Echo ===
        Echo.private(`product.${this.produk}`)
            .listen("BidSent", (e) => {
                let highest = Number(e.price);
                // === Update bid berikutnya berdasarkan harga tertinggi + kelipatan ===
                this.newMessage = highest + Number(this.kelipatan);
            });
    },

    methods: {
        // === Mengambil riwayat bid untuk menentukan bid berikutnya ===
        fetchMessages() {
            axios.get(`/bid/messages/${window.productSlug}`).then((res) => {
                if (res.data.length > 0) {
                    // === Ambil bid terakhir (paling besar/terakhir diajukan) ===
                    let last = res.data[res.data.length - 1];
                    // === Bid selanjutnya = bid terakhir + kelipatan ===
                    this.newMessage = Number(last.message) + Number(this.kelipatan);
                } else {
                    // === Jika belum ada bid, pakai harga awal sebagai bid ===
                    this.newMessage = Number(this.price);
                }
            });
        },

        /* === DIPANGGIL DARI TOMBOL BID MANUAL (dropdown) === */
        sendBidFromButton(val) {
            // === Set bid sesuai nominal yang dipilih lalu kirim ===
            this.newMessage = Number(val);
            this.sendMessage();
        },

        /* === KIRIM BID KE PARENT COMPONENT === */
        sendMessage() {
            // === Membuat timestamp manual (YYYY-MM-DD HH:mm:ss) ===
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

            // === Emit event ke parent agar bid diproses backend ===
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
