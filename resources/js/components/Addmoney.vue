<template>
<div class="postage-wrapper">
    <div class='row'>
        <div class='col-md-4'></div>
        <div class='col-md-4 bg-white mt-4 card'>
            <form @submit.prevent="submit" class="mt-4 main_form pt-4 pb-4 pl-3 pr-3" id="addmoneywallet">
        		<h3 class="mb-4"><big>Purchase Credit</big> <br><small>Pay with credit card</small> <img src="images/background/creditcard.png" alt="Merchant Equipment Store Credit Card Logos" width=" 30%" height="50%" class="mt-2 float-right"/></h3>

                <div v-if="withSavedCard">
                    <div class='form-row'>
                        <div class='col-xs-12 form-group'>
                            <label class='control-label text-dark'>Charge to saved card</label>
                            <h6 v-html="savedCardIinfo"></h6>
                        </div>
                    </div>
                </div>
                <div  v-else>
                    <div class='form-row'>
                        <div class='col-xs-12 form-group  required'>
                            <label class='control-label text-dark'>Card Number</label>
                            <input autocomplete='off' class='form-control card-number' size='20' type='text' name="card_no" v-model="fields.card_no">
                        </div>
                    </div>
                    <div class='form-row'>
                        <div class='col-xs-4 form-group cvc required'>
                            <label class='control-label text-dark'>CVV</label>
                            <input autocomplete='off' class='form-control card-cvc' placeholder='ex. 311' size='4' type='text' name="cvvNumber" v-model="fields.cvvNumber">
                        </div>
                        <div class='col-xs-4 form-group expiration required'>
                            <label class='control-label text-dark'>Expiration</label>
                            <input class='form-control card-expiry-month' placeholder='MM' size='2' type='text' name="ccExpiryMonth" v-model="fields.ccExpiryMonth">
                        </div>
                        <div class='col-xs-4 form-group expiration required'>
                            <label class='control-label'> </label>
                            <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text' name="ccExpiryYear" v-model="fields.ccExpiryYear">
                        </div>
                    </div>
                </div>

                <div class='form-row'>    
                    <div class='col-xs-4 form-group expiration required'>
                        <label class='control-label text-dark'>Amount</label>
                        <input class='form-control' placeholder='amount' type='text' name="amount" v-model="fields.amount">
                    </div>
                </div>

                <div class='form-row' v-if="totalAmount > 0">
                     <div class='col-xs-4 form-group'>
                     <h5>Total:
                     <span class='amount'>${{ totalAmount }}</span></h5>
                     </div>
                 </div>


                <div class='form-row'>
                    <div class='form-group'>
                        <button class='form-control btn btn-primary text-white' type='submit'>Add Money</button>
                        	
                    </div>
                </div>
            </form>
        </div>
        <div class='col-md-4'></div>
    </div>
</div>
</template>
<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
export default {
    data() {
        return {
                fields: {},
                error: {
                invalidFields: "Following fields have an invalid or a missing value:",
                general: "An error happened during submit.",
                generalMessage: "Form sending failed due to technical problems. Try again later.",
                fieldRequired: "{field} is required.",
                fieldInvalid: "{field} is invalid or missing.",
                fieldMaxLength: "{field} maximum characters exceeded."
            },
            savedCard: {},
            savedCardIinfo: '',
            withSavedCard: true,
            totalAmount: 0,
        }
    },
    created() {
        this.init();
    },
    watch:{
        "fields.amount":function(data){
            this.totalAmount = ((parseFloat(this.fields.amount)+.3)/0.971).toFixed(2);
        }
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
        submit() {
            showLoading();
            this.errors = {};
            this.success = {};
          
            let addmoneywallet = document.getElementById('addmoneywallet');
            let formData = new FormData(addmoneywallet);
            formData.append('withSavedCard', this.withSavedCard); 
            this.$http.post('/addmoney/stripe', formData).then(response => {
            	if (response.data.status == true) {
            	    hideLoading();
            	    this.success = response.data.stripeSuccess;
            	    showSuccessMsg(this.success);
            	    //this.$root.$emit("TopNav",true);
            	    this.$router.push('/shipment/wallet/');
            	}else{
            	    hideLoading();
                    this.errors = response.data.stripeError;
            	    showErrorMsg(this.errors);
            	}
            }).catch(error => {
                hideLoading();
            });
          
        },
    },
};
</script>
<style>
form.main_form h3{
    font-size: 25px;
}form.main_form h3 small{
    font-size: 18px;
}form.main_form h3 img{
        margin-top: -7px !important;
}
.form-group{
    margin-bottom: 16px;}
</style>