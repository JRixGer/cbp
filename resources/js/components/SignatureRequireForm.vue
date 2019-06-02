<template>
    <div>
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">SIGNATURE</h4><br>
             <div class="card-block">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="" role="form" method="POST">
                        <h5>Do you require signature?</h5>
                        <input type="radio" name="signature" v-model.number="req_signature" value="1" id="signature-yes">
                        <label for="signature-yes">YES</label>

                        <input type="radio" name="signature" v-model.number="req_signature" value="0" id="signature-no">
                        <label for="signature-no">NO</label>
                        <button class="btn btn-info" hidden v-bind:disabled="processing">Save and Continue</button>

                    </form>
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
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                req_signature:"0",
                component_error:false
            }
        },
        created(){
            // this.init();

            this.$root.$on("component_errors",(c)=>{
                this.component_error = c.signature_require
            })

                this.$root.$emit("signatureRequireModel",this.req_signature);
            
        },

        watch:{
             "req_signature":function(data){
                console.log("sdfsdf")
                this.$root.$emit("signatureRequireModel",this.req_signature);
            }
        },
        methods:{

        }
    }
</script>
