<template>
    <div>
        <div class="row page-titles toolbar">
            <div class="col-md-6 col-12 align-self-center">
                <h3 class="">{{ title }} </h3>
            </div>



<!--             <div class="col-md-6 col-12 align-self-end">
                    <div class="bt-switch" v-on:click="toggle">
                    <span>Imperial</span>
                    <switches v-model="enabled" theme="bootstrap"  color="info"></switches>
                    <span>Metric</span>
                </div>
            </div>
 -->

        </div>
    </div>
</template>


<script>
    import Switches from 'vue-switches';
    export default {
        props:['activeTab'],
        components: {
            Switches
        },
        mounted(){

        },
        data(){
            return {
                model:{},
                websiteName:'',
                title:"BATCH BULK UPLOAD",
                BBU:true,
                enabled: false
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
        watch:{
            enabled:function(data){
                if(data){
                    this.model.unit = "metric";
                }else{
                    this.model.unit = "imperial";

                }

                this.$root.$emit("metric-toggle",this.model);
            }
        },
        methods:{
            getInitialContents: function(){

            },
            toggle:function(){
                console.log("clicked")
                if(this.model.unit == "imperial"){
                    this.model.unit = "metric";
                }else{
                    this.model.unit =  "imperial";
                }
            }


        }
    }
</script>
