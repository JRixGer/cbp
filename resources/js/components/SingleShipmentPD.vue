<template>
    <div class="single-shipment-wrapper">
        <SingleShipmentToolbar v-bind:editShipmentModel="editShipmentModel" v-bind:activeTab="activetTab"></SingleShipmentToolbar>
       <div class="">
            <div class="row justify-content-left">
                <div class="col-md-4">
                    <RecipientInfoForm v-bind:editShipmentModel="editShipmentModel"></RecipientInfoForm>
                </div>
                <div class="col-md-4">
                    <ParcelTypesForm v-bind:editShipmentModel="editShipmentModel" v-bind:country="country" v-if="displayCards.parcelTypes"></ParcelTypesForm>
                    <USPSletterOptions v-bind:editShipmentModel="editShipmentModel" v-bind:singleShipmentModelUSPS="model" v-if="requireUSPSLetterOption && displayCards.uspsletteroptions"></USPSletterOptions>
                    <!-- <ItemInformation v-bind:shipmentModel="model"></ItemInformation> -->
                    <ParcelDimensionsForm v-bind:editShipmentModel="editShipmentModel" v-if="requireParcelDimension && displayCards.parcelDimensions" v-bind:activetTab="activetTab" v-bind:withPostage="withPostage"></ParcelDimensionsForm>
                    <USPSboxOptions v-bind:editShipmentModel="editShipmentModel" v-bind:singleShipmentModelUSPS="model" v-if="requireUSPSBoxOption && displayCards.uspsboxoptions"></USPSboxOptions>
                    <ItemInformation v-bind:editShipmentModel="editShipmentModel" v-bind:shipmentModel="model" v-if="displayCards.itemInfo"></ItemInformation>
                </div>


                <div class="col-md-4">
                    <PostageOptions v-bind:editShipmentModel="editShipmentModel" ></PostageOptions>
                    <ShipFromAddress v-bind:address="address" v-bind:editShipmentModel="editShipmentModel"></ShipFromAddress>
                    <!-- <SignatureRequireForm v-if="displayCards.signatureRequire"></SignatureRequireForm> -->
                    <!-- <InsuranceForm v-if="displayCards.insurance" v-bind:singleShipmentModel="model"></InsuranceForm> -->
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
            console.log(this.$route.query.id)

        },
        data(){
            return {
                processing: false,
                model:{
                    id:this.$route.query.id,
                    shipment_type:"PD",
                    unit_type:"imperial"
                },
                editShipmentModel:"",
                errors:{},
                activetTab:"pd",
                withPostage:"yes",
                country:"",
                rates:{},
                shipmentID:"",
                changeStatus:false,
                allowedToSubmit:false,
                requireParcelDimension:true,
                requireUSPSBoxOption:false,
                requireUSPSLetterOption: false,
                disableSubmit:true,
                disableSaveForLater:true,
                address:{},
                componentErrors:{
                    parcel_dimensions: true,
                    recipient:true,
                    ship_from_address:true,
                    item_information:true,
                    postage_option:true,
                    signature_require:true,
                    parcel_types:true,
                    usps_letter_options:true,
                    usps_box_options:true,
                },
                displayCards:{
                    parcelTypes:false,
                    parcelDimensions:false,
                    itemInfo:false,
                    uspsboxoptions:false,
                    uspsletteroptions:false,
                    signatureRequire:false,
                    insurance:false,
                },
                changesStatus:{
                    parcel_dimensions: false,
                    recipient:false,
                    ship_from_address:false,
                    item_information:false,
                    postage_option:false,
                    parcel_types:false,
                    usps_options:false,

                }

            }
        },
        created(){
            this.$root.$off();


            //FETCH ADDRESS
            this.getAddress();

            //EDITSHIPMENT
            if(this.model.id){
                this.editShipment(this.model.id);
            }

            $cookies.set("weight","");

             // will trigger event on country change
            this.$root.$on("countrySwitchEvent",(c)=>{
                this.country = c
                this.handleCardsVisibility();
                
            })

             // will trigger event on metric change
            this.$root.$on("metric-toggle",(c)=>{
                this.model.unit_type=c.unit
                this.changeStatus=true;
                this.validateParcelDimensions();
                this.handleRate();
                this.handleSubmitButtonVisibility();
                this.handleCardsVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);
                this.$root.$emit("singleShipmentModelShipFrom",this.model);
                
            })

            //RECIPIENT
            this.$root.$on("recipientInfoModel",(c)=>{

                this.model.recipient_model = c;
                this.changeStatus=true;
                this.$set(this.changesStatus,"recipient",true);
                this.handleCardsVisibility();
                // this.validateRecipient();
                this.handleRate();
                this.handleSubmitButtonVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);
                this.$root.$emit("singleShipmentModelShipFrom",this.model);


            })

            //SIGNATURE REQUIRE
            this.$root.$on("signatureRequireModel",(c)=>{
                this.model.signature_require_model = c;
                this.componentErrors.signature_require =  false;

                this.handleRate();
                this.handleCardsVisibility();


            })

            //LISTENS TO EMITTED MODELS IN INSURANCE Modal
            this.$root.$on("insurance_model",(c)=>{

                this.changeStatus=true;

                this.model.insurance_model = {"insured_value": c.insured_value, "currency":"CAD", "premium_fee":c.premium_fee};

                if(c.insured_value == "" || c.insured_value == 0){
                    delete this.model.insurance_model;
                }

                // this.handleRate();    
                
            })

            //PARCEL DIMENSIONS
            this.$root.$on("ParcelDimensionsModel",(c)=>{
                this.model.parcel_dimensions_model = c;
                // console.log(c)
                this.changeStatus=true;
                this.$set(this.changesStatus,"parcel_dimensions",true);

                this.validateParcelDimensions();
                this.handleRate();
                this.handleSubmitButtonVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);


            })

             //USPS BOX OPTIONS
            this.$root.$on("USPSOptionsModel",(c)=>{
                this.model.usps_options_model = c;
                // console.log(c)
                this.changeStatus=true;
                this.$set(this.changesStatus,"usps_options",true);

                this.$root.$emit("singleShipmentModelUSPS",this.model);
                this.componentErrors.parcel_dimensions =  false;
                this.model.parcel_dimensions_model = c;
                this.handleRate();
                this.handleSubmitButtonVisibility();
                // this.handleCardsVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);



            })



            //PARCEL TYPE
            this.$root.$on('parcel_types',(e)=>{
                
                this.model.parcel_types = e;

                delete this.model.usps_options_model
                this.handleCardsVisibility();
           

                if(e.parcel_type == "Letter" || e.parcel_type == "Envelope"){

                    delete this.model.parcel_dimensions_model;
                    
                    this.requireParcelDimension = false;
                    this.requireUSPSBoxOption = false;
                    this.requireUSPSLetterOption = true;
                    this.$root.$emit("singleShipmentModelUSPS",this.model);
                    this.$root.$emit("singleShipmentModelShipFrom",this.model);

                    this.$set(this.displayCards,"signatureRequire",false);



                }else{


                    this.requireUSPSLetterOption = false;
                    this.$set(this.displayCards,"signatureRequire",true);
                    

                    if(e.usps_box_status == "yes"){
                        this.requireParcelDimension = false;
                        this.requireUSPSBoxOption = true;
                        this.$root.$emit("singleShipmentModelUSPS",this.model);

                    }else if(e.usps_box_status == "no"){
                        this.$set(this.displayCards,"parcelDimensions",true);
                        this.requireParcelDimension = true;
                        this.requireUSPSBoxOption = false;

                    }

                }

                this.$root.$emit("displayCards",this.displayCards);
                this.$set(this.changesStatus,"parcel_types",true);

                this.componentErrors.parcel_types =  false;
                this.handleSubmitButtonVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);


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

                    // this.handleRate();
                    this.handleSubmitButtonVisibility();
                    // this.handleCardsVisibility();
                    
                }

                this.$set(this.changesStatus,"item_information",true);
                this.$root.$emit("changesStatus",this.changesStatus);



            })

            //SHIP FROM ADDRESS
            this.$root.$on("ShipFromAddressModel",(c)=>{
                // alert(JSON.stringify(c))
                this.model.ship_from_address_model = c;
                this.changeStatus=true;
                this.$set(this.changesStatus,"ship_from_address",true);
                this.handleCardsVisibility();
                this.handleRate();
                this.handleSubmitButtonVisibility();
                this.$root.$emit("changesStatus",this.changesStatus);


            })

             //SHIP FROM ADDRESS with autoselect
            this.$root.$on("ShipFromAddressModelAutoSelect",(c)=>{
                // alert(JSON.stringify(c))
                this.model.ship_from_address_model = c;

            })


            //POSTAGE OPTION

            this.$root.$on("PostageOptionsModel",(c)=>{
                this.model.postage_option_model = c;
                this.componentErrors.postage_option = false; 

                this.handleSubmitButtonVisibility();
                // this.handleCardsVisibility();

                this.changesStatus = {
                    parcel_dimensions: false,
                    recipient:false,
                    ship_from_address:true,
                    item_information:false,
                    postage_option:true,
                    parcel_types:false,
                    usps_options:false,

                }
                this.$root.$emit("changesStatus",this.changesStatus);

                
            })

            this.$root.$on("no-rates",(c)=>{
                // console.log("emiteds");
                this.$set(this.componentErrors,"recipient",true); 
                this.$root.$emit("component_errors",this.componentErrors)
                

            });



            //WHEN POSTAGE REFRESH IS CLICKED AND ERROR RETRIEVING POSTAGE
            this.$root.$on("forms-component-validation-check",(c)=>{
                this.handleFormValidation();
            })


            //SUBMIT SHIPMENT
            this.$root.$on('confirmation-print',(c)=>{
                this.model.shipment_status = 3;
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

            getAddress:function(data){

                this.$http.get("/senders/getShipFromAddresses").then(res=>{
                    // console.log(res.data)
                    this.address = res.data;
                })
            },

            handleFormValidation:function(){
                this.validateRecipient();
                this.validateSignatureRequired();
                this.validateParcelDimensions();
                this.validateSignatureRequired();
                this.validateShipFromAddress();
                this.validateParcelType();
                this.validateUSPSLetterOptions();
                this.validateItemInformation();
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


            handleCardsVisibility:function(){

                if(!this.model.ship_from_address_model || !this.model.recipient_model ) return false;
                let source = this.model.ship_from_address_model.country;
                let destination = this.model.recipient_model.country;


                if(!source || !destination) return false;


                //CANADA AS RECIPIENT
                if(destination == "CA"){

                    if(source == "CA"){

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:false,
                            uspsboxoptions:false,
                            uspsletteroptions:false,
                            signatureRequire:true,
                            insurance:true
                        }
                    }else if(source == "US"){
                        this.requireParcelDimension = true;
                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                            uspsboxoptions:false,
                            uspsletteroptions:false,
                            signatureRequire:true,
                            insurance:true
                        }
                    }

                }else if(destination == "US"){

                    // if(source == "CA"){

                    //     this.displayCards = {
                    //         parcelTypes:false,
                    //         parcelDimensions:true,
                    //         itemInfo:true,
                    //         uspsboxoptions:false,
                    //         uspsletteroptions:false,

                    //     }
                    // }else if(source == "US"){

                        this.displayCards = {
                            parcelTypes:true,
                            parcelDimensions:false,
                            itemInfo:true,
                            uspsboxoptions:true,
                            uspsletteroptions:true,
                            signatureRequire:true,
                            insurance:true

                        }

                        try{

                            if(this.model.parcel_types.parcel_type == "Letter"){
                                this.$set(this.displayCards,"signatureRequire",false);
                            }

                            if(this.model.parcel_types.usps_box_status == "no"){
                                this.$set(this.displayCards,"parcelDimensions",true);
                            }
                        }catch(e){

                        }
                    // }

                }else{

                    //international destiantion not CA & US

                        this.requireParcelDimension = true;

                        this.displayCards = {
                            parcelTypes:false,
                            parcelDimensions:true,
                            itemInfo:true,
                            uspsboxoptions:false,
                            uspsletteroptions:false,
                            signatureRequire:true,
                            insurance:true

                        }

                }
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

            },



            validateParcelDimensions:function(){
                try{

                    let d = this.model.parcel_dimensions_model;
        
                    if(d.length && d.width && d.height && d.weight){
                        this.$set(this.componentErrors,"parcel_dimensions",false)
                    }else{
                        this.$set(this.componentErrors,"parcel_dimensions",true)
                    }
                }catch(e){

                }

            },

            validateSignatureRequired:function(){

                let d = this.model.signature_require_model;
    
                if(d == 1 || d == 0){
                    this.$set(this.componentErrors,"signature_require",false)
                }else{
                    this.$set(this.componentErrors,"signature_require",true)
                }

            },

            validateShipFromAddress:function(){
                try{

                    let d = this.model.ship_from_address_model;
                    let r = this.model.recipient_model;
                    let p = this.model.parcel_types;

                    if(p.parcel_type == "Box" && r.country == "US" && p.usps_box_status == "no"){
                        this.$set(this.componentErrors,"ship_from_address",false)

                    }
                    else if(d.country ){
                        this.$set(this.componentErrors,"ship_from_address",false)
                    }else{
                        this.$set(this.componentErrors,"ship_from_address",true)
                    }
                }catch(e){
                        this.$set(this.componentErrors,"ship_from_address",false)
                    
                }

            },

            validateItemInformation:function(){

                let d = this.model.item_information_model;
                let error = false; 
                $.each(d, function(k,v){
                    if(v.country == "" || v.description == "" || v.quantity == 0 || v.value == 0){
                        error =true
                    }
                })

                if(error){
                    this.$set(this.componentErrors,"item_information",true)
                }else{
                    this.$set(this.componentErrors,"item_information",false)
                }
            },


            validateParcelType:function(){
                try{
                    let d = this.model.parcel_types;
            
                    if(d.parcel_type){
                        if(d.parcel_type == "Box"){
                            if(d.usps_box_status){
                                this.$set(this.componentErrors,"parcel_types",false)
                            }else{
                                this.$set(this.componentErrors,"parcel_types",true)
                                
                            }
                        }else{
                            this.$set(this.componentErrors,"parcel_types",false)

                        }
                    }else{
                        this.$set(this.componentErrors,"parcel_types",true)
                    }
                }catch(e){ }

            },


            validateUSPSLetterOptions:function(){
                try{
                    let d = this.model.usps_options_model;
                    let e = this.model.parcel_types;
                    
                    if(e.parcel_type == "Letter"){
                        if(d.usps_options){
                            this.$set(this.componentErrors,"usps_letter_options",false)
                        }else{
                            this.$set(this.componentErrors,"usps_letter_options",true)
                        }

                    }else if(e.parcel_type == "Box"){
                        if(d.weight && d.usps_options){
                            this.$set(this.componentErrors,"usps_box_options",false)
                        }else{
                            this.$set(this.componentErrors,"usps_box_options",true)

                        }
                    }else{
                        this.$set(this.componentErrors,"usps_letter_options",true)
                        this.$set(this.componentErrors,"usps_box_options",true)
                    }

                }catch(e){ }

            },

            handleRate:function(){
                // this.handleFormValidation();
                

                try{
                    if(this.model.postage_option_model) {
                        delete this.model.postage_option_model;
                        this.componentErrors.postage_option = true;
                    }

                    if(this.model.recipient_model.country == "CA" && this.model.ship_from_address_model.country == "CA"){
                        if(this.componentErrors.recipient==false && this.componentErrors.parcel_dimensions==false && this.componentErrors.ship_from_address==false){
                            //SEND MODEL COMPONENTS TO POSTAGE
                            this.$root.$emit("singleShipmentModel",this.model);
                        }

                    }else{


                        //REQUIRE ITEM DETAILS FOR CA TO US SHIPMENTS
                        if(this.componentErrors.recipient==false && this.componentErrors.parcel_dimensions==false && this.componentErrors.ship_from_address==false && this.componentErrors.item_information == false){
                            this.$root.$emit("singleShipmentModel",this.model);

                        }

                    }

                            this.$root.$emit("singleShipmentModel",this.model);



                    // this.validateConcact()

                } catch(e){
                    // console.log(e)
                }
            },


            // validateConcact:function(){
            //     if(this.model.recipient_model.contact_no && this.model.recipient_model.contact_no.length < 10){
            //         showErrorMsg("Contact Number must be at least 10 digits")
            //     }
            // },



            handleSubmitButtonVisibility: function(){
                this.allowedToSubmit = false;

                let w = this.componentErrors.item_information;
                let x = this.componentErrors.recipient;
                let y = this.componentErrors.parcel_dimensions;
                let z = this.componentErrors.ship_from_address;
                let v = this.componentErrors.postage_option;

                this.handleSaveForLaterVisibility();

                if(this.model.recipient_model){
                    if(this.model.recipient_model.country == "CA" ){
                       
                        if( x==false && y==false && z==false && v ==false){
                            //submit
                            this.allowedToSubmit = true;
                        }else{
                            this.allowedToSubmit = false;
                        }
                    }else if(this.model.recipient_model.country == "US" ){

                        if( v==false && w==false){
                            //submit
                            this.allowedToSubmit = true;
                        }else{
                            this.allowedToSubmit = false;
                        }
                    }else{

                        if( x==false && y==false && z==false && v ==false && w==false){
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


            handleSaveForLaterVisibility:function(){
                let x = this.componentErrors.recipient;
                let v = this.componentErrors.postage_option;
                if(v==false){
                    this.model.shipment_status = 2;
                }else{
                    this.model.shipment_status = 1;
                }

                this.disableSaveForLater = true;
                if(this.model.recipient_model){
                    this.disableSaveForLater = false;
                }
            },


            saveForLater:function(){
                this.disableSaveForLater = true;

                this.$http.post("shipment/saveShipment",this.model).then(res=>{
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

                console.log('>>>>> '+this.model.postage_option_model.total)
                this.$root.$emit("checkoutmodalship",parseFloat(this.model.postage_option_model.total));

            }
            
        }
    }
</script>
