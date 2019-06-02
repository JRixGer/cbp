<template>
    <div class="single-shipment-wrapper">
        <SingleShipmentToolbar v-bind:editShipmentModel="editShipmentModel" v-bind:activeTab="activetTab"></SingleShipmentToolbar>
       <div class="">
            <div class="row justify-content-left">
                <div class="col-md-4">
                    <RecipientInfoForm v-bind:editShipmentModel="editShipmentModel"></RecipientInfoForm>
                </div>
                <div class="col-md-4">
                    <!-- <SignatureRequireForm></SignatureRequireForm> -->
                    <IsLetterForm v-bind:editShipmentModel="editShipmentModel"></IsLetterForm>
                    
                    <!-- <ParcelTypesForm v-bind:country="country" v-if="displayCards.parcelTypes"></ParcelTypesForm> -->
                    <ParcelDimensionsForm v-bind:editShipmentModel="editShipmentModel" v-if="requireParcelDimension && displayCards.parcelDimensions" v-bind:activetTab="activetTab" v-bind:withPostage="withPostage"></ParcelDimensionsForm>
                    <ItemInformation v-bind:editShipmentModel="editShipmentModel" v-bind:shipmentModel="model"></ItemInformation>
                </div>


                <div class="col-md-4">
                    <!-- <ShipFromAddress></ShipFromAddress> -->
                    <DeliveryFee  v-bind:editShipmentModel="editShipmentModel"></DeliveryFee>
                    <!-- <PostageOptions ></PostageOptions> -->
                    
                    <!-- <button class="btn btn-info btn-lg btn-block" v-if="allowedToSubmit" v-on:click="showConfrim">SUBMIT ORDER</button> -->
                    <div class="row">
                    <!-- <button class="btn btn-info btn-lg btn-block"  v-on:click="showConfrim">SUBMIT ORDER</button> -->
                    <div class="col-md-6">
                        <button style="font-size:1rem" class="btn btn-info btn-lg btn-block" v-bind:disabled="disableSaveForLater" v-on:click="saveForLater">Save For Later</button>
                    </div>
                    <div class="col-md-6">
                        <button style="font-size:1rem" class="btn btn-success btn-lg btn-block" v-bind:disabled="disableSubmit" v-on:click="showConfrim">SUBMIT ORDER</button>
                    </div>
                    </div>

                </div>
            </div>
        </div>
        <SenderBusinessFormModal></SenderBusinessFormModal>
        <SenderMailingAddressFormModal></SenderMailingAddressFormModal>
        <ConfirmationTOSModal></ConfirmationTOSModal>
        <CheckoutModal></CheckoutModal>
        <CreditCardModal></CreditCardModal>
        <PaywithUserwalletModal></PaywithUserwalletModal>
        <ConfirmationPrintModal v-bind:shipmentID="shipmentID"></ConfirmationPrintModal>
    </div>
</template>


<script>

    import swal from 'sweetalert';
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {

            console.log('Profile Registration Component mounted.')

        },
        data(){
            return {
                processing: false,
                model:{
                    id:this.$route.query.id,
                    shipment_type:"DO",
                    unit_type:"imperial"
                },
                editShipmentModel:"",
                errors:{},
                activetTab:"do",
                withPostage:"no",
                country:"",
                rates:{},
                shipmentID:"",
                changeStatus:false,
                allowedToSubmit:false,
                disableSubmit:true,
                disableSaveForLater:true,
                requireParcelDimension:true,
                componentErrors:{
                    parcel_dimensions: true,
                    recipient:true,
                    ship_from_address:true,
                    item_information:true,
                    postage_option:true,
                    parcel_letter_status:true
                },
                displayCards:{
                    parcelTypes:false,
                    parcelDimensions:false,
                    itemInfo:false,
                }

            }
        },
        created(){
            this.$root.$off();
            
            //get shipfrom address and use canada as default address
            this.getAddress();


            //EDITSHIPMENT
            if(this.model.id){
                this.editShipment(this.model.id);
            }

             // will trigger event on country change
            this.$root.$on("countrySwitchEvent",(c)=>{
                this.country = c

            })

            // will trigger event on metric change
            this.$root.$on("metric-toggle",(c)=>{
                this.model.unit_type=c.unit
            })

            //RECIPIENT
            this.$root.$on("recipientInfoModel",(c)=>{
                this.model.recipient_model = c;
                this.changeStatus=true;
                this.handleCardsVisibility();
                this.handleRate();
                this.handleSubmitButtonVisibility();


            })

            //SIGNATURE REQUIRE
            this.$root.$on("signatureRequireModel",(c)=>{
                this.model.signature_require_model = c;


            })

            //PARCEL DIMENSIONS
            this.$root.$on("ParcelDimensionsModel",(c)=>{
                this.model.parcel_dimensions_model = c;
                this.changeStatus=true;
                this.handleRate();
                this.handleSubmitButtonVisibility();
                




            })


            //PARCEL TYPE
            this.$root.$on('parcel_types',(e)=>{
                
                this.model.parcel_types = e;
                
                if(e.parcel_type == "Letter" || e.parcel_type == "Envelope"){

                        this.requireParcelDimension = false;
                }else{

                    if(e.usps_box_status == "yes"){
                        this.requireParcelDimension = false;
                        
                    }else{

                        this.requireParcelDimension = true;
                    }

                }







            });



            //ITEM INFORMATION
            this.$root.$on("ItemInformationModel",(c)=>{
                this.model.item_information_model = c;

                if(this.handleItemInfoValidation(c)){
                    // showErrorMsg("Missing input for Item Details")
                }else{

                    if(c.length > 0){
                        this.componentErrors.item_information = false; 
                    }else{
                        this.componentErrors.item_information = true; 
                    }

                    this.handleRate();
                    this.handleSubmitButtonVisibility();
                    
                }




            })

            //SHIP FROM ADDRESS
            this.$root.$on("ShipFromAddressModel",(c)=>{


                this.model.ship_from_address_model = c;
                this.componentErrors.ship_from_address = false; 
                this.changeStatus=true;
                this.handleCardsVisibility();
                this.handleRate();
                this.handleSubmitButtonVisibility();


            })

            //is letter
            this.$root.$on("isLetter",(e)=>{
                this.model.parcel_letter_status = e
                this.changeStatus=true;
                this.handleRate();
                this.handleLetterStatusOnClick(e)
            });


            //DeliveryFeeModel
            this.$root.$on("DeliveryFeeModel",(c)=>{
                this.model.delivery_fee_model = c;
                this.componentErrors.postage_option = false; 
                this.handleSubmitButtonVisibility();


            })


            //WHEN POSTAGE REFRESH IS CLICKED AND ERROR RETRIEVING POSTAGE
            this.$root.$on("forms-component-validation-check",(c)=>{
                console.log("forms-component-validation-check")
                this.handleFormValidation();
            })


            //SUBMIT SHIPMENT
            this.$root.$on('confirmation-print',(c)=>{
                this.$http.post("/shipment/createShipment",this.model).then(res=>{

                    if(res.data.status){

                        this.$root.$emit("shipment-submitted",true);
                        this.shipmentID = res.data.shipment_id;
                    }
                })
            })



        },

        watch:{
            $route (to, from){
                console.log(to)
                console.log(from)

                if(to.query.id){
                    this.editShipment(to.query.id)
                }else{
                    // this.model.id = null
                    window.location.reload();
                }
                // this.show = false;
            }
        }, 

        methods:{

            init:function(){

            },

            editShipment:function(sid){

                this.$http.get("/shipment/edit/"+sid).then(res=>{
                    this.editShipmentModel = res.data;

                })
            },

            handleFormValidation:function(){
                this.validateRecipient();
                this.validateParcelDimensions();
                this.validateIsLetter();
                this.$root.$emit("component_errors",this.componentErrors)

            },

            handleItemInfoValidation:function(items){
                let _error = false;
                $.each(items,function(k,v){
                    if(v.country == ""){ _error = true}
                    if(v.description == ""){ _error = true}
                    if(v.quantity == "" || v.quantity == 0){ _error = true}
                    if(v.value == "" || v.value == 0){ _error = true}
                })

                return _error;
            },

            handleLetterStatusOnClick:function(status){
                if(status == "no"){
                     this.displayCards = {
                        parcelTypes:false,
                        parcelDimensions:true,
                        itemInfo:false,
                    }
                }else{
                    this.displayCards = {
                        parcelTypes:false,
                        parcelDimensions:false,
                        itemInfo:false,
                    }
                }

                // this.$root.$emit("component_errors",this.componentErrors);
                
            },


            handleCardsVisibility:function(){

                if(!this.model.ship_from_address_model || !this.model.recipient_model ) return false;
                let source = this.model.ship_from_address_model.country;
                let destination = this.model.recipient_model.country;


                //CANADA AS RECIPIENT
                if(destination == "CA"){

                    if(source == "CA"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:false,
                        }
                    }else if(source == "US"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                        }
                    }

                }else if(destination == "US"){
                    console.log("US_CA")
                    if(source == "CA"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                        }
                    }else if(source == "US"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                        }
                    }

                }else{

                    //international destiantion not CA & US
                    if(source == "CA"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                        }
                    }else if(source == "US"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                        }
                    }

                }


                this.handleLetterStatusOnClick(this.model.parcel_letter_status)
            },


            validateEmail: function(email) {
                var re = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
                return re.test(String(email).toLowerCase());
            },

            validatePhone:function(data){
                if(data.contact_no && data.contact_no.length < 10){
                    showErrorMsg("Contact Number must be at least 10 digits")
                }
            },

            validateRecipient:function(){

                try{

                    let d = this.model.recipient_model;

            
                    if(d.first_name  && d.last_name  && d.country  && d.address_1  && d.city   && d.province   && d.postal ){
                        this.$set(this.componentErrors,"recipient",false)

                        if(d.email){
                            if(!this.validateEmail(d.email)){
                                showErrorMsg("Invalid Email")
                                this.$set(this.componentErrors,"recipient",true)
                            }

                        }

                        if(d.contact_no){
                            this.validatePhone(d);
                        }
                    }else{
                        this.$set(this.componentErrors,"recipient",true)
                    }
                }catch(e){
                    
                }

            },

            validateIsLetter:function(){
                try{  

                    let d = this.model.parcel_letter_status;
        
                    if(d){
                        this.$set(this.componentErrors,"parcel_letter_status",false)
                    }else{
                        this.$set(this.componentErrors,"parcel_letter_status",true)
                    }
                }catch(e){
                    console.log(e)
                }

            },

            validateParcelDimensions:function(){
                try{  

                    let d = this.model.parcel_dimensions_model;
        
                    if( d.weight){
                        this.$set(this.componentErrors,"parcel_dimensions",false)
                    }else{
                        this.$set(this.componentErrors,"parcel_dimensions",true)
                    }
                }catch(e){
                    console.log(e)
                }

            },

            handleRate:function(){
                // this.handleFormValidation();
                        this.$root.$emit("singleShipmentModel",this.model);
                // if(this.model.parcel_letter_status == "no"){
                //     if(this.componentErrors.recipient==false && this.componentErrors.parcel_dimensions==false && this.componentErrors.item_information == false){

                //     }
                // }else{

                //     if(this.componentErrors.recipient==false  && this.componentErrors.item_information == false){
                //         this.$root.$emit("singleShipmentModel",this.model);

                //     }
                // }


                
            },



            handleSubmitButtonVisibility: function(){
                let w = this.componentErrors.item_information;
                let x = this.componentErrors.recipient;
                let y = this.componentErrors.parcel_dimensions;
                // let z = this.componentErrors.ship_from_address;
                // let v = this.componentErrors.postage_option;

                this.handleSaveForLaterVisibility();


                if(this.model.recipient_model){


                    if(this.model.parcel_letter_status == "no"){
                       
                        if( x==false && y==false && w ==false){
                            //submit
                            this.allowedToSubmit = true;
                        }else{
                            this.allowedToSubmit = false;
                        }
                    }else {

                        if( x==false  && w ==false){
                            //submit
                            this.allowedToSubmit = true;
                        }else{
                            this.allowedToSubmit = false;
                        }

                    }
                }


                if(this.allowedToSubmit){
                    this.disableSubmit = false
                }else{
                    this.disableSubmit = true
                }
            },

            getAddress:function(){

                this.$http.get("/senders/getShipFromAddresses").then(res=>{
                    // console.log(res.data)
                    // this.address = res.data;
                    this.model.ship_from_address_model = res.data.ca_address

                    this.componentErrors.ship_from_address = false; 
                    this.changeStatus=true;
                })
            },


            handleSaveForLaterVisibility:function(){
                this.model.shipment_status = 1;
                this.disableSaveForLater = true;
                if(this.model.recipient_model){
                    this.disableSaveForLater = false;
                }
            },


            saveForLater:function(){
                this.disableSaveForLater = true;
                this.$http.post("shipment/saveShipment",this.model).then(res=>{
                    console.log(res)
                    if(res.data.status){
                        swal("Shipment Saved!","To complete this shipment visit Allshipment Page","success").then((res)=>{
                            window.location.reload();
                        })
                    }else{
                        swal("Shipment Failed to Save!","There was error while saving the record","error")

                    }

                    this.disableSaveForLater = false;

                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.disableSaveForLater = false;
                    if (err.status == 422) {
                        this.errors = err.data;
                    }
                });
            },


            showConfrim:function(){
                this.$root.$emit("confirmation-tos",true);
                this.$root.$emit("checkoutmodalship",parseFloat(this.model.delivery_fee_model.total));

            }
            
        }
    }
</script>
