<template>
    <div class="modal show_message-modal" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true" style="display: none;">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="mySmallModalLabel">
                        <span>ANNOUNCEMENT</span>
                    </h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                </div>
                <form class="m-t-40 floating-labels" v-on:submit.prevent="formSubmit" method="post" id="registerform" action="">                    
                    <div class="modal-body">

                        <div class="row">
                            
                            <div class="col-md-12" style="text-align:left; padding:0px 20px 20px 20px">
                               <p style="font-size:10px">
                                {{ formatThisDate(m.created_at) }} <br><br>
                               </p>
                               <p style="font-weight:500">
                                {{ m.title }} <br>
                               </p>
                               <p><span v-html="m.body"> </span></p>
                            </div>   
                        </div>    
                    </div>

                    <div class="modal-footer" style="margin-top:10px">
                        <div style="text-align:right">
                            <button type="button" class="btn btn-outline btn-secondary" data-dismiss="modal" aria-hidden="true">CLOSE</button>
                        </div>
                    </div>
                </form>

            </div>
        </div>
    </div>
</template>

<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
import Datatable from '../components/Datatable.vue';
import Pagination from '../components/Pagination.vue';
import moment from 'moment';

var axios = require("axios");

export default {
    components: { datatable: Datatable, pagination: Pagination},
    data() {
        return {
            m: {},
            value: [],
            options: []
                    
        }
    },
    mounted() {
        //console.log('Access Form modal Component mounted.')
    },
    created(){
        this.$root.$on("show_message",(d)=>{
            console.log(d);
            this.m = d;
            this.markRead();

        })
    },
    methods:{
        init:function(){
            //this.getUsers('user/get_users');
        },
        formatThisDate: function(date) {
            return moment(date, 'YYYY-MM-DD').format('DD-MM-YYYY hh:mma');
        }, 
        //,
        // getUsers(url) {
        //     axios.get(url)
        //         .then(response => {
        //             console.log(response.data);
        //             this.options = response.data.data;
        //         })
        //         .catch(errors => {
        //             console.log(errors);
        //         });
        // },

        markRead: function(){
            showLoading();
            this.$http.post("site/mark_read",this.m).then(response=>{
                hideLoading();
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

.modal-dialog {
    width: 90%;
    max-width: 865px;
    margin: 20px auto;
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
.table td, .table th {
    font-size: .8rem;
}
.fixed-table-container {
    border: 0px solid #ccc; 
}
.m-t-40 {
    margin-top: 10px;
}
ul#sel li {
  display:inline;
}
.sel {
  margin-left: 5px;
  list-style-position:inside;
  border: 1px solid #eaeaea;
  padding: 1px 4px 2px 4px;
  font-size: 11px;
  float: left;
}
</style>
