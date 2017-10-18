<template lang="html">
    <div class="chat-log">
    	<admin-chat-message v-for="adminmessage in adminmessages" :key="adminmessage.id" :adminmessage="adminmessage"></admin-chat-message>
    	<div class="empty" v-show="adminmessages.length === 0">
    		No messages!!!
    	</div>
    </div>
</template>

<script>
export default{
    props: ['adminmessages']
}
</script>

<style lang="css">

.chat-log .chat-message:nth-child(even){
	backgroud-color: #ccc;
}

.empty{
	padding: 1rem;
	text-align: center;
}

</style>