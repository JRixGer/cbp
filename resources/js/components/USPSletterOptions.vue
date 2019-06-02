<template>
    <div class="uspsbox-wrapper">
        <div class="overlay" v-if="processing">
            <svg class="circular" v-if="processing" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">USPS Letter Options</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                    <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="card-body" >
                               
                               <div class="row">

                                   <div class="col-md-12">
                                        <div v-bind:class="{'form-group': true, 'focused':model.weight, 'has-error has-danger': errors.weight }">
                                            <input type="number" v-model="model.weight" min="0" id="weight" onkeypress="return (event.charCode != 45 && event.charCode != 43)" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="weight">Weight <span class="required">*</span></label>
                                            <span style="font-size:12px;">{{ weightUnit }}</span>
                                        </div>
                                    </div>
                                    
                                   <!--  <div class="col-md-12">

                                        <select v-model="model.usps_options" name="usps_options" class="form-control" id="upsps_option">
                                            <optgroup v-for="(options, index) in uspsOptions" v-bind:label="index">
                                                <option v-for="option in options" v-bind:value="option.service_code+'|'+option.package_type">{{ option.package_type }} </option>
                                            </optgroup>
                                        </select>

                                        <span class="help-block" v-if="noResult">Options not available</span>

                                    </div> -->
                                </div>
                        </div>

                    </form>
            </div>



        </div>
    </div>
</template>

<style>
    .swal-text,
    .swal-footer{
        text-align: center;
    }
</style>

<script>
    import swal from 'sweetalert';
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ['country','singleShipmentModelUSPS','editShipmentModel'],
        mounted() {
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{
                    usps_options:"US-FC|Large Envelope or Flat"
                },
                errors:{},
                uspsOptions:{},
                weightUnit:"Inches",
                sizeUnit:"Pounds",
                singleShipmentModel:{},
                component_error:false,
                noResult:false

            }
        },
        created(){
            
            // console.log(this.singleShipmentModelUSPS)
            this.singleShipmentModel = this.singleShipmentModelUSPS
            // this.getUSPSLetterOptions();
            //LISTENS TO EMITTED MODELS IN SINGLESHIPMENT MODEL
            this.$root.$on("singleShipmentModelUSPS",(c)=>{
                this.singleShipmentModel = c;
                // this.getUSPSLetterOptions();
            })

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.usps_letter_options
            })

            // will trigger event on metric change
            this.$root.$on("metric-toggle",(c)=>{
                this.metricToggler(c.unit)
                // this.getUSPSBoxOptions()
                this.handleWeightAlert();


            })


            if($cookies.get("imperial_metric") == "imperial"){
                this.sizeUnit = "Inches"
                this.weightUnit = "Pounds"
            }else{
                this.sizeUnit = "Centimeter"
                this.weightUnit = "Grams"
            }

            if(this.editShipmentModel){
                let data = this.editShipmentModel
                this.$set(this.model,"weight",data.weight)

            }

            // this.$root.$on("get_USPSBox_Options",(c)=>{
            //     this.getUSPSLetterOptions();
            // })
            

        },
// 
        watch:{

            model:{
                handler:function(data){
                    this.$root.$emit("USPSOptionsModel",this.model);
                    this.component_error = false
                    this.handleWeightAlert();
                    $cookies.set("weight",this.model.weight)

                },
                deep:true
            }

        },
        methods:{

            handleWeightAlert:function(){
                if($cookies.get("imperial_metric") == "imperial"){
                    if(this.model.weight > 70){
                        showErrorMsg("Weight above 70lbs is not allowed")
                        this.model.weight = 70
                    }

                    //3.51oz
                    if(this.model.weight >= 0.22){
                        this.weightAlert("0.22","lbs")
                       
                    }
                }else{
                    if(this.model.weight > 31751.5){
                        showErrorMsg("Weight above 31751.4659g is not allowed")
                        this.model.weight = 31751.5
                    }

                    //3.51oz
                    if(this.model.weight >= 99.51){
                        this.weightAlert("99.51","g")
                    }
                }
            },
            weightAlert:function(weight, unit){
                 swal({
                      title: "Warning",
                      text: "Items greater than or equal to "+weight+unit+" cannot be shipped as letter mail. Please use parcel option instead.",
                      icon: "warning",
                      buttons: {
                        cancel:"Go Back",
                        change: "Use Parcel Instead"
                      },
                      
                    })
                    .then((value) => {
                        switch(value){
                            case "change":
                                this.$root.$emit("changeOptionToParcel",weight);
                                break;
                            default:
                                this.model.weight = "";
                                break;
                        }

                        
                    });
            },


            metricToggler:function(metric){
                if(metric == "imperial"){
                    this.sizeUnit = "Inches";
                    this.weightUnit = "Pounds";
    
                }else{
                    this.sizeUnit = "Centimeter";
                    this.weightUnit = "Grams";
                    
                }
            },


            getUSPSLetterOptions:function(){
                this.processing = true;
                this.$http.post('/shipment/uspsoptions',this.singleShipmentModel).then(res=>{
                    let x = res.data
                    if(x.status){
                         this.uspsOptions = x.options
                        this.noResult = false;
                         
                    }else{
                        // showErrorMsg(x.error)
                        this.noResult = true;
                    }

                    this.processing = false;


                }).catch((err) => {
                    // handleErrorResponse(err.status);
                    this.noResult = true;
                   this.processing = false;
                });
            }
        }
    }
</script>
