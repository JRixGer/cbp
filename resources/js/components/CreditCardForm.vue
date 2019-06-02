<template>
    <div class="row">
    <div class="col-md-2"></div>
    <div class="col-md-8">
    <div class="card text-center">

        <div class="card-title center-title title-color">
            <div class="row">

                <div class="col-md-12">

                    <div class="icons-inline-big">
                        <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Add credit card" data-original-title="Tooltip on top" data-animation="false"  v-on:click="addCreditCard()"><i class="mdi mdi-credit-card-plus icon-custom-big"></i></button>
                    </div>

                    <div class="icons-inline-big">
                    <router-link to="/shipment/addmoney/" aria-expanded="false">
                        <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Purchase credit" data-original-title="Tooltip on top" data-animation="false"><i class="mdi mdi-currency-usd  icon-custom-big"></i></button>
                    </router-link>
                    </div>

                </div>

            </div>
        </div>

        <div class="card-block">

            <div class="card-body">
               <div class="users">
                     <datatable :columns="columns" :sortKey="sortKey" :sortOrders="sortOrders" @sort="sortBy">
                        <tbody>
                            <tr v-for="(creditcard, index) in creditcards" :key="creditcard.id">
                                <td>{{creditcard.brand}}</td>
                                <td>{{creditcard.exp_month}}</td>
                                <td>{{creditcard.exp_year}}</td>
                                <td>{{creditcard.last4}}</td>
                                <td>
                                    {{ creditcard.card_id }}
                                </td>
                                <td>{{creditcard.created_at}}</td>


                                <td style="padding: 0px 8px 0px 8px;">
                                    <div style="min-width:20px">
                                      <!-- <div class="icons-inline">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Edit" data-original-title="Tooltip on top" data-animation="false" v-on:click="editCoupon(creditcard.id)"><i class="mdi mdi-pencil icon-custom"></i></button>
                                      </div> -->

                                      <div class="icons-inline">
                                        <button type="button" class="btn btn-secondary btn-outline icon-btn" data-toggle="tooltip" data-placement="top" title="Remove credit card" data-original-title="Tooltip on top" data-animation="false" v-on:click="confirmAction(creditcard, 'delete')"><i class="mdi mdi-delete-forever icon-custom"></i></button>
                                      </div>
                                   </div> 
                                </td>
                           </tr>
                        </tbody>
                    </datatable>
                    <div class="row" style="display:none">
                        <div class="col-md-12">
                            <div style="width:auto; float:right">                            
                                <div class="select">
                                    <select v-model="tableData.length" @change="getCreditCards()" class="form-control">
                                        <option v-for="(records, index) in perPage" :key="index" :value="records">{{records}}</option>
                                    </select>
                                </div>
                            </div>
                            <div style="width:auto; float:right">
                                <pagination :pagination="pagination"
                                            @prev="getCreditCards(pagination.prevPageUrl)"
                                            @next="getCreditCards(pagination.nextPageUrl)">
                                </pagination>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <CreditCardListModal></CreditCardListModal>  
        <ConfirmationCreditCardModal></ConfirmationCreditCardModal>   
    </div>
    </div>
    <div class="col-md-2"></div>
    </div>
</template>


<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading, toggleTtooltip} from '../helpers/helper';
import Datatable from '../components/DatatableCC.vue';
import Pagination from '../components/PaginationCC.vue';

var axios = require("axios");

import DatePicker from 'vue2-datepicker';

export default {
    props: ['value'],
    components: { datatable: Datatable, pagination: Pagination, DatePicker },
    data() {
        let sortOrders = {};
        let columns = [
            {width: '20%', label: 'Brand', name: 'brand'},
            {width: '20%', label: 'ExpMonth', name: 'exp_month'},
            {width: '20%', label: 'ExpYear', name: 'exp_year'},
            {width: '20%', label: 'Last4', name: 'last4'},
            {width: '20%', label: 'Card_id', name: 'card_id'},            
            {width: '20%', label: 'CreatedAt', name: 'created_at'}
        ];
        columns.forEach((column) => {
           sortOrders[column.name] = -1;
        });
        return {
            creditcards: [],
            columns: columns,
            sortKey: 'created_at',
            sortOrders: sortOrders,
            perPage: ['10', '20', '30'],
            tableData: {
                draw: 0,
                length: 30,
                search: '',
                column: 0,
                dir: 'desc'
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
            clicked: [],
            delIndex: -1,
            model:{},
            option:{},
            isNarrow: false,
            isWide: false,
            errors:{},
            isValid: true,
            setting:'creditcard'
        }
    },
    mounted() {
        console.log('Access Form modal Component mounted.')
    },
    created(){
        this.init();
        this.$root.$on("refresh_creditcards",(d)=>{
            this.getCreditCards();
        })        
        this.$root.$on("process_action",(data, t, i)=>{
            
            $('.confirmation-creditcard-modal').modal('hide');
            this.processAction(data, t, i);
        });

    },
    watch:{
    },
    methods:{
        init:function(){
            this.getCreditCards();
        },
        // editCoupon:function(d){
        //     alert('still ongoing')
        // },   
        addCreditCard() {
            //this.$root.$emit("add_creditcard", true);
            var el = $('.add_credit_card-form-modal')
            el.modal({backdrop: 'static', keyboard: false});
        },   
        confirmAction(data, action, index) {
            let m = '';
            if(action == "delete")
                m = 'Delete this credit card?'
            this.$root.$emit("confirm_action", data, action, m, index);
            var el = $('.confirmation-creditcard-modal')
            el.modal({backdrop: 'static', keyboard: false});
        },  
        processAction: function(data, action, i){
            showLoading();
            let para = {};
            para['data'] = data;
            para['action'] = action;
            this.$http.post("payment/del_creditcard",para).then(response=>{

                this.creditcards.splice(i, 1);
                hideLoading();

            }, response=>{
                this.errors = response.data.errors;
                this.processing = false;
                showErrorMsg(response.data.message)
                hideLoading();
            }).catch((err) => {
                handleErrorResponse(err.status);
                this.processing = false;
                if (err.status == 422) {
                    this.errors = err.data;
                }
                hideLoading();
            });            
        },          
        getCreditCards(url = 'payment/get_creditcards') {
            const that = this;
            showLoading();
            this.tableData.draw++;
            this.clicked['column'] = this.tableData.column;
            this.clicked['sortType'] = this.tableData.dir;

            axios.get(url, {params: this.tableData})
                .then(response => {
                    let data = response.data;
                    hideLoading();
                    if (this.tableData.draw == data.draw) {
                        this.creditcards = data.data.data;
                        this.configPagination(data.data);
                        setTimeout(function(){ fixedThisTable('fixed-table_cc'); }, 100);
                    }
                    let col_ = this.columns[this.clicked['column']]['name'];
                    let dir_ = this.clicked['sortType'];

                    Vue.nextTick(function () {
                        $('[data-toggle="tooltip"]').tooltip();
                        that.$root.$emit("add_arrows", col_, dir_);
                    })
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
            this.getCreditCards();
        },
        getIndex(array, key, value) {
            return array.findIndex(i => i[key] == value)
        }        

    }
};
</script>
<style scoped>
    .fixed-table-container th, .fixed-table-container td{
        padding: 4px;
    }
    /*.fixed-table-container {
        height: 300px !important;
    }*/
    .form-control {
        border: 1px solid rgba(0, 0, 0, 0.08) !important;
        font-size: 11px !important;
    }  
    .table thead th, .table th {
        font-weight: 500 !important;
        padding: 5px;
    }    
    .floating-labels .focused label {
        font-size: 11px;
        color: #00c2f4;
    }

    .fixed-table-container {
        border: 1px solid #fff;
    }  

    .table-striped tbody tr:nth-of-type(odd) {
        background: #fff;
    }

    .fixed-table-container th, .fixed-table-container td {
        border-right: 1px solid #fff;
        border-bottom: 1px solid #fff;
    }

    .fixed-table-container td:first-child {
        background: #fff;
    }
    .btn-outline-active {
        color: #de00c9;
    }
    .btn-secondary:hover {
        color: #de00c9;
    }
    .page-wrapper .card .card-title {
        padding: 5px;
    }    
</style>

