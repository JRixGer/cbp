<template>
    <div class="modal confirmation-creditcard-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">


                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">CONFIRMATION</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click='processClose'><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                    <div class="body-wrapper horiz-scroll" style="max-height:500px">
                         <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
  
                            <div v-if="!disableButton">
                                <h5>
                                    {{ confirmMessage }}
                                </h5>
                            </div> 
                        </form>

                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true" @click='processClose'>CANCEL</button>
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
                note:'',
                disableButton: true,
                shipments: [],
                shipmentsTemp: [],
                isMultiple: false,
                recordIndex: 0,
            }
        },
        created(){
            this.$root.$on("confirm_action",(d, t, m, index)=>{
                this.model = d;
                this.confirmMessage = m;
                this.updateType = t;
                this.disableButton = false;
                this.isMultiple = false;
                this.recordIndex = index;
            })
        },
        methods:{
            processAction() {
                this.$root.$emit("process_action",this.model, this.updateType, this.recordIndex);   
                this.note = "";

            },
            processClose() {
                this.note = "";
                this.disableButton = false;
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