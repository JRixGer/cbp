<template>
    <div>
        <div v-bind:class="{'card':true}">
             <h4 class="card-title">CBP INSURANCE</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="" role="form" method="POST">
                        <div v-bind:class="{'form-group': true, 'focused':model.insured_value, 'has-error has-danger': errors.insured_value }">
                            <input type="number" v-model="model.insured_value" id="insured_value" onkeypress="return event.charCode != 45" min="0" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="insured_value">Insured value in CAD <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.insured_value">{{ error }}</span>
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
    props:["singleShipmentModel"],
    mounted() {
    },
    data(){
        return {
            processing: false,
            model:{ insured_value:0,premium_fee:0 },
            errors:{},
        }
    },
    created(){
        // this.init();
        // console.log(this.singleShipmentModel)

        //RECIPIENT
        // this.$root.$on("recipientInfoModel",(c)=>{
        //     this.singleShipmentModel.recipient_model = c

        //     this.handleInsuranceRate()
        // })
        
    },
    watch:{
        model:{
            handler:function(data){
                this.$root.$emit("insurance_model",this.model);
                
                // this.handleInsuranceRate()
            },
            deep:true
        }
    },
    methods:{

        // init:function(){
        //     this.$http.get("/api/countries").then(res=>{
        //         this.countries = res.data;
        //     })

        // },

        handleInsuranceRate:function(){
            let x = parseInt(parseFloat(this.model.insured_value) / 100);
            let r = 0;

            
            if(this.singleShipmentModel){


                if(this.singleShipmentModel.recipient_model.country == "CA"){
                    if(x > 1){

                        r = (x - 1)*3.50;
                        this.model.premium_fee = r;
                    }
                    this.model.premium_fee = r
                }else {
                    if(x > 1){
                        r = (x - 1)*4;
                        this.model.premium_fee = r;
                    }
                    this.model.premium_fee = r
                    
                }

                this.$root.$emit("insurance_model",this.model);
            }
        },




    }
}
</script>