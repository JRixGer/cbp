<template>
    <div class="modal insurance-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> CBP INSURANCE</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true" v-on:click="closeModal">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body">

                        <div v-bind:class="{'form-group': true, 'focused':model.insured_value, 'has-error has-danger': errors.insured_value }">
                            <input type="number" v-model="model.insured_value" id="insured_value" class="form-control" placeholder="">
                            <span class="bar"></span>
                            <label for="insured_value">Insured value in CAD <span class="required">*</span></label>
                            <span class="help-block" v-for="error in errors.insured_value">{{ error }}</span>
                        </div>

                        <h4 style="text-align:right">Premium Fee: <b>${{ model.premium_fee }} CAD</b></h4>

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
    },
    watch:{
        model:{
            handler:function(data){
                this.handleInsuranceRate()
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


            if(this.singleShipmentModel.recipient_model.country == "CA"){
                if(x > 1){

                    r = (x - 1)*3.50;
                    this.model.premium_fee = r;
                }
                this.model.premium_fee = r
            }else if(this.singleShipmentModel.recipient_model.country == "US"){
                if(x > 1){
                    r = (x - 1)*4;
                    this.model.premium_fee = r;
                }
                this.model.premium_fee = r
                
            }
        },


        closeModal:function(){
            this.model.submit_status = false;

            this.$root.$emit("insurance_model",this.model);

        },


        formSubmit:function(){
            this.model.submit_status = true;
            if(this.model.insured_value > 0){
                $('.insurance-modal').modal('hide');
                this.$root.$emit("insurance_model",this.model);
            }
        }


    }
}
</script>