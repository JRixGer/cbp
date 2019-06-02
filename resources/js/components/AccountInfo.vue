<template>
    <div>
        <div class="card">
            <h4 class="card-title">Account Info</h4><br>
             <div class="card-block">

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="row">
                            <div class="col-md-12">
                                <div v-bind:class="{'form-group': true, 'focused':model.first_name, 'has-error has-danger': errors.first_name }">
                                    <input type="text" v-model="model.first_name" id="first_name" class="form-control" placeholder="">
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
                            </div>
                        </div>

                        <div v-bind:class="{'form-group': true, 'focused':model.address_1, 'has-error has-danger': errors.address_1 }">
                                <GoogleAddress 
                                id="map"
                                class="form-control"
                                placeholder=""
                                v-model="model.address_1"
                                v-on:placechanged="getAddressData">
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

                        <div class="row">
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.city, 'has-error has-danger': errors.city }">
                                    <input type="text" v-model="model.city" id="city" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="city">City <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.city">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">

                                <div v-bind:class="{'form-group': true, 'focused':model.province, 'has-error has-danger': errors.province }">
                                    <input type="text" v-model="model.province" id="province" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="province">Province / State <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.province">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.postal, 'has-error has-danger': errors.postal }">
                                    <input type="text" v-model="model.postal" id="postal" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="postal">Postal Code / Zip Code <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.postal">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.country, 'has-error has-danger': errors.country }">
                                    <select v-model="model.country" id="country" class="form-control" placeholder="Country" >
                                        <option value="CA">CANADA</option>
                                        <option value="US">United States</option>
                                        <option disabled>_________________________</option>
                                        <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="country">Country <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                </div>
                            </div>
                            
   
                        </div>  

                        <div v-bind:class="{'form-group': true, 'focused':model.contact_no, 'has-error has-danger': errors.contact_no }">
                            <input type="text" v-model="model.contact_no" id="contact_no" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="contact_no">Contact Number</label>
                            <span class="help-block" v-for="error in errors.contact_no">{{ error }}</span>
                        </div>


                    </form>
                </div>
            </div>
        </div>
        
    </div>
</template>


<style scoped>
    .profile-wrapper{
        padding-top: 30px;
    }
</style>
<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            console.log('Profile Registration Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                countries:{},
            }
        },
        created(){
            this.init();


            this.$root.$on("emit_businessRegistrationData",(b)=>{
                this.model.business_registration = b
            });

            this.$root.$on("emit_senderMailingAddressData",(b)=>{
                this.model.mailing_address = b
            });


            this.$root.$on("formErrors",(e)=>{
                if(e.account_info){
                    this.errors = e.account_info
                }else{
                    this.errors = {}

                }
            })

        },

        watch:{
            "model":function(data){
                this.$root.$emit('account_info_model', this.model);
                this.$root.$emit('emit_ProfileRegistration', this.model);
            }
        },
        methods:{

            init:function(){
                this.$http.get("/api/countries").then(res=>{
                    this.countries = res.data;
                })

                this.$http.get("/profile/accountinfo").then(res=>{
                    let x = res.data.sender_physical_address;

                    this.model = res.data;

                    this.$set(this.model,"address_1",x.address_1);
                    this.$set(this.model,"address_2",x.address_2);
                    this.$set(this.model,"city",x.city);
                    this.$set(this.model,"postal",x.postal);
                    this.$set(this.model,"province",x.province);
                    this.$set(this.model,"country",x.country);
                })
            },

            getAddressData: function (addressData, placeResultData) {

                if( addressData.country != '' )
                    this.model.country = addressData.country;

                if( addressData.locality != '' )
                    this.model.city = addressData.locality;

                if( addressData.administrative_area_level_1 != '' )
                    this.model.province = addressData.administrative_area_level_1;

                if( addressData.postal_code != '')
                    this.model.postal = addressData.postal_code;

                this.model.address_1 = (addressData.street_number != '' ? addressData.street_number + ' ' : '') + addressData.route;

                this.$forceUpdate();

            },

            formSubmit:function(){
                this.processing = true;
                this.$http.post("/senders/profileRegistration",this.model).then(response=>{
                    console.log(response);
                    this.processing = false;
                    window.location.href="/"
                    if(this.errors){
                        this.errors = [];
                    }

                }, response=>{
                    this.errors = response.data.errors;
                    this.processing = false;

                   showErrorMsg(response.data.message)
                   
                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.processing = false;
                    if (err.status == 422) {
                        this.errors = err.data;
                    }
                });
            }
        }
    }
</script>
