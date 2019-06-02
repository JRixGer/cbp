<template>
    <div class="modal paywith-userwallet-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> PAY USING YOUR WALLET </span>                       
                    </h4>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" id="paywallet" method="POST">
                    <div class="modal-body">
            		    <span>Your wallet balance : {{ balance }}</span><br>
            		    <span>Checkout Amount : {{ amountToPaid }}</span><br>
            		    <hr>
            		    <span>New wallet balance : {{ newbalance }}</span><br>
            		    <input class='form-control' value='20' type='hidden' name="amount" v-model="fields.amount">

                    </div>
                    <div class="modal-footer">
                       <button class="btn btn-default" v-on:click="closeModal">Go Back</button>
                       <button class="btn btn-info" v-bind:disabled="processing" v-on:click="submit">Pay</button>
                    </div>
                </form>


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, showLoading, hideLoading} from '../helpers/helper';
export default {
    prop: ['hidden'],
    mounted() {
    },
    data(){
        return {
            processing: false,
            fields: {},
            errors:{},
    	    walletdata: {},
    	    balance: 0,
    	    amountToPaid: 0,
    	    newbalance: 0,
            batchIds:'',
            fromWhere: '',
        }
    },
    created(){

        this.$root.$on("paywith-userwallet-modal",(d, fromWhere)=>{
            console.log('batch -> '+d)
            this.processing = false;
            this.batchIds = d;
            this.fromWhere = fromWhere;
            $('.paywith-userwallet-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

        })

        this.$root.$on("shipment-submitted",(e)=>{
            this.processing = false;
            this.closeModal();
        })

    	this.$root.$on("checkoutmodalship",(c)=>{
            this.amountToPaid = parseFloat(c).toFixed(2);
    	     this.newbalance=parseFloat(this.balance).toFixed(2)-this.amountToPaid;

    	    this.newbalance= parseFloat(this.newbalance).toFixed(2);
    	     console.log(parseFloat(this.balance).toFixed(2)+ "-"+this.amountToPaid);

    	     console.log(parseFloat(this.newbalance).toFixed(2));
        })

	    this.fetchWalletdata();

    },
    watch:{

    },
    methods:{

        closeModal:function(){
            $(".paywith-userwallet-modal").modal("hide");
	        $(".confirmation-selectpayment-modal").modal("show");
            this.$root.$emit("enable_buttons",true);
        },
        submit:function(){
    	    showLoading();
    	    this.errors = {};
            this.processing = true;
    	      
    	    let paywallet = document.getElementById('paywallet');
    	    let formData = new FormData(paywallet);
    	    this.fields.amount = this.amountToPaid;
            this.fields.batchIds = this.batchIds;
            this.fields.fromWhere = this.fromWhere;
    	    this.$http.post('/addmoney/paywithwallet', this.fields).then(response => {
        		if (response.data.status == true) {
        		    hideLoading();
        		    this.$root.$emit("confirmation-print",true);
        		}else{
        		    hideLoading();
                    this.processing = false;
        		    this.errors = response.data.stripeError;
        		    showErrorMsg(this.errors);
        		}

                // this.processing = false;


    	    }).catch(error => {
    		    hideLoading();
                this.processing = false;

    	    });
        },

        formSubmit:function(){


        },

    	fetchWalletdata: function() {
    	    this.$http.get("/addmoney/walletdetail/All").then(res=>{
                        this.walletdata = res.data.transaction_history;
                        this.balance = parseFloat(res.data.cash).toFixed(2);
    		    hideLoading();

            });
        }


    }
}
</script>