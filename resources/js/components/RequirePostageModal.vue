<template>
    <div class="modal fade require-postage-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> SINGLE SHIPMENT</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <!-- <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST"> -->
                    <div class="modal-body" style="text-align:center">

                        <h5>Do you require postage?</h5>
                        <br>
                        <div class="row" >
                            <div class="col-md-6">
                                <button class="btn btn-info btn-block" v-on:click="requirePostage('yes')">Yes</button>
                            </div>
                            <div class="col-md-6">
                                <button class="btn btn-info btn-block" v-on:click="requirePostage('no')">No</button>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                       <input type="checkbox" v-model="model.nevershow" id="need-to-see">
                       <label for="need-to-see">I don't need to see this again</label>
                    </div>
                <!-- </form> -->


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    props:[],
    mounted() {
        console.log('Require postage modal Component mounted.')
    },
    data(){
        return {
            processing: false,
            model:{  },
            errors:{},
        }
    },
    created(){

    },
    watch:{
        model:{
            handler:function(data){
                $cookies.set("nevershowReqPostage",data.nevershow);
            },
            deep:true
        }
    },
    methods:{
        requirePostage: function(r){
            $(".require-postage-modal").modal("hide")
            
            if(r == "yes"){
                window.location.href="/#/shipment/single/pd";
                $cookies.set("reqPostage","y");

            }else{
                window.location.href="/#/shipment/single/do";
                $cookies.set("reqPostage","n");
            }
        }
    }
}
</script>