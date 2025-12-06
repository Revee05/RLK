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
                // jika server mengirim daftar nominals gunakan nilai pertama
                if (Array.isArray(e.nominals) && e.nominals.length) {
                    this.newMessage = Number(e.nominals[0]);
                    return;
                }
                // fallback menggunakan step dari event atau prop kelipatan
                const step = Number(e.step) || Number(this.kelipatan) || 10000;
                this.newMessage = highest + step;
            });
    },

    methods: {
        // === Mengambil riwayat bid untuk menentukan bid berikutnya ===
        fetchMessages() {
            axios.get(`/bid/messages/${window.productSlug}`).then((res) => {
                if (res.data.length > 0) {
                    // server mengembalikan daftar terurut DESC (index 0 = terbaru)
                    let latest = res.data[0];
                    // Bid selanjutnya = bid terakhir (terbaru) + kelipatan
                    this.newMessage = Number(latest.message) + Number(this.kelipatan);
                } else {
                    // === Jika belum ada bid, pakai harga awal sebagai bid ===
                    this.newMessage = Number(this.price);
                }
            });
        },

        /* === DIPANGGIL DARI TOMBOL BID MANUAL (dropdown) === */
        sendBidFromButton(val) {
            // === Set bid sesuai nominal yang dipilih lalu validasi & kirim ===
            this.newMessage = Number(val);
            this.validateAndSend(this.newMessage);
        },

        /* === KIRIM BID KE PARENT COMPONENT === */
        sendMessage() {
            // membuat timestamp (tetap seperti sebelumnya)
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

            // gunakan validateAndSend sehingga selalu sinkron dengan state server
            this.validateAndSend(this.newMessage, timestamp);
        },

        // === NEW: validasi terhadap state server sebelum emit ===
        validateAndSend(value, timestamp = null) {
            const slug = window.productSlug;
            if (!slug) {
                // fallback: langsung kirim jika slug tidak tersedia (seharusnya tidak terjadi)
                this.$emit("messagesent", {
                    user: this.user,
                    message: value,
                    produk: this.produk,
                    tanggal: timestamp || new Date().toISOString().slice(0,19).replace('T',' ')
                });
                return;
            }

            // ambil state terbaru dari server
            axios.get(`/bid/state/${slug}`).then(res => {
                const data = res.data || {};
                const highest = Number(data.highest) || 0;
                const nominals = data.nextNominals || data.nominals || null;
                const step = Number(data.step) || null;

                // fungsi bantu untuk membangun daftar nominal apabila hanya step tersedia
                const buildNominalsFromStep = (h, s) => {
                    const arr = [];
                    const useStep = (s && s > 0) ? s : (Number(this.kelipatan) || 10000);
                    for (let i=1;i<=5;i++) arr.push(h + (useStep * i));
                    return arr;
                };

                let valid = false;
                if (Array.isArray(nominals) && nominals.length) {
                    // jika server kirim list nominal, cek keanggotaan
                    valid = nominals.map(Number).includes(Number(value));
                    // juga rebuild dropdown agar sinkron
                    window.updateNominalDropdown(highest, nominals, step);
                } else {
                    // jika tidak ada array, hitung dari step
                    const arr = buildNominalsFromStep(highest, step);
                    valid = arr.includes(Number(value));
                    // rebuild dropdown dari perhitungan server
                    window.updateNominalDropdown(highest, arr, step);
                }

                if (valid) {
                    // emit ke parent untuk dikirim ke server
                    this.$emit("messagesent", {
                        user: this.user,
                        message: Number(value),
                        produk: this.produk,
                        tanggal: timestamp || new Date().toISOString().slice(0,19).replace('T',' ')
                    });
                } else {
                    // tolak pengiriman dan beri tahu user
                    // refresh ajax form supaya dropdown menggunakan kelipatan terbaru
                    if (typeof window.refreshStateImmediate === 'function') {
                        window.refreshStateImmediate();
                    } else if (typeof window.updateNominalDropdown === 'function') {
                        // fallback: rebuild from fetched data (we already built arr above)
                        if (Array.isArray(nominals) && nominals.length) {
                            window.updateNominalDropdown(highest, nominals, step);
                        } else {
                            const arr = buildNominalsFromStep(highest, step);
                            window.updateNominalDropdown(highest, arr, step);
                        }
                    }

                    alert("Kelipatan harga sudah berubah. Silakan pilih nominal terbaru.");
                    console.warn("[Bid] Rejected stale bid", { attempted: value, highest, step, nominals });
                    // fokus ke select agar user segera pilih opsi baru
                    const sel = document.getElementById('bidSelect');
                    if (sel) sel.focus();
                }
            }).catch(err => {
                // jika fetch state gagal, jangan kirim blindly — berikan peringatan atau fallback
                console.warn("[Bid] Failed to fetch state before sending:", err);
                alert("Gagal memeriksa status terkini. Silakan coba lagi.");
            });
        }
    }
};
</script>
