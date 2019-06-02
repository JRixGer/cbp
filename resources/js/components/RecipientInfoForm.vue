<template>
    <div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">RECIPIENT INFO</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">

                        <div v-bind:class="{'form-group': true, 'focused':model.first_name, 'has-error has-danger': errors.first_name }">
                            <input type="text" v-model="model.first_name"   id="first_name" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="first_name">First Name <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.first_name">{{ error }}</span>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.last_name, 'has-error has-danger': errors.last_name }">
                            <input type="text" v-model="model.last_name" id="last_name" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="last_name">Last Name <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.last_name">{{ error }}</span>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.company, 'has-error has-danger': errors.company }">
                            <input type="text" v-model="model.company" id="company" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="company">Company Name </label>
                            <span class="help-block" v-for="error in errors.company">{{ error }}</span>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.country, 'has-error has-danger': errors.country }">
                            <select v-model="model.country" id="country" class="form-control" placeholder="Country" >
                                <!-- <option value=""></option> -->
                                <option value="CA">CANADA</option>
                                <option value="US">United States</option>
                                <option disabled>_________________________</option>
                                <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                            </select>
                            <span class="bar"></span>
                            <label for="country">Country <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.address_1, 'has-error has-danger': errors.address_1 }">
                                <GoogleAddress 
                                id="map"
                                class="form-control"
                                placeholder=""
                                v-model="model.address_1"
                                v-on:placechanged="getAddressData"
                                >
                            </GoogleAddress>
                            <span class="bar"></span>
                            <label for="map">Address Line 1 <span class="required">*</span></label>

                            <span class="help-block" v-for="error in errors.address_1">{{ error }}</span>
                        </div>


                        <div v-bind:class="{'form-group': true, 'focused':model.address_2, 'has-error has-danger': errors.address_2 }">
                            <input type="text" v-model="model.address_2" id="address_2" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="address_2">Address Line 2 (Optional)</label>
                            <span style="font-size:12px;">Apartment, suite, unit, building, floor, etc.</span>
                            <span class="help-block" v-for="error in errors.address_2">{{ error }}</span>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.city, 'has-error has-danger': errors.city }">
                            <input type="text" v-model="model.city" id="city" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="city">City <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.city">{{ error }}</span>
                        </div>

                        <div class="row">

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.province, 'has-error has-danger': errors.province }">
                                    <input type="text" v-model="model.province" id="province" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="province">Province / State <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.province">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.postal, 'has-error has-danger': errors.postal }">
                                    <input type="text" v-model="model.postal" id="postal" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="postal">Postal Code / Zip Code <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.postal">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.contact_no, 'has-error has-danger': errors.contact_no }">
                                    <input type="text" v-model.lazy="model.contact_no" id="contact_no" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="contact_no">Contact Number</label>
                                    <span class="help-block" v-for="error in errors.contact_no">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.email, 'has-error has-danger': errors.email }">
                                    <input type="text" v-model="model.email" id="email" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="email">Email</label>
                                    <span class="help-block" v-for="error in errors.email">{{ error }}</span>
                                </div>
                            </div>
                        </div>  


                        <button class="btn btn-info" hidden v-bind:disabled="processing">Save and Continue</button>


                    </form>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ["editShipmentModel"],
        mounted() {
            console.log('Recipient Infro Form Component mounted.')
            
        },
        data(){
            return {
                processing: false,
                model:{ },
                errors:{},
                countries:{},
                businessRegistrationModel:{},
                mailingAddress:{},
                component_error:false
            }
        },
        created(){
            this.init();


            this.$root.$on("recipient_model_no_errors",(c)=>{
                this.errors = [];
            })

            this.$root.$on("recipient_model_errors",(c)=>{
                this.errors = c.errors
            })


            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.recipient
            })


            

        },

        watch:{
            editShipmentModel:{
                handler:function(data){

                    if(data){
                        let r = data.recipient;
                        let ra = data.recipient_address;
                        this.model = {
                            first_name : r.first_name,
                            last_name : r.last_name,
                            company : r.company,
                            country : ra.country,
                            address_1 : ra.address_1,
                            address_2 : ra.address_2,
                            city : ra.city,
                            province : ra.province,
                            postal : ra.postal
                        }
                    }
                },
                ddep:true
            },
            "model.country" : {
                handler:function(data){
                    this.$root.$emit("countrySwitchEvent",data)
                },
                deep:true
            },

            "model":{
                handler:function(data){

                    this.$root.$emit("countrySwitchEvent",this.model.country)
                    this.$root.$emit("recipientInfoModel",this.model);
                    this.filterInput(data.first_name,"first_name")
                    this.filterInput(data.last_name,"last_name")
                    this.filterInput(data.company,"company")
                    this.filterInput(data.address_1,"address_1",/[^A-Za-z0-9 - \d]/gi)
                    this.filterInput(data.address_2,"address_2",/[^A-Za-z0-9 - \d]/gi)
                    this.filterInput(data.city,"city")
                    this.filterInput(data.province,"province")
                    this.filterInput(data.postal,"postal")
                    this.filterInput(data.contact_no,"contact_no",/[^+0-9-\d]/gi)
                    this.filterInput(data.email,"email",/[^A-Za-z@_. -\d]/gi)

                    

                    

                },
                deep:true
            },

            // "model.first_name":function(data){
            // },
        },
        methods:{

            init:function(){
                this.$http.get("/api/countries").then(res=>{
                    this.countries = res.data;
                })
            },


            filterInput:function(data,field, regex=false){
                let re = /[^A-Za-z \d]/gi
                if(data){

                    if(regex){
                        re = regex;
                        this.$set(this.model, field, data.replace(re, ''))
                    }else{
                        this.$set(this.model, field, data.replace(re, ''))
                    }
                }

            },


            getAddressData: function (addressData, placeResultData) {

                console.log("addressData");
                console.log(addressData);
                let address_1 = "";
                if( addressData.country != '' )
                    // this.model.country = addressData.country;
                    this.$set(this.model,"country", addressData.country)

                if( addressData.locality ){

                    // this.model.city = addressData.locality;
                    this.$set(this.model,"city", addressData.locality)
                }else if (addressData.postal_town){
                    this.$set(this.model,"city", addressData.postal_town)

                }

                if( addressData.administrative_area_level_1 != '' )
                    // this.model.province = addressData.administrative_area_level_1;
                    this.$set(this.model,"province", addressData.administrative_area_level_1)

                if( addressData.postal_code != '')
                    // this.model.postal = addressData.postal_code;
                    this.$set(this.model,"postal", addressData.postal_code)


                if(addressData.street_number || addressData.route ){
                    address_1 = (addressData.street_number != '' ? addressData.street_number + ' ' : '') + addressData.route;
                }else{
                    address_1 = addressData.administrative_area_level_5;
                }
                this.$set(this.model,"address_1", address_1)
                this.$root.$emit("countrySwitchEvent",this.model.country)

                

            },

            formSubmit:function(){
                // this.processing = true;
                // this.$http.post("/recipients/validate",this.model).then(response=>{
                //     console.log(response);
                //     this.processing = false;
                //     window.location.href="/"
                //     if(this.errors){
                //         this.errors = [];
                //     }

                // }, response=>{
                //     this.errors = response.data.errors;
                //     this.processing = false;

                //    showErrorMsg(response.data.message)

                // }).catch((err) => {
                //     handleErrorResponse(err.status);
                //     this.processing = false;
                //     if (err.status == 422) {
                //         this.errors = err.data;
                //     }
                // });
            }
        }
    }
</script>
