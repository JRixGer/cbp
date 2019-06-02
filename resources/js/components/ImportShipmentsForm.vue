<template>
    <div class="col-md-6 content-div">
        <div class="card text-center download-div">
            <h4 class="card-title center-title title-color">DOWNLOAD FILE</h4><br>
            <div class="card-block">
   
                <div class="card-body">
                    <form class="floating-labels" v-on:submit.prevent="" role="form" method="POST">

                        <div class="form-group">
                            <h5><strong>What Do You Require?</strong></h5>
                            
                            <input type="radio" name="pddo-download" id="pd-download" @click="handlePDDownload" checked="checked">
                            <label for="pd-download" style="margin-right:20px">Postage & Delivery</label>

                            <input type="radio" name="pddo-download" id="do-download" @click="handleDODownload" >
                            <label for="do-download">Delivery Only</label>                              


                        </div>

                        <button class="btn btn-info margin-button" :disabled="isPD == true? false : true" @click="handleDownload('recipient_postage_and_delivery')">DOWNLOAD FILE:<br>POSTAGE & DELIVERY</button>
                        <button class="btn btn-info margin-button" :disabled="isDO == false? true : false" @click="handleDownload('recipient_delivery_only')">DOWNLOAD FILE:<br>DELIVERY</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="card text-center import-div">
            <h4 class="card-title center-title title-color"><span @click='goBackToCSV'><i class="back-csv mdi mdi-arrow-left-bold"></i></span>IMPORT SHIPMENTS</h4>
            <div class="card-block">
                <div class="card-body">

                    <div style="margin: 18px 0px 2px 0px;">
                        
                        <div id="notify-error-id" role="alert" class="alert alert-danger error-mess" style="text-align:center" v-on:click="showHideErrors()">
                            
                        </div>

                    </div>

                    <div class="csvfiledrop">
                        <form class="floating-labels" v-on:submit.prevent="" role="form" method="POST">
                            <span class="btn-file"
                                id="fileInput"
                                type="file"
                                @change="parseCSV">
                                <input type="file" class="dropify">
                            </span>
                              
                            <div class="parse">
                                <input type="text" name="" 
                                    class="entry-result"
                                    v-model='doc'
                                    hidden>
                            </div>
                        </form>
                        <div>
                            <p class="on-parsing">please wait, parsing...</p>
                        </div>
                    </div>

                    <div id="fixed-table-csv" class="fixed-table parsed-csv fixed-table-container h-w horiz-scroll">

                        <table id="parsed-csv-tbl" class="table table-bordered table-striped table-hover" v-if="csv.length > 0">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th>#</th>
                                <th>FirstName</th>
                                <th>LastName</th> 
                                <th>BusinessName</th> 
                                <th>AddressLine1</th> 
                                <th>AddressLine2</th> 
                                <th>City</th> 
                                <th>ProvState</th> 
                                <th>IntlProvState</th> 
                                <th>PostalZipCode</th> 
                                <th>Country</th>  

                                <th v-if="importInfo['fileName']=='PD'">Length</th>  
                                <th v-if="importInfo['fileName']=='PD'">Width</th> 
                                <th v-if="importInfo['fileName']=='PD'">Height</th>  
                                <!-- <th v-if="importInfo['fileName']=='PD'" style="color:#ccc">SizeUnit</th>   -->
                                <th>Weight</th>
                                <!-- <th style="color:#ccc">WeightUnit</th>   -->
                                <th>isSignatureReq</th> 

                                <th v-if="importInfo['fileName']=='PD'">Email</th>  
                                <th v-if="importInfo['fileName']=='PD'">Phone</th>  

                                <th>LetterMail</th>  


                                <th>Item1</th> 
                                <th>Qty1</th> 
                                <th>ItemValue1</th> 
                                <th>OriginCountry1</th>
                                <th>Item2</th> 
                                <th>Qty2</th> 
                                <th>ItemValue2</th> 
                                <th>OriginCountry2</th>
                                <th>Item3</th> 
                                <th>Qty3</th> 
                                <th>ItemValue3</th> 
                                <th>OriginCountry3</th>
                                <th>Item4</th> 
                                <th>Qty4</th> 
                                <th>ItemValue4</th> 
                                <th>OriginCountry4</th>
                                <th>Item5</th> 
                                <th>Qty5</th> 
                                <th>ItemValue5</th> 
                                <th>OriginCountry5</th>
                                <th>Item6</th> 
                                <th>Qty6</th> 
                                <th>ItemValue6</th> 
                                <th>OriginCountry6</th>
                                <th>Item7</th> 
                                <th>Qty7</th> 
                                <th>ItemValue7</th> 
                                <th>OriginCountry7</th>
                                <th>Item8</th> 
                                <th>Qty8</th> 
                                <th>ItemValue8</th> 
                                <th>OriginCountry8</th>
                                <th>Item9</th> 
                                <th>Qty9</th> 
                                <th>ItemValue9</th> 
                                <th>OriginCountry9</th>
                                <th>Item10</th> 
                                <th>Qty10</th> 
                                <th>ItemValue10</th> 
                                <th>OriginCountry10</th>
                                <th>Item11</th> 
                                <th>Qty11</th> 
                                <th>ItemValue11</th> 
                                <th>OriginCountry11</th>
                                <th>Item12</th> 
                                <th>Qty12</th> 
                                <th>ItemValue12</th> 
                                <th>OriginCountry12</th>
                                <th>Item13</th> 
                                <th>Qty13</th> 
                                <th>ItemValue13</th> 
                                <th>OriginCountry13</th>
                                <th>Item14</th> 
                                <th>Qty14</th> 
                                <th>ItemValue14</th> 
                                <th>OriginCountry14</th>
                                <th>Item15</th> 
                                <th>Qty15</th> 
                                <th>ItemValue15</th> 
                                <th>OriginCountry15</th>
                                <th>Item16</th> 
                                <th>Qty16</th> 
                                <th>ItemValue16</th> 
                                <th>OriginCountry16</th>
                                <th>Item17</th> 
                                <th>Qty17</th> 
                                <th>ItemValue17</th> 
                                <th>OriginCountry17</th>
                                <th>Item18</th> 
                                <th>Qty18</th> 
                                <th>ItemValue18</th> 
                                <th>OriginCountry18</th>
                                <th>Item19</th> 
                                <th>Qty19</th> 
                                <th>ItemValue19</th> 
                                <th>OriginCountry19</th>
                                <th>Item20</th> 
                                <th>Qty20</th> 
                                <th>ItemValue20</th> 
                                <th>OriginCountry20</th>

                            </tr>

                            </thead>
                            <tbody>
                            <tr v-for="(data, index) in csv.slice(0, (Object.keys(csv).length)-0)">
                                <td v-on:click="csvEdit(data)" style="padding: 6px;"><i class="mdi mdi-pencil icon-csv"></i></td>
                                <td v-on:click="csvEdit(data)">{{ index + 1 }}</td>
                                <td v-on:click="csvFieldEdit(data,'FirstName')" v-bind:class="{'bg-invalid': validate('FirstName',data.FirstName)}">{{ data.FirstName }}</td>
                                <td v-on:click="csvFieldEdit(data,'LastName')" v-bind:class="{'bg-invalid': validate('LastName',data.LastName)}">{{ data.LastName }}</td>
                                <td v-on:click="csvFieldEdit(data,'BusinessName')" v-bind:class="{'bg-invalid': validate('BusinessName',data.BusinessName)}">{{ data.BusinessName }}</td>

                                <td :id="'addressLine1_'+setId(index)" v-on:click="csvFieldEdit(data,'AddressLine1')" v-bind:class="{'bg-invalid': validate('AddressLine1',data.AddressLine1)}">{{ data.AddressLine1 }}</td>
                                <td :id="'addressLine2_'+setId(index)" v-on:click="csvFieldEdit(data,'AddressLine2')" v-bind:class="{'bg-invalid': validate('AddressLine2',data.AddressLine2)}">{{ data.AddressLine2}}</td>
                                <td :id="'city_'+setId(index)" v-on:click="csvFieldEdit(data,'City')" v-bind:class="{'bg-invalid': validate('City',data.City)}">{{ data.City }}</td>
                                <td :id="'provState_'+setId(index)" v-on:click="csvFieldEdit(data,'ProvState')" v-bind:class="{'bg-invalid': validate('ProvState',data.ProvState)}">{{ data.ProvState }}</td>
                                <td :id="'intlProvState_'+setId(index)" v-on:click="csvFieldEdit(data,'IntlProvState')" v-bind:class="{'bg-invalid': validate('IntlProvState',data.IntlProvState)}">{{ data.IntlProvState }}</td>
                                <td :id="'postalZipCode_'+setId(index)" v-on:click="csvFieldEdit(data,'PostalZipCode')" v-bind:class="{'bg-invalid': validate('PostalZipCode',data.PostalZipCode)}">{{ data.PostalZipCode }}</td>
                                <td :id="'country_'+setId(index)" v-on:click="csvFieldEdit(data,'Country')" v-bind:class="{'bg-invalid': validate('Country',data.Country)}">{{ data.Country }}</td> 

                                <td v-if="importInfo['fileName']=='PD'" v-on:click="csvFieldEdit(data,'Length')" v-bind:class="{'bg-invalid': validate('Length',data.Length)}">{{ rndOff(data.Length,1) }}</td> 
                                <td v-if="importInfo['fileName']=='PD'" v-on:click="csvFieldEdit(data,'Width')" v-bind:class="{'bg-invalid': validate('Width',data.Width)}">{{ rndOff(data.Width,1) }}</td>
                                <td v-if="importInfo['fileName']=='PD'" v-on:click="csvFieldEdit(data,'Height')" v-bind:class="{'bg-invalid': validate('Height',data.Height)}">{{ rndOff(data.Height,1) }}</td> 
                                
                                <!-- <td v-if="importInfo['fileName']=='PD'" style="color:#ccc">IN</td>  -->
                                <td v-on:click="csvFieldEdit(data,'Weight')" v-bind:class="{'bg-invalid': validate('Weight',data.Weight)}">{{ rndOff(data.Weight,1) }}</td> 
                                <!-- <td style="color:#ccc">LBS</td>  -->

                                <td v-on:click="csvFieldEdit(data,'isSignatureReq')" v-bind:class="{'bg-invalid': validate('isSignatureReq',data.isSignatureReq)}">{{ data.isSignatureReq }}</td>

                                <td v-if="importInfo['fileName']=='PD'" v-on:click="csvFieldEdit(data,'Email')" v-bind:class="{'bg-invalid': validate('Email',data.Email)}">{{ data.Email }}</td> 
                                <td v-if="importInfo['fileName']=='PD'" v-on:click="csvFieldEdit(data,'Phone')" v-bind:class="{'bg-invalid': validate('Phone',data.Phone, data.Country)}">{{ data.Phone }}</td> 
                                <td v-on:click="csvFieldEdit(data,'LetterMail')" v-bind:class="{'bg-invalid': validate('LetterMail',data.LetterMail)}">{{ data.LetterMail }}</td> 


                                <td v-on:click="csvFieldEdit(data,'Item1')" v-bind:class="{'bg-invalid': validate('Item1',data.Item1, data.Country)}">{{ data.Item1 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty1')" v-bind:class="{'bg-invalid': validate('Qty1',data.Qty1, data.Country)}">{{ data.Qty1 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue1')" v-bind:class="{'bg-invalid': validate('ItemValue1',data.ItemValue1, data.Country)}">{{ data.ItemValue1 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry1')" v-bind:class="{'bg-invalid': validate('OriginCountry1',data.OriginCountry1, data.Country)}">{{ data.OriginCountry1}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item2')" v-bind:class="{'bg-invalid': validate('Item2',data.Item2)}">{{ data.Item2 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty2')" v-bind:class="{'bg-invalid': validate('Qty2',data.Qty2)}">{{ data.Qty2 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue2')" v-bind:class="{'bg-invalid': validate('ItemValue2',data.ItemValue2)}">{{ data.ItemValue2 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry2')" v-bind:class="{'bg-invalid': validate('OriginCountry2',data.OriginCountry2)}">{{ data.OriginCountry2}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item3')" v-bind:class="{'bg-invalid': validate('Item3',data.Item3)}">{{ data.Item3 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty3')" v-bind:class="{'bg-invalid': validate('Qty3',data.Qty3)}">{{ data.Qty3 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue3')" v-bind:class="{'bg-invalid': validate('ItemValue3',data.ItemValue3)}">{{ data.ItemValue3 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry3')" v-bind:class="{'bg-invalid': validate('OriginCountry3',data.OriginCountry3)}">{{ data.OriginCountry3}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item4')" v-bind:class="{'bg-invalid': validate('Item4',data.Item4)}">{{ data.Item4 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty4')" v-bind:class="{'bg-invalid': validate('Qty4',data.Qty4)}">{{ data.Qty4 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue4')" v-bind:class="{'bg-invalid': validate('ItemValue4',data.ItemValue4)}">{{ data.ItemValue4 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry4')" v-bind:class="{'bg-invalid': validate('OriginCountry4',data.OriginCountry4)}">{{ data.OriginCountry4}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item5')" v-bind:class="{'bg-invalid': validate('Item5',data.Item5)}">{{ data.Item5 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty5')" v-bind:class="{'bg-invalid': validate('Qty5',data.Qty5)}">{{ data.Qty5 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue5')" v-bind:class="{'bg-invalid': validate('ItemValue5',data.ItemValue5)}">{{ data.ItemValue5 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry5')" v-bind:class="{'bg-invalid': validate('OriginCountry5',data.OriginCountry5)}">{{ data.OriginCountry5}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item6')" v-bind:class="{'bg-invalid': validate('Item6',data.Item6)}">{{ data.Item6 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty6')" v-bind:class="{'bg-invalid': validate('Qty6',data.Qty6)}">{{ data.Qty6 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue6')" v-bind:class="{'bg-invalid': validate('ItemValue6',data.ItemValue6)}">{{ data.ItemValue6 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry6')" v-bind:class="{'bg-invalid': validate('OriginCountry6',data.OriginCountry6)}">{{ data.OriginCountry6}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item7')" v-bind:class="{'bg-invalid': validate('Item7',data.Item7)}">{{ data.Item7 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty7')" v-bind:class="{'bg-invalid': validate('Qty7',data.Qty7)}">{{ data.Qty7 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue7')" v-bind:class="{'bg-invalid': validate('ItemValue7',data.ItemValue7)}">{{ data.ItemValue7 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry7')" v-bind:class="{'bg-invalid': validate('OriginCountry7',data.OriginCountry7)}">{{ data.OriginCountry7}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item8')" v-bind:class="{'bg-invalid': validate('Item8',data.Item8)}">{{ data.Item8 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty8')" v-bind:class="{'bg-invalid': validate('Qty8',data.Qty8)}">{{ data.Qty8 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue8')" v-bind:class="{'bg-invalid': validate('ItemValue8',data.ItemValue8)}">{{ data.ItemValue8 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry8')" v-bind:class="{'bg-invalid': validate('OriginCountry8',data.OriginCountry8)}">{{ data.OriginCountry8}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item9')" v-bind:class="{'bg-invalid': validate('Item9',data.Item9)}">{{ data.Item9 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty9')" v-bind:class="{'bg-invalid': validate('Qty9',data.Qty9)}">{{ data.Qty9 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue9')" v-bind:class="{'bg-invalid': validate('ItemValue9',data.ItemValue9)}">{{ data.ItemValue9 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry9')" v-bind:class="{'bg-invalid': validate('OriginCountry9',data.OriginCountry9)}">{{ data.OriginCountry9}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item10')" v-bind:class="{'bg-invalid': validate('Item10',data.Item10)}">{{ data.Item10 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty10')" v-bind:class="{'bg-invalid': validate('Qty10',data.Qty10)}">{{ data.Qty10 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue10')" v-bind:class="{'bg-invalid': validate('ItemValue10',data.ItemValue10)}">{{ data.ItemValue10 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry10')" v-bind:class="{'bg-invalid': validate('OriginCountry10',data.OriginCountry10)}">{{ data.OriginCountry10}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item11')" v-bind:class="{'bg-invalid': validate('Item11',data.Item11)}">{{ data.Item11 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty11')" v-bind:class="{'bg-invalid': validate('Qty11',data.Qty11)}">{{ data.Qty11 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue11')" v-bind:class="{'bg-invalid': validate('ItemValue11',data.ItemValue11)}">{{ data.ItemValue11 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry11')" v-bind:class="{'bg-invalid': validate('OriginCountry11',data.OriginCountry11)}">{{ data.OriginCountry11}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item12')" v-bind:class="{'bg-invalid': validate('Item12',data.Item12)}">{{ data.Item12 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty12')" v-bind:class="{'bg-invalid': validate('Qty12',data.Qty12)}">{{ data.Qty12 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue12')" v-bind:class="{'bg-invalid': validate('ItemValue12',data.ItemValue12)}">{{ data.ItemValue12 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry12')" v-bind:class="{'bg-invalid': validate('OriginCountry12',data.OriginCountry12)}">{{ data.OriginCountry12}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item13')" v-bind:class="{'bg-invalid': validate('Item13',data.Item13)}">{{ data.Item13 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty13')" v-bind:class="{'bg-invalid': validate('Qty13',data.Qty13)}">{{ data.Qty13 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue13')" v-bind:class="{'bg-invalid': validate('ItemValue13',data.ItemValue13)}">{{ data.ItemValue13 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry13')" v-bind:class="{'bg-invalid': validate('OriginCountry13',data.OriginCountry13)}">{{ data.OriginCountry13}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item14')" v-bind:class="{'bg-invalid': validate('Item14',data.Item14)}">{{ data.Item14 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty14')" v-bind:class="{'bg-invalid': validate('Qty14',data.Qty14)}">{{ data.Qty14 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue14')" v-bind:class="{'bg-invalid': validate('ItemValue14',data.ItemValue14)}">{{ data.ItemValue14 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry14')" v-bind:class="{'bg-invalid': validate('OriginCountry14',data.OriginCountry14)}">{{ data.OriginCountry14}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item15')" v-bind:class="{'bg-invalid': validate('Item15',data.Item15)}">{{ data.Item15 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty15')" v-bind:class="{'bg-invalid': validate('Qty15',data.Qty15)}">{{ data.Qty15 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue15')" v-bind:class="{'bg-invalid': validate('ItemValue15',data.ItemValue15)}">{{ data.ItemValue15 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry15')" v-bind:class="{'bg-invalid': validate('OriginCountry15',data.OriginCountry15)}">{{ data.OriginCountry15}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item16')" v-bind:class="{'bg-invalid': validate('Item16',data.Item16)}">{{ data.Item16 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty16')" v-bind:class="{'bg-invalid': validate('Qty16',data.Qty16)}">{{ data.Qty16 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue16')" v-bind:class="{'bg-invalid': validate('ItemValue16',data.ItemValue16)}">{{ data.ItemValue16 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry16')" v-bind:class="{'bg-invalid': validate('OriginCountry16',data.OriginCountry16)}">{{ data.OriginCountry16}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item17')" v-bind:class="{'bg-invalid': validate('Item17',data.Item17)}">{{ data.Item17 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty17')" v-bind:class="{'bg-invalid': validate('Qty17',data.Qty17)}">{{ data.Qty17 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue17')" v-bind:class="{'bg-invalid': validate('ItemValue17',data.ItemValue17)}">{{ data.ItemValue17 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry17')" v-bind:class="{'bg-invalid': validate('OriginCountry17',data.OriginCountry17)}">{{ data.OriginCountry17}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item18')" v-bind:class="{'bg-invalid': validate('Item18',data.Item18)}">{{ data.Item18 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty18')" v-bind:class="{'bg-invalid': validate('Qty18',data.Qty18)}">{{ data.Qty18 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue18')" v-bind:class="{'bg-invalid': validate('ItemValue18',data.ItemValue18)}">{{ data.ItemValue18 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry18')" v-bind:class="{'bg-invalid': validate('OriginCountry18',data.OriginCountry18)}">{{ data.OriginCountry18}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item19')" v-bind:class="{'bg-invalid': validate('Item19',data.Item19)}">{{ data.Item19}}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty19')" v-bind:class="{'bg-invalid': validate('Qty19',data.Qty19)}">{{ data.Qty19 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue19')" v-bind:class="{'bg-invalid': validate('ItemValue19',data.ItemValue19)}">{{ data.ItemValue19 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry19')" v-bind:class="{'bg-invalid': validate('OriginCountry19',data.OriginCountry19)}">{{ data.OriginCountry19}}</td> 

                                <td v-on:click="csvFieldEdit(data,'Item20')" v-bind:class="{'bg-invalid': validate('Item20',data.Item20)}">{{ data.Item20 }}</td> 
                                <td v-on:click="csvFieldEdit(data,'Qty20')" v-bind:class="{'bg-invalid': validate('Qty20',data.Qty20)}">{{ data.Qty20 }}</td>
                                <td v-on:click="csvFieldEdit(data,'ItemValue20')" v-bind:class="{'bg-invalid': validate('ItemValue20',data.ItemValue20)}">{{ data.ItemValue20 }}</td>
                                <td v-on:click="csvFieldEdit(data,'OriginCountry20')" v-bind:class="{'bg-invalid': validate('OriginCountry20',data.OriginCountry20)}">{{ data.OriginCountry20}}</td> 

 
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <div style="width: 100%;display: inline-block;">
                        <button class="btn btn-danger errors-button" type='button' v-on:click="revalidate()" id="showErrors_id">REVALIDATE FIELDS</button> 
                        <button class="btn btn-info import-button" @click='importCSV' type='button' download :disabled='isDisabled'>IMPORT</button> 
                    </div>
                    <div style="width: 100%" id="errDiv">
                        <div id="invalidMessages" class="alert alert-warning invalid-mess" role="alert"></div>
                    </div>

                </div>
            </div>
        </div>


        <div class="card text-center summary-div">
            <h4 class="card-title center-title title-color"><span @click='goBackToCSV'><i class="back-csv mdi mdi-arrow-left-bold"></i></span>ORDER SUMMARY</h4><br>
            <div class="card-block">

                <div class="card-body">
                    <div id="fixed-table2" class="summary-csv fixed-table-container h-w horiz-scroll">

                         <table id="summary-csv-tbl" class="table table-bordered" v-if="orderSummary.length > 0" border="1">
                            <thead>
                            <tr>
                                <th>&nbsp;</th>
                                <th style="max-width:600px; min-width:490px;">Recipient/Parcel Information</th>
                                <th>Postage</th>
                                <th>Item</th>
                            </tr>

                            </thead>                                
                            <tbody>
                           
                            <tr v-for="(c_data, index) in orderSummary.slice(0, (Object.keys(orderSummary).length))" :id="'s_row'+setId(index)" class="row-border">  
                                 <!-- <td :id="'s_fname'+setId(index)" class="summary-width"> -->
                                <td>{{ index+1 }}</td>
                                <td style="max-width:600px; min-width:490px;border-right: 0pt dotted #c5c5c5;">
                                    <div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="address-dimension-carrier row" v-on:click="summaryEdit(c_data,'recipient')" data-toggle="tooltip" data-placement="top" title="Update Personal Info" data-original-title="Update Personal Info" data-animation="false">
                                                <!-- <div class="edit-icon"><i class="mdi mdi-pencil icon-csv"  v-on:click="summaryEdit(c_data,'recipient')"></i></div> -->
                                                <div class="col-md-12 summary-info">{{ c_data.FirstName }} {{ c_data.LastName }}</div>
                                                <div class="col-md-12 summary-info-secondary">{{ c_data.AddressLine1 }}, {{ c_data.AddressLine2 }}, {{ c_data.City }}, {{ c_data.ProvState }}, {{ c_data.PostalZipCode }}, {{ c_data.Country }}</div>
                                                <div class="col-md-6">
                                                    <div class="summary-label inner-div">Business: </div><div class="summary-info-secondary inner-div">{{ c_data.BusinessName }}</div> 
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="summary-label inner-div">Tracking:</div><div class="summary-info-secondary  inner-div">{{ c_data.Tracking }}</div>
                                                </div>
                                            </div>
                                            <div class="address-dimension-carrier row" v-on:click="summaryEdit(c_data,'parcel') "data-toggle="tooltip" data-placement="top" title="Update Parcel Info" data-original-title="Update Parcel Info" data-animation="false">
                                                <div class="col-md-6" v-if="importInfo['fileName']=='PD'">
                                                    <div class="summary-label inner-div">Length: </div><div class="summary-info-secondary inner-div">{{ c_data.Length }}</div> 
                                                </div>
                                                <div class="col-md-6" v-if="importInfo['fileName']=='PD'">
                                                    <div class="summary-label inner-div">Width:</div><div class="summary-info-secondary  inner-div">{{ c_data.Width }}</div>
                                                </div>
                                                <div class="col-md-6" v-if="importInfo['fileName']=='PD'">
                                                    <div class="summary-label inner-div">Height: </div><div class="summary-info-secondary inner-div">{{ c_data.Height }}</div> 
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="summary-label inner-div">Weight:</div><div class="summary-info-secondary  inner-div">{{ c_data.Weight }}</div>
                                                </div>
                                                <div class="col-md-6" v-if="importInfo['fileName']=='PD'">
                                                    <div class="summary-label inner-div">SizeUnit: </div><div class="summary-info-secondary inner-div">{{ c_data.SizeUnit }}</div> 
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="summary-label inner-div">WeightUnit:</div><div class="summary-info-secondary  inner-div">{{ c_data.WeightUnit }}</div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-md-12">
                                            <div class="note-import note-import-error">{{ showIfError(c_data.import_status) }}</div>     
                                        </div>
                                    </div>
                                   </div>
                                </td>
                                
                                <td style="border-right: 1pt dotted #c5c5c5;">
                                    <div>
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div v-bind:class="{'address-dimension-carrier': importInfo['fileName']=='PD'}" class="row" v-on:click="carrierEdit(c_data)" data-toggle="tooltip" data-placement="top" title="Update Carrier Info" data-original-title="Update Carrier Info" data-animation="false">
                                                <!-- <div class="edit-icon"><i class="mdi mdi-pencil icon-csv" v-on:click="carrierEdit(c_data)"></i></div> -->
                                                <div class="col-md-12  summary-info">{{ carrierFormattter(index) }}</div>
                                                <div  v-if="importInfo['fileName']=='PD'" class="col-md-4 summary-label">Insurance: </div><div  v-if="importInfo['fileName']=='PD'" class="col-md-8 summary-info-secondary">{{ c_data.InsuranceCoverAmount }}</div>
                                                <div class="col-md-4 summary-label">{{ rateOrDelivery(c_data.shipment_type) }}: </div><div class="col-md-8 summary-info-secondary">${{ c_data.TotalFees }}{{ c_data.Currency }}</div>
                                                <div class="col-md-4 summary-label summary-label-total">Total: </div><div class="col-md-8 summary-info summary-info-total">${{ subTotal(c_data.InsuranceCoverAmount, c_data.TotalFees) }}{{ c_data.Currency }}</div>
                                            </div> 
                                        </div>
                                    </div>
                                    </div>
                                </td>
                                <td>
                                    <div style="width:250px;">
                                        <div class="row">
                                            <div class="col-md-12" :id="'item'+setId(c_data.shipment_id)" style="white-space: normal; font-size:11px">

                                            </div>
                                        </div>
                                    </div>
                                </td>

                                  
                            </tr>
                          </tbody>
                        </table>
                    </div>
                    <div style="width: 100%; display: inline-block;">
                        <div class="row">
                            <div class="col-md-3">
                                <div role="alert" class="alert alert-info overall-total" style="text-align:left ">
                                        <h5 class="summary-label-overall-total" >Overall Total: <span class="summary-info-overall-total">${{ overAll }}CAD</span></h5>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div style="width:100%">
                                    <div style="float:left; width:30%">
                                        <div class="form-group">
                                            <input type="text" v-model="couponData.search" class="form-control" placeholder="Coupon Code" style="height: 35px; min-height: 35px;" @input="checkCoupon()"  v-bind:class="{'coupon-not-found': couponData.isNotFound, 'coupon-found': !couponData.isNotFound }" :disabled='couponApplied || isDisabledSummary'>
                                            <span class="bar"></span>
                                        </div>
                                    </div>
                                    <div style="float:left;">
                                    <h5 style="padding:9px; font-weight:500">
                                        {{ showCoupon() }}
                                    </h5>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-5">
                                <button class="btn btn-outline btn-secondary batch-bulk-button" type='button' @click='processShipment("cancel")' title="Cancel this shipment" :disabled='isDisabledSummary'>CANCEL</button>  
                                <button class="btn btn-outline btn-info batch-bulk-button" type='button' @click='processShipment("payment")' title="Save and pay this Shipment" :disabled='isDisabledSummary || couponData.isNotFound'>PAY NOW</button> 
                                <button class="btn btn-outline btn-primary batch-bulk-button" type='button' @click='processShipment("save")' title="Save this Shipment for future use" :disabled='isDisabledSummary || couponData.isNotFound'>SAVE</button>                          
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <BatchBulkEditFormModal></BatchBulkEditFormModal>
        <PostageOptionsModal></PostageOptionsModal>
        <ConfirmationTOSModal></ConfirmationTOSModal>
	    <CheckoutModal></CheckoutModal>
        <CreditCardModal></CreditCardModal>
        <PaywithUserwalletModal></PaywithUserwalletModal>
        <!-- <ConfirmationPrintModal></ConfirmationPrintModal>   -->
        <ConfirmationPrintBatchModal></ConfirmationPrintBatchModal>      
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse, CSVValidator_before, CSVValidator_after, orderSummary, initDropify, backToCSV, validate_data, doGeocode, showHide, showLoading, hideLoading, checkYN} from '../helpers/helper';
    import Papa from 'papaparse'
    import Blob from 'blob'
    import FileSaver from 'file-saver'
    import axios from 'axios'
    Vue.prototype.$axios = axios

                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                
    export default {
        props: [],
        mounted() {
            initDropify();
        },
        data () {
            return {
                overAll: 0,
                summaryTotal: 0,
                processing: false,
                doc: null,
                fileInfo: {},
                importInfo: {},
                csv: {},
                validate_csv: {},
                orderSummary: [],
                orderItems: [],
                carrierInfo: {},
                address: {},
                dt : new Date(),
                isPD: true,
                isDO: false,
                isImport: false,
                isSummary: false,
                isDisabled: false,
                isDisabledSummary: false,
                isValid: true,
                isBatch: false,
                table: {},
                errors: {},
                shipmentIdSummary: [],
                couponData: {
                    draw: 0,
                    search: '',
                    isNotFound: false
                },
                couponType: '',
                couponAmount: 0,
                couponCode: '',
                totalCouponAmount: 0,
                couponApplied: false,
                importBatch: '',
                applyButton: 'APPLY'


            }    
        },
        created(){
            //this.init();
            this.$root.$on("validate_csv",(d)=>{
                this.validate_csv = $('#parsed-csv-tbl').tableToJSON();
                this.isDisabled = false;
                CSVValidator_after(this.validate_csv);
            })
            this.$root.$on("no_error",(n)=>{
                this.isDisabled = false;
            })
            //SUBMIT SHIPMENT
            this.$root.$on('confirmation-print',(c)=>{

                if(this.isBatch)
                {
                    //if(this.shipmentIdSummary){
                        this.$root.$emit("shipment-submitted-batch",this.shipmentIdSummary, 'from-payment');
                    //}

                    // this.$http.post("/shipment/createShipment",this.model).then(res=>{
                    //     if(res.data.status){
                    //         this.$root.$emit("shipment-submitted",true);
                    //         this.shipmentID = res.data.shipment_id;
                    //     }
                    // })

                }
            })
            this.$root.$on("recompute",(n)=>{
                this.processOverAll();
            })
            this.$root.$on("cancel_added",(n)=>{
                console.log('add function to cancel here, just retrive the order summary. no need to reprocess, use this controller private function getOrderSummary($r)');
                this.refreshSummary();
            })
            this.$root.$on("successful_update",(n)=>{
                this.refreshSummary();
            })

            this.$root.$on("single_carrier_update",(d)=>{
                this.carrierInfo[d['id']] = d;
                console.log(d);
                console.log(this.carrierInfo);
            })
 
            this.$root.$on("enable_buttons",(d)=>{
                this.isDisabledSummary = false;
            })
 
         },
        computed: {
           //overallTotal(){
                //this.overAll = this.getOverallTotal();this.carrierInfo
                //return this.orderSummary.reduce((sum, c_data) => {
                    //return Math.round(sum + parseFloat(c_data.TotalFee)*100)/100;
                    //return (parseFloat(c_data.PostageFee)+parseFloat(c_data.InsuranceCoverAmount)+sum).toPrecision(3);
                //    return (parseFloat(c_data.PostageFee)+parseFloat(c_data.InsuranceCoverAmount)+sum);
                    //return this.overAll;
                //}, 0)
           //}
        },
        methods: {
            rateOrDelivery:function(d){
                if(d=='PD')
                    return 'Postage Fee';
                else 
                    return 'Delivery Fee';
            },             
            rndOff:function(d, n){
                if(isNaN(d))
                    return d;
                else 
                    return parseFloat(d).toFixed(n);
            },            
            processOverAll:function(){
                this.summaryTotal = this.getSummaryTotal();
                this.overAll = (this.summaryTotal-this.totalCouponAmount).toFixed(2);
            },            
            getSummaryTotal:function(){
                return this.orderSummary.reduce((sum, c_data) => {
                    return (parseFloat(c_data.TotalFees)+parseFloat(c_data.InsuranceCoverAmount)+sum);
                    //return (parseFloat(c_data.TotalFees)+sum);
                }, 0)
            },            
            showCoupon: function() {

                if(this.couponType == '$' && this.couponAmount > 0)
                    return "("+this.couponType+this.couponAmount+")";
                else if(this.couponType == '%' && this.couponAmount > 0)
                    return "("+this.couponAmount+this.couponType+")";
                else
                    return '';
            },
            applyCoupon: function() {
                //console.log('>>> here')
                if(this.couponType == '$')
                {
                    this.totalCouponAmount = this.couponAmount;
                }
                else if(this.couponType  == '%')
                {
                    this.totalCouponAmount =  this.couponAmount/100;
                }
                else
                    this.totalCouponAmount = 0;
                //this.couponApplied = true;    
                this.processOverAll();
                //this.applyButton = 'APPLIED';
                
                // var applyCoupon = {};
                // applyCoupon['couponAmount'] = this.couponAmount;
                // applyCoupon['couponCode'] = this.couponData.search;
                // applyCoupon['couponType'] = this.couponType; 
                // applyCoupon['updateType'] = 'coupon';
                // applyCoupon['importBatch'] = this.importBatch; 

                // this.$http.post("/document/process_shipment",applyCoupon).then(response=>{
                //     showSuccessMsg(response.data.message);
                //     this.processing = false;
                //     hideLoading();

                // }, response=>{
                //     this.errors = response.data.errors;
                //     this.processing = false;
                //     showErrorMsg(response.data.message)
                // }).catch((err) => {
                //     handleErrorResponse(err.status);
                //     this.processing = false;
                //     if (err.status == 422) {
                //         this.errors = err.data;
                //     }
                // });



            },            
            checkCoupon(url = '/document/check_coupon') {
                this.couponType = '';
                const that = this;
                this.couponData.draw++;
                axios.get(url, {params: this.couponData})
                    .then(response => {
                        let data = response.data;

                        if (this.couponData.draw == data.draw) {
                            //if(data.amount > 0 && this.couponData.search.length > 0)
                            //{
                                this.couponAmount = data.amount;
                                this.couponCode = data.couponCode;
                                this.couponType = data.type;
                                this.couponData.isNotFound = data.notFound;
                                this.applyCoupon();
                            //}
                        }


                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            },            
            carrierFormattter(index) {

                //console.log('>>>-> '+index+' >>> '+JSON.stringify(this.orderSummary[index]))
                if(this.orderSummary[index].shipment_type == "DO")
                    return this.orderSummary[index].CarrierDesc;
                else
                    return this.orderSummary[index].CarrierDesc +" - "+ this.orderSummary[index].Duration;

            },
            setId(index) {
                return index;
            },
            subTotal(i, r) {
               return parseFloat(i)+parseFloat(r);
            },            
            showIfError(import_status)
            {
                if(import_status!="Imported successfully")
                   return import_status;
                else
                   return "";
            },
            summaryEdit:function(data, e){
                if(!this.isDisabledSummary)
                {
                    if(e == 'recipient')
                    {
                        this.$root.$emit("recipient_edit",data);
                        var el = $('.batchbulk-form-modal')
                        el.modal({backdrop: 'static', keyboard: false});
         
                    }
                    else if(e == 'parcel')
                    {
                        this.$root.$emit("parcel_edit",data);
                        var el = $('.batchbulk-form-modal')
                        el.modal({backdrop: 'static', keyboard: false});
                    }
                }
            },
            addressDimEdit:function(data){
              
                if(!this.isDisabledSummary)
                {
                    this.$root.$emit("address_edit",data);
                    var el = $('.batchbulk-form-modal')
                    el.modal({backdrop: 'static', keyboard: false});
     
                }

            },
            carrierEdit:function(data){
      
                if(!this.isDisabledSummary)
                {
                    if(data.postage_rate_id > 0)
                    {
                        if(data['shipment_type']=="PD" && JSON.stringify(this.carrierInfo[data.shipment_id])!='[]')
                        {
                            this.$root.$emit("postage_option_edit",data,this.carrierInfo, this.overAll);
                            var el = $('.postage-options-form-modal')
                            el.modal({backdrop: 'static', keyboard: false});
                        }
                        // else
                        // {
                        //     this.$root.$emit("delivery_fee_edit",data);
                        //     var el = $('.delivery-fee-form-modal')
                        //     el.modal({backdrop: 'static', keyboard: false});
                        // }
                    }
                    // else cannot select rates, not allowed
                }      
            },
            csvEdit:function(data){
                this.$root.$emit("csv_edit",data, this.importInfo['fileName']);
                var el = $('.batchbulk-form-modal')
                el.modal({backdrop: 'static', keyboard: false});
            },
            csvFieldEdit:function(data, fldname){
                this.$root.$emit("csv_field_edit",data, fldname, this.importInfo['fileName']);
                var el = $('.batchbulk-form-modal')
                el.modal({backdrop: 'static', keyboard: false});
            },
            fieldEdit:function(data, fldname){
                this.$root.$emit("field_edit",data, fldname);
                var el = $('.batchbulk-form-modal')
                el.modal({backdrop: 'static', keyboard: false});
            },          
            handlePDDownload:function(){
                this.isPD = true,
                this.isDO = false

            },
            handleDODownload:function(){
                this.isPD = false,
                this.isDO = true
            },      
            handleDownload:function(t){
                axios({
                    url: '/templates/'+t+'.csv',
                    method: 'GET',
                    responseType: 'blob', 
                }).then((response) => {
                    const url = window.URL.createObjectURL(new Blob([response.data]));
                    const link = document.createElement('a');
                    link.href = url;
                    link.setAttribute('download', t+'_'+this.formatThisDate(this.dt)+'.csv');
                    document.body.appendChild(link);
                    link.click();
                });
            }, 
            formatThisDate: function(date) {
                return moment(date, 'YYYY-MM-DD').format('DD.MM.YY.hh:mma');
            }, 
            showHideErrors: function() {
                showHide();
            },
            processShipment: function(t) {
                this.processing = true;
                //event.target.disabled = true;
                showLoading();
                var shipmentUpdate = {};
                shipmentUpdate['shipment'] = this.orderSummary; 
                shipmentUpdate['updateType'] = t;

                var applyCoupon = {};
                applyCoupon['couponAmount'] = this.couponAmount;
                applyCoupon['couponCode'] = this.couponData.search;
                applyCoupon['couponType'] = this.couponType; 
                shipmentUpdate['importBatch'] = this.importBatch; 
                shipmentUpdate['coupon'] = applyCoupon;
                
                this.$http.post("/document/process_shipment",shipmentUpdate).then(response=>{
                    showSuccessMsg(response.data.message);
                    this.processing = false;
                    hideLoading();
                    this.$nextTick(() => {
                        if(t == 'cancel' || t == 'save' )
                        {
                            //self.$router.push('/#/shipment/batchbulk/upload');
                            backToCSV();
                        }
                        else
                        {

                            this.isDisabledSummary = true;
                            this.shipmentIdSummary = response.data.shipment_ids;
                            this.isBatch = true;			    
                            this.$root.$emit("confirmation-tos",true);
            			    this.$root.$emit("checkoutmodalship",this.overAll, this.importBatch, 'from_import');
            			    console.log(this.overAll);
                            
                        }
                    }) 
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
            parseCSV (e) {
                $('.on-parsing').show();
                this.parsingCSV();
            },
            revalidate: function() {
                this.validate_csv = $('#parsed-csv-tbl').tableToJSON();
                this.isValid = CSVValidator_after(this.validate_csv, this.importInfo['fileName']);
            },
            parsingCSV: function() {
                this.processing = true;
                const that = this;
                const fileToLoad = event.target.files[0];
                const reader = new FileReader();

                // this.fileInfo['fileName'] = $("#fileInput .dropify-filename-inner").html();
                // if(this.fileInfo['fileName'].substr(0, 30) != "recipient_postage_and_delivery" && this.fileInfo['fileName'].substr(0, 23) != "recipient_delivery_only")
                // {
                //     showErrorMsg("Invalid CSV file!");
                //     return;
                // }

                reader.onload = fileLoadedEvent => {
                    Papa.parse(fileLoadedEvent.target.result, {
                        header: true,
                        skipEmptyLines: true,
                        beforeFirstChunk: function( chunk ) {
                            var index = chunk.match( /\r\n|\r|\n/ ).index;
                            var headings = chunk.substr(0, index).split( ',' );
                            if(headings[12] == 'Length(in)')
                            {
                                headings[12] = 'Length';
                                headings[13] = 'Width';
                                headings[14] = 'Height';
                                headings[15] = 'Weight';
                            }
                            else
                                headings[10] = 'Weight';   

                            return headings.join() + chunk.substr(index);
                        },
                        complete (results) {
                            that.doc = JSON.stringify(results.data, null, 2);
                            that.importInfo = CSVValidator_before(results);


                            that.processing = false;
                            if(that.importInfo['isValid'])
                            {
                                that.isDisabled = false; 
                                that.csv = results.data;
                                that.$nextTick(() => {
                                    that.validate_csv = $('#parsed-csv-tbl').tableToJSON();
                                    that.importInfo['isValid'] = CSVValidator_after(that.validate_csv, that.importInfo['fileName']);
                                })                                
                            }
                            else
                            {
                                that.isDisabled = true; 
                                that.csv = {};
                            }
                        },
                        error (errors) {
                            console.log('error', errors)
                        }
                    })
                }
                reader.readAsText(fileToLoad)
            },
            // save () {
            //     const blob = new Blob([this.parseJSONtoCSV()], { type: 'text/csv' })
            //     FileSaver.saveAs(blob, 'test.csv')
            // },
            importCSV (){
                showLoading();
                this.processing = true;
                this.isDisabled = true;
                this.table = $('#parsed-csv-tbl').tableToJSON();
                $(".import-div").hide();
                $(".summary-div").show();              
                this.processing = true;
                this.fileInfo['csv'] = this.table;
                this.fileInfo['fileName'] =  this.importInfo['fileName'];
                
                this.$http.post("/document/import_csv",this.fileInfo).then(response=>{
                    this.processing = false;
                    hideLoading();
                    this.orderSummary = response.body.orderSummary;
                    this.orderItems = response.body.orderItems;
                    this.carrierInfo = response.body.carrierInfo;
                    this.importBatch = response.body.import_batch;
                    //console.log(this.carrierInfo);
                    //this.overAll = this.getOverallTotal();
                    this.$nextTick(() => {
                        orderSummary(this.orderSummary, this.orderItems);
                        this.isDisabled = false;
                        this.processOverAll();
                    })                 
                    
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
            refreshSummary(url = '/document/retrieve_summary') {
                axios.get(url, {params: this.importBatch})
                    .then(response => {
                        let data = response.data;

                        console.log(data.orderSummary);
                        
                        this.orderSummary = data.orderSummary;
                        this.$nextTick(() => {
                            orderSummary(this.orderSummary, this.orderItems);
                            this.processOverAll();
                        })                 

                    })
                    .catch(errors => {
                        console.log(errors);
                    });
            }, 
            parseJSONtoCSV () {
              //return Papa.unparse(this.doc)
            },
            goBackToCSV () {
                backToCSV();
            },
            validate (c, v, country=null) {
                var n = validate_data(c, v, this.importInfo['fileName'], country);
                if(n > 0)
                    this.isDisabled = true; 
                return n;
            }
         }
    };
</script>
<style scoped>
.margin-button
{
    margin:5px 5px 15px 5px;

}
.title-color
{
    color: #00a6ff;
}
.title-color
{
    color: #00a6ff;
}
.body {
    display: flex;
    justify-content: center;
    margin-top: 30px;
}
.invalid-mess{
    display:none;
    margin-top:0px;
    text-align:left;
}
.import-button{
    margin:0px 10px 10px 10px;
    float:right;
    display:none;
}
.batch-bulk-button{
    margin:0px 10px 10px 10px;
    float:right;
}
.errors-button{
    margin:0px 10px 10px 10px;
    float:right;
    display:none;
}
.parsed-csv{
    display:none;
}
.on-parsing{
    margin-bottom: .1rem;
    display:none;
}

.page-wrapper .card .card-block {
    margin-top: -30px;
}
.card-block {
    padding: .1rem;
}
.back-csv {
    float: left;
    display:none;
    font-size:25px;
    color:#4cae50;
}
.csvfiledrop {
    margin-bottom: -18px;
}
.alert {
    margin-bottom: .01rem;
}
.bg-invalid {
    background-color: #fffe8363 !important;
    color: #ff0202;
    border-bottom-color: #ff0202;
    font-weight: 400;
}
.error-mess{
    display:none;
}
.summary-div{
    display:none;
}
.summary-label{
    /*font-weight: 500;*/
    color: #949494;
    font-size: 12px;
}
.summary-info{
    font-style: italic;
    font-size: 18px;
    color: #4b91af;
    font-weight: 400;
}
.summary-info-secondary{
    font-style: italic;
    font-size: 12px;
    color: #4b91af;
    font-weight: 400;
}
.summary-label-total{
    font-size: 18px;
}
.summary-info-total{
    font-size: 20px;
    /*text-decoration: underline;*/
    color: #ffaa00;
    font-weight: 600;
}
.summary-note{
    font-size: 11px;
    width: 95%;
    float:left;
    margin: 2px;
    font-style: italic;
    color: tan;
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

.inner-div {
    float: left;
    width: 50%;
}


.table td {
    padding: .5rem;
    vertical-align: top;
    text-align: left;
}
.table th {
    padding: .5rem;
}
.table-bordered td, .table-bordered th {
    border: 0px solid transparent;
}
.table thead th, .table th {
    font-weight: 600 !important;
}  
.row-border {
    border-top: 1pt solid #c5c5c5;
}
.td-bg {
    background: #fcfcfc !important;
}
hr{
    margin-top: .2rem;
    margin-bottom: .2rem;
    border: 0;
    border-top: 1px dotted rgba(0,0,0,.1);
}
.tolal{
    text-align: left;
    margin: 5px 0px 0px 3px;
    font-weight: 500;
}
.edit-icon {
    position: absolute;
    right: 30px;
    text-align: right;
    z-index: 1000;
}
.overall-total {
    padding: 7px 5px 0px 5px;
    margin-bottom: 1rem;
    /*margin-left: .5rem;*/
    border-radius: .25rem;
    /*width: 170px;   */
}

.address-dimension-carrier{
    width: 100% !important;
    background-color:#fff0 !important;
}
.address-dimension-carrier:hover{
    background-color:#ffffe0 !important;
    width: 100% !important;
    background-image: url(data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAA8AAAAPCAYAAAA71pVKAAABDElEQVQokZ3SMUqDQRCG4WdDSGFlIZ7AIpVYheABLEREGxFJYeMJBEWws/IUFtqJnShobSVqkUJQIdgIoiTYqFhkbTaSxD+/wdliYXbemW9mNkTRXxaEIsbxGsWvjr8wBFhADftY7nmMOScln0EDES2soBTFwZVTxSVM4QxtjGIHkwNlB6GUJG5iEY84wCeaeM+UnRKu4gpHSeobNrCLyk9sBjiLGxziOfUacYFyT3wfWEM9ga0u8BbVXyr7plrHaV/FBuYyt5HgKi6xh5cu8D4lLeTBCzhHBdv4SBOez/sH3auaxhru8IB1nGStsmPFdDdxjTImsBXF4zwQQhQFYQRjydfGUxTbQ8H/tW/Ru6CTgpAYzgAAAABJRU5ErkJggg==);
    background-repeat: no-repeat;
    background-position: right 0px top 0px;

}
.icon-csv {
    font-size:15px;
    color:#4cae50;
}
.note-import {
    padding-top: 5px;
    font-size:12px;
    color:#cccccc;
    overflow: visible;
}
.note-import-error {
    color:#ff0000 !important;
}
.import-error {
    background-color:#8f8f8f5e !important;
}

.coupon-not-found {
    background-color: #ff626270;
}

.coupon-found {
    background-color: #fff27e36;
}

input::-webkit-input-placeholder {
  color: #e0e0e0 !important;
  font-size: 12px !important;
}

input::-moz-placeholder {
  color: #e0e0e0 !important;
  font-size: 12px !important;
}

input:-ms-input-placeholder {
  color: #e0e0e0 !important;
  font-size: 12px !important;
}

input::placeholder {
  color: #e0e0e0 !important;
  font-size: 12px !important;
}
</style>


