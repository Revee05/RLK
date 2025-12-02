<template>
  <div>
    <div v-for="(msg,i) in messages" :key="i" class="mb-2">
      <strong>{{ msg.user.name }}</strong>
      <span> Rp {{ format(msg.message) }}</span>
      <div style="font-size:11px; color:#777;">{{ msg.tanggal }}</div>
      <hr>
    </div>
  </div>
</template>

<script>
export default {
  props: ["messages"],

  methods: {
    format(n) {
      return Number(n)
        .toString()
        .replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    }
  }
};
</script>
