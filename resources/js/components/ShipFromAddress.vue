<template>
    <div class="shipfrom-wrapper">
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">SHIP FROM ADDRESS</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="" role="form" method="POST">

                        <input name="address" v-bind:disabled="disableCA" v-model.number="model.ship_from_address" v-on:click="handleClick(1)" v-bind:value="1" type="radio" id="address-1">
                        <label for="address-1" > 
                            <h4  >CANADA
                                <!-- <span v-if="address.ca_address" class="edit" v-on:click="edit(address.ca_address)">Edit</span> -->
                                <!-- <span v-else class="edit"  v-on:click="add">Add</span> -->
                            </h4>
                        </label>
                        <ul v-if="address.ca_address" >
                       
                            <!-- <li><b class="address-label">Address:</b> {{ address.ca_address.address_1 }} {{ address.ca_address.address_2 }}</li>
                            <li><b class="address-label">City:</b> {{ address.ca_address.city }}</li>
                            <li><b class="address-label">State:</b> {{ address.ca_address.province }}</li>
                            <li><b class="address-label">Zip Code:</b> {{ address.ca_address.postal }}</li>
                            <li><b class="address-label">Country:</b> {{ address.ca_address.country }}</li> -->
                        </ul>
                        <div v-else style="text-align:center">
                            No registered Canadian Address 
                        </div>

                        <hr>

                        <input name="address" v-bind:disabled="disableUS" v-on:click="handleClick(2)"  v-model.number="model.ship_from_address" v-bind:value="2" type="radio" id="address-2">
                        <label for="address-2" > 
                            <h4 v-bind:class="{'disabled':country == 'CA'}">USA 
                                <!-- <span v-if="address.us_address" class="edit" v-on:click="edit(address.us_address)">Edit</span> -->
                                <!-- <span v-else class="edit" v-on:click="add">Add</span> -->
                            </h4>
                        </label>
                        <ul v-if="address.us_address" v-bind:class="{'disabled':country == 'CA'}">
                       
                            <!-- <li><b class="address-label">Address:</b> {{ address.us_address.address_1 }} {{ address.us_address.address_2 }}</li>
                            <li><b class="address-label">City:</b> {{ address.us_address.city }}</li>
                            <li><b class="address-label">State:</b> {{ address.us_address.province }}</li>
                            <li><b class="address-label">Zip Code:</b> {{ address.us_address.postal }}</li>
                            <li><b class="address-label">Country:</b> {{ address.us_address.country }}</li> -->
                        </ul>

                        <div v-else style="text-align:center">
                            No registered U.S. Address
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <ShipFromAddressFormModalSS></ShipFromAddressFormModalSS>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ["editShipmentModel","address"],
        mounted() {

        },
        data(){
            return {
                processing: false,
                model:{ },
                errors:{},
                country:"",
                // address:{},
                disableCA:false,
                disableUS:false,
                component_error:false,
                editShipmentStatus:true
            }
        },
        created(){

            // this.getAddress();


            // will trigger event on country change
            this.$root.$on("countrySwitchEvent",(c)=>{

                this.country = c
                this.model.ship_from_address = 1   
                this.disableCA = false;
                this.disableUS = false;
                this.handleClick(1);
                
                if(c =="CA"){
                    this.disableUS = true;
                    this.disableCA = false;
                    this.model.ship_from_address = 1;  

                    
                    if(this.model.ship_from_address == 2){
                        this.model = {}
                        this.$root.$emit("ShipFromAddressModel",{});
                        this.model.ship_from_address = "";
                    }

                }else{
                    this.disableCA = false;
                    this.disableUS = false;
                }

            })

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.ship_from_address
            })



            this.$root.$on("singleShipmentModelShipFrom",(c)=>{

                try{
                    if(c.recipient_model.country != "CA"){
                        this.handleTriggers(c.parcel_types);
                    }
                }catch(e){
                    console.log("error")
                }
                
            });



            this.$root.$on("parcel_types",(c)=>{
                this.handleTriggers(c);
            });
            //listens to event when there are changes with ShipfromAddresses
            this.$root.$on("emit_senderShipFromAddressData",(s)=>{
                // this.getAddress();
            })


            //autoselect ship from in ship to is US
            this.$root.$on("auto-select-shipfrom",(e)=>{

                if(e == "CA_US"){
                    // this.model.ship_from_address = 1
                    this.$set(this.model,"ship_from_address",1)
                    this.$root.$emit("ShipFromAddressModelAutoSelect",this.address.ca_address);

                }else{
                    // this.model.ship_from_address = 2
                    this.$set(this.model,"ship_from_address",2)
                    this.$root.$emit("ShipFromAddressModelAutoSelect",this.address.us_address);


                }
            })
        },

        watch:{

            editShipmentModel:{
                handler:function(data){
                    if(data){
                        this.$root.$emit("ShipFromAddressModel",data.shipfrom);

                    }
                },
                deep:true
            }


            // model:{
            //     handler:function(data){
            //         if(this.model.ship_from_address == 1){
            //             this.$root.$emit("ShipFromAddressModel",this.address.ca_address);
            //         }else if(this.model.ship_from_address == 2){
            //             this.$root.$emit("ShipFromAddressModel",this.address.us_address);
            //         }else{
            //             this.$root.$emit("ShipFromAddressModel","");
            //         }
            //     },
            //     deep:true
            // }

        },
        methods:{
            // edit:function(data){
            //     //send event to shipfromaddress modal
            //     this.$root.$emit("edit_address",data);

            //     var el = $('.ship-from-address-form-modal')
            //     el.modal({backdrop: 'static', keyboard: false}) ;
            // },

            add: function(){
                var el = $('.ship-from-address-form-modal')
                el.modal({backdrop: 'static', keyboard: false}) ;
            },

            handleTriggers:function(c){
                this.disableUS = false;
                this.disableCA = false;
                // alert(JSON.stringify(this.editShipmentModel))
                
                if(this.country == "US"){

                    if(c.parcel_type == "Letter"){
                        this.model = {}
                        this.disableCA = true;
                        this.model.ship_from_address = 2
                        this.$root.$emit("ShipFromAddressModel",this.address.us_address);
                        
                    }else if(c.usps_box_status == "no"){
                        this.disableCA = false;
                        this.disableUS = false;
                        this.model = {}
                        this.$root.$emit("ShipFromAddressModel",{});

                        if(this.editShipmentModel){
                            if(this.editShipmentModel.shipfrom.country == "CA"){
                                this.$set(this.model,"ship_from_address",1);
                            }else{
                                this.$set(this.model,"ship_from_address",2);
                            }
                            this.$root.$emit("ShipFromAddressModel",this.editShipmentModel.shipfrom);

                        }

                    }else{
                        this.disableCA = true;
                        this.model.ship_from_address = 2
                        
                        this.$root.$emit("ShipFromAddressModel",this.address.us_address);
                    }
                }else{
                    this.disableUS = false;
                    this.disableCA = false;
                }

            },


            handleClick:function(d){
    
                if(d == 1){
                    this.$root.$emit("ShipFromAddressModel",this.address.ca_address);
                }else if(d == 2){
                    this.$root.$emit("ShipFromAddressModel",this.address.us_address);
                }else{
                    this.$root.$emit("ShipFromAddressModel","");
                }
            },


            // getAddress:function(data){


            //     // this.address.ca_address = { 
            //     //     "address_1":"1300 Kamato Rd #12",
            //     //     "address_2":"",
            //     //     "city":"Mississauga",
            //     //     "province":"ON",
            //     //     "postal":"L4W 2N2",
            //     //     "country":"CA"
            //     // }

            //     // this.address.us_address = { 
            //     //     "address_1":"1823 Maryland Avenue",
            //     //     "address_2":"",
            //     //     "city":"Niagara Falls",
            //     //     "province":"NY",
            //     //     "postal":"14305",
            //     //     "country":"US"
            //     // }

            //     this.$http.get("/senders/getShipFromAddresses").then(res=>{
            //         // console.log(res.data)
            //         this.address = res.data;
            //     })
            // }
        }
    }
</script>
