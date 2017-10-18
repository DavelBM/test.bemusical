<template lang="html">
    <div>
    	<li class="left clearfix"><span class="chat-img pull-left">
            <img :src="message.image" :alt="message.image" class="img-circle" height="50" width="50"/>
        </span>
            <div class="chat-body clearfix">
                <div class="header">
		            <strong class="primary-font"> {{ message.name }} </strong> <small class="pull-right text-muted">
		                <span class="glyphicon glyphicon-time"></span>{{ message.time }}</small>
		        </div>
		        <p> {{ message.message }} </p>
            </div>
        </li>
    </div>
</template>

<script>
	export default{
		props: ['message']
	}
</script>

<style lang="css">

</style>