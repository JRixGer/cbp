<template>
    <div class="modal fade sender-business-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> Business Details</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body">


                        <div v-if="ask_import_number">

                                <span>Do you have an import number?</span>
                             <div class="form-group">
                                <input type="radio" name="with_import_number" v-model.number="model.with_import_number" value="yes" id="with-import-number-yes">
                                <label for="with-import-number-yes">YES</label>

                                <input type="radio" name="with_import_number" v-model.number="model.with_import_number" value="no" id="with-import-number-no">
                                <label for="with-import-number-no">NO</label>
                            </div>
                        </div>

                        <div v-if="model.with_import_number == 'yes'">
                            <div v-bind:class="{'form-group': true, 'focused':model.import_number, 'has-error has-danger': errors.import_number }">
                                <input type="text" v-model="model.import_number" id="import_number" class="form-control" placeholder="">
                                <span class="bar"></span>
                                <label for="import_number">Enter Import Number Here... <span class="required">*</span></label>
                                <span class="help-block" v-for="error in errors.import_number">{{ error }}</span>
                            </div>
                        </div>

                        <div v-if="model.with_import_number == 'no'">
                            
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

                        </div>


                        <div>
                            <h3></h3>
                            Kindly download the following forms <a href="#" v-on:click="download('poa')"><b>General POA</b></a> and <a href="#" v-on:click="download('ina')"><b>Import Number Application</b></a>
                             <br><br>
                            
                            <div class="row">
                                <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                        <div class="card-block">
                                            <h4 class="card-title">POA Uploader</h4>
                                            <!-- <label for="input-file-now">Upload</label> -->
                                            <input v-on:change="upload_POA" type="file"  id="input-file-now" class="dropify upload_poa" data-allowed-file-extensions="pdf png jpg jpeg docx doc"/>
                                        </div>
                                    </div>
                                </div>

                                <div class="col-lg-6 col-md-6">
                                    <div class="card">
                                        <div class="card-block">
                                            <h4 class="card-title">Import Number Uploader</h4>
                                            <!-- <label for="input-file-now">Your so fresh input file — Default version</label> -->
                                            <input v-on:change="upload_Import_Number" type="file" id="input-file-now" class="dropify upload_import_number" />
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                       <button class="btn btn-info" v-bind:disabled="processing">Save</button>
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
    },
    watch:{
        "model.mailing_address_chk" : function(data){
            if(data == 1){
                var el = $('.sender-mailing-address-form-modal')
                el.modal({backdrop: 'static', keyboard: false});

                $('.sender-business-form-modal').modal("hide");
            }
        }
    },
    methods:{

        init:function(){
            this.$http.get("/api/countries").then(res=>{
                this.countries = res.data;
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


        download:function(data){
            if(data == "poa"){
                this.$http.post("/document/request",this.model).then(res=>{
                    window.location.href = "/document/poa";
                })
            }else{
                 this.$http.post("/document/request",this.model).then(res=>{
                    window.location.href = "/document/import_number";
                })
            }
        },

        destroyDropify:function(category){
            if(category == "poa"){
                    // $(".upload_poa").val('')
                    var drEvent = $('.upload_poa').dropify();
               }else{
                    var drEvent = $('.upload_import_number').dropify();
                    // $(".upload_import_number").val('')
               }

            drEvent = drEvent.data('dropify');
            drEvent.resetPreview();
            drEvent.clearElement();
        },
        filterAllowedUploadFormat:function(files, category){


            // console.log(files[0]['type'])
            var allowedFormat = ['image/jpeg','image/png','application/pdf','application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if($.inArray(files[0]['type'], allowedFormat) === -1){
                // alert('invalid Format');
               showErrorMsg("Invalid Format")

               this.destroyDropify("poa");
                return false;
            }else{
                return true;
            }
        },
        upload_POA: function(e){

            var files = e.target.files || e.dataTransfer.files;
            let allowedToUpload = this.filterAllowedUploadFormat(files, "poa");

            if(!allowedToUpload) return false;

            this.model.file = files[0];
            this.model.upload_category = "POA";

            let formData = new FormData();

            $.each(this.model, function(k,v){
                formData.append(k,v);
            })

            this.$http.post("/document/upload",formData).then(res=>{
                let d = res.data;
                
                if(d.status){
                    showSuccessMsg(d.message)
                }else{
                    this.destroyDropify("poa");
                    showErrorMsg(d.message);
                }

            }).catch((err) => {
                handleErrorResponse(err.status);
                this.processing = false;
                if (err.status == 422) {
                    this.errors = err.data;
                }
            });
            
        },

        upload_Import_Number: function(e){
            var files = e.target.files || e.dataTransfer.files;
            let allowedToUpload = this.filterAllowedUploadFormat(files, "import_number");
            

            if(!allowedToUpload) return false;

            this.model.file = files[0];
            this.model.upload_category = "IMPORT NUMBER APPLICATION";


            let formData = new FormData();

            $.each(this.model, function(k,v){
                formData.append(k,v);
            })

            this.$http.post("/document/upload",formData).then(res=>{
                let d = res.data;
                
                if(d.status){
                    showSuccessMsg(d.message)
                }else{
                    this.destroyDropify("import_number");
                    showErrorMsg(d.message);
                }

            }).catch((err) => {
                handleErrorResponse(err.status);
                this.processing = false;
                if (err.status == 422) {
                    this.errors = err.data;
                }
            });
        },


        formSubmit:function(){
            this.processing = true;
            this.$http.post("/senders/validateBusinessRegistration",this.model).then(response=>{
                console.log(response);
                this.processing = false;
                this.$root.$emit("emit_businessRegistrationData",this.model);
                $('.sender-business-form-modal').modal("hide");
                // window.location.href="/"
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


