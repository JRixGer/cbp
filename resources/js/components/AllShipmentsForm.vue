<template>
<div class="col-md-12 content-div">
    <div class="card text-center">

        <div class="card-title center-title title-color">
            <div class="row">
                <div class="col-md-2">
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" v-model="tableData.search" placeholder="Search"
                            @input="getShipments()" style="min-height: 25px;
                                                            padding: .55rem 2.2rem;
                                                            font-size: .8rem;
                                                            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                                                                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                                                            ">
                    </div>
                </div>   
                <div class="col-md-2">
                    <date-picker v-model="tableData.dtStart" :first-day-of-week="1" @input="getShipments()" placeholder="From Date" lang="en"></date-picker>
                </div>

                <div class="col-md-2">
                    <date-picker v-model="tableData.dtEnd" :first-day-of-week="1" @input="getShipments()" placeholder="End Date" lang="en"></date-picker>
                </div>

 
                <div class="col-md-6">
                    <div style="min-width:200px">
                        <div class="icons-inline-big">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Archive" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'archive', null, 'multiSelected')"><i class="mdi mdi-archive icon-custom-big"></i></button>
                        </div>
                        <div class="icons-inline-big">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}"  data-toggle="tooltip" data-placement="top" title="Void" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'void', null, 'multiSelected')"><i class="mdi mdi-stop-circle icon-custom-big"></i></button>
                        </div>
                        <div v-if="$can('allow', 'isDownload')" class="icons-inline-big">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active':  checkedShipmentIds.length>0}"  data-toggle="tooltip" data-placement="top" title="Download" data-original-title="Tooltip on top" data-animation="false" v-on:click="priorToProcess(null, 'download', null, 'multiSelected')"><i class="mdi mdi-download icon-custom-big"></i></button>
                        </div>

                        <div class="icons-inline-big" v-if="$can('allow', 'isPrintBillOfLading')">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn"  v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Generate packing slip" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'multi-print-bill', null, 'multiSelected')"><i class="mdi mdi-file-document icon-custom-big"></i></button>
                        </div>
                        <div class="icons-inline-big" v-if="$can('allow', 'isPrintPostage')">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn"  v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Generate label" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'multi-print-label', null, 'multiSelected')"><i class="mdi mdi-printer icon-custom-big"></i></button>
                        </div>
                        <div class="icons-inline-big">
                            <router-link to="/shipment/report/grouped" aria-expanded="false">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': true }"  data-toggle="tooltip" data-placement="top" title="Reverse grouped order" data-original-title="Tooltip on top" data-animation="false"><i class="mdi mdi-credit-card-off icon-custom-big"></i></button>
                            </router-link>
                        </div>                        
                        <div class="icons-inline-big">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}"  data-toggle="tooltip" data-placement="top" title="Create order" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'order', null, 'multiSelected')"><i class="mdi mdi-credit-card-plus icon-custom-big"></i></button>
                        </div>                        
                        <div class="icons-inline-big">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn"  v-bind:class="{'btn-outline-active': checkedShipmentIds.length>0}" data-toggle="tooltip" data-placement="top" title="Pay now" data-original-title="Tooltip on top" data-animation="false" :disabled="checkedShipmentIds.length==0" v-on:click="priorToProcess(null, 'payment', null, 'multiSelected')"><i class="mdi mdi-currency-usd icon-custom-big"></i></button>
                        </div>
                    </div>

                </div>

            </div>
        </div>

        <div class="card-block">

            <div class="card-body">
                <div class="shipments">
                    <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
                        <tbody>
                            <tr v-for="(shipment, index) in shipments" :key="shipment.shipment_id">
                                
                                <td style="padding-top: 8px;">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" :value="[shipment.shipment_id]" :id="[shipment.shipment_id]" v-model="checkedShipmentIds" @click="check($event, shipment.TotalFees, shipment.name)">
                                        <label class="custom-control-label" :for="[shipment.shipment_id]"></label>
                                    </div>
                                </td>
                                <td>{{shipment.internal_ship_id}}</td>
                                <td>{{shipment.internal_order_id}}</td>
                                <td>{{customFormatter(shipment.Created)}}</td>
                                <td>{{shipment.RecipientName}}</td>
                                <td>{{shipment.Country}}</td>
                                <td>{{typeFormatter(shipment.shipment_type)}}</td>
                                <td>{{shipment.Carrier}}</td>
                                <td>{{shipment.name}}</td>
                                <td>{{shipment.TotalFees}}</td>
                                <td>{{shipment.Tracking}}</td>
                                <td style="padding: 0px 8px 0px 8px;">
                                    <div style="min-width:120px">
                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Tooltip on top" data-animation="false" v-on:click="orderEdit(shipment.shipment_id, shipment.shipment_type, shipment.ship_status_id)"><i class="mdi mdi-pencil icon-custom"></i></button>
                                        </div>

                                        <div class="icons-inline" v-if="$can('allow', 'isPrintBillOfLading')">
                                            <button type="button"class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Generate manifest" data-original-title="Tooltip on top" data-animation="false" v-on:click="priorToProcess(shipment, 'single-print-mani', shipment.LetterMail, shipment.name)" :disabled="ifpaid(shipment.name)" ><i class="mdi mdi-file-document icon-custom" :disabled="checkShipment(shipment)"></i></button>
                                        </div>

                                        <div class="icons-inline">
                                            <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Copy" data-original-title="Tooltip on top" data-animation="false" v-on:click="orderCopy(shipment.shipment_id, shipment.shipment_type, shipment.ship_status_id)"><i class="mdi mdi-content-copy icon-custom"></i></button>
                                        </div>
                                    </div>
                                </td>
                           </tr>
                        </tbody>
                    </datatable>
                    <div class="row">
                        
                        <div class="col-md-6">
                            <div style="width:auto; float:left">       
                                <div role="alert" class="alert alert-info overall-total" style="text-align:left ">
                                        <h5 class="summary-label-overall-total" >Selected Total: <span class="summary-info-overall-total">${{ selectedTotal.amount }}CAD</span></h5>
                                </div>                     
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div style="width:auto; float:right">                            
                            <div class="select">
                                <select v-model="tableData.length" @change="getShipments()" class="form-control">
                                    <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                                </select>
                            </div>
                            </div>
                            <div style="width:auto; float:right">
                                <pagination :pagination="pagination"
                                            @prev="getShipments(pagination.prevPageUrl)"
                                            @next="getShipments(pagination.nextPageUrl)">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <ShipmentsAllModal></ShipmentsAllModal>
    <ConfirmationModal></ConfirmationModal>
    <ConfirmationTOSModal></ConfirmationTOSModal>
    <CheckoutModal></CheckoutModal>
    <CreditCardModal></CreditCardModal>
    <PaywithUserwalletModal></PaywithUserwalletModal>
    <BatchBulkEditFormModal></BatchBulkEditFormModal>
    <ConfirmationPrintBatchModal></ConfirmationPrintBatchModal> 
    <ConfirmationPrintModal v-bind:shipmentID="shipmentID"></ConfirmationPrintModal>       
    <InfoModal></InfoModal> 
</div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading, checkYN} from '../helpers/helper';
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
        let sortOrders = {};
        let columns = [
            {width: '5%', label: 'ShipId', name: 'internal_ship_id' },
            {width: '5%', label: 'OrderId', name: 'internal_order_id' },
            {width: '5%', label: 'DateCreated', name: 'Created' },
            {width: '5%', label: 'RecipientName', name: 'RecipientName' },
            {width: '5%', label: 'RecipientCountry', name: 'Country' },
            {width: '5%', label: 'ShipmentType', name: 'shipment_type' },
            {width: '5%', label: 'Carrier', name: 'Carrier' },
            {width: '5%', label: 'Status', name: 'name' },
            {width: '5%', label: 'TotalFees', name: 'TotalFees' },
            {width: '5%', label: 'TrackingNo', name: 'Tracking' }
        ];

        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });

        return {
            selectedTotal: {
                amount: 0,
                amountUnpaid: 0
            },
            shipments: [],
            columns: columns,
            sortKey: 'id',
            sortOrders: sortOrders,
            perPage: ['10', '20', '30'],
            tableData: {
                draw: 0,
                length: 10,
                search: '',
                dtStart: '',
                dtEnd: '',
                column: 0,
                dir: 'desc',
            },
            pagination: {
                lastPage: '',
                currentPage: '',
                total: '',
                lastPageUrl: '',
                nextPageUrl: '',
                prevPageUrl: '',
                from: '',
                to: ''
            },
            ckbState: false,
            isMultiple: false,
            clicked: [],
            delIndex: -1,
            checkedShipmentIds: [],
            shipmentIdSummary: [], 
            downloadData: [], 
            senderInfo: [],
            dt : new Date(),
            isBatch: false,
            shipmentID:"",
            shipmentIDtemp:"",
            reportType:"",
            shortcuts: [
                    {
                      text: 'Today',
                      onClick: () => {
                        this.dtStart = [ new Date(), new Date() ],
                        this.dtEnd = [ new Date(), new Date() ]
                      }
                    }
                  ],


        }
    },
    mounted () {
        
    },  
    watch:{
    }, 
    created() {
        this.getShipments();

        this.$root.$on("check_on_off",(state)=>{
            
            this.ckbState  = state;
            this.selectedTotal['amount'] = 0;
            this.selectedTotal['amountUnpaid'] = 0;
            if(state)
            {
                for(let shipment of this.shipments)
                {
                    this.checkedShipmentIds.push([shipment.shipment_id]);
                    this.selectedTotal['amount'] = this.selectedTotal['amount'] + shipment.TotalFees;
                    if(shipment.name == 'Unpaid' || shipment.name == 'Imcomplete')
                        this.selectedTotal['amountUnpaid'] = this.selectedTotal['amountUnpaid'] + shipment.TotalFees;
                }
                this.selectedTotal['amount'] = this.selectedTotal['amount'].toFixed(2);
            }    
            else
            {
                this.checkedShipmentIds = [];
                this.selectedTotal = [];
                this.selectedTotal['amount'] = 0;
                this.selectedTotal['amountUnpaid'] = 0;
            }
        });
        this.$root.$on("process_shipment",(id, t, other = null)=>{
            

            let data = {};
            data['shipmentDate'] = id['shipment_date'];

            console.log('---+ '+data['shipmentDate']);

            if (id['excludeIds'] !== undefined && id['excludeIds'].length  > 0)  
            {
                let remmoveIds = id['excludeIds'];
                id = jQuery.grep(id, function(value) {
                      return (jQuery.inArray(""+value, remmoveIds) === -1)
                });    
            }

            data['id'] = id;
            if(/print/.test(t))
                this.print(id, t, other);
            else if(/payment/.test(t) || /order/.test(t))
                this.processShipment(id, t, other, data['shipmentDate']);
            else
                this.processShipment(id, t, other);

        });
        this.$root.$on('confirmation-print',(c, t)=>{ // this printing here is used right after clicking  payment button

            if(this.isBatch)
            {
                this.$root.$emit("shipment-submitted-batch",this.shipmentIdSummary);
            }
            else
            {
                this.$root.$emit("shipment-submitted",false);
                this.shipmentID = this.shipmentIDtemp;
            }
        });

        this.$root.$on("refresh_allshipment",(e)=>{
            this.getShipments();
        });
    },
    methods: {
        checkYesNo(d) {
            return checkYN(d);
        },  
        checkIf(d) {
            if(d != 'Incomplete')  
                return true;
            else
                return false;

        },
        ifpaid(d)
        {
            if(d == 'Unpaid')  
                return true;
            else
                return false;
        },
        checkShipment(d) {
            if(d.Country == 'CA' && d.cbp_country == 'CA')   
                return true;
            else
                return false;

        },        
        typeFormatter(d) {
            if(d == 'DO')  
                return "Delivery only";
            else
                return "Postage & Delivery";

        },
        customFormatter(date) {
            return moment(date).format('MM/DD/YY');
        },
        getShipments(url = '/report/all_shipments') {
            this.checkedShipmentIds = [];
            this.$root.$emit("on_get_shipment",false);

            this.selectedTotal['amountUnpaid'] = 0;                
            this.selectedTotal['amount'] = 0;

            const that = this;
            if(this.tableData.search.length == 0 && this.tableData.dtStart.length == 0  && this.tableData.dtEnd.length == 0)
                showLoading();
            this.tableData.draw++;

            this.clicked['column'] = this.tableData.column;
            this.clicked['sortType'] = this.tableData.dir;

            axios.get(url, {params: this.tableData})
                .then(response => {
                    let data = response.data;
                    if(this.tableData.search.length == 0 && this.tableData.dtStart.length == 0  && this.tableData.dtEnd.length == 0)
                        hideLoading();

                    if (this.tableData.draw == data.draw) {
                        console.log(data.data.data);
                        this.shipments = data.data.data;
                        this.configPagination(data.data);
                        fixedThisTable();
                    }

                    let col_ = this.columns[this.clicked['column']]['name'];
                    let dir_ = this.clicked['sortType'];

                    Vue.nextTick(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                        that.$root.$emit("add_arrows", col_, dir_);
                    });

                    that.resetVar();
                })
                .catch(errors => {
                    console.log(errors);
                });
        },
        configPagination(data) {
            this.pagination.lastPage = data.last_page;
            this.pagination.currentPage = data.current_page;
            this.pagination.total = data.total; 
            this.pagination.lastPageUrl = data.last_page_url;
            this.pagination.nextPageUrl = data.next_page_url;
            this.pagination.prevPageUrl = data.prev_page_url;
            this.pagination.from = data.from;
            this.pagination.to = data.to;
        },
        sortBy(key) {
            this.sortKey = key;
            this.sortOrders[key] = this.sortOrders[key] * -1;
            this.tableData.column = this.getIndex(this.columns, 'name', key);
            this.tableData.dir = this.sortOrders[key] === 1 ? 'asc' : 'desc';
            this.getShipments();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
        orderEdit:function(id, type, status){

            if(status <= 2){
                window.location.href="/#/shipment/single/"+type+"?id="+id
            }else{
                swal("Error","Cannot edit this record","warning")
            }
            //alert('still ongoing')
            // this.$root.$emit("order_edit",data, t);
            // var el = $('.batchbulk-form-modal')
            // el.modal({backdrop: 'static', keyboard: false});
        },  
        orderCopy:function(id, type, status){

            if(status <= 2){
                this.$http.post("/shipment/copy",{"id":id}).then((res)=>{
                swal("Success","Shipment has been duplicated","success");

                })
            }else{
                swal("Error","Cannot duplicate this record","warning")
            }
            //alert('still ongoing')
            // this.$root.$emit("order_edit",data, t);
            // var el = $('.batchbulk-form-modal')
            // el.modal({backdrop: 'static', keyboard: false});
        },        
        check: function(e, tf, s) {

            console.log(e +' > '+ tf +' > '+ s)
            if (e.target.checked) 
            {
                this.selectedTotal['amount'] = parseFloat(this.selectedTotal['amount']) + tf;
                if(s == 'Unpaid' || s == 'Imcomplete')
                    this.selectedTotal['amountUnpaid'] = this.selectedTotal['amountUnpaid'] + tf;                
            }
            else
            {
                this.selectedTotal['amount'] = parseFloat(this.selectedTotal['amount']) - tf;
                if(s == 'Unpaid' || s == 'Imcomplete')
                    this.selectedTotal['amountUnpaid'] = this.selectedTotal['amountUnpaid'] - tf;                
            }
            this.selectedTotal['amount'] = this.selectedTotal['amount'].toFixed(2);     
        },
        download(){
            const blob = new Blob([this.parseJSONtoCSV()], { type: 'text/csv' })
            //All_Shipment_mmddyyyy
            FileSaver.saveAs(blob, 'All_Shipment_'+this.formatThisDate(this.dt)+'.csv')
        },
        parseJSONtoCSV () {
          return Papa.unparse(this.downloadData)
        },
        formatThisDate: function(date) {
            return moment(date, 'YYYY-MM-DD').format('MMDDYYYY');
        }, 
        resetVar: function() {
            this.isMultiple = false;
            this.isBatch = false;
            this.ckbState = false;
        },
        priorToProcess: function(data, processType, isLetter = null, other = null){
            
            let statuses = ["Incomplete","Ready","Received","Attention Required","Prepared","In Transit","Delivered"];
            let processTypes = ["archive","delete","void","Ready","download"];

            if((jQuery.inArray(other, statuses) === -1) && other!='multiSelected' && processType!='reverse' && (jQuery.inArray(processType, processTypes) === -1))
            {
                console.log('-> !Incomplete');
                let mess = "This order is not yet processed, cannot print this time";
                this.$root.$emit("inform_action", mess, true);
                var el = $('.info-modal')
                el.modal({backdrop: 'static', keyboard: false});  
            }
            else if(isLetter == 'YES' && /print-bill/.test(other))
            {
                console.log('-> isLetter');
                let mess = "This is a letter only, cannot generate packing slip";
                this.$root.$emit("inform_action", mess, true);
                var el = $('.info-modal')
                el.modal({backdrop: 'static', keyboard: false}); 
            }
            else
            {
                if(processType=="multi-print-bill")
                {

                    console.log('-> multi-print-bill');
                    this.reportType = 'bill';
                    this.multiSelectedConfirm(processType);

                }
                else if(processType=="multi-print-label")
                {

                    console.log('-> multi-print-label');
                    this.reportType = 'label';
                    this.multiSelectedConfirm(processType);

                }
                else if(processType=="single-print-bill")
                {

                    console.log('-> single-print-bill');
                    this.reportType = 'bill';
                    this.singleConfirm(data, data.shipment_id, processType, other); 

                } 
                else if(processType=="single-print-mani")
                {

                    console.log('-> single-print-mani');
                    this.reportType = 'mani';
                    this.singleConfirm(data, data.shipment_id, processType, other); 

                }                            
                else if(processType=="single-print-label")
                {

                    console.log('-> single-print-label');
                    this.reportType = 'label';
                    this.singleConfirm(data, data.shipment_id, processType, other); 

                }            
                else if(processType=="order")
                {

                    console.log('-> order');
                    //this.isMultiple = true;
                    this.multiSelectedConfirm(processType);

                }                 
                else if(processType=="download")
                {

                    console.log('-> download');
                    this.multiSelectedConfirm(processType);

                }
                else if(processType=="void")
                {

                    console.log('-> void');
                    if(other == 'multiSelected')
                        this.multiSelectedConfirm(processType);
                    else
                        this.singleConfirm(data, data.shipment_id, processType, other)

                }
                else if(processType=="archive")
                {

                    console.log('-> archive');
                    this.multiSelectedConfirm(processType);

                }
                else if(processType=="payment")
                {

                    console.log('-> payment');
                    this.multiSelectedConfirm(processType);

                }
                else if(processType=="copy")
                {

                } 
                else if(processType=="edit")
                {

                }
            }

        },
        singleConfirm: function(data, data_id, processType, i) {

            this.isMultiple = false;
            this.delIndex = i;
            let mess = "";
            let disable = true;
            if(processType=="void")
            {
                
                if(data.name == 'Ready')
                {
                    
                    mess = "Void this record?"; // voiding need to recalculate accordingly. to less the paid amount
                    disable = false;
                }
                else
                {
                    mess = "Cannot void unpaid/delivered/incomplete shipment";
                    disable = true;
                }
            }
            else if(processType=="delete")
            {
                if(data.name != 'Incomplete' || data.name != 'Unpaid')
                {
                    mess = "Cannot delete paid shipment";
                    disable = true;
                }
                else
                {
                    mess = "Delete this record?";
                    disable = false;
                }                
            }
            else if(processType=="single-print-bill")
            {
                
                if(data.shipment_type == 'DO')
                {
                    mess = "Cannot generate the packing slip on postage only";
                    disable = true;
                }
                else
                {
                    mess = "Generate packing slip?";
                    disable = false;
                }                   
            }
            else if(processType=="single-print-mani")
            {
                
                if(data.name == 'Unpaid')
                {
                    mess = "Cannot generate the manifest on unpaid order";
                    disable = true;
                }
                else
                {
                    mess = "Generate manifest?";
                    disable = false;
                }                   
            }            
            else if(processType=="single-print-label")
            {
                mess = "Generate label?";
                disable = false;
            }

            this.$root.$emit("confirm_action",data_id, processType, mess, disable);
            var el = $('.confirmation-modal')
            el.modal({backdrop: 'static', keyboard: false});
        },        
        multiSelectedConfirm: function(processType){
            if(this.checkedShipmentIds.length > 0)
            {

                this.isMultiple = true;
                let mess = "";
                if(processType=="void")
                    mess = "Confirm Void?";
                else if(processType=="delete")
                    mess = "Confirm Delete?";
                else if(processType=="download")
                    mess = "Confirm Download?";                
                else if(processType=="archive")
                    mess = "Confirm Archive?";                
                else if(processType == 'payment')
                    mess = "Continue your payment?";
                else if(processType=="multi-print-bill")
                    mess = "Generate packing slip?";
                else if(processType=="multi-print-label")
                    mess = "Generate label?";
                else if(processType=="order")
                    mess = "Create this batch?";                
               
                this.$root.$emit("selected_multiple", this.shipments, this.checkedShipmentIds, processType, mess, this.selectedTotal);
                var el = $('.confirmation-modal')
                el.modal({backdrop: 'static', keyboard: false});                
            }
        },         
        print: function(id, t, other = null){  // this printing here is used when printing only (not payment)
            
            if(other == "mulSelected")
            {
                this.isBatch = true;    
                this.shipmentIdSummary = id;
                this.$root.$emit("shipment-report-batch",this.shipmentIdSummary, this.reportType);
            }
            else if(other == "singleSelected")
            {
                this.isBatch = false;
                this.$root.$emit("shipment-submitted",false, t);
                this.shipmentID = id;
            }
            else
                alert('unhandled path!');
        },    
        processShipment: function(data, processType, other = null, shipmentDate = null){

            console.log('>>>= '+shipmentDate);

            if(processType == 'payment' || processType =='delete' || processType =='void' || processType =='download' || processType =='archive' || processType =='order')
            {
                $('.confirmation-modal').modal('hide');
                showLoading();
            }

            var shipmentUpdate = {};
            shipmentUpdate['shipmentInfo'] = data; 
            shipmentUpdate['updateType'] = processType;
            shipmentUpdate['isMultiple'] = this.isMultiple;
            shipmentUpdate['shipmentDate'] = shipmentDate;

            console.log(shipmentUpdate);

            this.$http.post("/report/process_shipment",shipmentUpdate).then(response=>{
                this.ckbState  = false;
                hideLoading();
                if(processType == 'payment' && other == 'mulSelected')
                {
                    this.shipmentIdSummary = response.data.shipment_ids;
                    this.isBatch = true;
                    this.$root.$emit("confirmation-tos",true);
		            this.$root.$emit("checkoutmodalship",response.data.TotalAmount, data, 'from_allshipment');
                }
                else if(processType=="download")
                {
                    this.downloadData = response.data.download_data;
                    this.senderInfo = response.data.sender_info;
                    this.download();
                }
                else if(processType=="archive")
                {
                    this.getShipments();
                    showSuccessMsg(response.data.message);
                }
                else if(this.isMultiple)
                    this.getShipments();
                else
                    this.shipments.splice(this.delIndex, 1);

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
        }    

    }
};
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
</style>
