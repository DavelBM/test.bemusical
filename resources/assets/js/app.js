
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.socialmedia = require('vue');

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('example', require('./components/Example.vue'));
Vue.component('chat-message', require('./components/ChatMessage.vue'));
Vue.component('chat-log', require('./components/ChatLog.vue'));
Vue.component('chat-composer', require('./components/ChatComposer.vue'));
Vue.component('admin-chat-message', require('./components/AdminChatMessage.vue'));
Vue.component('admin-chat-log', require('./components/AdminChatLog.vue'));
Vue.component('admin-chat-composer', require('./components/AdminChatComposer.vue'));
socialmedia.component('facebook', require('./components/facebook.vue'));
socialmedia.component('twitter', require('./components/twitter.vue'));

function formatAMPM(date) 
{
    var hours = date.getHours();
    var minutes = date.getMinutes();
    var seconds = date.getSeconds();
    var strTime = hours + ':' + minutes + ':' + seconds;
    return strTime;
}  

function formatDay(date) 
{
    var month = date.getUTCMonth() + 1; //months from 1-12
    var day = date.getUTCDate();
    var year = date.getUTCFullYear();
    var newdate = year + "-" + month + "-" + day;
    return newdate;
}

function getDate() 
{
    var time_formatted = formatAMPM(new Date());
    var day_formatted = formatDay(new Date());
    var date = day_formatted+' '+time_formatted;
    return date;
}

const sm = new socialmedia({
    el: '#sm'
});

const app = new Vue({
    el: '#vue-app',
    data:{
    	messages: [],
        adminmessages: [],
        userid: $('#user_id_chating').val(),
    },

    methods: {
    	addMessage(message){
    		this.messages.push(message);
            axios.post('/post/messages', {
                message: message.message,
                time: getDate(),
            }).then(response => {
                // console.log(message);
            });
    	},

        addadminMessage(adminmessage){
            this.adminmessages.push(adminmessage);
            axios.post('/admin/post/messages/'+this.userid, {
                message: adminmessage.message,
                time: getDate(),
            }).then(response => {
                // console.log(adminmessage);
            });
        }
    },

    created() {
        axios.get('/admin/get/messages/'+this.userid+'/').then(response => {
            this.adminmessages = response.data;
        });

        axios.get('/get/messages').then(response => {
            this.messages = response.data;
        });

        Echo.join('chatroom')
            .listen('MessagePosted', (e) => {
                this.messages.push({
                    message: e.message.message,
                    time: e.message.time,
                    name: e.message.name,
                    image: e.message.image
                });

                this.adminmessages.push({
                    message: e.adminmessage.message,
                    time: e.adminmessage.time,
                    name: e.adminmessage.name,
                    image: e.adminmessage.image
                });
                // console.log(e);
            });

        Echo.join('adminchatroom')
            .listen('AdminMessagePosted', (e) => {
                this.messages.push({
                    message: e.message.message,
                    time: e.message.time,
                    name: e.message.name,
                    image: e.message.image
                });

                // this.adminmessages.push({
                //     message: e.adminmessage.message,
                //     time: e.adminmessage.time,
                //     name: e.adminmessage.name,
                //     image: e.adminmessage.image
                // });
                // console.log(e);
            });
    }

});
