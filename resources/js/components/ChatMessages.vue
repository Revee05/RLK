<template>
    <ul class="chat">
        <li class="left clearfix" v-for="message in messages">
            <div class="chat-body clearfix">
                <div class="header d-flex justify-content-between">
                    <div class="primary-font fw-500">
                        {{ message.user.name }}
                    </div>
                    <div class="bid-date">
                        {{ message.tanggal | formatDate}}
                    </div>
                </div>
                <p>
                    {{ Number(message.message).toLocaleString() }}
                </p>
            </div>
        </li>
    </ul>
</template>

<script>
  export default {
    props: ['messages']
  };
</script>
<style type="text/css">
    .bid-date {
        font-size: 13px;
    }
</style>
