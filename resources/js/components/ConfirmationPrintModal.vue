<template>
    <div class="modal confirmation-print-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    
                    <h4 class="modal-title" v-if="isAllShipment">
                        <span> {{ printTitle }}</span>
                    </h4>                 

                    <h4 class="modal-title" id="mySmallModalLabel" v-else>
                        <span> CONFIRMED & PRINT</span>
                    </h4>

                    <button type="button" v-on:click="closeModal" class="close" data-dismiss="modal"  aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body">
                        <h3 style="color:#1e88e5" v-bind:class="{hideThis: hideThis}">Your Order Is Confirmed!</h3>

                        <h4>Amount Deducted: ${{ numberWithCommas(shipment.total_fee) }}</h4>

                        <div v-if="shipment.shipment_code">
                            <h4>Shipment ID: {{ shipment.shipment_code }}</h4>
                            <h4>Tracking #: {{ shipment.tracking_no }}</h4>
                           <!-- <div v-if="shipment.carrier=='CANADA POST'">
                               <button class="btn btn-info btn-block" v-on:click="closeModal">CLOSE</button>
                           </div> -->

                           <!-- <div v-else> -->
                               <button class="btn btn-info btn-block" v-on:click="addShipment" v-bind:class="{'hideThis': hideThis}">ADD ANOTHER SHIPMENT</button>
                               <button class="btn btn-info btn-block" v-on:click="allShipment" v-bind:class="{'hideThis': hideThis}">ALL SHIPMENT</button>
                               <button class="btn btn-info btn-block" v-on:click="printLabel" v-bind:class="{'hideThis': hideThis}">PRINT LABEL</button>
                               <button class="btn btn-info btn-block" v-on:click="downloadLabel"  v-bind:class="{'hideThis': hideThis}">DOWNLOAD LABEL</button>

                               <button class="btn btn-info btn-block" v-on:click="print" v-bind:class="{'hideThis': !hideThis}">PRINT</button>
                               <button class="btn btn-info btn-block" v-on:click="download" v-bind:class="{'hideThis': !hideThis}">DOWNLOAD</button>                        
                               
                           <!-- </div> -->
                        </div>
                        <div v-else>
                            <h4>Can't issue the Label at the moment. Please contact System administrator</h4>
                            <button class="btn btn-info btn-block" v-on:click="closeModal">CLOSE</button>
                            
                        </div>

                       

                    </div>

                </form>


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    props:["shipmentID"],
    mounted() {
    },
    data(){
        return {
            processing: false,
            shipment:{},
            errors:{},
            isAllShipment:false,
            hideThis: false,
            printTitle: '',
            printType: '',
        }
    },
    created(){
        this.$root.$on("shipment-submitted",(e, t=null)=>{
            
            this.printType = t;
            if(/bill/.test(t))
            {
                this.hideThis = true;
                this.printTitle = 'PRINT PACKING SLIP';
                this.isAllShipment = true;
            }
            else if(/label/.test(t))
            {
                this.hideThis = true;
                this.printTitle = 'PRINT LABEL';
                this.isAllShipment = true;
            }
            else if(/mani/.test(t))
            {
                this.hideThis = true;
                this.printTitle = 'PRINT MANIFEST';
                this.isAllShipment = true;
            }            
            else
            {
                this.hideThis = false;
                this.isAllShipment = false;
            }

            $('.confirmation-print-modal').modal({
                backdrop: 'static',
                keyboard: false
            })

            $('.confirmation-modal').modal('hide');

        })
    },
    watch:{
        shipmentID:{
            handler:function(data){
                this.getShipment(data);
            },
            deep:true
        }

    },
    methods:{
        numberWithCommas: function(x) {
            if(x){
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }else{
                return 0.00;
            }
        },


        download:function(){
            if(/bill/.test(this.printType))
               this.downloadBill();     
            else if(/label/.test(this.printType))
                this.downloadLabel();
            else if(/mani/.test(this.printType))
                this.downloadMani();        
        },
        
        print:function(){
            if(/bill/.test(this.printType))
               this.printBill();     
            else if(/label/.test(this.printType))
                this.printLabel();
            else if(/mani/.test(this.printType))
                this.printMani();        
        },
        addShipment:function(data){
            window.location.reload();
        },

        allShipment:function(data){
            window.location.href="/#/shipment/report/all";
        },
        
        downloadBill:function(){
            window.location.href="/report/downloadBill/"+this.shipment.shipment_code;
        },

        printBill:function(){
            window.open("/storage/packing_slip/"+this.shipment.shipment_code+".pdf").print();
        },

        downloadLabel:function(){
            window.location.href="/shipment/downloadLabel/"+this.shipment.shipment_code;
        },

        printLabel:function(){
            window.open("/shipment/printLabel/"+this.shipment.shipment_code).print();
        },

        closeModal:function(){

            this.hideThis = false;
            this.printTitle = '';
            if(!this.isAllShipment)
            {
               this.isAllShipment = false;            
                window.location.reload();
            }
        },

        getShipment:function(id){
            this.$http.get("/shipment/getShipment/"+id).then(res=>{
                //console.log(res.data);
                this.shipment = res.data
                $(".confirmation-selectpayment-modal").modal("hide");
            })
        },

        formSubmit:function(){
            // $('.insurance-modal').modal('hide');
            // if(this.model.insured_value){
            //     this.$root.$emit("insurance_model",this.model.insured_value);
            // }
        }


    }
};
</script>
<style>
.hideThis{
    display:none;
}
</style>