<template>
    <div class="modal shipment-options-all-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md narrow">
            <div class="modal-content">
                
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span>Selected Shipments</span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <div class="modal-body">
                    <div class="body-wrapper">
                        
                        
                        <div class="modal-body-text">
                            <ul>    
                                <li v-for="(shipment, index) in shipmentsTemp">{{shipment}}</li>
                            </ul>

                        </div>

                        <div class="modal-body-buttons">
                            <h5 class="action-text-title">Action</h5>
                            <!-- <div class="icons-inline-big">
                                <button type="button" class="btn btn-secondary btn-outline icon-btn-big" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Tooltip on top" data-animation="false" v-on:click="processShipment(shipment.shipment_id, 'edit')"><i class="mdi mdi-pencil icon-custom-big"></i></button>
                            </div> -->
                            <div class="icons-inline-big">
                                <button type="button" class="btn btn-secondary btn-outline icon-btn-big" data-toggle="tooltip" data-placement="top" title="Print" data-original-title="Tooltip on top" data-animation="false" v-on:click="processShipment(shipment.shipment_id, 'print')"><i class="mdi mdi-printer icon-custom-big"></i></button>
                            </div>

                            <!-- <div class="icons-inline-big">
                                <button type="button" class="btn btn-secondary btn-outline icon-btn-big" data-toggle="tooltip" data-placement="top" title="Copy" data-original-title="Tooltip on top" data-animation="false" v-on:click="processShipment(shipment.shipment_id, 'copy')"><i class="mdi mdi-content-copy icon-custom-big"></i></button>
                            </div> -->

                            <div class="icons-inline-big">
                                <button type="button" class="btn btn-secondary btn-outline icon-btn-big" data-toggle="tooltip" data-placement="top" title="Void" data-original-title="Tooltip on top" data-animation="false" v-on:click="confirm(shipment.shipment_id, 'void', index)"><i class="mdi mdi-stop-circle icon-custom-big"></i></button>
                            </div>
                            <div class="icons-inline-big">
                                <button type="button" class="btn btn-secondary btn-outline icon-btn-big" data-toggle="tooltip" data-placement="top" title="Delete" data-original-title="Tooltip on top" data-animation="false" v-on:click="confirm(shipment.shipment_id, 'delete', index)"><i class="mdi mdi-delete-forever icon-custom-big"></i></button>
                            </div>
                      </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal" aria-hidden="true">CLOSE</button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            //console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                shipments: [],
                selectedShipmentIds: [],
                shipmentsTemp: [],
                errors: [],
            }
        },
        created(){
            this.$root.$on("selected_multiple",(d, s)=>{
                this.shipments = d;
                this.selectedShipmentIds = s;

                let selIds = [];
                for(var i = 0; i < this.selectedShipmentIds.length; i++)
                    selIds.push(""+this.selectedShipmentIds[i])  

                this.shipmentsTemp = [];
                for(var i = 0; i < this.shipments.length; i++)
                    if(jQuery.inArray(""+this.shipments[i]['shipment_id'], selIds) !== -1 )
                        this.shipmentsTemp.push(this.shipments[i]['shipment_id']+" "+this.shipments[i]['FirstName']+" "+this.shipments[i]['LastName']);

            })
        },

        watch:{
            
        },
        methods:{


        }
    };
</script>
<style scoped>
.body-wrapper
{
    margin: 2px;
}
.modal-body-buttons
{
    min-width:300px; 
    text-align: center;
}
.modal-body-text
{
    padding:0px 10px 5px 10px; 
    font-size: 13px;
}
.action-text-title
{
    text-align:left; 
    margin-bottom: 20px;
}

</style>