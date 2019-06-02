<template>
    <div class="modal postage-options-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span>POSTAGE OPTIONS</span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true"  @click='cancel'>×</button>
                </div>

                <div class="postage-wrapper">
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="form-group">
                            <table>
                                <tr v-for="(rate, index) in rates">

                                    <td>
                                        <h4>{{ rate.postage_type }} <span>{{ rate.currency }} {{ rate.total }}</span></h4>
                                        <h6>{{ rate.estimated_delivery }}</h6>
                                        <h6>{{ rate.desc }}</h6>
                                    </td>
                                    <td >
                                        <input type="radio" name="postage"  v-model="model.postage" v-bind:value="index" v-bind:id="'postage' + index"/>
                                        <label v-bind:for="'postage' + index"></label>
                                    </td>
                                </tr>


                                 <tr>
                                    <td>
                                        <h4>Insurance <span></span></h4>
                                        <h6>Get additional coverage.</h6>
                                    </td>
                                    <td >
                                        <input type="checkbox" name="insurance" v-model="model.insurance" id="insurance" />
                                        <label for="insurance" v-on:click="withInsurance = !withInsurance"></label>
                                    </td>
                                </tr>
                                <tr v-if="withInsurance">
                                    <td >
                                        <div style="margin-top:20px">
                                            <div v-bind:class="{'form-group': true, 'focused':model.InsuranceCoverAmount, 'has-error has-danger': errors.InsuranceCoverAmount }">
                                                <input type="number" v-model="model.InsuranceCoverAmount" id="InsuranceCoverAmount" class="form-control" placeholder="">
                                                <span class="bar"></span>
                                                <label for="InsuranceCoverAmount">Insured value in CAD <span class="required">*</span></label>
                                                <span class="help-block" v-for="error in errors.InsuranceCoverAmount">{{ error }}</span>
                                            </div>
                                            <h4 style="text-align:right">Premium Fee: <b>${{ model.InsuranceCoverAmount }} CAD</b></h4>
                                        </div>
                                    </td>
                                </tr>

                            </table>

                            <hr>
                            <h3 class="total">Total: <b>${{ total }} CAD</b></h3>
                        </div>
                    </form>

                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true" @click='cancel'>CANCEL</button>
                    <button type="button" class="btn btn-info btn-outline" data-dismiss="modal" aria-hidden="true" @click='saveCarrier'>UPDATE</button>
                </div>
            </div>
        </div>
    </div>
</template>


<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse, showLoading, stopLoading} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                rates:{},
                selectedRate:{},
                index:1,
                withRates:false,
                orderSummary:{},
                carrierSelected:{},
                total:0.00,
                firstRateIntial:true,
                postage_option_edit:{},
                withInsurance:false
            }
        },
        created(){
            //LISTENS TO EMITTED MODELS IN SINGLESHIPMENT MODEL
            this.$root.$on("postage_option_edit",(d,c)=>{
                this.model = d;
                //this.selectedRate = c[this.model.shipment_id];
                this.rates = c[this.model.shipment_id];
                this.model.insurance = false;
                this.withInsurance = false;
            })
        },

        watch:{
            model:{
                handler: function(data){
                    this.calculateTotalPostage();
                },
                deep:true
            }
        },
        methods:{
            cancel: function(){
                this.$root.$emit("cancel_added",true);
            },
            refresh:function(){
                 this.getRates(this.orderSummary);
            },
            calculateTotalPostage:function(){
                let i = (this.model.InsuranceCoverAmount > 0)? this.model.InsuranceCoverAmount:0;
                let x = this.getRateValue(this.model.postage, i);
                x = (x)? x : 0;


                if(i && x){
                    this.total = (parseFloat(i) + parseFloat(x)).toFixed(2);;
                }
                else if(i){
                    this.total = i;
                }else{
                    this.total = x;
                }
            },

            getRates:function(data){
                this.withRates = false;
                this.processing = true;
                this.$http.post("/shipment/rates",data).then(res=>{
                    // console.log(res.data.rates)
                    if(res.data.status){
                        this.rates = res.data.response.total;
                        this.withRates =true;
			            this.$root.$emit("checkoutmodalship",this.rates);
                        //reset status
                        this.changeStatus= false;
                    }else{
                        showErrorMsg(res.data.message);
                        this.rates = {};
                    }
                    this.processing = false;
                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.processing = false;
                    if (err.status == 422 || err.status == 500) {
                        this.errors = err.data;

                        this.rates = {};
                        this.withRates =false;
                    }
                });
                
            },


            getRateValue:function(index, i){
                if(isNaN(index))
                    index = 0;
                    
                    console.log('>>> '+index);

                    this.model.Carrier = this.rates[index].carrier;
                    this.model.CarrierDesc = this.rates[index].postage_type;
                    this.model.Currency = this.rates[index].currency;
                    this.model.TotalFees = this.rates[index].total;
                    this.model.Duration = this.rates[index].estimated_delivery;
                    this.carrierSelected =this.rates[index];
                    this.model.InsuranceCoverAmount = i;
                    
                    this.$root.$emit("recompute",true);
                    //this.orderSummary.InsuranceCoverAmount = (this.model.insured_value > 0)? this.model.insured_value:0;
                    console.log(this.rates[index])

                if(this.rates.length && !isNaN(index)){
                    return this.rates[index].total;
                }else{
                    return 0;
                }
            },

            handleDeliveryDays:function(data){
                
                let days = this.handleDateDifference(data['est_delivery_time'])

                if(data['est_delivery_time'] == ""){
                    return "Delivery not specified"+ "("+data['carrier']+")";
                }
                else if( days == 1){
                    return "1 Day Delivery";
                }else{
                    return "1 -"+ days+" Days  Delivery " + "("+data['carrier']+")";
                }
            },


            handleDateDifference:function(d2){
                var date1 = new Date();
                var date2 = new Date(d2);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());

                return Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            },
            saveCarrier:function(){
                this.processing = true;
                let updateCarrier = {};
                updateCarrier['orderSummary'] = this.model;
                updateCarrier['carrierSelected'] = this.carrierSelected;
                this.$http.post("/document/update_carrier",updateCarrier).then(response=>{

                    console.log(response);

                    this.processing = false;
                    showSuccessMsg(response.data.message);
                    if(this.errors){
                        this.errors = [];
                    }
                }, response=>{
                    this.errors = response.data.errors;
                    this.processing = false;
                    showErrorMsg(response.data.message)
                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.processing = false;
                    if (err.status == 422) {
                        this.errors = err.data;
                    }
                });        
            }
        }
    };
</script>
<style scoped>
.postage-wrapper
{
    margin: 20px;
}
</style>

