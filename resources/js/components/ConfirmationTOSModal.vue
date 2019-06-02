<template>
    <div class="modal confirmation-tos-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> CONFIRMATION & TERMS OF SERVICE</span>
                       
                    </h4>
                    <button type="button" class="close" v-on:click="closeModal" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <!-- <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST"> -->

                    <div class="overlay" v-if="processing">
                        <svg class="circular" v-if="processing" viewBox="25 25 50 50">
                            <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
                        </svg>
                    </div>
                    <div class="modal-body">
                        <div style="font-size:12px;">
                        <p>
                            By check marking this you agree this is the legal equivalent of your manual signature. You certify to the best of you ability, that the information you have provided us is accurate. You agree that Cross Border Pickups will not, under any circumtance, be liable for loss of or damage to any pacakge which has arisen from any cause outside reasonable control.

                        </p>
                        
                        <p>
                            Any discrepancies between your manifest and the actual parcel count received by Cross Border Pickups will incur a $75(CAD) administration fee -  charged to you before the release of your goods.

                        </p>

                        <p>
                            Any restricted foods shipped or misdeclaration of value or any other information that the CBP or US FDA authorities deem unacceptable will incur a $300(CAD) administration fee on top of any CBP fines -  charged to you before the release of your goods.
                        </p>

                        </div>
                       
                       <div style="text-align:center">
                           <input type="checkbox" v-model="model.agree"  id="agree">
                           <label for="agree">I AGREE</label>
                       </div>

                    </div>
                    <div class="modal-footer">
                       <button class="btn btn-info btn-block" v-on:click="submit" v-if="model.agree" v-bind:disabled="processing">SUBMIT</button>
                       <button class="btn btn-default btn-block" v-on:click="closeModal" v-else v-bind:disabled="processing">CANCEL</button>
                    </div>
                <!-- </form> -->


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, showLoading, hideLoading} from '../helpers/helper';
export default {
    props:[],
    mounted() {
    },
    data(){
        return {
            processing: false,
            model:{},
            errors:{},
        }
    },
    created(){

        this.$root.$on("confirmation-tos",(e)=>{
            this.processing = false;
            $('.confirmation-tos-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

        })

         /*this.$root.$on("shipment-submitted",(e)=>{
            this.processing = false;
            //this.closeModal();
        })*/
    },
    watch:{

    },
    methods:{

        closeModal:function(){
            $(".confirmation-tos-modal").modal("hide");
            this.$root.$emit("enable_buttons",true);
        },
        submit:function(){
            this.processing = true;
            this.model.agree = false;
            //this.$root.$emit("confirmation-print-modal",true);
            this.closeModal();
            this.$root.$emit("confirmation-selectpayment-modal",true);



        },

        formSubmit:function(){
            
        }


    }
}
</script>