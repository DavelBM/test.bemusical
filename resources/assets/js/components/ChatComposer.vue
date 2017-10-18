<template lang="html">
    <div class="chat-composer">
        <div class="panel-footer">
            <div class="input-group">
                <input id="btn-input" class="form-control input-sm" type="text" placeholder="type your message..." v-model="messageText" @keydown.enter="sendMessage">
                <span class="input-group-btn">
                    <button id="btn-chat" class="btn btn-warning btn-sm" @click="sendMessage">Send</button>
                </span>
            </div>
        </div>
    </div>
</template>

<script>

    function formatAMPM(date) {
        var hours = date.getHours();
        var minutes = date.getMinutes();
        var ampm = hours >= 12 ? 'PM' : 'AM';
        hours = hours % 12;
        hours = hours ? hours : 12; // the hour '0' should be '12'
        minutes = minutes < 10 ? '0'+minutes : minutes;
        var strTime = hours + ':' + minutes + ' ' + ampm;
        return strTime;
    }  

    function formatDate(date) 
    {
        var month = date.getUTCMonth() + 1; //months from 1-12
        var day = date.getUTCDate();
        var year = date.getUTCFullYear();
        switch (month) {
            case 1:
                month = 'January'; 
            break;
            case 2:
                month = 'February'; 
            break;
            case 3:
                month = 'March'; 
            break;
            case 4:
                month = 'April'; 
            break;
            case 5:
                month = 'May'; 
            break;
            case 6:
                month = 'June'; 
            break;
            case 7:
                month = 'July'; 
            break;
            case 8:
                month = 'August'; 
            break;
            case 9:
                month = 'September'; 
            break;
            case 10:
                month = 'Octuber'; 
            break;
            case 11:
                month = 'November'; 
            break;
            case 12:
                month = 'December'; 
            break;
        }
        var newdate = month + " " + day + ", " + year;
        return newdate;
    }

    var time_formatted = formatAMPM(new Date());
    var date_formatted = formatDate(new Date());
    var date = date_formatted+' '+time_formatted;

    export default{
        data(){
        	return{
        		messageText: ''
        	}
        },

        methods:{
        	sendMessage(){
        		this.$emit('messagesent', {
        			message: this.messageText,
        			name: $('#name-user-display').text(),
                    image: $("#profile_picture_user").attr("src"),
                    time: date
        		});
        		this.messageText = '';
        	}
        }
    }
</script>

<style lang="css">

.chat-composer{
	display: flex;
}

.chat-composer input{
	flex: 1 auto;
}

</style>