<template>
    <div class="item-information-wrapper">
        <div v-bind:class="{'card':true,'card-error':component_error}">
             <h4 class="card-title">ITEM DECLARATION<span class="mdi mdi-plus-circle" v-on:click="addRow"></span> </h4><br>
             <div class="">
                <!-- <h4><b>Address Verification</b></h4><br> -->

                <div class="card-body">
                     <table class="table">
                        <thead>
                             <th>Description</th>
                             <th>Country Origin</th>
                             <th>Qty</th>
                             <th>Value (USD)</th>
                             <th></th>
                        </thead>
                        <tbody>
                            
                         <!-- <tr v-if="items.length > 0" v-for="(item, index) in items"> -->
                            <!--  <td>{{ item.description }}</td>
                             <td>{{ item.country }}</td>
                             <td>{{ item.quantity }}</td>
                             <td>{{ item.value }}</td> -->
                             
                         <!-- </tr> -->

                        <!--  <tr v-if="items.length == 0">
                             <td><input type="text" v-model="items[0].description"  name="description" class="form-control"></td>
                             <td><input type="text" v-model="items[0].country"  name="country" class="form-control"></td>
                             <td><input type="number" v-model="items[0].quantity"  name="quantity" min="0" class="form-control"></td>
                             <td><input type="text " v-model="items[0].value" name="value" class="form-control"></td>
                             <td><span class="mdi mdi-close remove" v-on:click="remove(index)"></span></td>
                         </tr> -->

                         <tr v-for="(item, index) in items">
                             <td><input type="text"  v-model="items[index].description"  name="description" class="form-control"></td>
                             <td>
                                 <select v-model="items[index].country" id="country" class="form-control" placeholder="Country" >
                                    <!-- <option value=""></option> -->
                                    <option value="CA">CANADA</option>
                                    <option value="US">United States</option>
                                    <option disabled>_________________________</option>
                                    <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                </select>
                             </td>
                             <td><input type="number" v-model="items[index].quantity" name="quantity" onkeypress="return (event.charCode != 45 && event.charCode != 43)" min="0" class="form-control"></td>
                             <td><input type="number" v-model="items[index].value" name="value" onkeypress="return (event.charCode != 45 && event.charCode != 43)"  min="0" class="form-control"></td>
                             <td><span class="mdi mdi-close remove" v-on:click="remove(index)"></span></td>
                         </tr>



                         <tr v-if="items.length > 0">
                             <td colspan="4" style="text-align:right"><b style="font-size:15px">TOTAL: {{ numberWithCommas(itemsTotal) }}</b></td>
                             <td></td>
                         </tr>
                         <tr v-else >
                             <td colspan="4" style="text-align:center">No Data to display</td>
                         </tr>
                        </tbody>
                     </table>
                </div>
            </div>
        </div>
        <!-- <ItemInformationModal></ItemInformationModal> -->

    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse} from '../helpers/helper';
    export default {
        props: ["shipmentModel",'editShipmentModel'],
        mounted() {
            console.log('Signature Requirement Form Component mounted.')
        },
        data(){
            return {
                processing: false,
                model:{},
                errors:{},
                countries:{},
                // items:[],
                component_error:false,
                itemsTotal:0,
                items:[
                    {
                        'description': "",
                        'country': "",
                        'quantity': 0,
                        'value': 0
                    }
                ]
            }
        },

        watch:{
            items:{
                handler:function(data){
                     this.$root.$emit("ItemInformationModel",this.items);
                    this.calculateTotal();
                    var self = this
                    $.each(data,function(k,v){
                        self.filterInput(v.description,k,"description",/[^A-Za-z ]/gi)
                        self.filterInput(v.quantity,k,"quantity",/[^0-9]/gi)
                        self.filterInput(v.value,k,"value",/[^0-9]/gi)
                    })
                },
                deep:true
            },

            editShipmentModel:{
                handler:function(d){
                    let data = d.shipmentitems;
                    let arr = [];
                    $.each(data, function(k,v){
                        arr[k] = {
                            'id':v.id,
                            'description':v.description,
                            'value':v.value,
                            'quantity':v.qty,
                            'country':v.origin_country
                        }
                        
                    })

                    this.items = arr;
                },
                deep:true
            }

        },
        created(){
            this.init();
            
            this.$root.$on("component_errors",(c)=>{
                console.log("item details component_error")
                this.component_error = c.item_information
            })

            this.$root.$on("emit-item-info",(b)=>{
                this.items.push(b)
            })


            if(this.editShipmentModel){
                // this.
                let data = this.editShipmentModel.shipmentitems;
                let arr = [];
                $.each(data, function(k,v){
                    arr[k] = {
                        'id':v.id,
                        'description':v.description,
                        'value':v.value,
                        'quantity':v.qty,
                        'country':v.origin_country
                    }
                    
                })

                this.items = arr;
                // console.log(arr)


            }

        },

    
        methods:{
             init:function(){
                 this.$http.get("/api/countries").then(res=>{
                    this.countries = res.data;
                })
            },

            filterInput:function(data,index, field, regex=false){
                let re = /[^A-Za-z \d]/gi
                if(data){
                    // console.log(index);
                    // console.log(this.items[0]);
                    if(regex){

                        re = regex;
                        this.$set(this.items[index], field, data.replace(re, ''))
                    }else{
                        this.$set(this.items[index], field, data.replace(re, ''))
                    }
                }

            },


            numberWithCommas: function(x) {
                return x.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ",");
            },

            addRow:function(){
                this.items.push({description:"", country:"", quantity:0, value:0})
            },

            showModal:function(){
                var el = $('.item-information-modal')
                el.modal({backdrop: 'static', keyboard: false}) ;
            },

            calculateTotal:function(){
                let x = 0;
                 $.each(this.items,function(k,v){
                    let qty = 0;
                    let val = 0;

                    if(v.value) qty = v.value
                    if(v.quantity) val = v.quantity;
                // console.log(sa)
                     x += (parseFloat(qty) * parseFloat(val));
                 })


                 this.itemsTotal = x
                 this.detectAmountLimit(x)


            },

            detectAmountLimit:function(total){
                try{

                    if(this.shipmentModel.recipient_model.country == "US"){
                        if(total > 800) showErrorMsg("You have exceeded the allowed total item value of $800.")
                    }

                    if(this.shipmentModel.recipient_model.country != "US" && this.shipmentModel.recipient_model.country != "CA"){
                        if(total > 1000) showErrorMsg("You have exceeded the allowed total item value of $1000.")
                    }
                }catch(e){
                    // console.log(e)
                }
            },

            remove:function(index){

                if(this.items.length > 1){
                    this.items.splice(index, 1)
                    this.calculateTotal();
                }
                // console.log(this.items.length);
            }

        }
    }
</script>
