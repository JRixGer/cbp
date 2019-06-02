<template>
    <div class="modal add_credit_card-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h4 v-if="addCreditCard" class="modal-title" id="mySmallModalLabel">Add Credit Card
                    </h4>
                    <h4 v-else-if="purchaseCreditYN" class="modal-title" id="mySmallModalLabel">Confirmation
                    </h4>
                    <h4 v-else-if="purchaseCredit" class="modal-title" id="mySmallModalLabel">Purchase Credit
                    </h4>
                    
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body" v-if="addCreditCard">
                    <form @submit.prevent="submit" class="mt-4 main_form pt-4 pb-4 pl-3 pr-3" id="addCreditCardFrm">
                        <h3 class="mb-4"><img src="images/background/creditcard.png" alt="Merchant Equipment Store Credit Card Logos" width="30%" height="50%" class="mt-2 float-right"/></h3>
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
                                <label class='control-label'></label>
                                <input class='form-control card-expiry-year' placeholder='YYYY' size='4' type='text' name="ccExpiryYear" v-model="fields.ccExpiryYear">
                            </div>
                        </div>

                        <div class='form-row'>
                            <div class='form-group'>
                                <button class='form-control btn btn-primary text-white' type='submit'>Save Card</button>
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-body" v-else-if="purchaseCreditYN" style="margin-top:30px">
                    <div class='form-row'>
                        <div class='form-group'>
                            <div class='col-xs-12'>
                                <h4>Do you wish to add credits? <br><small>Save on per-transaction fees when you purchase credits</small> </h4>
                            </div>
                        </div>                            
                        <div style="width:auto">
                            <div class='form-group'>
                                <div style="float:right; width:100px; padding:5px">
                                    <button class='form-control btn btn-primary text-white' type='button' v-on:click="processAction('purchaseYNStepYes')">Yes</button>
                                </div>
                                <div style="float:right; width:100px; padding:5px">
                                    <button class='form-control btn btn-secondary text-dark' type="button" v-on:click="processAction('purchaseYNStepClose')">Cancel</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-body" v-else-if="purchaseCredit" style="margin-top:-30px">
                    <form @submit.prevent="submit" class="mt-4 main_form pt-4 pb-4 pl-3 pr-3" id="purchaseCreditFrm">
                        <h3 class="mb-4"></h3>
                        
                        <div class='form-row'>
                            <div class='col-xs-12 form-group  required'>
                                <label class='control-label text-dark'><span style="font-size:14px">How many credits do you wish to purchase?</span><br>(min $10.00CAD) </label>
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
                                <button class='form-control btn btn-primary text-white' type='submit'>Submit</button>
                            </div>
                        </div>
                    </form>

                </div>

            </div>
        </div>
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
import Datatable from '../components/Datatable.vue';
import Pagination from '../components/Pagination.vue';

var axios = require("axios");

export default {
    props: ['value'],
    components: { datatable: Datatable, pagination: Pagination},
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
            addCreditCard: true,
            purchaseCreditYN: false,
            purchaseCredit: false,
            stepAction: 'addCreditCard',
            totalAmount: 0,
        }
    },
    watch:{
        "fields.amount":function(data){
            this.totalAmount = ((parseFloat(this.fields.amount)+.3)/0.971).toFixed(2);
        }
    },
    methods: {
        submit() {
            showLoading();
            this.errors = {};
            this.success = {};

            if(this.stepAction == 'purchaseYNStepYes')
            {
                let maintainPayment = document.getElementById('purchaseCreditFrm');
                let formData = new FormData(maintainPayment);
                formData.append('action', this.stepAction);                
                this.$http.post('/payment/purchase_credit', formData).then(response => {
                    if (response.data.status == true) {
                        hideLoading();
                        this.success = response.data.stripeSuccess;
                        showSuccessMsg(this.success);
                        $('.add_credit_card-form-modal').modal('hide');
                    }else{
                        hideLoading();
                        this.errors = response.data.stripeError;
                        showErrorMsg(this.errors);
                        $('.add_credit_card-form-modal').modal('hide');
                    }
                }).catch(error => {
                    hideLoading();
                });
            }
            else
            {
                let maintainPayment = document.getElementById('addCreditCardFrm');
                let formData = new FormData(maintainPayment);
                formData.append('action', this.stepAction);  

                this.$http.post('/payment/add_card', formData).then(response => {
                    if (response.data.status == true) {
                        hideLoading();
                        this.$root.$emit("refresh_creditcards", true);
                        this.success = response.data.stripeSuccess;
                        showSuccessMsg(this.success);
                        this.addCreditCard = false;
                        this.purchaseCreditYN = true;               
                    }else{
                        hideLoading();
                            this.errors = response.data.stripeError;
                        showErrorMsg(this.errors);
                    }
                }).catch(error => {
                    hideLoading();
                });
            }
          
        },
        processAction(action)
        {
            if(action == 'purchaseYNStepClose')
            {
                this.addCreditCard = true;
                this.purchaseCreditYN = false;
                this.purchaseCredit = false;
                this.stepAction = action;               
            }
            else if(action == 'purchaseYNStepYes')
            {
                this.addCreditCard = false;
                this.purchaseCreditYN = false;
                this.purchaseCredit = true;
                this.stepAction = action;
            }
        }
    }
};
</script>
<style scoped>

.modal-dialog {
    width: 100%;
    max-width: 400px;
    margin: 30px auto;
}
.modal-header {
    margin-bottom: 5px;
}
.mx-input {
    width: 89% !important;
}

.dirty {
    border-color: #5A5;
    background: #EFE;
}

.dirty:focus {
    outline-color: #8E8;
}

.error {
    border-color: red;
    background: #FDD;
}

.error:focus {
    outline-color: #F99;
}

.mx-input {
    border-right: 1px solid #ffffff !important;
    border-top: 1px solid #ffffff !important;
    border-left: 1px solid #ffffff !important;
    border-bottom: 1px solid #ebebeb !important;
}
.mx-datepicker {
    width: auto !important;
}
.floating-labels .focused label {
    top: -10px !important;
    font-size: 11px;
    color: #008bf4;
} 
.form-group {
    text-align: left;
}   
.modal-body {
    padding-top: 0px !important;
    margin-top: -30px;
}
</style>