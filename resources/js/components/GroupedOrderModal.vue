<template>
    <div class="modal grouped-order-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header" style="padding: 10px;">
                    <h4 class="modal-title" id="mySmallModalLabel">
                         Grouped Order Detail {{ shipDate(shipmentDate) }}
                    </h4>
                    <button type="button" class="close" v-on:click="closeModal" aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body" style="padding: 10px;">
                        <div class="label-text">
                            <table id="label-tbl" class="table table-bordered table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ShipmentId</th>
                                    <th>OrderId</th>
                                    <th>Recipient</th>
                                    <th>RecipientAddress</th>
                                    <th colspan="2"></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(data, index) in groupedOrder">
                                    <td>{{ data.internal_ship_id }}</td>
                                    <td>{{ data.internal_order_id }}</td> 
                                    <td>{{ data.RecipientName }}</td>                                    
                                    <td>{{ data.AddressLine1 }} {{ data.City }} {{ data.ProvState }} {{ data.PostalZipCode }} {{ data.Country }}</td> 
                                    <td>
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn btn-outline-active" data-toggle="tooltip" data-placement="top" title="Remove from order" data-original-title="Tooltip on top" data-animation="false" v-on:click="removeFromGroup(data.shipment_id)"><i class="mdi mdi-delete-forever icon-custom"></i></button>
                                        </div>                                        
                                    </td>
                                </tr>
                            </tbody>
                            </table>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div style="width:auto; float:left;">  
                                    <h5>
                                        Reverse this order ?
                                    </h5>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div style="width:auto; float:right">  
                                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true" @click='closeModal'>CANCEL</button>
                                    <button type="button" class="btn btn-info btn-outline" aria-hidden="true" @click='reverseGroup' :disabled="disableButton">YES</button>                    
                                </div>
                            </div>
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
            disableButton: true,
            groupedOrder:{},
            errors:{},
            gid:'',
            d:'',
            shipmentDate:''
        }
    },
    created(){
        this.$root.$on("show_grouped_order",(d, gId, sDate)=>{
            this.d = d;
            this.gid = gId;
            this.shipmentDate = sDate?sDate:'';
            this.getGroupedOrder();
         })       
    },
    methods:{
        shipDate:function(sDate){
            if(sDate.length > 0)
                return "Shipment Date: "+sDate;
            else
                return "";
        },
        closeModal:function(){
            this.shipmentDate = '';
            this.$root.$emit("refresh_groupedorders",true);
        },
        getGroupedOrder:function(){
            this.$http.post("/report/grouped_order",this.d).then(res=>{
                console.log(res.data);
                this.groupedOrder = res.data;

                if(this.groupedOrder.length > 0)
                    this.disableButton = false;
            })
        },
        removeFromGroup:function(id){
            this.$http.post("/report/remove_order",id).then(res=>{
                this.getGroupedOrder();
            })
        },
        reverseGroup:function(){
            this.$http.post("/report/reverse_order",this.gid).then(res=>{
                this.shipmentDate = '';
                this.$root.$emit("refresh_groupedorders",true);               
            })
        },

        formSubmit:function(){

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
    max-width: 600px;
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