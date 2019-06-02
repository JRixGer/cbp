<template>
    <div class="single-shipment-wrapper">
        <GroupedOrdersToolbar v-bind:activeTab="activetTab"></GroupedOrdersToolbar>
       <div class="">
            <div class="row justify-content-left">
                    <GroupedOrdersForm></GroupedOrdersForm>
            </div>
             
        </div>

    </div>
</template>


<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            console.log('All Shipments Component mounted.')
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
    };
</script>
