<template>
    <div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
            <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                     <h4 class="card-title">Is it a Letter?</h4><br>
                     <div class="card-block">
                        <div class="card-body" >
                                <div class="row">
                                    <div class="col-md-12" >
                                        <!-- <h4></h4> -->
                                        <div class="row">
                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.parcel_letter_status" v-on:click="handleLetterStatusOnClick('yes')" name="parcel_letter_status" value="yes" id="letter_status_yes">
                                                <label for="letter_status_yes">YES</label>
                                            </div>
                                            <div class="col-md-3">
                                                <input type="radio" v-model="model.parcel_letter_status" v-on:click="handleLetterStatusOnClick('no')" name="parcel_letter_status" value="no" id="letter_status_no">
                                                <label for="letter_status_no">NO</label>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                        </div>
                    </div>
            </form>
        </div>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props:['editShipmentModel'],
        mounted() {
            console.log('Is Letter Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                component_error:false
            }
        },
        created(){
            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.parcel_letter_status
            })

        },

        watch:{

            editShipmentModel:{
                handler:function(data){
                    if(data.weight){
                        this.$set(this.model,"parcel_letter_status","no")
                        this.$root.$emit("isLetter","no");
                    }else{
                        this.$set(this.model,"parcel_letter_status","yes")
                        this.$root.$emit("isLetter","yes");

                    }


                },
                deep:true
            }

          
        },
        methods:{
            handleLetterStatusOnClick:function(status){

                this.$root.$emit("isLetter",status);
                
            },
        }
    }
</script>
