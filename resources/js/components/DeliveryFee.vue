<template>
    <div class="postage-wrapper">
        <div class="overlay" v-if="withRates==false">
            <svg class="circular" v-if="processing" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div class="card">
             <h4 class="card-title">PAYMENTS</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                    <div class="refresh-wrapper">
                        <!-- <button class="btn btn-info btn-sm" v-if="withRates==false && firstRateIntial==false" v-on:click="refresh">Refresh Fees</button> -->
                         <span  v-if="withRates==false && firstRateIntial==false" v-on:click="refresh" class="mdi mdi-reload reload-postage"></span>
                    </div>
                    <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <table>
                            <tr >
                               
                                <td>
                                    <h4>Delivery Fee <span>$ {{ rate }} CAD </span></h4>
                                </td>
                            </tr>

                            <tr>
                                <td>
                                    <br>
                                    <h4>Coupon Code <span class="pull-right">{{ couponValueDisplay }}</span></h4>
                                    <div class="form-group">
                                        <input type="text" v-model="model.coupon" v-on:change="getCouponCode" class="form-control" id="coupon">
                                        <!-- <label for="coupon">Coupon Code</label> -->
                                    </div>
                                </td>
                            </tr>

                        </table>
                    </form>

                    <hr>
                    <h3 class="total">Total: <b>${{ total }} CAD</b></h3>
                </div>
            </div>
        </div>
        <InsuranceModal></InsuranceModal>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ["editShipmentModel"],
        mounted() {
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                withRates:false,
                total:0.00,
                singleShipmentModel:{},
                firstRateIntial:true,
                rate:0.00,
                couponValueDisplay:"",
                couponValue:0,
            }
        },
        created(){
            //LISTENS TO EMITTED MODELS IN SINGLESHIPMENT MODEL
            this.$root.$on("singleShipmentModel",(c)=>{
                this.singleShipmentModel = c;
                console.log(c)
                if(this.firstRateIntial){
                    // this.getRates(c);
                    this.firstRateIntial = false
                }else{
                    this.rate = 0.00;
                    this.withRates = false;
                }
            })


            this.$root.$on("component_errors",(c)=>{

                
                if(this.singleShipmentModel.parcel_letter_status == "yes"){
                    if(c.recipient==false && c.item_information==false){
                        this.getRates(this.singleShipmentModel);    
                    }
                }else if(this.singleShipmentModel.parcel_letter_status == "no"){
                    if(c.recipient==false && c.item_information==false && c.parcel_dimensions==false){
                        this.getRates(this.singleShipmentModel);    
                    }
                }
                
                
            })


        },

        watch:{
            rate:{
                handler: function(data){
                    if(this.rate){
                        // this.model.details = this.rates[this.model.postage];
                        this.$root.$emit("DeliveryFeeModel", this.model);
                        this.calculateTotalFee();
                    }
                    
                },
                deep:true
            },

            editShipmentModel:{
                handler:function(data){
                    this.$set(this.singleShipmentModel, "parcel_dimensions_model",{"weight":data.weight})
                    // this.getRates(this.singleShipmentModel);    
                    this.refresh()

                },
                deep:true
            }
        },
        methods:{

            refresh:function(){

                this.$root.$emit("forms-component-validation-check",true);

                // this.getRates(this.singleShipmentModel);
            },

            getRates:function(data){
                this.withRates = false;
                this.processing = true;
                this.$http.post("/shipment/rates",data).then(res=>{
         
                    if(res.data.status){
                        this.rate = parseFloat(res.data.rate).toFixed(2);
                        this.model.rate = this.rate; 
                        // this.$root.$emit("checkoutmodalship",this.model);
                        this.withRates =true;

                        //reset status
                        this.changeStatus= false;
                        
                    }else{
                        showErrorMsg(res.data.message);
                        this.rate = 0.00;
                        
                    }
                    
                    this.total = this.rate
                    this.processing = false;

                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.processing = false;
                    if (err.status == 422 || err.status == 500) {
                        this.errors = err.data;
                        this.rate = 0;
                        this.withRates =false;
                    }
                });
                
            },


            calculateTotalFee:function(){
                // try{

                    if(this.model.coupon_model && this.rate != 0){

                        if(this.model.coupon_model.type == "$"){
                            this.couponValue = this.model.coupon_model.amount;

                        }else{
                            this.couponValue = (this.model.coupon_model.amount / 100) * this.total
                        }
                        
                        if(this.couponValue > this.rate){
                            this.total = 0
                        }else{
                            this.total = this.rate - this.couponValue;
                        }

                    }else{
                        this.total = this.rate;
                    }

                    this.model.total = this.total
                // } catch(e){
                //     console.log(e)
                // }
            },


            getCouponCode:function(){

                this.$http.get("shipment/getCouponCode?code="+this.model.coupon).then(res=>{
                    if(res.data.status){
                        // this.model.coupon = res.data.coupon.coupon
                        this.model.coupon_model = res.data.coupon
                        this.handleCoupon();
                    }else{

                        showErrorMsg("Coupon code is invalid!");
                        this.couponValueDisplay = "";
                        delete this.model.coupon_model
                    }

                    if(this.rate){
                        this.calculateTotalFee();
                    }
                })
            },


            handleCoupon:function(){
                if(this.model.coupon_model){
                    if(this.model.coupon_model.type == "$"){
                        this.couponValueDisplay =  "($"+this.model.coupon_model.amount+")";
                        this.couponValue = this.model.coupon_model.amount

                    }else{ 
                        //percentage
                        this.couponValueDisplay =  "("+this.model.coupon_model.amount+"%)";
                        this.couponValue = (this.model.coupon_model.amount / 100) * this.total;

                    }

                    if(this.model.rate){
                        this.calculateTotalFee();
                    }
                }
            },


            formSubmit:function(){

            }

        }
    }
</script>
