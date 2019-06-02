<template>
    <div class="modal fade item-information-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-sm">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span> ADD ITEM DETAILS</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>

                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body">


                            <!-- <div class="row"> -->
              
                                <div v-bind:class="{'form-group': true, 'focused':model.description, 'has-error has-danger': errors.description }">
                                    <input type="text" v-model="model.description" id="description" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="description">Description <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.description">{{ error }}</span>
                                </div>
             

                                <div v-bind:class="{'form-group': true, 'focused':model.quantity, 'has-error has-danger': errors.quantity }">
                                    <input type="number" v-model="model.quantity" id="quantity" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="quantity">Qty <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.quantity">{{ error }}</span>
                                </div>



                                <div v-bind:class="{'form-group': true, 'focused':model.value, 'has-error has-danger': errors.value }">
                                    <input type="number" v-model="model.value" id="value" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="value"> Value in USD <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.value">{{ error }}</span>
                                </div>  


                                <div v-bind:class="{'form-group': true, 'focused':model.country, 'has-error has-danger': errors.country }">
                                    <select v-model="model.country" id="country" class="form-control" placeholder="Country" >
                                        <!-- <option value=""></option> -->
                                        <option value="CA">CANADA</option>
                                        <option value="US">United States</option>
                                        <option disabled>_________________________</option>
                                        <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="country">Country <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                </div>


     
                            <!-- </div> -->

                    </div>
                    <div class="modal-footer">
                       <button class="btn btn-info btn-block" v-on:click="addItem">ADD ITEM</button>
                    </div>
                </form>


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
            errors:{ },
            countries:{}

        }
    },
    created(){
        this.init();
    },
    watch:{
    },
    methods:{

        init:function(){
             this.$http.get("/api/countries").then(res=>{
                this.countries = res.data;
            })
        },
        addItem: function(){

            let errorCounter = this.validateField(this.model);
            if(errorCounter > 0){
                return false;
            }else{
                this.$root.$emit("emit-item-info", this.model)
                $(".item-information-modal").modal("hide")
                this.model = {}
            }
        },
        validateField:function(data){
            let  x = 0;
            if(!data.description){
                this.errors.description = {"0":"The Description field is required"};
                x++;
            }else{
                delete this.errors.description;
            }

            if(!data.quantity){
                this.errors.quantity = {"0":"The Qty field is required"};
                x++;
            }else{
                delete this.errors.quantity;
            }

            if(!data.value){
                this.errors.value = {"0":"The Value field is required"};
                x++;
            }else{
                delete this.errors.value;
            }

            if(!data.country){
                this.errors.country = {"0":"The Value field is required"};
                x++;
            }else{
                delete this.errors.country;
            }

            this.$forceUpdate();

            return x;

        },

        formSubmit: function(){

        }

    }
}
</script>