<template>
    <div class="modal confirmation-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">
      
 
                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">CONFIRMATION</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click='processClose'><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="body-wrapper">
                         <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                            
                            <div class="modal-body-text" v-if="isMultiple">
                                <ul>    
                                    <li v-for="(shipment, index) in shipmentsTemp">{{shipment}}</li>
                                </ul>

                            </div>   

                            <div class="modal-body-text" v-else>

                                 <div v-if="isError">
                                    <div role="alert" class="alert alert-danger" style="text-align:center; font-size:12px">
                                        {{ confirmMessage }}
                                    </div>
                                </div>    
                                <div v-else>
                                    {{ confirmMessage }}
                                </div> 
                            </div>    

                            <div style="width:100%; float:left"  v-if="updateType == 'payment'">       
                                <div class="overall-total" style="text-align:left;">
                                    <h5 class="summary-label-overall-total" >Amount: <span class="summary-info-overall-total">${{ unpaidAmount }}CAD</span></h5>
                                </div>                     
                            </div>

                            <div v-if="note!=''">
                                <div style="text-align:left; font-size:12px" v-html="note">
                                    
                                </div>
                            </div>    

                            <div  v-if="(unpaidAmount > 0 && updateType!='void' && updateType!='multi-print-label' && updateType!='archive' && updateType!='download') || updateType=='order'" style=" margin-top:25px">
                                <date-picker v-model="shipmentDate" :first-day-of-week="1" placeholder="Ship Date (optional)" lang="en"></date-picker>
                            </div>

                        </form>

                    </div>
                </div>
                <div class="modal-footer">
   
                    <div class="col-md-6">
                        <div style="width:auto; float:left; margin-left:-5px">  
                            <div v-if="isMultiple">
                                <h5>
                                    {{ confirmMessage }}
                                </h5>
                            </div> 
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div style="width:auto; float:right">  
                            <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true" @click='processClose'>CANCEL</button>
                            <button type="button" class="btn btn-info btn-outline" aria-hidden="true" @click='processAction' :disabled="disableButton">YES</button>                    
                        </div>
                    </div>


                </div>

            </div>
        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';

    import DatePicker from 'vue2-datepicker';

    export default {
        props: [],
        components: {DatePicker },
        mounted() {
        },
        data(){
            return {
                model:{},
                errors:{},
                confirmMessage:'',
                updateType:'',
                unpaidAmount:0,
                note:'',
                disableButton: true,
                shipments: [],
                shipmentsTemp: [],
                isMultiple: false,
                isError: false,
                excludeIds: [],
                shipmentDate : '',
                shortcuts: [
                    {
                      text: 'Today',
                      onClick: () => {
                        this.shipmentDate = [ new Date(), new Date() ]
                      }
                    }
                  ]                
            }
        },
        created(){
            this.$root.$on("confirm_action",(d, t, m, disable)=>{
                this.model = d;
                this.confirmMessage = m;
                this.updateType = t;
                this.disableButton = disable;
                this.isMultiple = false;
                this.isError = disable;
                this.excludeIds = [];
                this.unpaidAmount = 0;
            })
            this.$root.$on("selected_multiple",(d, s, t, m, u)=>{
                this.shipments = d;
                this.confirmMessage = m;
                this.updateType = t;
                this.model = s;
                this.unpaidAmount = u['amountUnpaid'].toFixed(2);
                this.disableButton = false;
                this.isMultiple = true;
                this.excludeIds = [];

                let selIds = [];
                for(var i = 0; i < this.model.length; i++)
                    selIds.push(""+this.model[i])  

                console.log(this.shipments)
                let e = "";
                let p = "";
                let ectr = 0;
                let xctr = 0;
                
                let allowedProcess = ["payment","delete","archive","void","edit"];
                let allowPrint = true;
                let hasPrint = 0
                for(var i = 0; i < this.shipments.length; i++)
                {

                    console.log('>>>> '+this.shipments[i]['shipment_type']);

                    allowPrint = true;
                    if(jQuery.inArray(""+this.shipments[i]['shipment_id'], selIds) !== -1){
                        //xctr ++;
                        console.log(this.shipments[i]['name']);
                        e = ""; 
                        if(this.shipments[i]['name'] == "Unpaid")
                        {
                            //e += " - (Unpaid)"; 
                            //ectr ++;
                            allowPrint = false;

                        }
                        else if(this.shipments[i]['LetterMail'] == "YES" || this.shipments[i]['LetterMail'] == "1")
                        {
                            //e += " - (Letter mail)";
                            //ectr ++;
                            allowPrint = false;
                        }
                        // else if(this.shipments[i]['shipment_type'] == "DO" && /print-bill/.test(this.updateType))
                        // {
                        //    allowPrint = false;
                        // }   
                        else if(/print-bill/.test(this.updateType) && (this.shipments[i]['Country'] == 'CA' && this.shipments[i]['cbp_country'] == 'CA'))
                        {
                            //e += " - (Letter mail)";
                            //consolelog('--> '+this.processType);
                            //ectr ++;
                            allowPrint = false;
                        }        

                        //alert(this.updateType)

                        //p = this.shipments[i]['shipment_id']+" "+this.shipments[i]['FirstName']+" "+this.shipments[i]['LastName']+e;
                        p = this.shipments[i]['shipment_id']+" "+this.shipments[i]['FirstName']+" "+this.shipments[i]['LastName'];

                        if(allowPrint && (/print/.test(this.updateType)))
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }
                        else if(/payment/.test(this.updateType) && (this.shipments[i]['name'] == "Unpaid" || this.shipments[i]['name'] == "Incomplete"))
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }
                        else if(/void/.test(this.updateType) && (this.shipments[i]['name'] == "Ready"))  // need to check in detail what statuses need to be voided
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }
                        else if(/archive/.test(this.updateType) && (this.shipments[i]['name'] == "Delivered"))  // need to check in detail what statuses need to be voided
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }  
                        else if(/download/.test(this.updateType))
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }  
                        else if(/order/.test(this.updateType) && (this.shipments[i]['name'] == "Ready" || this.shipments[i]['name'] == "Received" || this.shipments[i]['name'] == "Attention required"))  
                        {
                            this.shipmentsTemp.push(p);
                            hasPrint++;
                        }  
                        else
                        {
                            // for id removal
                            this.excludeIds.push(""+this.shipments[i]['shipment_id']);
                        }
                    }
                }

                if(/print/.test(this.updateType))
                {
                    this.note = 'Note: unpaid/letter mail/CA to CA';
                    // if(/print-bill/.test(this.updateType))
                    //     this.note += '/delivery only/CA to CA';

                    this.note += ' shipments will not be processed';
                }
                else if(/order/.test(this.updateType))
                    this.note = 'Note: Only shipment that has "Ready/Received/Attention required" status can be included in a group order';
                else if(/payment/.test(this.updateType))
                    this.note = 'Note: Double payment not allowed';
                else if(/void/.test(this.updateType))
                    this.note = 'Note: Cannot void unpaid/delivered/incomplete shipments';
                else if(/archive/.test(this.updateType))
                    this.note = 'Note: Archive is applicable only for already delivered shipments';
                
                if(hasPrint == 0)
                    this.disableButton = true;
                else
                    this.disableButton = false;

            })            
        },
        methods:{
            processAction() {
                if (this.excludeIds !== undefined && this.excludeIds.length > 0)    
                    this.model['excludeIds'] = this.excludeIds;
                
                if(this.isMultiple)
                    this.model['shipment_date'] = this.shipmentDate;
                
                if(this.isMultiple)
                    this.$root.$emit("process_shipment",this.model, this.updateType, 'mulSelected');   
                else
                    this.$root.$emit("process_shipment",this.model, this.updateType, 'singleSelected');   

                this.note = "";
                this.shipmentsTemp = [];
                this.shipmentDate = '';

            },
            processClose() {
                this.shipmentsTemp = [];
                this.note = "";
                this.disableButton = false;

            } 
        }
    };
</script>
<style scoped>
.body-wrapper
{
    margin: 2px;
}
.summary-label-overall-total h5 {
    font-style: italic;
}
.summary-info-overall-total {
    border-bottom: 1pt solid black; 
    padding:0px !important;
    font-style: italic;
    color: #ffaa00;
    font-weight: 600;
}
</style>

<style>

.mx-datepicker {
    width: 165px !important;
}

.mx-input-wrapper {
    position: relative;
    width: 165px  !important;
}


.mx-input {
    font-size: 12px;
}

</style>
