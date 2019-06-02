<template>
    <div>
         <div class="row page-titles toolbar">
            <div class="col-md-12 col-12 align-self-center">
                <h3 class="">{{ title }} </h3>
            </div>

        </div>
    </div>
</template>


<script>
    export default {
        props:['activeTab'],
        data(){
            return {
                model:{},
                title:"CREDIT CARDS",
                enabled: false
            }
        },
        created(){
        },
        methods:{
            getInitialContents: function(){

            },



        }
    }
</script>
