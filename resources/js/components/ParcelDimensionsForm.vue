<template>
    <div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">PARCEL DIMENSIONS</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                    <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="card-body" v-if="withPostage == 'yes'">
                                <h5>Dimensions:</h5>
                                <br>
                                <div class="row">
                                <div class="col-md-3">
                                    <div v-bind:class="{'form-group': true, 'focused':model.length, 'has-error has-danger': errors.length }">
                                        <input type="number" min="0" v-model.lazy="model.length" step="0.01" onkeypress="return event.charCode != 45"  id="length" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="length">Length <span class="required">*</span></label>
                                        <span style="font-size:12px;">{{ sizeUnit }}</span>
                                        <!-- <span class="help-block" v-for="error in errors.length">{{ error }}</span> -->
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div v-bind:class="{'form-group': true, 'focused':model.width, 'has-error has-danger': errors.width }">
                                        <input type="number" min="0" v-model.lazy="model.width" step="0.01" onkeypress="return event.charCode != 45" id="width" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="width">Width <span class="required">*</span></label>
                                        <span style="font-size:12px;">{{ sizeUnit }}</span>
                                        <!-- <span class="help-block" v-for="error in errors.width">{{ error }}</span> -->
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div v-bind:class="{'form-group': true, 'focused':model.height, 'has-error has-danger': errors.height }">
                                        <input type="number" min="0" v-model.lazy="model.height" step="0.01" onkeypress="return event.charCode != 45" id="height" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="height">Height <span class="required">*</span></label>
                                        <span style="font-size:12px;">{{ sizeUnit }}</span>
                                        <!-- <span class="help-block" v-for="error in errors.height">{{ error }}</span> -->
                                    </div>
                                </div>

                                <div class="col-md-3">
                                    <div v-bind:class="{'form-group': true, 'focused':model.weight, 'has-error has-danger': errors.weight }">
                                        <input type="number" min="0" v-model.lazy="model.weight" step="0.01" onkeypress="return event.charCode != 45" id="weight" class="form-control" placeholder="">
                                        <span class="bar"></span>
                                        <label for="weight">Weight <span class="required">*</span></label>
                                        <span style="font-size:12px;">{{ weightUnit }}</span>
                                        <!-- <span class="help-block" v-for="error in errors.weight">{{ error }}</span> -->
                                    </div>
                                </div>
                                <button class="btn btn-info" hidden v-bind:disabled="processing">Save and Continue</button>
                                </div>
                        </div>

                        <div class="card-body" v-else>
                            <div class="col-md-12">
                                <div v-bind:class="{'form-group': true, 'focused':model.weight, 'has-error has-danger': errors.weight }">
                                    <input type="number" min="0" v-model.lazy="model.weight" step="0.01" onkeypress="return event.charCode != 45" id="weight" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="weight">Weight <span class="required">*</span></label>
                                    <span style="font-size:12px;">Pounds</span>
                                    <span class="help-block" v-for="error in errors.weight">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                    </form>
            </div>



        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ['withPostage','activetTab','editShipmentModel'],
        mounted() {
            console.log('parcel dimensions Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                sizeUnit:"Inches",
                weightUnit:"Pounds",
                metric:"",
                component_error:false
            }
        },
        created(){
            // this.init();
            // console.log("parcel dimensions")
            // console.log(this.$route)

            this.$root.$on("parcel_dimensions_model_no_errors",(c)=>{
                this.errors = [];
            })

            this.$root.$on("parcel_dimensions_model_errors",(c)=>{
                this.errors = c.errors
            })

            // will trigger event on metric change
            this.$root.$on("metric-toggle",(c)=>{
                this.metricToggler(c.unit)
            })

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.parcel_dimensions
            })


            if($cookies.get("weight")){
                this.model.weight = $cookies.get("weight")
            }

            
            if($cookies.get("imperial_metric") == "imperial"){
                this.sizeUnit = "Inches"
                this.weightUnit = "Pounds"
            }else{
                this.sizeUnit = "Centimeter"
                this.weightUnit = "Grams"
            }



            if(this.editShipmentModel){
                let d  = this.editShipmentModel;
                this.$set(this.model,"weight",d.weight);
                this.$set(this.model,"width",d.width);
                this.$set(this.model,"length",d.length);
                this.$set(this.model,"height",d.height);
            }


            

        },

        watch:{
            

            "model.width":function(data){
                if(data < 0){
                    this.model.width = 0;
                }
                // this.$root.$emit("ParcelDimensionsModel",this.model);
            },

            "model.length":function(data){
               if(data < 0){
                    this.model.length = 0;
                }
                 // this.$root.$emit("ParcelDimensionsModel",this.model);
            },

            "model.height":function(data){
                if(data < 0){
                    this.model.height = 0;
                }
                // this.$root.$emit("ParcelDimensionsModel",this.model);
            },

            "model.weight":function(data){
                if(data < 0){
                    this.model.weight = 0;
                }
                // this.$root.$emit("ParcelDimensionsModel",this.model);
            },

            // "model": function(data){
                // this.$root.$emit("ParcelDimensionsModel",this.model);
            // }

            model:{    
                handler:function(data){
                    if(this.activetTab == "do"){

                        if(data.weight){
                            this.$root.$emit("ParcelDimensionsModel",this.model);
                        }

                    }else{
                        if(data.length && data.width  && data.height  && data.weight){
                            console.log(data)
                            this.$root.$emit("ParcelDimensionsModel",this.model);
                        }
                        
                    }


                    if(this.$route.path == "/shipment/single/do"){
                        var weightImperial = 99
                        var weightMetric = 44905.63
                    }else{
                        var weightImperial = 70
                        var weightMetric = 31751.5
                    }
                    if($cookies.get("imperial_metric") == "imperial"){
                        if(data.weight > weightImperial){
                            showErrorMsg("Weight above "+weightImperial+"lbs is not allowed")
                        }
                    }else{
                        if(data.weight > weightMetric){
                            showErrorMsg("Weight above "+weightMetric+"g is not allowed")
                        }
                    }


                    this.filterInput(data.length,"length")
                    this.filterInput(data.width,"width")
                    this.filterInput(data.height,"height")
                    this.filterInput(data.weight,"weight")
                },
                deep:true
            }
        },
        methods:{
            nonFloatingNumbers:function(data){
                console.log(data)
            },

            filterInput:function(data, field, regex=false){
                let re = /[^0-9.\d]/gi
                if(data){
                    console.log(data);
                    // console.log(this.items[0]);
                    if(regex){
                        re = regex;
                        this.$set(this.model, field, data.replace(re, ''))
                    }else{
                        console.log("replace")
                        console.log(data.replace(re, ''))
                        this.$set(this.model, field, data.replace(re, ''))
                    }
                }

            },

            metricToggler:function(metric){
                if(metric == "imperial"){
                    this.sizeUnit = "Inches";
                    this.weightUnit = "Pounds";
    
                }else{
                    this.sizeUnit = "Centimeter";
                    this.weightUnit = "Grams";
                    
                }
            }
        }
    }
</script>
