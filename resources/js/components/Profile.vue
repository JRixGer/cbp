<template>
    <div class="profile-wrapper">
        <div class="">
            <div class="row justify-content-left">
                <div class="col-md-4">
                    <AccountInfo></AccountInfo>
                </div>

                <div class="col-md-4">
                    <ImportNumber></ImportNumber>
                    <BusinessAddress v-if="displayCard.business_address"></BusinessAddress>

                    <button v-if="showSave == 1"  style="font-size:1rem" class="btn btn-success btn-lg btn-block" v-bind:disabled="processing" v-on:click="formSubmit">SAVE CHANGES</button>
                </div>

                <div class="col-md-4">
                    <MailingAddress v-if="displayCard.mailing_address"></MailingAddress>
                    <button  v-if="showSave == 2"  style="font-size:1rem" class="btn btn-success btn-lg btn-block" v-bind:disabled="processing" v-on:click="formSubmit">SAVE CHANGES</button>

                </div>
            </div>
        </div>
    </div>
</template>


<style scoped>
    .profile-wrapper{
        padding-top: 30px;
    }
</style>
<script>

    import swal from 'sweetalert';
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: [],
        mounted() {
            console.log('Profile Registration Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                displayCard:{
                    business_address:false,
                    mailing_address:false,
                },
                showSave:1
            }
        },
        created(){

            this.$root.$on("account_info_model",(c)=>{
                this.model.account_info_model = c;
            })

            this.$root.$on("import_number_model",(c)=>{
                this.model.import_number_model = c;

                if(c.with_import_number == "yes"){
                    this.displayCard = {
                            business_address:false,
                            mailing_address:false,
                        }
                    this.showSave = 1
                }else if(c.with_import_number == "no"){
                    this.displayCard = {
                            business_address:true,
                            mailing_address:false,
                        }
                    this.showSave = 1

                }
            })

            


            this.$root.$on("business_address_model",(c)=>{
                this.model.business_address_model = c;

                //show mailing if same with physical address is checked
                try{

                    if(c.mailing_address_chk){

                        this.$set(this.displayCard,"mailing_address",true)
                        this.showSave = 2

                    }else{

                        this.$set(this.displayCard,"mailing_address",false)
                        this.showSave = 1

   
                    }
                }catch(e){

                }
            })

            this.$root.$on("mailing_address_model",(c)=>{
                console.log("profile mailing address")
                this.model.mailing_address_model = c;
            })
            
        },

        watch:{
            model:{
                handler:function(data){
                    

                    
                  
                },
                deep:true
            }
           
        },
        methods:{




           fetchProfile: function() {
                this.$http.get("/profile/details/").then(res=>{

                    this.model = res.data;


                });
            },  

            formSubmit:function(){
                this.processing = true;
                this.$http.post("/profile/update",this.model).then(response=>{

                    // if(response.data.status){
                        this.processing = false;
                         swal("Saved Successfully!","","success")
                        
                    // }
                    // window.location.href="/"
                    if(this.errors){
                        this.errors = [];
                    }

                }, response=>{
                    this.errors = response.data;
                    this.processing = false;

                    this.$root.$emit("formErrors",this.errors)

                   showErrorMsg(response.data.message)
                   
                }).catch((err) => {
                    handleErrorResponse(err.status);
                    this.processing = false;
                    if (err.status == 422) {
                        this.$root.$emit("formErrors",err.data)
                        this.errors = err.data;
                    }
                });
            }
        }
    }
</script>
