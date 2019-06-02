<template>
<div class="col-md-12 content-div">
    <div class="card text-center">

        <div class="card-title center-title title-color">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group has-search">
                        <span class="fa fa-search form-control-feedback"></span>
                        <input type="text" class="form-control" v-model="tableData.search" placeholder="Type Shipment ID or Order ID"
                            @input="getGroupedOrders()" style="min-height: 25px;
                                                            padding: .55rem 2.2rem;
                                                            font-size: .8rem;
                                                            -webkit-box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                                                                box-shadow: inset 0 1px 1px rgba(0, 0, 0, 0.075);
                                                            ">
                    </div>
                </div>   
                <div class="col-md-9">
                    <div style="width:30px; float:right">
                        <div class="icons-inline-big" style="float:right;">
                            <router-link to="/shipment/report/all" aria-expanded="false">
                            <button type="button" class="btn btn-secondary btn-outline icon-btn" v-bind:class="{'btn-outline-active': true }"  data-toggle="tooltip" data-placement="top" title="Back to all groupedOrders" data-original-title="Tooltip on top" data-animation="false"><i class="mdi mdi-arrow-left-bold icon-custom-big"></i></button>
                            </router-link>
                        </div> 
                    </div>

                </div>

            </div>
        </div>

        <div class="card-block">

            <div class="card-body">
                <div class="groupedOrders">
                    <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
                        <tbody>
                            <tr v-for="(g_order, index) in groupedOrders" :key="g_order.shipment_id">
                                <td>{{formatThisDate(g_order.created_at)}}</td>
                                <td>{{g_order.shipment_date}}</td>
                                <td>{{g_order.groupedIds}}</td>
                                <td style="padding: 0px 8px 0px 8px;">
                                  <div style="min-width:20px">
   
                                      <div class="icons-inline">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="View details" data-original-title="Tooltip on top" data-animation="false" v-on:click="groupedOrderView(g_order, g_order.grouped_order_id, g_order.shipment_date)"><i class="fa fa-search icon-btn-custom"></i></button>
                                      </div>

                                  </div>
                                </td>
                           </tr>
                        </tbody>
                    </datatable>
                    <div class="row">
                        

                        <div class="col-md-12">
                            <div style="width:auto; float:right">                            
                            <div class="select">
                                <select v-model="tableData.length" @change="getGroupedOrders()" class="form-control">
                                    <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                                </select>
                            </div>
                            </div>
                            <div style="width:auto; float:right">
                                <pagination :pagination="pagination"
                                            @prev="getGroupedOrders(pagination.prevPageUrl)"
                                            @next="getGroupedOrders(pagination.nextPageUrl)">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
    <GroupedOrderModal></GroupedOrderModal>
    <ConfirmationModal></ConfirmationModal>
</div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading, checkYN} from '../helpers/helper';
import Datatable from './DatatableGO.vue';
import Pagination from './Pagination.vue';
import Papa from 'papaparse'
import Blob from 'blob'
import FileSaver from 'file-saver'

var axios = require("axios");

import DatePicker from 'vue2-datepicker';

export default {
    props: ['value'],
    components: { datatable: Datatable, pagination: Pagination, DatePicker },
    data() {
        let sortOrders = {};

        let columns = [
            {width: '5%', label: 'DateCreated', name: 'created_at' },
            {width: '5%', label: 'ShipmentDate', name: 'shipment_date' },
            {width: '5%', label: 'GroupedIds', name: 'groupedIds' },
        ];


        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            selectedTotal: {
                amount: 0,
                amountUnpaid: 0
            },
            groupedOrders: [],
            columns: columns,
            sortKey: 'created_at',
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
        this.getGroupedOrders();
        this.$root.$on("refresh_groupedorders",(e)=>{
            $('.grouped-order-modal').modal('hide');
            this.getGroupedOrders();
        });
    },
    methods: {
        groupedOrderView: function(d, gId, sD) {
            this.$root.$emit("show_grouped_order", d, gId, sD);
            var el = $('.grouped-order-modal')
            el.modal({backdrop: 'static', keyboard: false}); 
        },                    
        formatThisDate: function(date) {
            return moment(date).format('MM/DD/YYYY hh:mma');
        },        
        getGroupedOrders(url = '/report/grouped_orders') {
            //this.$root.$emit("on_get_shipment",false);
            
            const that = this;
            if(this.tableData.search.length == 0)
                showLoading();
            this.tableData.draw++;

            this.clicked['column'] = this.tableData.column;
            this.clicked['sortType'] = this.tableData.dir;

            axios.get(url, {params: this.tableData})
                .then(response => {
                    let data = response.data;
                    if(this.tableData.search.length == 0)
                        hideLoading();

                    if (this.tableData.draw == data.draw) {
                        this.groupedOrders = data.data.data;
                        console.log('//////////// -> '+JSON.stringify(this.groupedOrders));                        
                        this.configPagination(data.data);
                        fixedThisTable("fixed-table_go");
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
            this.getGroupedOrders();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        },
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
.icon-custom {
    font-size: 14px;
}
.icon-btn-custom {
    padding: 0px 2px 4px 2px;
    font-size: 15px;
}
</style>
