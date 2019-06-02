<template>
    <div class="modal fade ship-from-address-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> SHIP FROM ADDRESS EDIT</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body">

                            <div v-bind:class="{'form-group': true, 'focused':model.address_1, 'has-error has-danger': errors.address_1 }">
                                    <GoogleAddress 
                                    id="ssmap"
                                    class="form-control"
                                    placeholder=""
                                    v-model="model.address_1"
                                    v-on:placechanged="getAddressData">
                                </GoogleAddress>
                                <span class="bar"></span>
                                <label for="ssmap">Address Line 1 <span class="required">*</span></label>

                                <span class="help-block" v-for="error in errors.address_1">{{ error }}</span>
                            </div>


                            <div v-bind:class="{'form-group': true, 'focused':model.address_2, 'has-error has-danger': errors.address_2 }">
                                <input type="text" v-model="model.address_2" id="ssaddress_2" class="form-control" placeholder="">
                                <span class="bar"></span>
                                <label for="ssaddress_2">Address Line 2 (Optional)</label>
                                <span style="font-size:12px;">Apartment, suite, unit, building, floor, etc.</span>
                                <span class="help-block" v-for="error in errors.address_2">{{ error }}</span>
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.city, 'has-error has-danger': errors.city }">
                                        <input type="text" v-model="model.city" id="sscity" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="sscity">City <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.city">{{ error }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.province, 'has-error has-danger': errors.province }">
                                        <input type="text" v-model="model.province" id="ssprovince" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="ssprovince">Province / State <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.province">{{ error }}</span>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.postal, 'has-error has-danger': errors.postal }">
                                        <input type="text" v-model="model.postal" id="sspostal" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="sspostal">Postal Code / Zip Code <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.postal">{{ error }}</span>
                                    </div>
                                </div>

                                <div class="col-md-6">
                                    <div v-bind:class="{'form-group': true, 'focused':model.country, 'has-error has-danger': errors.country }">
                                        <select v-model="model.country" id="sscountry" class="form-control" placeholder="Country" >
                                            <option value="CA">CANADA</option>
                                            <option value="US">United States</option>
                                            <option disabled>_________________________</option>
                                            <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                        </select>
                                        <span class="bar"></span>
                                        <label for="sscountry">Country <span class="required">*</span></label>
                                        <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                    </div>
                                </div>
                            </div>  

                    </div>
                    <div class="modal-footer">
                       <button class="btn btn-info" v-bind:disabled="processing">SUBMIT</button>
                    </div>
                </form>


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    props:[],
    mounted() {
        console.log('Ship from address Form modal Component mounted.')
    },
    data(){
        return {
            processing: false,
            model:{  },
            errors:{},
            countries:{},
        }
    },
    created(){
        this.init();

        this.$root.$on("edit_address",(d)=>{
            console.log('edit now '+JSON.stringify(this.model));
            this.model = d
        })
    },
    watch:{
    },
    methods:{

        init:function(){
            this.$http.get("/api/countries").then(res=>{
                this.countries = res.data;
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
            console.log('2 >>>   '+JSON.stringify(this.model));
            this.processing = true;
            this.$http.post("/address/register",this.model).then(response=>{
                console.log(response);
                this.processing = false;
                this.$root.$emit("emit_senderShipFromAddressData",this.model);
                // window.location.href="/"
                $(".ship-from-address-form-modal").modal("hide")
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