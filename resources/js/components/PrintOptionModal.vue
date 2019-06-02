<template>
    <div class="modal confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md narrow">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">CONFIRMATION</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="body-wrapper">
                         <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                                
                            <div class="modal-body-text" v-if="isMultiple">
                                <ul>    
                                    <li v-for="(shipment, index) in shipmentsTemp">{{shipment}}</li>
                                </ul>
                            </div>    
                            <div>
                                <h5>
                                    {{ confirmMessage }}
                                </h5>
                            </div>    
                        </form>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true">CANCEL</button>
                    <button type="button" class="btn btn-info btn-outline" aria-hidden="true" @click='processAction' :disabled="disableButton">YES</button>
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
            
        },
        data(){
            return {
                model:{},
                errors:{},
                confirmMessage:'',
                updateType:'',
                disableButton: true,
                shipments: [],
                shipmentsTemp: [],
                isMultiple: false
            }
        },
        created(){
            this.$root.$on("confirm_action",(d, t, m)=>{
                this.model = d;
                this.confirmMessage = m;
                this.updateType = t;
                this.disableButton = false;
                this.isMultiple = false;
            })
            this.$root.$on("selected_multiple",(d, s, t, m)=>{
                this.shipments = d;
                this.confirmMessage = m;
                this.updateType = t;
                this.model = s;
                this.disableButton = false;
                this.isMultiple = true;

                let selIds = [];
                for(var i = 0; i < this.model.length; i++)
                    selIds.push(""+this.model[i])  

                this.shipmentsTemp = [];
                for(var i = 0; i < this.shipments.length; i++)
                    if(jQuery.inArray(""+this.shipments[i]['shipment_id'], selIds) !== -1)
                        this.shipmentsTemp.push(this.shipments[i]['shipment_id']+" "+this.shipments[i]['FirstName']+" "+this.shipments[i]['LastName']);

            })            
        },
        methods:{
            processAction() {

                this.enableButton = true;
                this.$root.$emit("process_shipment",this.model, this.updateType, 'multiple');   

            } 
        }
    }
</script>
<style scoped>
.body-wrapper
{
    margin: 2px;
}
</style>