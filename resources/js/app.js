require('./bootstrap');
import moment from 'moment';

window.Vue = require('vue');


Vue.component('chat-messages', require('./components/ChatMessages.vue').default);
Vue.component('chat-form', require('./components/ChatForm.vue').default);

Vue.filter('formatDate', function(value) {
    if (value) {
        return moment(String(value)).format('D-mm-Y hh:mm:ss')
    }
});

const app = new Vue({
    el: '#app',

    data: {
        messages: [],
        bidding: '',
        // newMessage:''
    },

    created() {
        this.fetchMessages();
        Echo.private('chat')
          .listen('MessageSent', (e) => {
            this.messages.push({
              message: e.bid,
              user: e.user,
              tanggal: e.tanggal
            });
          });
    },

    methods: {
        fetchMessages() {
            const url = window.location.href;
            const slug = url.split("/").slice(-1)[0];
            console.log(slug);

            axios.get('/bid/messages/'+slug).then(response => {
                // console.log("fetchMessages",response.data);
                this.messages = response.data;
            });
        },

        addMessage(message) {
           if(!this.messages.some(data => data.message === message.message)){
                //don't exists
                this.messages.push(message);
                axios.post('/bid/messages', message).then(response => {
                  console.log("add messages",response.data);
                });
            
            }else{
                //exists because Jonh Doe has id 1
                alert("Bid sudah ada");
                window.location.reload(); 
            }
        }
    }

});
