<template>
    <div>
        <div class="row page-titles toolbar">
            <div class="col-md-2 col-12 align-self-center">
                <h3 class="">{{ title }} </h3>
            </div>

            <div class="col-md-4 col-12 align-self-center">
                <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item"> 
                        <router-link to="/shipment/single/pd"  v-bind:class="{'nav-link':true, 'active':PD}" data-toggle="tab" href="#home" role="tab" aria-expanded="false">
                            <span class="hidden-sm-up"><i class="ti-home"></i></span> 
                            <span class="hidden-xs-down">Postage & Delivery</span>
                        </router-link> 
                    </li>
                    <li class="nav-item"> 
                        <router-link to="/shipment/single/do" v-bind:class="{'nav-link':true, 'active':DO}" data-toggle="tab" href="#messages" role="tab" aria-expanded="false">
                            <span class="hidden-sm-up"><i class="ti-email"></i></span> 
                            <span class="hidden-xs-down">Delivery Only</span>
                        </router-link> 
                    </li>
                </ul>
                </div>
            </div>
            <div class="col-md-6 col-12 align-self-end">
                 <div class="bt-switch" v-on:click="toggle">
                    <span>Imperial</span>
                    <switches v-model="enabled" theme="bootstrap"  color="info"></switches>
                    <span>Metric</span>
                    <!-- <input type="checkbox"  checked data-size="mini" data-on-color="info" data-on-text="Imperial" data-off-text="Metric"/> -->
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import Switches from 'vue-switches';
    export default {
        props:['activeTab','editShipmentModel'],
        components: {
            Switches
        },
        mounted(){
 

        },
        data(){
            return {
                model:{ unit: "imperial"},
                websiteName:'',
                title:"NEW ORDER",
                PD:false,
                DO:false,
                enabled: false


            }
        },
        created(){

            // this.getInitialContents();
            if(this.activeTab == "pd"){
                this.PD = true;
            }

            if(this.activeTab == "do"){
                this.DO = true;
            }


            console.log($cookies.get("imperial_metric"))
            if($cookies.get("imperial_metric") == "imperial"){
                 this.enabled = false   
            }else{
                this.enabled = true;
            }
        },

        watch:{
            editShipmentModel:{
                handler:function(data){
                    if(data){
                        if(data.size_unit == "IN"){
                            this.enabled = false
                        }else{
                            this.enabled = true

                        }
                    }
                },
                deep:true
            },
            enabled:function(data){
                if(data){
                    this.model.unit = "metric";
                    $cookies.set("imperial_metric","metric");
                }else{
                    this.model.unit = "imperial";
                    $cookies.set("imperial_metric","imperial");

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
