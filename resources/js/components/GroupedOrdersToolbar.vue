<template>
    <div>
        <div class="row page-titles toolbar">
            <div class="col-md-6 col-12 align-self-center">
                <h3 class="">{{ title }} </h3>
            </div>

            <div class="col-md-6 col-12 align-self-end">
                 <div class="bt-switch">
                    <!-- <input type="checkbox" checked data-size="mini" data-on-color="info" data-on-text="Imperial" data-off-text="Metric"/> -->
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    export default {
        props:['activeTab'],
        mounted(){
            //$(".bt-switch input[type='checkbox'], .bt-switch input[type='radio']").bootstrapSwitch();
        },
        data(){
            return {
                model:{},
                websiteName:'',
                title:"GROUPED ORDERS",
                AS:true,
            }
        },
        created(){
            // this.getInitialContents();
            // if(this.activeTab == "bbu"){
            //     this.BBU = true;
            // }

            // if(this.activeTab == "bbp"){
            //     this.BBP= true;
            // }
        },
        methods:{
            getInitialContents: function(){

            },



        }
    }
</script>
