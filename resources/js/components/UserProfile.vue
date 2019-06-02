<template>
    <div class="single-shipment-wrapper">
        <UserProfileToolbar v-bind:activeTab="activetTab"></UserProfileToolbar>
       <div class="">
            <div class="row justify-content-left">
                    <UserProfileForm></UserProfileForm>
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
                activetTab:"upp",
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
