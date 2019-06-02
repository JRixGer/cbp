<template>
    <div class="modal confirmation-selectpayment-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title headingTop text-center font-weight-bold" id="mySmallModalLabel">
                        <span> Select Your Payment Method </span>                       
                </h4>
            </div>
            <form v-on:submit.prevent="formSubmit" role="form" method="POST">
                <div class="modal-body">
                        <div class="container">
                            <div class="row">
                                <!-- <div class="col-lg-4 col-md-4 col-sm-4 bg-white text-center border-right card">
                                    <div class="form-check">
                                        <label class="form-check-label" style="position: inherit;">
                                            <input type="radio" class="form-check-input" v-model="sel_method" value="paypal" style="position: inherit;left: 0;opacity: 1;"><br>
                                            <h4 class="text-center">Paypal</h4>
                                            <br>
                                            <img src="" width="100" height="50" alt="">
                                        </label>
                                    </div>
                                </div> -->
                                <div class="col-lg-6 col-md-6 col-sm-6 bg-white text-center border-right p-4">
                                    <div class="form-check">
                                        <label class="form-check-label" style="position: inherit;">
                                            <input type="radio" class="form-check-input" v-model="sel_method" value="card" style="position: inherit;left: 0;opacity: 1;" v-on:click="showAmount('c')"><br>
                                            <h4 class="text-center">Credit / Debit Card</h4>
                                            <br>
                                            <img src="images/background/creditcard.png" class="w-100" alt="">
                                        </label>
                                    </div>
                                </div>
                                <div class="col-lg-6 col-md-6 col-sm-6 bg-white text-center border-left p-4">
                                    <div class="form-check">
                                        <label class="form-check-label" style="position: inherit;">
                                            <input type="radio" class="form-check-input" v-model="sel_method" value="wallet" style="position: inherit;left: 0;opacity: 1;" :disabled="lowbalance" v-on:click="showAmount('w')"><br> 
                                            <h4 class="text-center">Wallet $ {{balance}}</h4>
                                            <br>
					                        <div v-if="lowbalance == true" style="color:#ff0000 !important;">Insufficient Balance</div>
                                            <i class="ti-wallet" style="font-size: 75px;"></i>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-lg-12">
                                <p>Transaction Fee: ${{transFee}}</p>
                            </div>
                        </div>
                </div>
                <div class="modal-footer">
                    <div style="width:100%">
                        <div style="float:left; width:50%">
                            <h5 class="summary-label-overall-total" >Amount: <span class="summary-info-overall-total">${{ amountWithFee }}CAD</span></h5>
                        </div>
                        <div style="float:right; width:50%">
                            <button class="btn btn-default" v-on:click="closeModal">Go Back</button>
                            <button class="btn btn-info" v-on:click="submit">Continue to Pay</button>
                        </div>
                    </div>
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
        	importBatch: '',
            fromWhere: '',
            balance: 0,
        	lowbalance: false,
        	sel_method: 'card',
        	processing: false,
        	model:{},
        	errors:{},
        	amountToPaid: 0,
            amountWithFee: 0,
            transFee:0
        }
    },
    created() {

    	//$(".confirmation-tos-modal").modal("hide");
        this.$root.$on("confirmation-selectpayment-modal",(e)=>{
            this.processing = false;
            $('.confirmation-selectpayment-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

        })
	
	    this.fetchWalletdata();
	
	    //LISTENS TO EMITTED CHECKOUT AMOUNT
	    this.$root.$on("checkoutmodalship",(c, ib, fromWhere)=>{
    	    console.log(c +'> rico '+this.balance);
            this.fromWhere = fromWhere;
            this.importBatch = ib;
    	    this.amountToPaid = parseFloat(c);
            this.amountWithFee = ((this.amountToPaid +.3)/0.971).toFixed(2);
    	    if(Number(this.balance) >=Number(this.amountToPaid))
            {
    		    this.lowbalance = false;
            }
            else
            {
    	        this.lowbalance = true;
            }
            this.transFee = (this.amountWithFee- this.amountToPaid).toFixed(2);
	    })	
	
    },
    watch:{

    },
    methods: {
            
    	fetchWalletdata: function() {
    	    this.$http.get("/addmoney/walletdetail/All").then(res=>{
                this.balance = parseFloat(res.data.cash).toFixed(2);
    		    if(this.balance > 0){
    		    	this.lowbalance = false;
    		    }else{
    		    	this.lowbalance = true;
    		    }
            });
        },
    	closeModal:function(){
            $(".confirmation-selectpayment-modal").modal("hide");
            this.$root.$emit("enable_buttons",true);
        },
        showAmount:function(d){

            if(d == 'c')
            {
                this.amountWithFee = ((this.amountToPaid +.3)/0.971).toFixed(2);
                this.transFee = (this.amountWithFee- this.amountToPaid).toFixed(2);
            }
            else
            {
                this.amountWithFee = this.amountToPaid.toFixed(2);
                this.transFee = 0;
            }

        },
    	submit:function(){

            //this.processing = true;
            //this.model.agree = false;

    	    if(this.sel_method == "paypal"){
        		alert('paypal');
    	    }
            else if(this.sel_method == "card")
            {
                   
    	        this.closeModal();
                this.$root.$emit("creditcard-modal", this.importBatch, this.fromWhere);
    	        this.$root.$emit("CardCheckout-modal",this.amountToPaid);

    	    }
            else if(this.sel_method == "wallet")
            {
    	    
    	        this.closeModal();
                this.$root.$emit("paywith-userwallet-modal", this.importBatch, this.fromWhere);
    	        this.$root.$emit("CardCheckout-modal",this.amountToPaid);
        
            }
        },
    	formSubmit:function(){


        }
    }
};
</script>
<style scoped>
.body-wrapper
{
    margin: 2px;
}
.summary-label-overall-total h5 {
    font-style: italic;
}
.summary-info-overall-total {
    border-bottom: 1pt solid black; 
    padding:0px !important;
    font-style: italic;
    color: #ffaa00;
    font-weight: 600;
}
</style>