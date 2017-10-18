<template lang="html">
    <div>
    	<li class="left clearfix"><span class="chat-img pull-left">
            <img :src="adminmessage.image" :alt="adminmessage.image" class="img-circle" height="50" width="50"/>
        </span>
            <div class="chat-body clearfix">
                <div class="header">
		            <strong class="primary-font"> {{ adminmessage.name }} </strong> <small class="pull-right text-muted">
		                <span class="glyphicon glyphicon-time"></span>{{ adminmessage.time }}</small>
		        </div>
		        <p> {{ adminmessage.message }} </p>
            </div>
        </li>
    </div>
</template>

<script>
	export default{
		props: ['adminmessage']
	}
</script>

<style lang="css">

</style>