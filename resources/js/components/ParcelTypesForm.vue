<template>
    <div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">PARCEL TYPES</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                    <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="card-body" >
                                <div class="row">
                                    <div class="col-md-12">
                                        <!-- <div class="row">
                                            <div class="col-md-12">
                                                <div v-bind:class="{'form-group': true, 'focused':model.weight, 'has-error has-danger': errors.weight }">
                                                    <input type="number" v-model="model.weight" min="0" id="weight" v-on:change="getUSPSBoxOptions" class="form-control" placeholder="">
                                                    <span class="bar"></span>
                                                    <label for="weight">Weight <span class="required">*</span></label>
                                                    <span style="font-size:12px;">Pounds</span>
                                                </div>
                                            </div>
                                        </div> -->

                                        <div class="row">
                                            <div class="col-md-12">
                                                <h4>Select One:</h4>
                                            </div>

                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.parcel_type" name="parcel_type" value="Letter" id="parcel_type_letter">
                                                <label for="parcel_type_letter">Letter</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.parcel_type" name="parcel_type" value="Box" id="parcel_type_box">
                                                <label for="parcel_type_box">Parcel</label>
                                            </div>
                                        </div>

                                    </div>

                                    <div class="col-md-12" v-if="model.parcel_type == 'Box' && country == 'US'" style="margin-top:10px">
                                        <h4>Is this USPS supplied?</h4>
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.usps_box_status" name="usps_box_status" value="yes" id="usps_box_yes">
                                                <label for="usps_box_yes">YES</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.usps_box_status" name="usps_box_status" value="no" id="usps_box_no">
                                                <label for="usps_box_no">NO</label>
                                            </div>
                                        </div>
                                    </div>

                                    <button class="btn btn-info" hidden v-bind:disabled="processing">Save and Continue</button>
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
        props: ["editShipmentModel"],
        mounted() {
            console.log('parcel type Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                component_error:false,
                country:"US",

            }
        },
        created(){
            // this.init();
            this.$root.$on("countrySwitchEvent",(e)=>{
                this.country = e;
            })

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.parcel_types
            })


            //listens to USPS letter options
            this.$root.$on("changeOptionToParcel",(weight)=>{

                this.model.parcel_type = "Box"
            });

            if(this.editShipmentModel){

                let data = this.editShipmentModel
                if(data.length && data.width && data.height){
                    // this.model.parcel_type = "Box" 
                    this.$set(this.model,"parcel_type","Box")
                    this.$set(this.model,"usps_box_status","no")


                }else{
                    
                    let lo = data.letter_option.split("|")
                    if(lo[0] == "US-FC"){
                        this.$set(this.model,"parcel_type","Letter")
                    }else{
                        this.$set(this.model,"parcel_type","Box")
                    }

                    if(data.carrier == "USPS"){
                        // this.model.usps_box_status = "yes";
                        this.$set(this.model,"usps_box_status","yes")
                    }else{
                        // this.model.usps_box_status = "no";
                        this.$set(this.model,"usps_box_status","no")


                    }

                }
            }

        },

        watch:{

            editShipmentModel:{
                handler:function(data){

                    if(data){

                       if(data.length && data.width && data.height){
                            // this.model.parcel_type = "Box" 
                            this.$set(this.model,"parcel_type","Box")
                            this.$set(this.model,"usps_box_status","no")


                        }else{
                            
                            let lo = data.letter_option.split("|")
                            if(lo[0] == "US-FC"){
                                this.$set(this.model,"parcel_type","Letter")
                            }else{
                                this.$set(this.model,"parcel_type","Box")
                            }

                            if(data.carrier == "USPS"){
                                this.$set(this.model,"usps_box_status","yes")
                            }else{
                                this.$set(this.model,"usps_box_status","no")


                            }

                        }
                    }
                },
                ddep:true
            },

            model:{
                handler:function(data){
                    this.$root.$emit("parcel_types",this.model);


                    // if(this.model.weight > 70){
                    //      showErrorMsg("Weight above 70lbs is not allowed")
                    //  }
                    
                    
                },
                deep:true
            }

        },
        methods:{

            // getUSPSBoxOptions:function(){
            //     console.log("getUSPSBoxOptions")
            //     this.$root.$emit("get_USPSBox_Options",true);
            // }

        }
    }
</script>
