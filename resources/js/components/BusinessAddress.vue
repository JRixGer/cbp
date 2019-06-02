<template>
    <div>
        <div class="card">
            <h4 class="card-title">Business Address</h4><br>
             <div class="card-block">

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">

                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.business_name, 'has-error has-danger': errors.business_name }">
                                        <input type="text" v-model="model.business_name" id="business_name" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="business_name">Business Name <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.business_name">{{ error }}</span>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.business_number, 'has-error has-danger': errors.business_number }">
                                        <input type="text" v-model="model.business_number" id="business_number" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="business_number">Business Number <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.business_number">{{ error }}</span>
                                    </div>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-12">
                                    <div v-bind:class="{'form-group': true, 'focused':model.business_location, 'has-error has-danger': errors.business_location }">
                                        <input type="text" v-model="model.business_location" id="business_location" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="business_location">Physical Business Location <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.business_location">{{ error }}</span>
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


                             <div class="form-group">
                                <input type="checkbox" name="mailing_address_chk"  v-model="model.mailing_address_chk" id="mailing_address_chk" />
                                <label for="mailing_address_chk">Put check if mailing address is different from physical address.</label>
                            </div>


                    </form>
                </div>
            </div>
        </div>
        
    </div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    props:[],
    mounted() {
        console.log('Sender Business Form Component mounted.')
    },
    data(){
        return {
            processing: false,
            model:{  },
            errors:{},
            countries:{},
            ask_import_number:1,
            with_import_number:"",
        }
    },
    created(){
        this.init();

        this.$root.$on("formErrors",(e)=>{

            if(e.business_address){
                this.errors = e.business_address
            }else{
                this.errors = {}

            }
        })
    },
    watch:{
        model:{
            handler:function(){

            this.$root.$emit('business_address_model', this.model);
            },
            deep:true
        
        }
    },
    methods:{

        init:function(){
            this.$http.get("/api/countries").then(res=>{
                this.countries = res.data;
            })

            this.$http.get("/profile/businessinfo").then(res=>{
                // let x = res.data;
                this.model= res.data;

                if(res.data.sender_mailing_address_id){
                    this.$set(this.model,"mailing_address_chk",true)
                }

                // this.$set(this.model,"business_name",x.business_name);
                // this.$set(this.model,"business_number",x.business_number);
                // this.$set(this.model,"business_location",x.business_location);
                
                // this.$set(this.model,"address_1",x.address_1);
                // this.$set(this.model,"address_2",x.address_2);
                // this.$set(this.model,"city",x.city);
                // this.$set(this.model,"postal",x.postal);
                // this.$set(this.model,"province",x.province);
                // this.$set(this.model,"country",x.country);
            })

            this.$root.$on('emit_ProfileRegistration', (a)=>{ 
                let _a = this.handleUndefined(a.address_1);
                let b = this.handleUndefined(a.address_2);
                let c = this.handleUndefined(a.city);
                let d = this.handleUndefined(a.province);
                let e = this.handleUndefined(a.postal);
                let f = this.handleUndefined(a.country);

                this.model.business_location = _a+" "+b+" "+c+" "+d+" "+e+" "+f;
                this.$forceUpdate();

            });


        },

        handleUndefined:function(str){
            return (str) ? str : ""; 
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

        }


    }
}
</script>

