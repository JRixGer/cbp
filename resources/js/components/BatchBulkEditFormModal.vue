<template>
    <div class="modal batchbulk-form-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog modal-md" v-bind:class="{narrow: isNarrow, wide: isWide}">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span>{{ updateType }} UPDATE</span>
                       
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST">
                    <div class="modal-body" v-if="updateType=='CSV' || updateType=='ORDER'">
                        <div class="row">
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.FirstName, 'has-error has-danger': errors.FirstName }">
                                    <input type="text" v-model="model.FirstName" id="_firstName" class="form-control" placeholder="" @input="fixThis(model.FirstName,'FirstName')">
                                    <span class="bar"></span>
                                    <label for="_firstName">First Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.FirstName">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.LastName, 'has-error has-danger': errors.LastName }">
                                    <input type="text" v-model="model.LastName" id="_lastName" class="form-control" placeholder="" @input="fixThis(model.LastName,'LastName')">
                                    <span class="bar"></span>
                                    <label for="_lastName">Last Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.LastName">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.BusinessName, 'has-error has-danger': errors.BusinessName }">
                                    <input type="text" v-model="model.BusinessName" id="_businessName" class="form-control" placeholder="" @input="fixThis(model.BusinessName,'BusinessName')">
                                    <span class="bar"></span>
                                    <label for="_businessName">Business Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.BusinessName">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.AddressLine1, 'has-error has-danger': errors.AddressLine1 }">
                                    <input type="text" v-model="model.AddressLine1" id="_addressLine1" class="form-control" placeholder="" @input="fixThis(model.AddressLine1,'AddressLine1')">
                                    <span class="bar"></span>
                                    <label for="_addressLine1">Address Line 1 <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.AddressLine1">{{ error }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.AddressLine2, 'has-error has-danger': errors.AddressLine2 }">
                                    <input type="text" v-model="model.AddressLine2" id="_addressLine2" class="form-control" placeholder="" @input="fixThis(model.AddressLine2,'AddressLine2')">
                                    <span class="bar"></span>
                                    <label for="_addressLine2">Address Line 2 <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.AddressLine2">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.ProvState, 'has-error has-danger': errors.ProvState }">
                                    <input type="text" v-model="model.ProvState" id="_provState" class="form-control" placeholder="" @input="fixThis(model.ProvState,'ProvState')">
                                    <span class="bar"></span>
                                    <label for="_provState">Province/State <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.ProvState">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.IntlProvState, 'has-error has-danger': errors.IntlProvState }">
                                    <input type="text" v-model="model.IntlProvState" id="_intlProvState" class="form-control" placeholder="" @input="fixThis(model.IntlProvState,'IntlProvState')">
                                    <span class="bar"></span>
                                    <label for="_intlProvState">Intl.Prov/State <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.IntlProvState">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.PostalZipCode, 'has-error has-danger': errors.PostalZipCode }">
                                    <input type="text" v-model="model.PostalZipCode" id="_PostalZipCode" class="form-control" placeholder="" @input="fixThis(model.PostalZipCode,'PostalZipCode')">
                                    <span class="bar"></span>
                                    <label for="_PostalZipCode">Postal ZipCode <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.PostalZipCode">{{ error }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.City, 'has-error has-danger': errors.City }">
                                    <input type="text" v-model="model.City" id="_city" class="form-control" placeholder="" @input="fixThis(model.City,'City')">
                                    <span class="bar"></span>
                                    <label for="_city">City <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.City">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Country, 'has-error has-danger': errors.Country }">
                                    <input type="text" v-model="model.Country" id="_country" class="form-control" placeholder="" @input="fixThis(model.Country,'Country')">
                                    <span class="bar"></span>
                                    <label for="_country">Country <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Country">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Email, 'has-error has-danger': errors.Email }">
                                    <input type="text" v-model="model.Email" id="_email" class="form-control" placeholder="" v-bind:class="{'bg-invalid': validate('Email',model.Email)}" @input="fixThis(model.Email,'Email')">
                                    <span class="bar"></span>
                                    <label for="_email">Email <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Email">{{ error }}</span>
                                </div>
                            </div>
                             <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.LetterMail, 'has-error has-danger': errors.LetterMail }">

                                    <select type="text" v-model="model.LetterMail" id="_LetterMail" class="form-control" placeholder=""> 
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>

                                    <span class="bar"></span>
                                    <label for="_LetterMail">Letter Mail <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.LetterMail">{{ error }}</span>
                                </div>
                            </div>

                        </div>

                        <div class="row" v-if="this.fileName=='PD'">
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Length, 'has-error has-danger': errors.Length }">
                                    <input type="text" v-model="model.Length" id="_Length" class="form-control" placeholder="" @input="fixThis(model.Length,'Length')">
                                    <span class="bar"></span>
                                    <label for="_Length">Length <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Length">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Width, 'has-error has-danger': errors.Width }">
                                    <input type="text" v-model="model.Width" id="_Width" class="form-control" placeholder="" @input="fixThis(model.Width,'Width')">
                                    <span class="bar"></span>
                                    <label for="_Width">Width <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Width">{{ error }}</span>
                                </div>
                            </div>                            
                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Height, 'has-error has-danger': errors.Height }">
                                    <input type="text" v-model="model.Height" id="_Height" class="form-control" placeholder="" @input="fixThis(model.Height,'Height')">
                                    <span class="bar"></span>
                                    <label for="_Height">Height <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Height">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Weight, 'has-error has-danger': errors.Weight }">
                                    <input type="text" v-model="model.Weight" id="_Weight" class="form-control" placeholder="" @input="fixThis(model.Weight,'Weight')">
                                    <span class="bar"></span>
                                    <label for="_Weight">Weight <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Weight">{{ error }}</span>
                                </div>
                            </div>    

                        </div>

                       <div class="row">

                            <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.Phone, 'has-error has-danger': errors.Phone }">
                                    <input type="text" v-model="model.Phone" id="_Phone" class="form-control" placeholder="" v-bind:class="{'bg-invalid': validate('Phone',model.Phone)}"  @input="fixThis(model.Phone,'Phone')">
                                    <span class="bar"></span>
                                    <label for="_Phone">Phone <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Phone">{{ error }}</span>
                                </div>
                            </div>

                             <div class="col-md-3">
                                <div v-bind:class="{'form-group': true, 'focused':model.isSignatureReq, 'has-error has-danger': errors.isSignatureReq }">
                                    
                                    <select type="text" v-model="model.isSignatureReq" id="_Length" class="form-control" placeholder=""> 
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>

                                    <span class="bar"></span>
                                    <label for="_isSignatureReq">Signature Required? <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.isSignatureReq">{{ error }}</span>
                                </div>
                            </div>                              
                        </div>

                    </div>
                    
                    <div class="modal-body" v-else-if="(updateType=='ITEM') || (updateType=='CSV FIELD')">
                        <div class="row narrow-margin">

                            
                            <div class="col-md-12"  v-if="fieldName=='isSignatureReq' || fieldName=='LetterMail'">
                                 <div v-bind:class="{'form-group': true, 'focused':model[fieldName], 'has-error has-danger': errors[fieldName] }">
                                    <select type="text" v-model="model[fieldName]" :id="'_'+fieldName" class="form-control" placeholder=""> 
                                        <option value="Y">Yes</option>
                                        <option value="N">No</option>
                                    </select>

                                    <span class="bar"></span>
                                    <label :for="'_'+fieldName">{{fieldName}} <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors[fieldName]">{{ error }}</span>
                                </div>

                            </div>
                            
                            <div class="col-md-12" v-else>
                                <div v-bind:class="{'form-group': true, 'focused':model[fieldName], 'has-error has-danger': errors[fieldName] }">
                                    <input type="text" v-model="model[fieldName]" :id="'_'+fieldName" class="form-control" placeholder="" v-bind:class="{'bg-invalid': validate(fieldName,model[fieldName])}"  @input="fixThis(model[fieldName],fieldName)">
                                    <span class="bar"></span>
                                    <label :for="'_'+fieldName">{{fieldName}} <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors[fieldName]">{{ error }}</span>
                                </div>
                            </div>

                        </div>         
                    </div>

                    <div class="modal-body" v-else-if="updateType=='RECIPIENT'">
                       
                        <div class="row narrow-margin">   
                        
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.FirstName, 'has-error has-danger': errors.FirstName }">
                                    <input type="text" v-model="model.FirstName" id="_firstName" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_firstName">First Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.FirstName">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.LastName, 'has-error has-danger': errors.LastName }">
                                    <input type="text" v-model="model.LastName" id="_lastName" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_lastName">Last Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.LastName">{{ error }}</span>
                                </div>
                            </div>
                        </div>
                        <div class="row">   
                        
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.BusinessName, 'has-error has-danger': errors.BusinessName }">
                                    <input type="text" v-model="model.BusinessName" id="_businessName" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_businessName">Business Name <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.BusinessName">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.AddressLine1, 'has-error has-danger': errors.AddressLine1 }">
                                    <input type="text" v-model="model.AddressLine1" id="_addressLine1" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_addressLine1">Address Line 1 <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.AddressLine1">{{ error }}</span>
                                </div>
                            </div>
                        </div>   


                        <div class="row">   
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.AddressLine2, 'has-error has-danger': errors.AddressLine2 }">
                                    <input type="text" v-model="model.AddressLine2" id="_addressLine2" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_addressLine2">Address Line 2 <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.AddressLine2">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.ProvState, 'has-error has-danger': errors.ProvState }">
                                    <input type="text" v-model="model.ProvState" id="_provState" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_provState">Province/State <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.ProvState">{{ error }}</span>
                                </div>
                            </div>
                        </div>    
                        <div class="row">    
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.PostalZipCode, 'has-error has-danger': errors.PostalZipCode }">
                                    <input type="text" v-model="model.PostalZipCode" id="_PostalZipCode" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_PostalZipCode">Postal ZipCode <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.PostalZipCode">{{ error }}</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.City, 'has-error has-danger': errors.City }">
                                    <input type="text" v-model="model.City" id="_city" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_city">City <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.City">{{ error }}</span>
                                </div>
                            </div>
                        </div>    
                        <div class="row">    
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.Country, 'has-error has-danger': errors.Country }">
                                    <input type="text" v-model="model.Country" id="_country" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_country">Country <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Country">{{ error }}</span>
                                </div>
                            </div>
                        </div>    

                    </div>     

                    <div class="modal-body" v-else-if="updateType=='CARRIER'">
                        <div class="row narrow-margin">
                            <div class="col-md-12">
                                <div v-bind:class="{'form-group': true, 'focused':carrierKey}">
                                     <select @change="onChange()" class="form-control" v-model="carrierKey" style="font-size: .8rem;" :disabled="model.Carrier == '-'? true : false">
                                       <option style="font-size: .8rem;" v-for="carrier in option" :value="carrier">{{carrier}}</option>
                                    </select>
                                    <span class="bar"></span>
                                    <label for="_Carrier">Select Carrier</label>
                                </div>
                            </div>
                        </div>         
                                  

                        <div class="row">
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.Carrier, 'has-error has-danger': errors.Carrier }">
                                    <input type="text" v-model="model.Carrier" id="_Carrier" class="form-control" placeholder="" disabled="disabled">
                                    <span class="bar"></span>
                                    <label for="_Carrier">Carrier <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Carrier">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.CarrierDesc, 'has-error has-danger': errors.CarrierDesc }">
                                    <input type="text" v-model="model.CarrierDesc" id="_Carrier" class="form-control" placeholder="" disabled="disabled">
                                    <span class="bar"></span>
                                    <label for="_CarrierDesc">Carrier Description <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.CarrierDesc">{{ error }}</span>
                                </div>
                            </div>
                        </div>         
                        <div class="row">
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.TotalFee, 'has-error has-danger': errors.TotalFee }">
                                    <input type="text" v-model="model.TotalFee" id="_TotalFee" class="form-control" placeholder="" disabled="disabled">
                                    <span class="bar"></span>
                                    <label for="_TotalFee">Rate <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.TotalFee">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.Duration, 'has-error has-danger': errors.Duration }">
                                    <input type="text" v-model="model.Duration" id="_Duration" class="form-control" placeholder="" disabled="disabled">
                                    <span class="bar"></span>
                                    <label for="_Duration">Duration <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Duration">{{ error }}</span>
                                </div>
                            </div>

                        </div>         
                    </div>       

                    <div class="modal-body" v-else>
                        
                        <div class="row narrow-margin" v-if="this.fileName=='PD'">
                            <div class="col-md-6" v-if="this.fileName=='PD'">
                                <div v-bind:class="{'form-group': true, 'focused':model.Length, 'has-error has-danger': errors.Length }">
                                    <input type="text" v-model="model.Length" id="_Length" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_Length">Length <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Length">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-6" v-if="this.fileName=='PD'">
                                <div v-bind:class="{'form-group': true, 'focused':model.Width, 'has-error has-danger': errors.Width }">
                                    <input type="text" v-model="model.Width" id="_Width" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_Width">Width <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Width">{{ error }}</span>
                                </div>
                            </div>                            
                        </div>    

                        <div class="row">
                            <div class="col-md-6" v-if="this.fileName=='PD'">
                                <div v-bind:class="{'form-group': true, 'focused':model.Height, 'has-error has-danger': errors.Height }">
                                    <input type="text" v-model="model.Height" id="_Height" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_Height">Height <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Height">{{ error }}</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div v-bind:class="{'form-group': true, 'focused':model.Weight, 'has-error has-danger': errors.Weight }">
                                    <input type="text" v-model="model.Weight" id="_Weight" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="_Weight">Weight <span class="required">*</span></label>
                                    <span class="help-block" v-for="error in errors.Weight">{{ error }}</span>
                                </div>
                            </div>
                        </div>

                    </div>       
                    <div class="modal-footer">
                       <button type="button" class="btn btn-outline btn-secondary" data-dismiss="modal" aria-hidden="true" v-bind:class="{hideButton: !doneOnly}">CANCEL</button>

                       <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" @click='doneCSV' v-bind:class="{hideButton: !csvButtonAll}">DONE</button>
                       <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" @click='doneCSV' v-bind:class="{hideButton: !csvButtonItem}">DONE</button>
                       <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" @click='updateShipment("recipient")' v-bind:class="{hideButton: !recipientButton}">UPDATE RECIPIENT</button>
                       <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" @click='updateShipment("parcel")' v-bind:class="{hideButton: !parcelButton}">UPDATE PARCEL</button>
                       <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" @click='updateShipment("order")' v-bind:class="{hideButton: !orderButton}">UPDATE ORDER</button>
                    
                    </div>
                </form>


            </div>

        </div>

        
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, validate_data, CSVValidator_after, showLoading, hideLoading, removeTrash} from '../helpers/helper';
export default {
    props:[],
    mounted() {
    },
    data(){
        return {
            processing: false,
            model:{},
            option:{},
            fieldName:{},
            fileName:{},
            validate_csv: {},
            updateType: '',
            isNarrow: false,
            isWide: false,
            doneOnly: false,
            errors:{},
            carrierKey: '',
            isValid: true,
            recipientButton: false,
            parcelButton: false,
            csvButtonItem: false,
            csvButtonAll: false,
            orderButton: false,
            isLetter: false,
            isSignatureReq: false,     
            yesNo: ["YES","Yes","yes","Y","y","1"]  
        }
    },
    created(){
        this.init();
        this.$root.$on("order_edit",(d, f)=>{
            this.fileName = f;
            this.model = d;
            this.isNarrow = false;
            this.isWide = false;
            this.doneOnly = true;
            this.updateType = 'ORDER';
            this.recipientButton = false;
            this.parcelButton = false;
            this.carrierButton = false;
            this.csvButtonItem = false;
            this.csvButtonAll = false;
            this.orderButton = true;
            this.isYes();

        })
        this.$root.$on("csv_edit",(d, f)=>{
            this.fileName = f;
            this.model = d;
            this.isNarrow = false;
            this.isWide = false;
            this.doneOnly = false;
            this.updateType = 'CSV';
            this.recipientButton = false;
            this.parcelButton = false;
            this.carrierButton = false;
            this.csvButtonItem = false;
            this.csvButtonAll = true;
            this.orderButton = false;
            this.isYes();
        })
        this.$root.$on("csv_field_edit",(d, n, f)=>{
            this.fileName = f;
            this.model = d;
            this.fieldName = n;
            this.isNarrow = true;
            this.isWide = false;
            this.doneOnly = false;
            this.updateType = 'CSV FIELD';
            this.recipientButton = false;
            this.parcelButton = false;
            this.csvButtonItem= true;            
            this.csvButtonAll = false;         
            this.orderButton = false;   
            this.isYes();
        })        
        this.$root.$on("field_edit",(d, n)=>{
            this.fileName = d.shipment_type;
            this.model = d;
            this.fieldName = n;
            this.isNarrow = true;
            this.isWide = false;
            this.doneOnly = true;
            this.updateType = 'ITEM';
            this.recipientButton = false;
            this.parcelButton = false;
            this.csvButtonItem = false;
            this.csvButtonAll = false; 
            this.orderButton = false;
            this.isYes();
        })
        this.$root.$on("recipient_edit",(d)=>{
            this.fileName = d.shipment_type;
            this.model = d;
            this.isNarrow = true;
            this.isWide = false;
            this.doneOnly = true;
            this.updateType = 'RECIPIENT';
            this.recipientButton = false;
            this.parcelButton = true;
            this.csvButtonItem = false;
            this.csvButtonAll = false;      
            this.orderButton = false;
            //this.isYes();                  
        })  
        this.$root.$on("parcel_edit",(d)=>{
            this.fileName = d.shipment_type;
            this.model = d;
            this.isNarrow = true;
            this.isWide = false;
            this.doneOnly = true;
            this.updateType = 'PARCEL';
            this.recipientButton = false;
            this.parcelButton = true;
            this.csvButtonItem = false;                        
            this.csvButtonAll = false;   
            this.orderButton = false;   
            //this.isYes();                 
        })          

        

    },
    watch:{
        // model:{
        //     handler:function(data){
        //         console.log('>>' +data);
        //     },
        //     deep:true
        // }
        //var regex = /^[a-zA-Z0-9!@#\$%\^\&*\)\(+=._-]+$/g
    },
    methods:{
        init:function(){
        },
        isYes:function(){

            console.log(this.model);

            if(jQuery.inArray(this.model.isSignatureReq, this.yesNo) !== -1)
                this.model.isSignatureReq = 'Y'; 
            else
                this.model.isSignatureReq = 'N';  

            if(jQuery.inArray(this.model.LetterMail, this.yesNo) !== -1)
                this.model.LetterMail = 'Y'; 
            else
                this.model.LetterMail = 'N';
  
        }, 
        fixThis:function(d,f){
            this.model[f] = removeTrash(d,f);    
        },        
        onChange() {
            console.log(this.carrierKey);
            var eachOpt = this.carrierKey.split(", ");
            this.model.Carrier = eachOpt[0];
            this.model.CarrierDesc = eachOpt[1];
            this.model.Currency = eachOpt[2].replace(/[.0-9]/g, '');
            this.model.PostageFee = eachOpt[2].replace('CAD', '');
            this.model.Duration = eachOpt[3];
        },        
        doneCSV:function(){
        },
        saveCarrier:function(){
            this.processing = true;
            this.$http.post("/document/updateCarrier",this.model).then(response=>{

                console.log(response);
                this.processing = false;
                showSuccessMsg(response.data.message);
                if(this.errors){
                    this.errors = [];
                }
            }, response=>{
                this.errors = response.data.errors;
                this.processing = false;
                showErrorMsg(response.data.message)
            }).catch((err) => {
                handleErrorResponse(err.status);
                this.processing = false;
                if (err.status == 422) {
                    this.errors = err.data;
                }
            });        
        },
        updateShipment:function(t){
            this.processing = true;
            showLoading();
            this.model['updateType'] = t;
            this.$http.post("/document/update_shipment",this.model).then(response=>{
                console.log(response);

                this.processing = false;
                hideLoading();
                if(response.data.status == false)
                {
                    showErrorMsg(response.data.message);
                }
                else
                {

                    let singleCarrierInfo = [];
                    singleCarrierInfo = response.data.singleCarrierInfo;
                    this.$root.$emit("single_carrier_update", singleCarrierInfo);    
                    showSuccessMsg(response.data.message);
                    this.$root.$emit("successful_update");
                }
                if(this.errors){
                    this.errors = [];
                }
            }, response=>{
                this.errors = response.data.errors;
                this.processing = false;
                showErrorMsg(response.data.message)
            }).catch((err) => {
                handleErrorResponse(err.status);
                this.processing = false;
                if (err.status == 422) {
                    this.errors = err.data;
                }
            });   
        },                
        validate (c, v) {
            var n = validate_data(c, v, this.fileName, this.model.Country);
            if(n == 0)
                this.$root.$emit("no_error", n);

            return n;
        }

    }
};
</script>
<style scoped>
.modal-dialog {
    width: 80%;
    max-width: 800px;
    margin: 30px auto;
}
.form-group {
    margin-bottom: 40px !important;
}
.floating-labels .focused label {
    top: -15px !important;
}
.modal-header {
    margin-bottom: 5px;
}
.floating-labels .focused label {
    font-size: 11px;
    color: #0083c4;
}
.floating-labels .form-control {
    padding: 0px 10px 0px 0;
}
.bg-invalid {
    background-color: #fffe8363 !important;
    color: #ff0202;
    border-bottom-color: #ff0202;
    font-weight: 400;
}

.hideButton{
    display:none;
}

</style>