<template>
    <div class="input-group input-group-sm">
     <input id="btn-input" type="text" name="message" class="form-control form-control-sm" v-model="newMessage" @keyup.enter="sendMessage" disabled>
      <div class="input-group-append group-bid">
        <button class="btn btn-outline-secondary btn-sm rounded-0 btn-bid" id="btn-chat" @click="sendMessage">Bid -</button>
      </div>
    </div>
</template>

<script>
    export default {
        props: ['user','produk','kelipatan','price','messagesent'],

        data() {
            return {
                newMessage: '',
                messages:[],
                messagesLastCount:'',
                bidding:'',
                message:'',
                timestamp:'',
            }
        },
        created() {
          this.fetchMessages();
          Echo.private('bid')
          .listen('BidSent', (b) => {
            console.log("new Bid",b.price);
            this.newMessage = parseInt(b.price) + parseInt(this.kelipatan);
          });
        },
        methods: {
            fetchMessages() {
                const url = window.location.href;
                const slug = url.split("/").slice(-1)[0];
                console.log(slug);

                axios.get('/bid/messages/'+slug).then(response => {
                    console.log("chat fetchMessages",response.data,this.price);
                    this.messages = response.data;
                     var lastPosition = this.messages.length-1;
                     if (lastPosition != '-1') {
                        this.messagesLastCount = this.messages[lastPosition].message;
                        this.newMessage = parseInt(this.messagesLastCount) + parseInt(this.kelipatan)   
                        console.log('newMessage a',this.newMessage,this.messagesLastCount);
                     } else {
                        this.newMessage = this.price;   

                     }
                });
            },
            sendMessage() {
                const today = new Date();
                const date = today.getFullYear()+'-'+String(today.getMonth()+1).padStart(2, '0')+'-'+String(today.getDate()).padStart(2, '0');
                const time = today.getHours() + ":" + today.getMinutes() + ":" + today.getSeconds();
                const dateTime = date +' '+ time;

                this.$emit('messagesent', {
                    user: this.user,
                    message: this.newMessage,
                    produk: this.produk,
                    tanggal: dateTime,
                });
            }
        }    
    }
</script>
<style type="text/css">
    .group-bid {
        position: absolute;
        width: 100%;
    }
    .btn-bid {
        width: 100%;
        padding-right: 40px;
        text-align: center;
    }
    #btn-input {
        text-align: center;
        padding-left:50px;
        color:#6c757d;
    }
    .btn-outline-secondary:hover {
        color:#6c757d;
        background-color: transparent !important;
        border-color: #6c757d;
    }
</style>
