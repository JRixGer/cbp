<template>
    <div class="single-shipment-wrapper">
        <CreditCardToolbar></CreditCardToolbar>
       <div class="">
            <div class="row justify-content-left">
                <div class="col-md-12">
                    <CreditCardForm></CreditCardForm>
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
            //console.log('All Shipments Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
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
