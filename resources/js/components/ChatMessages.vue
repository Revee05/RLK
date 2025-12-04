<template>
  <div>
    <!-- === Melakukan loop setiap item pesan dan menampilkannya dalam layout sederhana === -->
    <div v-for="(msg,i) in messages" :key="i" class="mb-2">
      <!-- === Menampilkan nama user yang mengirim pesan === -->
      <strong>{{ msg.user.name }}</strong>

      <!-- === Menampilkan nilai message dalam format Rupiah yang sudah diformat === -->
      <span> Rp {{ format(msg.message) }}</span>

      <!-- === Menampilkan tanggal pesan dengan style kecil dan warna abu === -->
      <div style="font-size:11px; color:#777;">{{ msg.tanggal }}</div>

      <hr>
    </div>
  </div>
</template>

<script>
export default {
  // === Menerima props "messages" yang berisi daftar objek pesan ===
  props: ["messages"],

  methods: {
    // === Memformat angka menjadi format ribuan dengan titik ===
    // contoh: 10000 -> 10.000
    format(n) {
      return Number(n)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
  }
};
</script>
