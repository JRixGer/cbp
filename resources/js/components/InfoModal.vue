<template>
    <div class="modal info-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md">
            <div class="modal-content">

                <div class="modal-header">
                    <h4 class="modal-title" id="exampleModalLabel1">INFORMATION</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">×</span></button>
                </div>
                <div class="modal-body">
                     <form v-on:submit.prevent="formSubmit" role="form">
                        <div v-if="isError">
                            <div role="alert" class="alert alert-danger" style="text-align:center; font-size:12px">
                                    {{ infoMessage }}
                            </div>
                        </div>    
                        <div v-else>
                            <div role="alert" class="alert alert-info" style="text-align:center; font-size:12px">
                                    {{ infoMessage }}
                            </div>
                        </div>                           
                    </form>

                    </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-outline" data-dismiss="modal" aria-hidden="true">OK</button>
                </div>
            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    mounted() {
    },
    data(){
        return {
            infoMessage:'',
            isError: false
        }
    },
    created(){
        this.$root.$on("inform_action",(m, isError)=>{
            this.infoMessage = m;
            this.isError =  isError;

        })
    }

}
</script>