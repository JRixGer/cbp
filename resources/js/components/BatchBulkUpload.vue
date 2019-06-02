<template>
    <div class="single-shipment-wrapper">
        <BatchBulkShipmentToolbar v-bind:activeTab="activetTab"></BatchBulkShipmentToolbar>
       <div class="">
            <div class="row justify-content-left">
                    <ImportShipmentsForm></ImportShipmentsForm>
            </div>
        </div>

    </div>
</template>


<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            console.log('Batch Bulk Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                activetTab:"bbu",
              }
        },
        created(){
  
        },

        methods:{

            init:function(){

            }
            
        }
    }
</script>
