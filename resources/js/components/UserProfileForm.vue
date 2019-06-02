<template>
    <div class="col-md-12">

        <div class="row">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-title center-title title-color">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">US Address</h4>
                            </div>   

                            <div class="col-md-12">
                                <div style="min-width:200px">
                                    <!-- <div class="icons-inline-big">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Archive" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'archive', null, 'multiSelected')"><i class="mdi mdi-archive icon-custom-big"></i></button>
                                    </div>
                                    <div class="icons-inline-big">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}"  data-toggle="tooltip" data-placement="top" title="Void" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'void', null, 'multiSelected')"><i class="mdi mdi-stop-circle icon-custom-big"></i></button>
                                    </div> -->
                                </div>

                            </div>

                        </div>
                    </div>
                                                                                                                                                                                                                                                                                                         
                    <div class="card-block">
                        <div class="card-body" v-if="loaded" style="padding:20px; text-align:left">
                            <div class="row">
                            <div class="col-md-12" style="margin: 10px  10px  10px  10px">
                                <h5> {{ userInfo.sender_id }} (Customer ID) </h5>
                                
                            </div>
                            <div class="col-md-12" style="margin: 10px  10px  10px  10px">
                                <h5> {{ usAddress.address_1 }} <br>{{ usAddress.city }}, {{ usAddress.province }}, {{ usAddress.postal }}, {{ usAddress.country }}</h5>
                            </div>
                            <div class="col-md-12" style="margin: 10px  10px  10px  10px; font-style: italic">
                                <p>Please note that this address is to provide the customer for personal use.</p>
                            </div>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-title center-title title-color">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Password Reset</h4>
                            </div>   

                            <div class="col-md-12">
                                <div style="min-width:200px">

                                </div>

                            </div>

                        </div>
                    </div>
                                                                                                                                                                                                                                                                                                         
                    <div class="card-block">
                        <div class="card-body" v-if="loaded" style="padding:20px; text-align:left">
                            <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST" id="pwChange">
                                <div class="row">
                                    <div class="col-md-12" style="margin: 20px  10px  20px  10px">
                                        <div v-bind:class="{'form-group': true, 'focused':current_password, 'has-error has-danger': errors.current_password }">
                                            <input type="password" v-model="current_password" id="current_password" class="form-control" placeholder="" name="current_password">
                                            <span class="bar"></span>
                                            <label for="current_password">Old Password <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.current_password">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="margin: 20px  10px  20px  10px">
                                        <div v-bind:class="{'form-group': true, 'focused':password, 'has-error has-danger': errors.password }">
                                            <input type="password" v-model="password" id="password" class="form-control" placeholder="" name="password">
                                            <span class="bar"></span>
                                            <label for="password">New Password <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.password">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-12" style="margin: 20px  10px  20px  10px">
                                        <div v-bind:class="{'form-group': true, 'focused':password_confirmation, 'has-error has-danger': errors.password_confirmation }">
                                            <input type="password" v-model="password_confirmation" id="password_confirmation" class="form-control" placeholder=""  name="password_confirmation">
                                            <span class="bar"></span>
                                            <label for="password_confirmation">Confirm Password<span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.password_confirmation">{{ error }}</span>
                                        </div>
                                    </div>

                                </div>
               
                                <div class="row">
                                    <div class="col-md-12" style="margin: 20px  10px  20px  10px">
                                        <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" v-on:click="formSubmit('reset')">RESET</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>            
        </div>




        <div class="row">
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-title center-title title-color">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Individual</h4>
                            </div>   

                            <div class="col-md-12">
                                <div style="min-width:200px">
                                    <!-- <div class="icons-inline-big">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Archive" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'archive', null, 'multiSelected')"><i class="mdi mdi-archive icon-custom-big"></i></button>
                                    </div>
                                    <div class="icons-inline-big">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}"  data-toggle="tooltip" data-placement="top" title="Void" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'void', null, 'multiSelected')"><i class="mdi mdi-stop-circle icon-custom-big"></i></button>
                                    </div> -->
                                </div>

                            </div>

                        </div>
                    </div>
                                                                                                                                                                                                                                                                                                         
                    <div class="card-block">
                        <div class="card-body" v-if="loaded" style="padding:20px; text-align:left">
                             <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST" id="individual">
                                <div class="row">
                                    <div class="col-md-12" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.first_name, 'has-error has-danger': errors.first_name }"  style="margin: 10px  0px  50px  0px">
                                            <input type="text" v-model="userInfo.first_name" id="first_name" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="first_name">First Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.first_name">{{ error }}</span>
                                        </div>

                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.last_name, 'has-error has-danger': errors.last_name }" style="margin: 50px  0px  10px  0px">
                                            <input type="text" v-model="userInfo.last_name" id="last_name" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="last_name">Last Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.last_name">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div v-bind:class="{'form-group': true, 'focused':userInfo.address_1, 'has-error has-danger': errors.address_1 }" style="margin: 20px  0px  50px  0px">
                                        <GoogleAddress 
                                        id="map"
                                        class="form-control"
                                        placeholder=""
                                        v-model="userInfo.address_1"
                                        v-on:placechanged="getAddressData">
                                    </GoogleAddress>
                                    <span class="bar"></span>
                                    <label for="map">Address Line 1 <span class="required">*</span></label>

                                    <span class="help-block" v-for="error in errors.address_1">{{ error }}</span>
                                </div>


                                <div v-bind:class="{'form-group': true, 'focused':userInfo.address_2, 'has-error has-danger': errors.address_2 }" style="margin: 50px  0px  10px  0px">
                                    <input type="text" v-model="userInfo.address_2" id="address_2" class="form-control" placeholder="">
                                    <span class="bar"></span>
                                    <label for="address_2">Address Line 2 (Optional)</label>
                                    <span style="font-size:12px;">Apartment, suite, unit, building, floor, etc.</span>
                                    <span class="help-block" v-for="error in errors.address_2">{{ error }}</span>
                                </div>

                                <div class="row">
                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.city, 'has-error has-danger': errors.city }">
                                            <input type="text" v-model="userInfo.city" id="city" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="city">City <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.city">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">

                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.province, 'has-error has-danger': errors.province }">
                                            <input type="text" v-model="userInfo.province" id="province" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="province">Province / State <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.province">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.postal, 'has-error has-danger': errors.postal }">
                                            <input type="text" v-model="userInfo.postal" id="postal" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="postal">Postal Code / Zip Code <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.postal">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.country, 'has-error has-danger': errors.country }">
                                            <select v-model="userInfo.country" id="country" class="form-control" placeholder="Country" >
                                                <option value="CA">CANADA</option>
                                                <option value="US">United States</option>
                                                <option disabled>_________________________</option>
                                                <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="country">Country <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>  

                                <div class="row">
                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.email, 'has-error has-danger': errors.email }">
                                            <input type="text" v-model="userInfo.email" id="email" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="email">Email</label>
                                            <span class="help-block" v-for="error in errors.email">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  20px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':userInfo.contact_no, 'has-error has-danger': errors.contact_no }">
                                            <input type="text" v-model="userInfo.contact_no" id="contact_no" class="form-control" placeholder="">
                                            <span class="bar"></span>
                                            <label for="contact_no">Contact Number</label>
                                            <span class="help-block" v-for="error in errors.contact_no">{{ error }}</span>
                                        </div>
                                    </div>

                                </div>  


               
                                <div class="row">
                                    <div class="col-md-12" style="margin: 20px  0px 0px  0px">
                                        <button type="button" class="btn btn-outline btn-info" aria-hidden="true" v-on:click="formSubmit('user_info')">UPDATE</button>
                                    </div>
                                </div>

                            </form>
                        </div>
                    </div>
                </div>
            </div>

              <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-title center-title title-color">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Update Email</h4>
                            </div>   

                            <div class="col-md-12">
                                <div style="min-width:200px">

                                </div>

                            </div>

                        </div>
                    </div>
                                                                                                                                                                                                                                                                                                         
                    <div class="card-block">
                        <div class="card-body" v-if="loaded" style="padding:20px; text-align:left">
                            <form v-on:submit.prevent="formSubmit" role="form" method="POST" id="emailChange">
                                <div class="row floating-labels">
                                    <div class="col-md-12" style="margin: 30px  10px  20px  10px">
                                        <div v-bind:class="{'form-group': true, 'focused':email, 'has-error has-danger': errors.email }">
                                            <input type="text" v-model="email" id="emailUpdate" class="form-control" placeholder="" name="email">
                                            <span class="bar"></span>
                                            <label for="email">Login Email <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.email">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>



                                <div class="row"  v-if="ifChanged()">
                                    <div class="col-md-12" style="margin: 30px  10px  20px  10px">
                                        <div style="text-align: center">
                                            <label for="check_password"><h4>SECURED</h4>Please input your password to proceed</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6 offset-md-3">                                        
                                        <input type="password" v-model="check_password" id="check_password" class="form-control" placeholder="" name="check_password">
                                    </div>
                                </div>

    
                                <div class="row">
                                    <div class="col-md-6" style="margin: 20px  10px  20px  10px">
                                        <button type="button" class="btn btn-outline btn-info" data-dismiss="modal" aria-hidden="true" v-on:click="formSubmit('update_email')" :disabled="check_password.length==0">UPDATE</button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>  
        </div>




        <div class="row">            
            <div class="col-md-6">
                <div class="card text-center">
                    <div class="card-title center-title title-color">
                        <div class="row">
                            <div class="col-md-12">
                                <h4 class="card-title">Business</h4>
                            </div>   

                            <div class="col-md-12">
                                <div style="min-width:200px">
                                </div>
                            </div>

                        </div>
                    </div>
                                                                                                                                                                                                                                                                                                         
                    <div class="card-block">
                        <div class="row">
                                <div class="col-md-12" style="margin: 20px  0px  10px  0px">  


                                    <div class="form-group" style="margin-bottom:20px">
                                         <h5 style="margin-bottom:20px"><strong>Do you have a Canadian import mumber?</strong></h5>
                                        <input type="radio" name="has_canadian_in" v-model.number="powerOfAtty.has_canadian_in" value="1" id="has-has_canadian_in-yes">
                                        <label for="has-has_canadian_in-yes">YES</label>

                                        <input type="radio" name="has_canadian_in" v-model.number="powerOfAtty.has_canadian_in" value="0" id="has-has_canadian_in-no">
                                        <label for="has-has_canadian_in-no">NO</label>
                                    </div>

                                    <button title="FOR BLANK PoA FORM CLICK HERE" v-if="powerOfAtty.has_canadian_in == 1 || powerOfAtty.has_canadian_in == 0" class="btn btn-secondary margin-button" @click="handleDownload('poa')">DOWNLOAD: Blank PoA Form</button>
                                    <button title="FOR BLANK IMPORT NUMBER APPLICATION FORM CLICK HERE" v-if="powerOfAtty.has_canadian_in == 0" class="btn btn-secondary margin-button" @click="handleDownload('ina')">DOWNLOAD: Blank INA Form</button>

                            </div>
                        </div>


                        <form class="floating-labels" v-on:submit.prevent="formSubmit" role="form" method="POST" id="poa"  style="padding-top:10px; margin-top:20px">
                            <div class="card-body" v-if="loaded" style="padding:20px; text-align:left;">

                                <div class="row" v-if="powerOfAtty.has_canadian_in == 1">
                                <!-- <div class="row"> -->
                                    <div class="col-md-12" style="margin: 20px  0px  10px  0px">    
                                            <div v-bind:class="{'form-group': true, 'focused':ina.import_number, 'has-error has-danger': errors.import_number }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.ina.import_number.$model" id="import_number" class="form-control" placeholder="" :class="status($v.ina.import_number)">
                                            <span class="bar"></span>
                                            <label for="import_number">Import Number <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.import_number">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>    

                                <div class="row" v-if="powerOfAtty.has_canadian_in != null">
                                    <div class="col-md-12" style="margin: 20px  0px  10px  0px">    
                                        <h4 class="card-title">Power Of Attorney</h4>
                                    </div>
                                </div>    

                                <div class="row" v-if="powerOfAtty.has_canadian_in != null">

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused': powerOfAtty.business_name, 'has-error has-danger': errors.business_name }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.business_name.$model" id="business_name" class="form-control" placeholder="" :class="status($v.powerOfAtty.business_name)">
                                            <span class="bar"></span>
                                            <label for="business_name">Business Name (Legal name)<span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.business_name">{{ error }}</span>
                                        </div>
                                    </div>

                                   <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.address_1, 'has-error has-danger': errors.address_1 }"  style="margin: 10px  0px  10px  0px">
                                            <GoogleAddress 
                                            id="map"
                                            class="form-control"
                                            placeholder=""
                                            v-model="powerOfAtty.address_1"
                                            v-on:placechanged="getAddressDataPoA">
                                            </GoogleAddress>
                                            <span class="bar"></span>
                                            <label for="map">Address 1 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.address_1">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.city, 'has-error has-danger': errors.city }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.city.$model" id="poa_city" class="form-control" placeholder="" :class="status($v.powerOfAtty.city)">
                                            <span class="bar"></span>
                                            <label for="poa_city">City<span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.city">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.province_state, 'has-error has-danger': errors.province_state }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.province_state.$model" id="province_state" class="form-control" placeholder="" :class="status($v.powerOfAtty.province_state)">
                                            <span class="bar"></span>
                                            <label for="province_state">Province State <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.province_state">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.country, 'has-error has-danger': errors.country }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.country.$model" id="poa_country" class="form-control" placeholder="" :class="status($v.powerOfAtty.country)">
                                            <span class="bar"></span>
                                            <label for="poa_country">Country <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.full_name_of_corp_client, 'has-error has-danger': errors.full_name_of_corp_client }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.full_name_of_corp_client.$model" id="full_name_of_corp_client" class="form-control" placeholder="" :class="status($v.powerOfAtty.full_name_of_corp_client)">
                                            <span class="bar"></span>
                                            <label for="full_name_of_corp_client">Full Name of Corp. Client <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.full_name_of_corp_client">{{ error }}</span>
                                        </div>
                                    </div>

                                   <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.name_of_municipality, 'has-error has-danger': errors.name_of_municipality }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.name_of_municipality.$model" id="name_of_municipality" class="form-control" placeholder="" :class="status($v.powerOfAtty.name_of_municipality)">
                                            <span class="bar"></span>
                                            <label for="name_of_municipality">Name of Municipality <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.name_of_municipality">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.name_of_province_state, 'has-error has-danger': errors.name_of_province_state }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.name_of_province_state.$model" id="name_of_province_state" class="form-control" placeholder="" :class="status($v.powerOfAtty.name_of_province_state)">
                                            <span class="bar"></span>
                                            <label for="name_of_province_state">Name of Province/State <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.name_of_province_state">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.name_of_country, 'has-error has-danger': errors.country }">
                                            <select v-model="$v.powerOfAtty.name_of_country.$model" id="country" class="form-control" placeholder="Country" >
                                                <option value="CA">CANADA</option>
                                                <option value="US">United States</option>
                                                <option disabled>_________________________</option>
                                                <option v-for="item in countries" v-if="item.code != 'US' && item.code != 'CA' " v-bind:value="item.code">{{ item.name }}</option>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="country">Name of Country <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.country">{{ error }}</span>
                                        </div>


                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.name_of_signing_authority_1, 'has-error has-danger': errors.name_of_signing_authority_1 }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.name_of_signing_authority_1.$model" id="name_of_signing_authority_1" class="form-control" placeholder="" :class="status($v.powerOfAtty.name_of_signing_authority_1)">
                                            <span class="bar"></span>
                                            <label for="name_of_signing_authority_1">Name of Signing Authority 1 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.name_of_signing_authority_1">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.office_held_by_signing_authority_1, 'has-error has-danger': errors.office_held_by_signing_authority_1 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.office_held_by_signing_authority_1.$model" id="office_held_by_signing_authority_1" class="form-control" placeholder="" :class="status($v.powerOfAtty.office_held_by_signing_authority_1)">
                                            <span class="bar"></span>
                                            <label for="office_held_by_signing_authority_1">Office Held By Signing Authority 1 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.office_held_by_signing_authority_1">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.signature_1, 'has-error has-danger': errors.signature_1 }" style="margin: 10px  0px  10px  0px">
                                            <input type="hidden" v-model="powerOfAtty.signature_1" id="signature_1">
                                            <img height="35px" :src="imgUrl(1)">
                                            <span class="bar"></span>
                                            <label for="signature_1" style="width:100%">Signature 1 <span class="required">*</span>
                                                <span>
                                                <button type="button" class="btn btn-secondary btn-outline icon-btn" v-on:click="drawSignature(1)" style="float:right"><i class="mdi mdi-pencil icon-custom-big"></i></button>
                                                </span>    
                                            </label>
                                            
                                            <span class="help-block" v-for="error in errors.signature_1">{{ error }}</span>
                                        </div>
                                    </div>
  
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.name_of_signing_authority_2, 'has-error has-danger': errors.name_of_signing_authority_2 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.name_of_signing_authority_2.$model" id="name_of_signing_authority_2" class="form-control" placeholder="" :class="status($v.powerOfAtty.name_of_signing_authority_2)">
                                            <span class="bar"></span>
                                            <label for="name_of_signing_authority_2">Name of Signing Authority 2 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.name_of_signing_authority_2">{{ error }}</span>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.office_held_by_signing_authority_2, 'has-error has-danger': errors.office_held_by_signing_authority_2 }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.powerOfAtty.office_held_by_signing_authority_2.$model" id="office_held_by_signing_authority_2" class="form-control" placeholder="" :class="status($v.powerOfAtty.office_held_by_signing_authority_2)">
                                            <span class="bar"></span>
                                            <label for="office_held_by_signing_authority_2">Office Held By Signing Authority 2 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.office_held_by_signing_authority_2">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':powerOfAtty.signature_2, 'has-error has-danger': errors.signature_2 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="hidden" v-model="powerOfAtty.signature_2" id="signature_2">
                                            <img height="35px" :src="imgUrl(2)">                                            
                                            <span class="bar"></span>
                                            <label for="signature_2" style="width: 100%">Signature 2 <span class="required">*</span>
                                                <span>    
                                                <button type="button" class="btn btn-secondary btn-outline icon-btn" v-on:click="drawSignature(2)" style="float:right"><i class="mdi mdi-pencil icon-custom-big"></i></button>
                                                </span>
                                            </label>
                                            <span class="help-block" v-for="error in errors.signature_2">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>




                                <div class="row" v-if="powerOfAtty.has_canadian_in == 0">
                                    <div class="col-md-12" style="margin: 20px  0px  10px  0px">    
                                        <h4 class="card-title">Import Number Application</h4>
                                    </div>
                                </div>    
                                <div class="row" v-if="powerOfAtty.has_canadian_in == 0">
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.business_number, 'has-error has-danger': errors.business_number }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.business_number.$model" id="business_number" class="form-control" placeholder="" :class="status($v.importNumApp.business_number)">
                                            <span class="bar"></span>
                                            <label for="business_number">Business Number <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.business_number">{{ error }}</span>
                                        </div>
                                    </div>
 
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.language, 'has-error has-danger': errors.language }"  style="margin: 10px  0px  10px  0px">
                                            <select type="text" v-model="importNumApp.language" id="language" class="form-control" placeholder=""> 
                                                <option value="English">English</option>
                                                <option value="French">French</option>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="language">Language <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.language">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.operating_trade, 'has-error has-danger': errors.operating_trade }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.operating_trade.$model" id="operating_trade" class="form-control" placeholder="" :class="status($v.importNumApp.operating_trade)">
                                            <span class="bar"></span>
                                            <label for="operating_trade">Operating Trade <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.operating_trade">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.import_export_program_account_name, 'has-error has-danger': errors.import_export_program_account_name }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.import_export_program_account_name.$model" id="import_export_program_account_name" class="form-control" placeholder="" :class="status($v.importNumApp.import_export_program_account_name)">
                                            <span class="bar"></span>
                                            <label for="import_export_program_account_name">Import/Export Program Account Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.import_export_program_account_name">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.physical_business_location, 'has-error has-danger': errors.physical_business_location }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.physical_business_location.$model" id="physical_business_location" class="form-control" placeholder="" :class="status($v.importNumApp.physical_business_location)">
                                            <span class="bar"></span>
                                            <label for="physical_business_location">Physical Business Location <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.physical_business_location">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.physical_city, 'has-error has-danger': errors.physical_city }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.physical_city.$model" id="physical_city" class="form-control" placeholder="" :class="status($v.importNumApp.physical_city)">
                                            <span class="bar"></span>
                                            <label for="physical_city">Physical City <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.physical_city">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.physical_province_state, 'has-error has-danger': errors.physical_province_state }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.physical_province_state.$model" id="physical_province_state" class="form-control" placeholder="" :class="status($v.importNumApp.physical_province_state)">
                                            <span class="bar"></span>
                                            <label for="physical_province_state">Physical Province/State <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.physical_province_state">{{ error }}</span>
                                        </div>
                                    </div>

                                     <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.physical_postal_zip_code, 'has-error has-danger': errors.physical_postal_zip_code }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.physical_postal_zip_code.$model" id="physical_postal_zip_code" class="form-control" placeholder="" :class="status($v.importNumApp.physical_postal_zip_code)">
                                            <span class="bar"></span>
                                            <label for="physical_postal_zip_code">Physical Postal/Zip Code <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.physical_postal_zip_code">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.physical_country, 'has-error has-danger': errors.physical_country }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.physical_country.$model" id="physical_country" class="form-control" placeholder="" :class="status($v.importNumApp.physical_country)">
                                            <span class="bar"></span>
                                            <label for="physical_country">Physical Country <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.physical_country">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_title, 'has-error has-danger': errors.contact_person_title }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_title.$model" id="contact_person_title" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_title)">
                                            <span class="bar"></span>
                                            <label for="contact_person_title">Contact Person Title <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_title">{{ error }}</span>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_first_name, 'has-error has-danger': errors.contact_person_first_name }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_first_name.$model" id="contact_person_first_name" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_first_name)">
                                            <span class="bar"></span>
                                            <label for="contact_person_first_name">Contact Person First Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_first_name">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_last_name, 'has-error has-danger': errors.contact_person_last_name }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_last_name.$model" id="contact_person_last_name" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_last_name)">
                                            <span class="bar"></span>
                                            <label for="contact_person_last_name">Contact Person Last Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_last_name">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_work_tel_no, 'has-error has-danger': errors.contact_person_work_tel_no }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_work_tel_no.$model" id="contact_person_work_tel_no" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_work_tel_no)">
                                            <span class="bar"></span>
                                            <label for="contact_person_work_tel_no">Contact Person Work Tel No <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_work_tel_no">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_ext, 'has-error has-danger': errors.contact_person_ext }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_ext.$model" id="contact_person_ext" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_ext)">
                                            <span class="bar"></span>
                                            <label for="contact_person_ext">Contact Person Ext <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_ext">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_work_fax_no, 'has-error has-danger': errors.contact_person_work_fax_no }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_work_fax_no.$model" id="contact_person_work_fax_no" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_work_fax_no)">
                                            <span class="bar"></span>
                                            <label for="contact_person_work_fax_no">Contact Person Work Fax No <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_work_fax_no">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.contact_person_mobile_no, 'has-error has-danger': errors.contact_person_mobile_no }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.contact_person_mobile_no.$model" id="contact_person_mobile_no" class="form-control" placeholder="" :class="status($v.importNumApp.contact_person_mobile_no)">
                                            <span class="bar"></span>
                                            <label for="contact_person_mobile_no">Contact Person Mobile No <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.contact_person_mobile_no">{{ error }}</span>
                                        </div>

                                    </div>
                                     <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.transport, 'has-error has-danger': errors.transport }" style="margin: 10px  0px  10px  0px">
                                            <select type="text" v-model="importNumApp.transport" id="transport" class="form-control" placeholder=""> 
                                                <option value="Importer">Importer</option>
                                                <option value="Exporter">Exporter</option>
                                                <option value="Both Importer-exporter">Both Importer-Exporter</option>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="transport">Transport <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.transport">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.type_of_goods, 'has-error has-danger': errors.type_of_goods }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.type_of_goods.$model" id="type_of_goods" class="form-control" placeholder="" :class="status($v.importNumApp.type_of_goods)">
                                            <span class="bar"></span>
                                            <label for="type_of_goods">Type Of Goods <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.type_of_goods">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.estimated_annual_value, 'has-error has-danger': errors.estimated_annual_value }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.estimated_annual_value.$model" id="estimated_annual_value" class="form-control" placeholder="" :class="status($v.importNumApp.estimated_annual_value)">
                                            <span class="bar"></span>
                                            <label for="estimated_annual_value">Estimated Annual Value<span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.estimated_annual_value">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.major_bus_description, 'has-error has-danger': errors.major_bus_description }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.major_bus_description.$model" id="major_bus_description" class="form-control" placeholder="" :class="status($v.importNumApp.major_bus_description)">
                                            <span class="bar"></span>
                                            <label for="major_bus_description">Major Business Description <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.major_bus_description">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.major_bus_product_services_1, 'has-error has-danger': errors.major_bus_product_services_1 }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.major_bus_product_services_1.$model" id="major_bus_product_services_1" class="form-control" placeholder="" :class="status($v.importNumApp.major_bus_product_services_1)">
                                            <span class="bar"></span>
                                            <label for="major_bus_product_services_1">Major Business Product Services 1 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.major_bus_product_services_1">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.revenue_from_product_services_1, 'has-error has-danger': errors.revenue_from_product_services_1 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.revenue_from_product_services_1.$model" id="revenue_from_product_services_1" class="form-control" placeholder="" :class="status($v.importNumApp.revenue_from_product_services_1)">
                                            <span class="bar"></span>
                                            <label for="revenue_from_product_services_1">Revenue From Product Services 1 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.revenue_from_product_services_1">{{ error }}</span>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.major_bus_product_services_2, 'has-error has-danger': errors.major_bus_product_services_2 }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.major_bus_product_services_2.$model" id="major_bus_product_services_2" class="form-control" placeholder="" :class="status($v.importNumApp.major_bus_product_services_2)">
                                            <span class="bar"></span>
                                            <label for="major_bus_product_services_2">Major Business Product Services 2 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.major_bus_product_services_2">{{ error }}</span>
                                        </div>
                                    </div>
                                 <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.revenue_from_product_services_2, 'has-error has-danger': errors.revenue_from_product_services_2 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.revenue_from_product_services_2.$model" id="revenue_from_product_services_2" class="form-control" placeholder="" :class="status($v.importNumApp.revenue_from_product_services_2)">
                                            <span class="bar"></span>
                                            <label for="revenue_from_product_services_2">Revenue From Product Services 2 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.revenue_from_product_services_2">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.major_bus_product_services_3, 'has-error has-danger': errors.major_bus_product_services_3 }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.major_bus_product_services_3.$model" id="major_bus_product_services_3" class="form-control" placeholder="" :class="status($v.importNumApp.major_bus_product_services_3)">
                                            <span class="bar"></span>
                                            <label for="major_bus_product_services_3">Major Busiess Product Services 3 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.major_bus_product_services_3">{{ error }}</span>
                                        </div>
                                    </div>

                                 <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.revenue_from_product_services_3, 'has-error has-danger': errors.revenue_from_product_services_3 }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.revenue_from_product_services_3.$model" id="revenue_from_product_services_3" class="form-control" placeholder="" :class="status($v.importNumApp.revenue_from_product_services_3)">
                                            <span class="bar"></span>
                                            <label for="revenue_from_product_services_3">Revenue From Product Services 3 <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.revenue_from_product_services_3">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.partner_type, 'has-error has-danger': errors.partner_type }" style="margin: 10px  0px  10px  0px">
                                            <select type="text" v-model="importNumApp.partner_type" id="partner_type" class="form-control" placeholder=""> 
                                                <option value="An owner">An owner</option>
                                                <option value="A corporate director">A corporate director</option>
                                                <option value="A trustee of an estate">A trustee of an estate</option>
                                                <option value="A partner of a partnership">A partner of a partnership</option>
                                                <option value="An officer of a non-profit organization">An officer of a non-profit organization</option>
                                                <option value="A third party requestor">A third party requestor</option>
                                            </select>
                                            <span class="bar"></span>
                                            <label for="partner_type">Partner Type<span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.partner_type">{{ error }}</span>
                                        </div>
                                    </div>

   
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.sign_title, 'has-error has-danger': errors.sign_title }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.sign_title.$model" id="sign_title" class="form-control" placeholder="" :class="status($v.importNumApp.sign_title)">
                                            <span class="bar"></span>
                                            <label for="sign_title">Sign Title <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.sign_title">{{ error }}</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    

                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.sign_first_name, 'has-error has-danger': errors.sign_first_name }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.sign_first_name.$model" id="sign_first_name" class="form-control" placeholder="" :class="status($v.importNumApp.sign_first_name)">
                                            <span class="bar"></span>
                                            <label for="sign_first_name">Sign First Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.sign_first_name">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.sign_last_name, 'has-error has-danger': errors.sign_last_name }"  style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.sign_last_name.$model" id="sign_last_name" class="form-control" placeholder="" :class="status($v.importNumApp.sign_last_name)">
                                            <span class="bar"></span>
                                            <label for="sign_last_name">Sign Last Name <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.sign_last_name">{{ error }}</span>
                                        </div>

                                    </div>
                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">    
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.sign_tel_no, 'has-error has-danger': errors.sign_tel_no }" style="margin: 10px  0px  10px  0px">
                                            <input type="text" v-model="$v.importNumApp.sign_tel_no.$model" id="sign_tel_no" class="form-control" placeholder="" :class="status($v.importNumApp.sign_tel_no)">
                                            <span class="bar"></span>
                                            <label for="sign_tel_no">Sign Tel No <span class="required">*</span></label>
                                            <span class="help-block" v-for="error in errors.sign_tel_no">{{ error }}</span>
                                        </div>
                                    </div>

                                    <div class="col-md-6" style="margin: 20px  0px  10px  0px">
                                        <div v-bind:class="{'form-group': true, 'focused':importNumApp.sign_signature, 'has-error has-danger': errors.sign_signature }"  style="margin: 10px  0px  10px  0px">
                                            <input type="hidden" v-model="importNumApp.sign_signature" id="sign_signature">
                                            <img height="35px" :src="imgUrl(3)">
                                            <span class="bar"></span>
                                            <label for="sign_signature" style="width: 100%">Sign Signature <span class="required">*</span>
                                                <span>    
                                                <button type="button" class="btn btn-secondary btn-outline icon-btn" v-on:click="drawSignature(3)" style="float:right"><i class="mdi mdi-pencil icon-custom-big"></i></button>
                                                </span>
                                            </label>
                                            <span class="help-block" v-for="error in errors.sign_signature">{{ error }}</span>
                                        </div>
                                    </div>
                                </div>

                                <div class="row" v-if="powerOfAtty.has_canadian_in != null">
                                    <div class="col-md-12" style="margin: 20px  0px  10px 0px">
                                        <button type="button" class="btn btn-outline btn-info" aria-hidden="true" v-on:click="formSubmit('user_poa')">SUBMIT</button>
                                        <!-- <br>
                                        <a href="#" v-on:click="pdfEmail('poa')"><b>poa</b></a> - <a href="#" v-on:click="pdfEmail('ina')"><b>ina</b></a> -->
                                    </div>
                                </div>                                

                            </div>
                        </form>
                    </div>
                </div>
            </div>            
        </div>
        
        <DrawPadModal></DrawPadModal> 

    </div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading, checkYN, poaValidateData} from '../helpers/helper';

import { required, minLength, between, alphaNum, helpers} from 'vuelidate/lib/validators';
const alpha = helpers.regex('alpha', /^[a-zA-Z]*$/);
const number = helpers.regex('number', /^\d*$/);
const phoneNumber = helpers.regex('phoneNumber', /^[+]*[(]{0,1}[0-9]{1,4}[)]{0,1}[-\s\./0-9]*$/);
const alphaNumSpace = helpers.regex('alphaNumSpace', /^[A-Za-z,\d\s]+$/);
const alphaSpace = helpers.regex('alphaSpace', /^[A-Za-z,\s]+$/);


import Datatable from './Datatable.vue';
import Pagination from './Pagination.vue';
import Papa from 'papaparse'
import Blob from 'blob'
import FileSaver from 'file-saver'
import swal from 'sweetalert';
var axios = require("axios");
import DatePicker from 'vue2-datepicker';
export default {
    props: ['value'],
    components: { datatable: Datatable, pagination: Pagination, DatePicker },
    data() {
        return {
            loggedId: '',
            loggedEmail: '',
            email: '',
            userInfo: {},
            info: {},
            usAddress: {},
            data: {},
            ina: {
                import_number: ''
            },    
            powerOfAtty: {
                business_name: '',
                address_1: '',
                city: '',
                province_state: '',
                country: '',
                full_name_of_corp_client: '',
                name_of_municipality: '',
                name_of_province_state: '',
                name_of_country: '',
                name_of_signing_authority_1: '',
                office_held_by_signing_authority_1: '',
                signature_1: '',
                name_of_signing_authority_2: '',
                office_held_by_signing_authority_2: '',
                signature_2: '',
                has_canadian_in: null 
            },
            importNumApp: {
                business_number: '',
                language: '',
                operating_trade: '',
                import_export_program_account_name: '',
                physical_business_location: '',
                physical_city: '',
                physical_province_state: '',
                physical_postal_zip_code: '',
                physical_country: '',
                contact_person_title: '',
                contact_person_first_name: '',
                contact_person_last_name: '',
                contact_person_work_tel_no: '',
                contact_person_ext: '',
                contact_person_work_fax_no: '',
                contact_person_mobile_no: '',
                transport: '',
                type_of_goods: '',
                estimated_annual_value: '',
                major_bus_description: '',
                major_bus_product_services_1: '',
                revenue_from_product_services_1: '',
                major_bus_product_services_2: '',
                revenue_from_product_services_2: '',
                major_bus_product_services_3: '',
                revenue_from_product_services_3: '',
                partner_type: '',
                sign_title: '',
                sign_first_name: '',
                sign_last_name: '',
                sign_tel_no: '',
                sign_signature: ''
            },
            password: '',
            current_password: '',
            password_confirmation: '',
            check_password: '',
            loaded: false,
            errors:{},
            countries:{},
            submitStatus: null
        }
    },
    validations: {
        ina: {
            import_number: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
        }, 
        powerOfAtty: {
            business_name: {
                minLength: minLength(2),
                alphaNumSpace
            },
            address_1: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            city: {
                minLength: minLength(2),
                alphaSpace
            },
            province_state: {
                minLength: minLength(2),
                alphaSpace
            },
            country: {
                minLength: minLength(2),
                alphaSpace
            },
            name_of_municipality: {
                required,
                minLength: minLength(2),
                alphaSpace
            },
            name_of_province_state: {
                required,
                minLength: minLength(2),
                alphaSpace
            },
            name_of_country: {
                required,
                minLength: minLength(2),
                alphaSpace
            },
            full_name_of_corp_client: {
                required,
                minLength: minLength(2),
                alphaSpace
            }, // alphaSpaceabove until here
            name_of_signing_authority_1: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            office_held_by_signing_authority_1: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            name_of_signing_authority_2: {
                minLength: minLength(2),
                alphaNumSpace
            },
            office_held_by_signing_authority_2: {
                minLength: minLength(2),
                alphaNumSpace
            }
        },
        importNumApp: {
            business_number: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            contact_person_title: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            contact_person_first_name: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            contact_person_last_name: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            physical_business_location: {
                minLength: minLength(2),
                alphaNumSpace
            },
            physical_city: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            physical_province_state: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            physical_postal_zip_code: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            type_of_goods: {
                minLength: minLength(2),
                alphaNumSpace
            },
            estimated_annual_value: {
                 minLength: minLength(2),
                alphaNumSpace
            },
            major_bus_description: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            major_bus_product_services_1: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            revenue_from_product_services_1: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            major_bus_product_services_2: {
                minLength: minLength(2),
                alphaNumSpace
            },
            revenue_from_product_services_2: {
                minLength: minLength(2),
                alphaNumSpace
            },
            major_bus_product_services_3: {
                minLength: minLength(2),
                alphaNumSpace
            },
            revenue_from_product_services_3: {
                minLength: minLength(2),
                alphaNumSpace
            },
            operating_trade: {
                minLength: minLength(2),
                alphaNumSpace
            },
            import_export_program_account_name: {
                minLength: minLength(2),
                alphaNumSpace
            },
            sign_title: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            sign_first_name: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            sign_last_name: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            },
            sign_tel_no: {
                required,
                minLength: minLength(2),
                alphaNumSpace
            }, 
            physical_postal_zip_code: {
                minLength: minLength(2),
                alphaNumSpace
            },                                 // alphaSpacenumeric above until here
            physical_country: {
                minLength: minLength(2),
                alphaSpace
            },            
            contact_person_work_tel_no: {
                required,
                phoneNumber
            },
            contact_person_ext: {
                required,
                phoneNumber
            },
            contact_person_work_fax_no: {
                phoneNumber
            },
            contact_person_mobile_no: {
                phoneNumber
            } // numeric above until here
        }        
     },     
    mounted () {
    },  
    watch:{

    }, 
    created() {
        this.init();
        this.$root.$on("show_sig",(d, whichSig)=>{
            if(whichSig == 1)
                this.powerOfAtty.signature_1 = d;   
            else if(whichSig == 2)
                this.powerOfAtty.signature_2 = d;   
            else if(whichSig == 3)
                this.importNumApp.sign_signature = d;   

             //$('.draw-pad-modal').modal('hide');
        })
    },
    methods: {
        ifChanged:function(){

            if(this.loggedEmail != this.email)
                return true;
            else
                return false;
        },
        init:function(){

            this.$http.get("/api/countries").then(res=>{
                this.countries = res.data;
            })

            this.$http.get("/profile/user/account_info").then(res=>{
                //console.log('>>>> '+JSON.stringify(res.data.userInfo));

                
                this.loggedId = res.data.loggedId;
                this.loggedEmail = res.data.loggedEmail;
                this.email = res.data.loggedEmail;
                
                this.usAddress = res.data.usAddress;

                let sender = res.data.userInfo;
                this.$set(this.userInfo,"sender_id",sender.id);
                this.$set(this.userInfo,"first_name",sender.first_name);
                this.$set(this.userInfo,"last_name",sender.last_name);
                this.$set(this.userInfo,"email",sender.email);
                this.$set(this.userInfo,"contact_no",sender.contact_no);
               
                let physical_address = res.data.userInfo.sender_physical_address;
                this.$set(this.userInfo,"address_id",physical_address.id);
                this.$set(this.userInfo,"address_1",physical_address.address_1);
                this.$set(this.userInfo,"address_2",physical_address.address_2);
                this.$set(this.userInfo,"city",physical_address.city);
                this.$set(this.userInfo,"postal",physical_address.postal);
                this.$set(this.userInfo,"province",physical_address.province);
                this.$set(this.userInfo,"country",physical_address.country);

                let powerOfAtty_ = res.data.userInfo.sender_power_of_atty;
                    
                //console.log('>>>> '+JSON.stringify(powerOfAtty_));
                //console.log( 'Index : ' + names_arr.indexOf('sumit') );

                this.powerOfAtty.sender_id =  (powerOfAtty_ != null)? powerOfAtty_.sender_id:sender.id;
                this.ina.import_number = (powerOfAtty_ != null)? powerOfAtty_.import_number:'';

                this.powerOfAtty.id =  (powerOfAtty_ != null)? powerOfAtty_.id:'';
                this.powerOfAtty.business_name = (powerOfAtty_ != null)? powerOfAtty_.business_name:'';
                this.powerOfAtty.address_1 = (powerOfAtty_ != null)? powerOfAtty_.address_1:'';
                this.powerOfAtty.city = (powerOfAtty_ != null)? powerOfAtty_.city:'';
                this.powerOfAtty.province_state = (powerOfAtty_ != null)? powerOfAtty_.province_state:'';
                this.powerOfAtty.country = (powerOfAtty_ != null)? powerOfAtty_.country:'';
                this.powerOfAtty.full_name_of_corp_client = (powerOfAtty_ != null)? powerOfAtty_.full_name_of_corp_client:'';
                this.powerOfAtty.name_of_municipality = (powerOfAtty_ != null)? powerOfAtty_.name_of_municipality:'';
                this.powerOfAtty.name_of_province_state = (powerOfAtty_ != null)? powerOfAtty_.name_of_province_state:'';
                this.powerOfAtty.name_of_country = (powerOfAtty_ != null)? powerOfAtty_.name_of_country:'';
                this.powerOfAtty.name_of_signing_authority_1 = (powerOfAtty_ != null)? powerOfAtty_.name_of_signing_authority_1:'';
                this.powerOfAtty.office_held_by_signing_authority_1 = (powerOfAtty_ != null)? powerOfAtty_.office_held_by_signing_authority_1:'';
                this.powerOfAtty.signature_1 = (powerOfAtty_ != null)? powerOfAtty_.signature_1:'';
                this.powerOfAtty.name_of_signing_authority_2 = (powerOfAtty_ != null)? powerOfAtty_.name_of_signing_authority_2:'';
                this.powerOfAtty.office_held_by_signing_authority_2 = (powerOfAtty_ != null)? powerOfAtty_.office_held_by_signing_authority_2:'';
                this.powerOfAtty.signature_2 = (powerOfAtty_ != null)? powerOfAtty_.signature_2:'';

                //////////
                this.importNumApp.business_number = (powerOfAtty_ != null)? powerOfAtty_.business_number:'';
                this.importNumApp.address_2 = (powerOfAtty_ != null)? powerOfAtty_.address_2:'';
                this.importNumApp.language = (powerOfAtty_ != null)? powerOfAtty_.language:'';
                this.importNumApp.operating_trade = (powerOfAtty_ != null)? powerOfAtty_.operating_trade:'';
                this.importNumApp.import_export_program_account_name = (powerOfAtty_ != null)? powerOfAtty_.import_export_program_account_name:'';
                this.importNumApp.physical_business_location = (powerOfAtty_ != null)? powerOfAtty_.physical_business_location:'';
                this.importNumApp.physical_city = (powerOfAtty_ != null)? powerOfAtty_.physical_city:'';
                this.importNumApp.physical_province_state = (powerOfAtty_ != null)? powerOfAtty_.physical_province_state:'';
                this.importNumApp.physical_postal_zip_code = (powerOfAtty_ != null)? powerOfAtty_.physical_postal_zip_code:'';
                this.importNumApp.physical_country = (powerOfAtty_ != null)? powerOfAtty_.physical_country:'';
                this.importNumApp.contact_person_title = (powerOfAtty_ != null)? powerOfAtty_.contact_person_title:'';
                this.importNumApp.contact_person_first_name = (powerOfAtty_ != null)? powerOfAtty_.contact_person_first_name:'';
                this.importNumApp.contact_person_last_name = (powerOfAtty_ != null)? powerOfAtty_.contact_person_last_name:'';
                this.importNumApp.contact_person_work_tel_no = (powerOfAtty_ != null)? powerOfAtty_.contact_person_work_tel_no:'';
                this.importNumApp.contact_person_ext = (powerOfAtty_ != null)? powerOfAtty_.contact_person_ext:'';
                this.importNumApp.contact_person_work_fax_no = (powerOfAtty_ != null)? powerOfAtty_.contact_person_work_fax_no:'';
                this.importNumApp.contact_person_mobile_no = (powerOfAtty_ != null)? powerOfAtty_.contact_person_mobile_no:'';
                this.importNumApp.transport = (powerOfAtty_ != null)? powerOfAtty_.transport:'';
                this.importNumApp.type_of_goods = (powerOfAtty_ != null)? powerOfAtty_.type_of_goods:'';
                this.importNumApp.estimated_annual_value = (powerOfAtty_ != null)? powerOfAtty_.estimated_annual_value:'';
                this.importNumApp.major_bus_description = (powerOfAtty_ != null)? powerOfAtty_.major_bus_description:'';
                this.importNumApp.major_bus_product_services_1 = (powerOfAtty_ != null)? powerOfAtty_.major_bus_product_services_1:'';
                this.importNumApp.revenue_from_product_services_1 = (powerOfAtty_ != null)? powerOfAtty_.revenue_from_product_services_1:'';
                this.importNumApp.major_bus_product_services_2 = (powerOfAtty_ != null)? powerOfAtty_.major_bus_product_services_2:'';
                this.importNumApp.revenue_from_product_services_2 = (powerOfAtty_ != null)? powerOfAtty_.revenue_from_product_services_2:'';
                this.importNumApp.major_bus_product_services_3 = (powerOfAtty_ != null)? powerOfAtty_.major_bus_product_services_3:'';
                this.importNumApp.revenue_from_product_services_3 = (powerOfAtty_ != null)? powerOfAtty_.revenue_from_product_services_3:'';
                this.importNumApp.partner_type = (powerOfAtty_ != null)? powerOfAtty_.partner_type:'';
                this.importNumApp.sign_title = (powerOfAtty_ != null)? powerOfAtty_.sign_title:'';
                this.importNumApp.sign_first_name = (powerOfAtty_ != null)? powerOfAtty_.sign_first_name:'';
                this.importNumApp.sign_last_name = (powerOfAtty_ != null)? powerOfAtty_.sign_last_name:'';
                this.importNumApp.sign_tel_no = (powerOfAtty_ != null)? powerOfAtty_.sign_tel_no:'';
                this.importNumApp.sign_signature = (powerOfAtty_ != null)? powerOfAtty_.sign_signature:'';
  
                this.loaded = true;
            })

        },

        resetPw: function(e) {

        },
        getAddressData: function (addressData, placeResultData) {

            if( addressData.country != '' )
                this.userInfo.country = addressData.country;

            if( addressData.locality != '' )
                this.userInfo.city = addressData.locality;

            if( addressData.administrative_area_level_1 != '' )
                this.userInfo.province = addressData.administrative_area_level_1;

            if( addressData.postal_code != '')
                this.userInfo.postal = addressData.postal_code;

            this.userInfo.address_1 = (addressData.street_number != '' ? addressData.street_number + ' ' : '') + addressData.route;

            this.$forceUpdate();

        }, 
        getAddressDataPoA: function (addressData, placeResultData) {

            if( addressData.country != '' )
                this.powerOfAtty.country = addressData.country;

            if( addressData.locality != '' )
                this.powerOfAtty.city = addressData.locality;

            if( addressData.administrative_area_level_1 != '' )
                this.powerOfAtty.province_state = addressData.administrative_area_level_1;

            if( addressData.postal_code != '')
                this.powerOfAtty.postal = addressData.postal_code;

            this.$forceUpdate();

        },          
        imgUrl:function(whichSig){

            let blank = 'data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAZAEMDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9/KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAP/2Q==';

            if(whichSig == 1)
                return (this.powerOfAtty.signature_1)? this.powerOfAtty.signature_1:blank;   
            else if(whichSig == 2)
                return (this.powerOfAtty.signature_2)? this.powerOfAtty.signature_2:blank; 
            else if(whichSig == 3)
                return (this.importNumApp.sign_signature)? this.importNumApp.sign_signature:blank;         
        },    
        formSubmit:function(d){

            if(d=="reset")
            {
                
                showLoading();
                let pwChange = document.getElementById('pwChange');
                let formData = new FormData(pwChange);
                
                formData.append('id', this.loggedId); 
                formData.append('email', this.loggedEmail); 
                
                this.$http.post('/profile/password_reset', formData).then(response => {
                    if (response.data.status == true) {
                        hideLoading();
                        this.success = response.data.info;
                        
                        this.info['userInfo'] = response.data.data;
                        this.$http.post("/senders/pw_reset_email",this.info).then(res=>{
                        
                        })

                        showSuccessMsg(this.success);
                    }else{

                        hideLoading();
                        this.errors = response.data.message;
                        showErrorMsg(this.errors);
                    }
                }).catch(error => {
                    hideLoading();

                    if(error.body.errors.password != null)
                        showErrorMsg(error.body.errors.password);                    
                    
                    if(error.body.errors.current_password != null)
                        showErrorMsg(error.body.errors.current_password);                    
  
                });

            }
            else if(d=="update_email")
            {
                
                showLoading();
                let emailChange = document.getElementById('emailChange');
                let formData = new FormData(emailChange);
                
                formData.append('id', this.loggedId); 
                formData.append('email', this.email); 
                formData.append('loggedEmail', this.loggedEmail); 
                
                this.$http.post('/profile/change_email', formData).then(response => {
                    if (response.data.status == true) {
                        hideLoading();
                        this.success = response.data.info;
                        
                        this.info['userInfo'] = response.data.data;

                        this.$http.post("/senders/em_change_email",this.info).then(res=>{
                        })

                        showSuccessMsg(this.success);
                    }else{

                        hideLoading();
                        this.errors = response.data.message;
                        showErrorMsg(this.errors);
                    }
                }).catch(error => {
                    hideLoading();

                    if(error.body.errors.password != null)
                        showErrorMsg(error.body.errors.password);                    
                    
                    if(error.body.errors.current_password != null)
                        showErrorMsg(error.body.errors.current_password);                    
  
                    if(error.body.errors.email != null)
                        showErrorMsg(error.body.errors.email);  

                });

            }
            else if(d=="user_info")
            {
                //this.processing = true;
                this.$http.post("/senders/updateUserAccountInfo",this.userInfo).then(response=>{
                    //console.log(response);
                    //this.processing = false;
                    showSuccessMsg("Information saved.");
                }, response=>{
                    this.errors = response.data.errors;
                    //this.processing = false;

                   showErrorMsg(response.data.message)
                   
                }).catch((err) => {
                    handleErrorResponse(err.status);
                    //this.processing = false;
                    if (err.status == 422) {
                        this.errors = err.data;
                    }
                });
            }
            else if(d=="user_poa")
            {

                //console.log('>>    '+this.powerOfAtty.name_of_signing_authority_2);
                this.$v.$touch()
                if (this.$v.powerOfAtty.$invalid && this.powerOfAtty.has_canadian_in == 1) 
                {
                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please fill up the form correctly.")
                }
                else if (this.$v.ina.$invalid && this.powerOfAtty.has_canadian_in == 1) 
                {
                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please fill up the form correctly.")
                }
                else if (this.$v.importNumApp.$invalid && this.powerOfAtty.has_canadian_in == 0) 
                {
                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please fill up the form correctly.")
                }
                else if(!this.powerOfAtty.signature_1)
                {
                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please provide your PoA 1st signature.")
                }
                else if(this.powerOfAtty.name_of_signing_authority_2 && this.powerOfAtty.office_held_by_signing_authority_2 && !this.powerOfAtty.signature_2)
                {
                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please provide your PoA 2nd signature.")
                }
                else if (this.powerOfAtty.has_canadian_in == 0 && !this.importNumApp.sign_signature) 
                {

                    this.submitStatus = 'ERROR'
                    showErrorMsg("Please provide your INA signature.")
                }                
                else 
                {
                    //this.processing = true;
                    this.data['poa'] = this.powerOfAtty;
                    this.data['ina'] = this.importNumApp;
                    this.data['import_number'] = this.ina;
                    this.$http.post("/senders/updateUserAccountPOA",this.data).then(response=>{
                        //console.log(response);
                        //this.processing = false;
                        if (this.powerOfAtty.has_canadian_in == 1) 
                            this.pdfEmail('poa');
                        else
                            this.pdfEmail('ina');                                    
                        showSuccessMsg("Document saved/sent.");

                    }, response=>{
                        this.errors = response.data.errors;
                        //this.processing = false;
                       showErrorMsg(response.data.message)
                    }).catch((err) => {
                        handleErrorResponse(err.status);
                        //this.processing = false;
                        if (err.status == 422) {
                            this.errors = err.data;
                        }
                    });
                } 
            }
          
        },
        poaValidate(c, v) {
            var n = poaValidateData(c, v);
            return n;
        },
        status(validation) {
            return {
                error: validation.$error,
                dirty: validation.$dirty
              }
        },
        
        handleDownload:function(t){
            axios({
                url: '/forms/'+t+'.pdf',
                method: 'GET',
                responseType: 'blob', 
            }).then((response) => {
                const url = window.URL.createObjectURL(new Blob([response.data]));
                const link = document.createElement('a');
                link.href = url;
                link.setAttribute('download', t+'.pdf');
                document.body.appendChild(link);
                link.click();
            });
        }, 
        pdfEmail:function(data){
            if(data == "poa"){
                this.$http.post("/senders/request",this.model).then(res=>{
                    this.info['userInfo'] = res.data.data;
                    this.$http.post("/senders/poa",this.info).then(res=>{
                    })
                })
            }else{
                 
                 this.$http.post("/senders/request",this.model).then(res=>{
                    this.info['userInfo'] = res.data.data;
                    this.$http.post("/senders/ina",this.info).then(res=>{
                    })
                })

                this.$http.post("/senders/request",this.model).then(res=>{
                    this.info['userInfo'] = res.data.data;
                    this.$http.post("/senders/poa",this.info).then(res=>{
                    })
                    this.$http.post("/senders/ina",this.info).then(res=>{
                    })
                })

            }
        },
        drawSignature:function(whichSig){
            this.$root.$emit("draw_pad",whichSig);
            var el = $('.draw-pad-modal')
            el.modal({backdrop: 'static', keyboard: false});  
        }, 
    }
};

// https://github.com/szimek/signature_pad

</script>
<style scoped>
.card-block {
    padding: .1rem;
}
.page-wrapper .card .card-block {
    margin-top: -12px;
}
.form-control {
    min-height: 25px;
    padding: .3rem .3rem;
    font-size: .8rem;    
}
select.form-control:not([size]):not([multiple]) {
    height: calc(1.6rem + 2px);
}
.custom-control {
    padding-left: .2rem;
    margin-right: .1rem;
    min-height: 1.2rem
}
[type="checkbox"] + label {
    height: 10px !important;
}

.fixed-table-container th, .fixed-table-container td {
    padding: 12px 3px 12px 7px;
    border-right: 0px solid #ccc; 
    border-bottom: 0px solid #ccc; 
}
.mx-datepicker {
  max-width: 150px;
  width: auto;
}
.has-search .form-control {
    padding-left: 2.375rem;
}

.has-search .form-control-feedback {
    position: absolute;
    z-index: 2;
    display: block;
    width: 2.375rem;
    height: 2.375rem;
    line-height: 1.8rem;
    text-align: center;
    pointer-events: none;
    color: #aaa;
    font-size: 19px;
}
.form-group {
    margin-bottom: 5px;
}

.btn-outline-active {
    color: #de00c9;
}
.btn-secondary:hover {
    color: #de00c9;
}
.page-wrapper .card .card-title {
    padding: 5px 3px 1px 3px;
}
.fixed-table-container td:first-child {
    background: #fff0; 
}
.summary-label-overall-total h5 {
    font-style: italic;
}
.summary-info-overall-total {
    border-bottom: 1pt solid black; 
    padding:0px !important;
    font-style: italic;
    color: #ffaa00;
    font-weight: 600;
}
.alert {
    padding: .5rem 1.25rem .0rem 1.25rem;
    margin-bottom: 0rem;
    border: 1px solid transparent;
    border-radius: .25rem;
}
.card-block {
    min-height: 334px;
}
.bg-invalid {
    background-color: #fffe8363 !important;
    color: #ff0202;
    border-bottom-color: #ff0202;
    font-weight: 400;
}
.dirty {
    border-color: #5A5;
    background: #EFE;
}

.dirty:focus {
    outline-color: #8E8;
}

.error {
    border-color: red;
    background: #fffe8363;
    color: red;
}

.error:focus {
    outline-color: #F99;
}
</style>
