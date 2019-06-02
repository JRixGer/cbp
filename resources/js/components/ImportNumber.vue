<template>
    <div>
        <div class="card">
            <h4 class="card-title">Do you have an import number?</h4><br>
             <div class="card-block">

                <div class="card-body">
                     <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">

                        <div class="form-group">
                            <input type="radio" name="with_import_number" v-model.number="model.with_import_number" value="yes" id="with-import-number-yes">
                            <label for="with-import-number-yes">YES</label>

                            <input type="radio" name="with_import_number" v-model.number="model.with_import_number" value="no" id="with-import-number-no">
                            <label for="with-import-number-no">NO</label>
                        </div>
                        
                        <div v-if="model.with_import_number == 'yes'">
                            <div v-bind:class="{'form-group': true, 'focused':model.import_number, 'has-error has-danger': errors.import_number }">
                                <input type="text" v-model="model.import_number" id="import_number" class="form-control" placeholder="">
                                <span class="bar"></span>
                                <label for="import_number">Enter Import Number Here... <span class="required">*</span></label>
                                <span class="help-block" v-for="error in errors.import_number">{{ error }}</span>
                            </div>
                        </div>


                    </form>
                </div>
            </div>
        </div>
        
    </div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
export default {
    props:[],
    mounted() {
        console.log('Sender Business Form Component mounted.')
    },
    data(){
        return {
            processing: false,
            model:{  },
            errors:{},
            ask_import_number:1,
            with_import_number:"",
        }
    },
    created(){

        this.$root.$on("formErrors",(e)=>{
            if(e.import_number){
                this.errors = e.import_number
            }else{
                this.errors = {}

            }
        })

        this.$root.$on("account_info_model",(c)=>{
            if(c.import_number){

                this.$set(this.model,"with_import_number","yes");
                this.$set(this.model,"import_number",c.import_number);

            }else{
                this.$set(this.model,"with_import_number","no");

            }

        })

    },
    watch:{
        model:{
            handler:function(){

            this.$root.$emit('import_number_model', this.model);
            },
            deep:true
        
        }
    },
    methods:{

       


    }
}
</script>

