<template>
    <div class="modal confirmation-print-batch-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" style="padding: 10px;">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> {{ reportTitle }}</span>
                       
                    </h4>
                    <button type="button" class="close" v-on:click="closeModal" aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    
                    <div class="modal-body" style="padding: 10px;">

                        <div class="label-text horiz-scroll" v-if="reportType == 'label'" style="max-height:500px">
                            <h5 style="color:#1e88e5" v-if="fromPayment">Your Orders Are Confirmed!</h5>

                            <table id="label-tbl" class="table table-striped table-hover" v-if="shipments.length > 0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Shipment Code</th>
                                    <th>Tracking No</th>
                                    <th colspan="2"></th>
                                    

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in shipments.slice(0, (Object.keys(shipments).length)-0)">

                                    <td>{{ index + 1 }}</td>
                                    <td>${{ numberWithCommas(data.total_fee) }}</td>
                                    <td>{{ data.shipment_code }}</td>
                                    <td>{{ data.tracking_no }}</td>
                                    <td>
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn btn-outline-active" data-toggle="tooltip" data-placement="top" title="Download" data-original-title="Tooltip on top" data-animation="false" v-on:click="downloadLabel(data.shipment_code)"><i class="mdi mdi-download icon-custom"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn btn-outline-active" data-toggle="tooltip" data-placement="top" title="Print" data-original-title="Tooltip on top" data-animation="false" v-on:click="printLabel(data.shipment_code)"><i class="mdi mdi-printer icon-custom"></i></button>
                                        </div>                                        
                                    </td>
                                </tr>
                            </tbody>
                            </table>

                        </div>

                        <div class="label-text" v-else>
                            <!-- <h5 style="color:#1e88e5">Bill of Lading Details</h5> -->

                            <table id="label-tbl" class="table table-bordered table-striped table-hover" v-if="shipments.length > 0">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Amount</th>
                                    <th>Shipment Code</th>
                                    <th>Tracking No</th>
                                    <th colspan="2"></th>
                                    

                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in shipments.slice(0, (Object.keys(shipments).length)-0)">

                                    <td>{{ index + 1 }}</td>
                                    <td>${{ numberWithCommas(data.total_fee) }}</td>
                                    <td>{{ data.shipment_code }}</td>
                                    <td>{{ data.tracking_no }}</td>
                                    <td>
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn btn-outline-active" data-toggle="tooltip" data-placement="top" title="Download" data-original-title="Tooltip on top" data-animation="false" v-on:click="downloadBill(data.shipment_code)"><i class="mdi mdi-download icon-custom"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn btn-outline-active" data-toggle="tooltip" data-placement="top" title="Print" data-original-title="Tooltip on top" data-animation="false" v-on:click="printBill(data.shipment_code)"><i class="mdi mdi-printer icon-custom"></i></button>
                                        </div>                                        
                                    </td>
                                </tr>
                            </tbody>
                            </table>

                        </div>
                      
                       <!-- <button class="btn btn-info btn-block" v-on:click="printLabel">PRINT ALL LABEL</button>
                       <button class="btn btn-info btn-block" v-on:click="downloadLabel">DOWNLOAD ALL LABEL</button> -->
                        
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
            reportType: '',
            reportTitle: '',
            fromPayment: false,
            shipments:{},
            errors:{},
            ids:{}
        }
    },
    created(){
        this.$root.$on("shipment-submitted-batch",(ids, from = null)=>{
            $('.confirmation-print-batch-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            console.log(from);
            this.ids = ids;
            this.reportType ='label';
            this.reportTitle ='LABELS';
            if(from == 'from-payment')
                this.fromPayment = true;

            $('.confirmation-modal').modal('hide');
            this.getShipments();
        }),

        this.$root.$on("shipment-report-batch",(ids, rType)=>{
            $('.confirmation-print-batch-modal').modal({
                backdrop: 'static',
                keyboard: false
            });
            this.ids = ids;
            this.reportType = rType;
            if(rType == 'bill')
                this.reportTitle ='PACKING SLIP';            
            else
                this.reportTitle ='LABELS';            

            $('.confirmation-modal').modal('hide');
            this.getShipments();
         })
    },
    // watch:{
    //     shipmentID:{
    //         handler:function(data){
    //             this.getShipment(data);
    //         },
    //         deep:true
    //     }

    // },
    methods:{
        numberWithCommas: function(x) {
            if(x){
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            }else{
                return 0.00;
            }
        },

        downloadBill:function(id){
            window.location.href="/report/downloadBill/ps-"+id;
        },

        printBill:function(id){
            window.open("/report/printBill/ps-"+id).print();
        },

        downloadLabel:function(id){
            window.location.href="/shipment/downloadLabel/"+id;
        },

        printLabel:function(id){
            window.open("/shipment/printLabel/"+id).print();
            //window.open("/storage/labels/"+id+".pdf").print();
        },

        closeModal:function(){
            var isFound = new RegExp("shipment/report/all");
            if (isFound.test(window.location.href)) {
                this.$root.$emit("refresh_allshipment",true);
                $(".confirmation-print-batch-modal").modal("hide");
            }
            else {
                window.location.reload();
            }

        },
        getShipments:function(){
            this.$http.post("/shipment/getShipments",this.ids).then(res=>{
                console.log(res.data);
                this.shipments = res.data
                $(".paywith-userwallet-modal").modal("hide");
                $(".creditcard-modal").modal("hide");
		
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
<style scoped>
    
.label-text{
    font-size: 12px;
}

.table td, .table th {
    padding: .4rem;
    vertical-align: middle;
}
.modal-sm {
    max-width: 450px;
}  
.icon-btn {
    padding: 2.1px 3.2px;
    font-size: 15px;
}  
.btn-outline-active {
    color: #01c8d6;
}
.table-striped > tbody > tr:nth-child(2n+1) > td, .table-striped > tbody > tr:nth-child(2n+1) > th {
   background-color: #fff;
}
.table-hover tbody tr:hover td, .table-hover tbody tr:hover th {
    background-color: #fff;
}  
</style>