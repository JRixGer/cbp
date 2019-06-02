<template>
    <div class="uspsbox-wrapper">
        <div class="overlay" v-if="processing">
            <svg class="circular" v-if="processing" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">USPS Box Options</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                    <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="card-body" >

                                <div class="row">
                                    <div class="col-md-12">
                                        <div v-bind:class="{'form-group': true, 'focused':model.weight, 'has-error has-danger': errors.weight }">
                                            <input type="number" v-model="model.weight" min="0" onkeypress="return event.charCode != 45" id="weight" v-on:change="getUSPSBoxOptions" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="weight">Weight <span class="required">*</span></label>
                                            <span style="font-size:12px;">{{ weightUnit  }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12" v-if="model.weight">
                                        <div v-bind:class="{'form-group': true, 'focused':model.usps_options }">

                                            <select v-model="model.usps_options" name="usps_options" class="form-control" id="upsps_option">
                                                <optgroup v-for="(options, index) in uspsOptions" v-bind:label="index">
                                                    <option v-for="option in options" v-bind:value="option.service_code+'|'+option.package_type">{{ option.package_type }} </option>
                                                </optgroup>
                                            </select>
                                            <label for="upsps_option">USPS Box Services</label>
                                        </div>

                                    </div>
                                </div>



                               
                               <!-- <ul>
                                   <li v-for="(option, index) in uspsOptions">
                                        <input type="radio" name="usps_options"  v-model="model.upsps_option" v-bind:value="option" v-bind:id="'option' + index" />
                                        <label v-bind:for="'option' + index">{{ option }}</label>
                                   </li>
                               </ul> -->
                        </div>

                    </form>
            </div>



        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ['country','singleShipmentModelUSPS','editShipmentModel'],
        mounted() {
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                uspsOptions:{},
                weightUnit:"Inches",
                sizeUnit:"Pounds",
                singleShipmentModel:{},
                component_error:false

            }
        },
        created(){
            
            //LISTENS TO EMITTED MODELS IN SINGLESHIPMENT MODEL
            this.singleShipmentModel = this.singleShipmentModelUSPS
            this.$root.$on("singleShipmentModelUSPS",(c)=>{
                // this.getUSPSBoxOptions(c);
                this.singleShipmentModel = c;
            })

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.usps_box_options
            })


            this.$root.$on("get_USPSBox_Options",(c)=>{
                console.log("getUSPSBoxOptions-main")
                this.getUSPSBoxOptions();
            })

            // will trigger event on metric change
            this.$root.$on("metric-toggle",(c)=>{
                this.metricToggler(c.unit)
                this.getUSPSBoxOptions()
            })


            if($cookies.get("imperial_metric") == "imperial"){
                this.sizeUnit = "Inches"
                this.weightUnit = "Pounds"
            }else{
                this.sizeUnit = "Centimeter"
                this.weightUnit = "Grams"
            }


            if($cookies.get("weight")){
                this.model.weight = $cookies.get("weight")
            }


            if(this.editShipmentModel){
                let data = this.editShipmentModel
                this.$set(this.model,"weight",data.weight)
                this.getUSPSBoxOptions()
                this.$set(this.model,"usps_options",data.letter_option)


                // this.$root.$emit("USPSOptionsModel",this.model);

            }



        },
// 
        watch:{

            model:{
                handler:function(data){
                     this.$root.$emit("USPSOptionsModel",this.model);

                     if($cookies.get("imperial_metric") == "imperial"){
                        if(this.model.weight > 70){
                            showErrorMsg("Weight above 70lbs is not allowed")
                            this.model.weight = 70
                        }
                    }else{
                        if(this.model.weight > 31751.5){
                            showErrorMsg("Weight above 31751.4659g is not allowed")
                            this.model.weight = 31751.5
                        }
                    }
                },
                deep:true
            }

        },
        methods:{

            metricToggler:function(metric){
                if(metric == "imperial"){
                    this.sizeUnit = "Inches";
                    this.weightUnit = "Pounds";
    
                }else{
                    this.sizeUnit = "Centimeter";
                    this.weightUnit = "Grams";
                    
                }
            },

            getUSPSBoxOptions:function(){
                this.processing = true;
                this.$http.post('/shipment/uspsoptions',this.singleShipmentModel).then(res=>{
                    let x = res.data
                    if(x.status){
                        this.uspsOptions = x.options
                    }else{
                        showErrorMsg(x.error)
                    }

                    this.processing = false;


                }).catch((err) => {
                    handleErrorResponse(err.status);
                   this.processing = false;
                });
            }
        }
    }
</script>
