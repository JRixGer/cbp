<template>
    <div class="container">
    <div class="row">
       <div class="col-lg-12 bg-white mt-4 pt-4 pb-4 bl-4 pr-4">
       <div class="row">
       	<div class="col-lg-9">
       		<h2> $ {{ balance }}</h2>
       		<p>Your wallet balance</p>
       	</div>
       <div class="col-lg-3 text-right">
       	<i class="ti-wallet" style=" 
       	    font-size: 75px;"></i>
       </div>
       </div>
       </div>
    
    </div>
    <div class="row">
    <div class="col-lg-12 bg-white mt-4 pt-4 pb-4 bl-4 pr-4 shipments" style="font-size:12px">

      <div style="width:150px; margin-bottom:10px; float:right">
          <select type="text" class="form-control" placeholder="" v-model="trans_type" @change="reload" > 
              <option value="All">All</option>
              <option value="Credit">Credit</option>
          </select>
      </div>  

        
        <table class="table table-bordered table-striped table-hover table-sm" v-if="trans_type == 'All'">
          <thead>
            <tr>
              <th width="20%">Date</th>
              <th width="10%">OrderID</th>
              <th>Amount Added</th>
              <th>Amount Used</th>
              <th>Balance</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(data, index) in walletdata">
              <td>{{ data.created_at }}</td>
              <td>{{ data.id }}</td>
              <td>{{ showAmount(data.credit_added) }}</td>
              <td>{{ showAmount(data.credit_used) }}</td>
              <td>{{ runningBalance[index] }}</td>
            </tr>
          </tbody>
        </tbody>
        </table>

        <table class="table table-bordered table-striped table-hover table-sm" v-else>
          <thead>
            <tr>
              <th width="20%">Date</th>
              <th width="10%">OrderID</th>
              <th>Amount</th>
              <th>Status</th>
            </tr>
          </thead>
          <tbody>
            <tr v-for="(data, index) in walletdata">
              <td>{{ data.created_at }}</td>
              <td>{{ data.id }}</td>
              <td>{{ data.amount }}</td>
              <td>{{ data.status }}</td>
            </tr>
          </tbody>
        </tbody>
        </table>
       </div>
    </div>
    </div>
</template>
<script>
import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
export default {
    data() {
      return {
        	walletdata: {},
          balance: 0,
          runningBalAdded: [],
          runningBalUsed: [],
          totalAdded: 0,
          totalUsed: 0,
          trans_type: 'All',
      }
    },
    created() {
    	  showLoading();
        this.fetchWalletdata();
    },
    computed: {
        runningBalance() {
            return this.walletdata.map((data) => {
                
                if(data.pay_with == 'credit_card')
                {
                    this.runningBalAdded[data.id] = data.credit_added;
                    this.runningBalUsed[data.id] = 0;

                }
                else  
                {
                    this.runningBalAdded[data.id] = data.credit_added;
                    this.runningBalUsed[data.id] = data.credit_used;
                }

                this.totalAdded = 0;
                this.totalUsed = 0;

                for (var i in this.runningBalAdded) {
                    this.totalAdded = parseFloat(this.totalAdded) + parseFloat(this.runningBalAdded[i]);
                    this.totalUsed = parseFloat(this.totalUsed) + parseFloat(this.runningBalUsed[i]);
                }
                return Number(this.totalAdded - this.totalUsed).toFixed(2);
            });
        }
    },
    methods: {
        reload()
        {
            if(this.trans_type != 'All') 
              this.fetchWalletdata();
            else
              this.fetchWalletdata();
        },
        showAmount:function(d)
        {
          if(d == '0.00')
            return '';
          else
            return d;

        },
        fetchWalletdata: function() {
            showLoading();
            this.walletdata = {};
            this.runningBalAdded = [];
            this.runningBalUsed = [];
	          this.$http.get("/addmoney/walletdetail/"+this.trans_type).then(res=>{
            this.walletdata = res.data.transaction_history;
            this.balance = res.data.cash ? parseFloat(res.data.cash).toFixed(2) : 0;
		        hideLoading();
            });
        }
    }
};
</script>
<style scoped>
  .form-control {
    font-size: .8rem;
  }
</style>

