<template>
    <div class="modal creditcard-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog modal-md">
        <div class="modal-content bg-white mt-4 card">
            <div class="modal-header">
                <h3 class="mb-4 w-100"><big>Payment</big> <br><small>Pay with credit card</small> <img src="images/background/creditcard.png" alt="Merchant Equipment Store Credit Card Logos" width=" 20%" height="20%" class="float-right" style="margin-top:-20px;"/></h3>
            </div>
    	    <form class="mt-4 main_form pt-4 pb-4 pl-3 pr-3" v-on:submit.prevent="formSubmit" method="POST" id="paywithstripe">	   

        	    <div class="modal-body" style="margin-top:-30px">
                    <div v-if="withSavedCard">
                        <div class='form-row'>
                            <div class='col-xs-12 form-group'>
                                <label class='control-label text-dark'>Charge to saved card</label>
                                <h6 v-html="savedCardIinfo"></h6>
                            </div>
                        </div>
                    </div>

                    <div v-else>    
        	           <div class='form-row'>
                            <div class='col-xs-12 form-group  required'>
                                <label class='control-label text-dark'>Card Number</label>
                                <input autocomplete='off' class='form-control card-number' size='20' type='text' name="card_no" v-model="fields.card_no">
                            </div>
                        </div>
                        <div class='form-row'>
                            <div class='col-xs-4 form-group cvc required'>
                                <label class='control-label text-dark'>CVV</label>
                                <input autocomplete='off' class='form-control card-cvc' size='4' type='text' name="cvvNumber" v-model="fields.cvvNumber">
                            </div>
                            <div class='col-xs-4 form-group expiration required'>
                                <label class='control-label text-dark'>Expiration Month</label>
                                <input class='form-control card-expiry-month' size='2' type='text' name="ccExpiryMonth" v-model="fields.ccExpiryMonth">
                            </div>
                            <div class='col-xs-4 form-group expiration required'>
                                <label class='control-label'>Expiration Year</label>
                                <input class='form-control card-expiry-year' size='4' type='text' name="ccExpiryYear" v-model="fields.ccExpiryYear">
                            </div>
                        </div>
                    </div>    
        	    </div>
        	    <div class="modal-footer">
        	       <button class="btn btn-default" v-on:click="closeModal">Go Back</button>
        	       <button class="btn btn-info" v-bind:disabled="processing" v-on:click="submit">Pay </button>
                </div>
    	    </form>
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
    data() {
        return {
        	fields: {},
        	processing: false,
        	model:{},
        	errors:{},
        	amountToPaid: 0,
            amount: 0,
            importBatch: '',
            fromWhere: '',
            savedCard: {},
            savedCardIinfo: '',
            withSavedCard: true,
        }
    },
    created() {

        this.init();

        this.$root.$on("creditcard-modal",(e)=>{
            this.processing = false;
            $('.creditcard-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

        })

       this.$root.$on("checkoutmodalship",(c, bi, fromWhere)=>{
            console.log(c +'> jose '+ bi);
            this.batchIds = bi;
            this.amountToPaid = c;		
            this.fromWhere = fromWhere;
        })
        
    },
    watch:{

    },
    methods: {        
        init:function(){
            this.$http.get("/payment/get_saved_card").then(res=>{
                if(res.data.status){
                    this.savedCard = res.data.savedCard;
                    console.log(this.savedCard);
                    this.savedCardIinfo = 'Card type: '+this.savedCard[0].brand +'<br>Last 4 digits: '+this.savedCard[0].last4 +'<br>Exp: '+this.savedCard[0].exp_month +'/'+this.savedCard[0].exp_year;
                    this.withSavedCard = true;

                }
                else
                {
                    this.withSavedCard = false;
                }
            })           
        },	
    	closeModal:function(){
            $(".creditcard-modal").modal("hide");
            $(".confirmation-selectpayment-modal").modal("show");
            this.$root.$emit("enable_buttons",true);
        },
            
    	submit:function(){
	        showLoading();
	        this.errors = {};
            let paywithstripe = document.getElementById('paywithstripe');
	        let formData = new FormData(paywithstripe);
          
            formData.append('amount', this.amountToPaid);
            formData.append('batchIds', this.batchIds);
            formData.append('fromWhere', this.fromWhere);
            formData.append('withSavedCard', this.withSavedCard);

            this.processing = true;
	        this.$http.post('/addmoney/paywithstripe', formData).then(response => {
        		if (response.data.status == true) {
        		    hideLoading();
        		    console.log(response.data.status);
        		    this.$root.$emit("confirmation-print",true);
        		    this.$root.$on("shipment-submitted",(e)=>{
        			    this.processing = false;
        			    this.closeModal();
        		    })
        		}else{
        		    hideLoading();
                    this.processing = false;
        		    this.errors = response.data.stripeError;
        		    showErrorMsg(this.errors);
        		}
	        }).catch(error => {
		        //hideLoading();
	        });
      
        },
    	formSubmit:function()
        {


        }
    }
};
</script>
<style>
form.main_form h3
{
    font-size: 25px;
}
form.main_form h3 small
{
    font-size: 18px;
}
form.main_form h3 img
{
        margin-top: -7px !important;
}
.form-group{
    margin-bottom: 16px;
}

.modal-header {
    padding: 15px 15px 5px 15px;
}

</style>