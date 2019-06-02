<template>
    <div class="postage-wrapper">
        <div class="overlay" v-if="withRates==false">
            <svg class="circular" v-if="processing" viewBox="25 25 50 50">
                <circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /> 
            </svg>
        </div>
        <div class="card">
             <h4 class="card-title">POSTAGE OPTIONS</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                    <div class="refresh-wrapper">
                        <div class="overlay">
                            <!-- <div  class="mdi mdi-reload reload-postage"><p>Reload Postage</p></div> -->
                            <span  v-if="withRates==false && firstRateIntial==false && processing==false" v-on:click="refresh" class="mdi mdi-reload reload-postage"></span>
                        </div>
                        <!-- <button class="btn btn-info btn-sm" >Refresh Postage</button> -->
                    </div>
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                        <div class="form-group">
                            <table>
                                <tr v-for="(rate, index) in rates">
                                    <!-- <td>
                                        <h4>{{ rate.desc }} <span>{{ rate.currency }} {{ rate.total }}</span></h4>
                                        <h6>{{ handleDeliveryDays(rate) }}</h6>
                                        <h6>{{ rate.package_type }}</h6>
                                    </td> -->
                                    <td>
                                        <h4>{{ rate.postage_type }} <span>CAD {{ rate.total }}</span></h4>
                                        <h5>{{ rate.estimated_delivery }}</h5>
                                        <h5>{{ rate.desc }} </h5>
                                        <h6><b>{{ rate.package_type }}</b></h6>
                                        <h6><i>Carrier <span class="pull-right"><b>{{ (rate.carrier) ? rate.carrier : "" }}</b></span></i></h6>
                                        <h6><i>Rate <span class="pull-right"><b>{{ rate.currency }} {{ (rate.negotiated_rate) ? rate.negotiated_rate : rate.rate }}</b></span></i></h6>
                                        <h6><i>Markup <span class="pull-right"><b>{{ (rate.markup) ? rate.markup : "0.00" }}</b></span></i></h6>
                                        
                                        <h6 v-if="rate.truck_fee !='0.00'"><i>Truck Fee <span class="pull-right"><b>{{ (rate.truck_fee != "0.00") ? rate.truck_fee :  "0.00" }}</b></span></i></h6>
                                        <h6 v-else><i>Delivery Fee <span class="pull-right"><b>{{ (rate.cbp_delivery_fee != "0.00") ? rate.cbp_delivery_fee : "0.00" }}</b></span></i></h6>
                                        
                                        <h6><i>EDT <span class="pull-right"><b>{{ (rate.est_delivery_time) ? rate.est_delivery_time : "" }}</b></span></i></h6>
                                        <h6><i>Tax <span class="pull-right"><b>{{ (rate.tax) ? rate.tax : "0.00" }}</b></span></i></h6>
                                        <h6><i>Signature Fee <span class="pull-right"><b>{{ (rate.signature_fee) ? rate.signature_fee : "0.00" }}</b></span></i></h6>
                                        <h6><i>Insurance Fee <span class="pull-right"><b>{{ (rate.insurance_fee) ? rate.insurance_fee : "0.00" }}</b></span></i></h6>
                                    </td>
                                    <td >
                                        <input type="radio" name="postage"  v-model="model.postage" v-bind:value="index" v-bind:id="'postage' + index" />
                                        <label v-bind:for="'postage' + index"></label>
                                    </td>
                                </tr>

                                <tr v-if="displaySignature">
                                    <td colspan="2">
                                        <br>
                                        <h4>Do you require signature?</h4>
                                        <br>
                                        <input type="radio" name="signature" v-model.number="model.req_signature" value="1" id="signature-yes">
                                        <label for="signature-yes">YES</label>

                                        <input type="radio" name="signature" v-model.number="model.req_signature" value="0" id="signature-no">
                                        <label for="signature-no">NO</label>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <br>
                                        <h4>Insurance</h4>
                                        <br>
                                        <div v-bind:class="{'form-group': true, 'focused':model.insured_value, 'has-error has-danger': errors.insured_value }">
                                            <input type="number" v-model.lazy="model.insured_value" id="insured_value" onkeypress="return event.charCode != 45" min="0" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="insured_value">Insured value in CAD <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.insured_value">{{ error }}</span>
                                        </div>
                                    </td>
                                </tr>

                                <tr>
                                    <td colspan="2">
                                        <br>
                                        <h4>Coupon Code <span class="pull-right" style="color:green">{{ couponValueDisplay }}</span></h4>
                                        <div class="form-group">
                                            <input type="text" v-model="model.coupon" v-on:change="getCouponCode" class="form-control" id="coupon">
                                            <!-- <label for="coupon">Coupon Code</label> -->
                                            <span class="help-block required" >{{ errors.coupon }}</span>

                                        </div>
                                    </td>
                                </tr>

                                 <!-- <tr v-if="singleShipmentModel.insurance_model">
                                    <td>
                                        <h4>Insurance <span></span></h4>
                                        <h6>Get additional coverage.</h6>
                                        <div >
                                            <h6><b>Insured Value <span class="pull-right">CAD {{ singleShipmentModel.insurance_model.insured_value }}</span></b></h6>
                                            <h6><b>Premium Fee <span class="pull-right">CAD {{ singleShipmentModel.insurance_model.premium_fee }}</span></b></h6>
                                        </div>
                                    </td>
                                </tr> -->


                            </table>

                            <hr>
                            <h3 class="total">Total: <b>${{ total }} CAD</b></h3>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <!-- <InsuranceModal v-bind:singleShipmentModel="singleShipmentModel"></InsuranceModal> -->
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
                rates:{},
                index:1,
                withRates:false,
                singleShipmentModel:{},
                total:0.00,
                firstRateIntial:true,
                couponValueDisplay:"",
                couponValue:0,
                componentErrors:{},
                ratesHold:{},
                displaySignature:true,
                editShipmentRateStatus:true



            }
        },
        created(){


            //LISTENS TO EMITTED MODELS IN SINGLESHIPMENT MODEL
            this.$root.$on("singleShipmentModel",(c)=>{
                this.singleShipmentModel = c;

                    

                    //edit shipments
                    if(this.editShipmentModel){
                        this.$set(this.model,"req_signature",this.editShipmentModel.require_signature)
                        this.$set(this.model,"insured_value",this.editShipmentModel.insurance_cover)
                    }

            })


            this.$root.$on("changesStatus",(c)=>{
                if(c.recipient || c.parcel_types || c.parcel_dimensions || c.usps_options){
                    this.model = {}
                    this.rates = {};
                    this.withRates = false;
                    this.firstRateIntial = false;
                    this.couponValueDisplay = ""
                }else if(c.ship_from_address && c.postage_option && this.singleShipmentModel.recipient_model.country == "US"){
                    this.withRates = true;
                }


            });


            this.$root.$on("ShipFromAddressModel",(c)=>{
                if(this.model.details && this.singleShipmentModel.recipient_model.country == "US"){
                    this.setLowestRateByShipFrom(this.rates, this.singleShipmentModel.ship_from_address_model.country)
                }
            
            });


            this.$root.$on("displayCards",(c)=>{
                this.displaySignature = c.signatureRequire;
            })


            


             this.$root.$on("component_errors",(c)=>{
                let shipFrom = this.singleShipmentModel.ship_from_address_model
                let shipTo = this.singleShipmentModel.recipient_model

                let insurance = (this.model.insured_value) ? this.model.insured_value : 0;
                this.singleShipmentModel.insurance_model = {"insured_value": insurance, "currency":"CAD", "premium_fee":0};

                if(shipFrom.country == "CA" && shipTo.country == "CA"){
                    if(c.recipient==false && c.parcel_dimensions==false && c.ship_from_address==false){
                        this.getRates(this.singleShipmentModel);

                    }
                }

                else if(shipTo.country == "US"){

                    let parcel = this.singleShipmentModel.parcel_types;

                    if(parcel.parcel_type == "Letter"){
                        if(c.recipient==false && c.parcel_types==false && c.usps_letter_options==false && c.item_information==false){
                            this.getRates(this.singleShipmentModel);
                        }
                    }else{

                        if(parcel.usps_box_status =="yes"){
                            if(c.recipient==false  && c.parcel_types==false && c.usps_box_options == false && c.item_information==false){
                                this.getRates(this.singleShipmentModel);
                            }
                        }else{
                            if(c.recipient==false  && c.parcel_types==false  && c.item_information==false){
                                this.getRates(this.singleShipmentModel);
                            }
                        }
                    }

                    
                }

                else if(shipFrom.country == "CA" && shipTo.country == "US"){
                    let parcelType = this.singleShipmentModel.parcel_types.parcel_type

                    if(parcelType == "Box"){
                        if(c.recipient==false && c.parcel_types==false && c.parcel_dimensions==false  && c.ship_from_address==false){
                            this.getRates(this.singleShipmentModel);
                        }
                        
                    }else{
                        if(c.recipient==false && c.parcel_types==false && c.parcel_dimensions==false  && c.ship_from_address==false){
                            this.getRates(this.singleShipmentModel);
                        }
                    }
                }
                else if(shipFrom.country == "US" && shipTo.country == "US"){
                    let parcel = this.singleShipmentModel.parcel_types;

                    
                    if(parcel.parcel_type == "Letter"){
                        if(c.recipient==false && c.ship_from_address==false && c.parcel_types==false && c.usps_letter_options==false && c.item_information==false){
                            this.getRates(this.singleShipmentModel);
                        }
                    }else if(parcel.parcel_type == "Box"){
                        
                        if(parcel.usps_box_status =="yes"){
                            if(c.recipient==false && c.ship_from_address==false && c.parcel_types==false && c.usps_box_options == false && c.item_information==false){
                                this.getRates(this.singleShipmentModel);
                            }
                        }else{
                            if(c.recipient==false && c.ship_from_address==false && c.parcel_types==false  && c.item_information==false){
                                this.getRates(this.singleShipmentModel);
                            }
                        }
                    }
                }else{

                    if(Object.keys(shipFrom).length > 0 && Object.keys(shipTo).length > 0){
                        if(this.model.postage){
                            this.getUpdatedSelectedRate(this.singleShipmentModel);
                        }else{
                            this.getRates(this.singleShipmentModel);
                        }
                    }
                    
                }
                
            })



        },

        watch:{
            model:{
                handler: function(data){
                    if(this.rates[this.model.postage]){
                        this.model.details = this.rates[this.model.postage];
                        this.$root.$emit("PostageOptionsModel",this.model);
                    }
                        
                    
                    
                    this.calculateTotalPostage();
                },
                deep:true
            },

            "model.postage": function(data){
                console.log("model.postage")
                if(this.rates[this.model.postage]){
                    this.singleShipmentModel.postage_option_model = this.model;
                    let insurance = (this.model.insured_value) ? this.model.insured_value : 0;
                    this.singleShipmentModel.insurance_model = {"insured_value": insurance, "currency":"CAD", "premium_fee":0};
                    this.getUpdatedSelectedRate(this.singleShipmentModel);
                    

                }
            },

            "model.req_signature": function(data){
                    console.log("model.req_signature")
                    if(this.rates[this.model.postage]){
                        this.singleShipmentModel.postage_option_model = this.model;
                        let insurance = (this.model.insured_value) ? this.model.insured_value : 0;

                        this.singleShipmentModel.insurance_model = {"insured_value": insurance, "currency":"CAD", "premium_fee":0};
                        this.getUpdatedSelectedRate(this.singleShipmentModel);
                    }
            },

            "model.insured_value": function(data){
                    console.log("model.insured_value")
                    if(this.rates[this.model.postage]){
                        this.singleShipmentModel.postage_option_model = this.model;
                        this.singleShipmentModel.insurance_model = {"insured_value": data, "currency":"CAD", "premium_fee":0};
                        this.getUpdatedSelectedRate(this.singleShipmentModel);
                        this.$root.$emit("insurance_model",this.model)
                    }
            },

            editShipmentModel:{
                handler:function(data){
                    if(data){
                        
                       
                        if(this.singleShipmentModel.recipient_model.country == "US" && this.singleShipmentModel.ship_from_address_model.country =="US"){
                            let parcel_types = {}
                            let parcel_dimensions = {}
                            if(data.length && data.width && data.height){
                                parcel_types.parcel_type = "Box";
                                parcel_types.usps_box_status = "no";

                                parcel_dimensions.weight = data.weight
                                parcel_dimensions.length = data.length
                                parcel_dimensions.width = data.width
                                parcel_dimensions.height = data.height


                            }else{
                                
                                let lo = data.letter_option.split("|")

                                if(lo[0] == "US-FC"){
                                    parcel_types.parcel_type = "Letter";
                                }else{
                                    parcel_types.parcel_type = "Box";

                                }

                                parcel_dimensions.weight = data.weight
                                parcel_dimensions.usps_options = data.letter_option

                                if(data.carrier == "USPS"){
                                    parcel_types.usps_box_status = "yes";
                                }else{
                                    parcel_types.usps_box_status = "no";
                                }

                            } 
                            // alert(JSON.stringify(parcel_types))
                            this.$set(this.singleShipmentModel,"parcel_types",parcel_types)
                            this.$set(this.singleShipmentModel,"parcel_dimensions_model",parcel_dimensions)
                            this.$set(this.singleShipmentModel,"usps_options_model",parcel_dimensions)

                            this.getRates(this.singleShipmentModel);  

                            // alert(JSON.stringify(this.singleShipmentModel.parcel_dimensions_model))
                        }
                        else if (this.singleShipmentModel.recipient_model.country == "CA" && this.singleShipmentModel.ship_from_address_model.country =="CA"){
                            let parcel_dimensions = {}
                            parcel_dimensions.weight = data.weight
                            parcel_dimensions.length = data.length
                            parcel_dimensions.width = data.width
                            parcel_dimensions.height = data.height

                            this.$set(this.singleShipmentModel,"parcel_dimensions_model",parcel_dimensions)

                            this.getRates(this.singleShipmentModel);
                        }
                    }
                },  
                deep:true
            }
        },
        methods:{

            showModal:function(){
                if(!this.model.insurance){
                    var el = $('.insurance-modal')
                    el.modal({backdrop: 'static', keyboard: false}) ;
                }else{
                    this.model.insured_value = 0;
                    this.model.premium_fee = 0;
                    this.getRates(this.singleShipmentModel);
                    this.calculateTotalPostage();
                    // this.model.insured_value();
                }
            },

            refresh:function(){

                this.$root.$emit("forms-component-validation-check",true);


            },


 

            calculateTotalPostage:function(){

                    console.log("pre-total");

                    // let i = (this.singleShipmentModel.insurance_model) ? this.singleShipmentModel.insurance_model.premium_fee : 0;
                    let x = (this.getRateValue(this.model.postage)) ? this.getRateValue(this.model.postage) : 0;

                    // if( x){
                    //     this.total = parseFloat(x);
                    // }
                    // else if(i){
                    //     this.total = i;
                    // }else{
                    // }
                    
                    this.total = x;

                    // deduct coupon
                    if(this.model.coupon && this.total != 0){

                        if(this.model.coupon_model.type == "$"){
                            this.couponValue = this.model.coupon_model.amount;

                        }else{
                            this.couponValue = (this.model.coupon_model.amount / 100) * this.total
                        }
                        
                        if(this.couponValue > this.total){
                            this.total = 0
                        }else{
                            this.total = this.total - this.couponValue;
                        }

                    }

                

                    this.total = parseFloat(this.total).toFixed(2);
                    this.model.total = this.total
                
                
            },

            // filterUSPSOptions:function(shipmentData, rates){
            //     let selectedUSPSOption = shipmentData.usps_options_model.upsps_option;
            //     let _rates = []
            //     if(selectedUSPSOption){
            //         $.each(rates, function(k,v){

            //         })
            //     }
            // },


            getRates:function(data){
                this.withRates = false;
                this.processing = true;
                this.$http.post("/shipment/rates",data).then(res=>{

                    if(res.data.status){
                        this.rates = res.data.response;
                        this.ratesHold = res.data.response;
			         // this.$root.$emit("checkoutmodalship",this.rates);


                        this.handleAutoSelectPostage(this.rates);

                        this.withRates = true;

                        //reset status
                        this.changeStatus= false;

                        
                     
                         //SEND ERROR TO RECIPIENT IF NO RATES
                        if(this.rates.length == 0){
                            console.log("emited");

                            this.$root.$emit("no-rates",true);
                        }
                        
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

            getUpdatedSelectedRate:function(data){
                this.withRates = false;
                this.processing = true;
                this.$http.post("/shipment/ratesByCarrier",data).then(res=>{

                    if(res.data.status){
                        this.rates = this.ratesHold;
                        let newRate = res.data.response;
                        this.replaceRateSelectedOnUpdate(newRate);

                        this.calculateTotalPostage();

                        if(newRate.address_flow){
                            this.$root.$emit("auto-select-shipfrom",newRate.address_flow)
                        }
                        
                     // this.$root.$emit("checkoutmodalship",this.rates);


                        // this.handleAutoSelectPostage(this.rates);

                        this.withRates = true;

                        //reset status
                        this.changeStatus= false;
                     
                         //SEND ERROR TO RECIPIENT IF NO RATES
                         if(this.rates.length == 0){
                            console.log("emited");

                             this.$root.$emit("no-rates",true);
                         }
                        
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


            replaceRateSelectedOnUpdate:function(newRate){
                let rate = []
                // console.log("this.rates");
                // console.log(this.rates);
                $.each(this.rates,function(k,v){
                    if(v.service_code == newRate.service_code && v.package_type == newRate.package_type && v.postage_type_code == newRate.postage_type_code){
                        rate.push(newRate);
                    }else{
                        rate.push(v)
                    }
                })

               this.rates = rate;
            },


            getRateValue:function(index){
                if(this.rates.length){
                    return this.rates[index].total;
                }else{
                    return 0;
                }
                // return this.rates[index].rate;
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


            getCouponCode:function(){

                this.$http.get("shipment/getCouponCode?code="+this.model.coupon).then(res=>{
                    if(res.data.status){
                        // this.model.coupon = res.data.coupon.coupon
                        this.model.coupon_model = res.data.coupon
                        this.handleCoupon();
                    }else{

                        showErrorMsg("Coupon code is invalid!");
                        this.$set(this.errors,"coupon","Coupon code is invalid!")
                        // this.errors.coupon = "Coupon code is invalid!";
                        this.couponValueDisplay = "";
                        delete this.model.coupon_model
                    }

                    if(this.model.details){
                        this.calculateTotalPostage();
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

                    if(this.model.details){
                        this.calculateTotalPostage();
                    }
                }
            },

            handleAutoSelectPostage:function(rates){
                this.getLowestRate(rates);
            },

            getLowestRate:function(rates){
                let x = [];

                let hold_rate = [];
                let postageIndex = [];
                $.each(rates,function(k,v){
                    hold_rate.push(v)
                    postageIndex.push(k)
                })


                var self = this
                let i = 0;

                this.$set(this.model,"details",hold_rate[i])
                this.$set(this.model,"postage",postageIndex[i])
                this.$set(this.model,"total",hold_rate[i]['total'])
                this.$set(this.model,"withRates",true)
                this.$root.$emit("PostageOptionsModel",this.model);
                this.calculateTotalPostage();
            },

            setLowestRateByShipFrom:function(rates, country){
                let x = [];
                let _flow = ""
                if(country == "CA"){
                    _flow = "CA_US"
                }else{
                    _flow = "US_US"
                }

                let hold_rate = [];
                let postageIndex = [];
                $.each(rates,function(k,v){
                    if( v.address_flow == _flow){
                        hold_rate.push(v)
                        postageIndex.push(k)
                    }
                })


                var self = this
                let i = 0;

                this.$set(this.model,"details",hold_rate[i])
                this.$set(this.model,"postage",postageIndex[i])
                this.$set(this.model,"total",hold_rate[i]['total'])
                this.$set(this.model,"withRates",true)
                this.$root.$emit("PostageOptionsModel",this.model);
                this.calculateTotalPostage();

            },


            handleDateDifference:function(d2){
                var date1 = new Date();
                var date2 = new Date(d2);
                var timeDiff = Math.abs(date2.getTime() - date1.getTime());

                return Math.ceil(timeDiff / (1000 * 3600 * 24)); 
            },

            formSubmit:function(){

            }

        }
    }
</script>
