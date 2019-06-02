<template>
   <div id="fixed-table3" class="fixed-table fixed-table-container h-w horiz-scroll"> 
    <table class="table table-bordered table-striped table-hover">
        <thead>
            <tr>
                <th style="padding-left: 0.6rem; padding: 11px 3px 8px 8px;">
                    <div class="custom-control custom-checkbox">
                        <input type="checkbox" class="custom-control-input" id="CheckAllId" @click="checkAll = !checkAll" @change="onChange(checkAll)">
                        <label class="custom-control-label" for="CheckAllId"></label>
                    </div>
                    
               </th>
                <th v-for="column in columns" :key="column.name" :id="column.name" @click="$emit('sort', column.name)"
                    :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                    :style="'width:'+column.width+';'+'cursor:pointer;'">
                    {{column.label}}
                </th>
                <th style="text-align:right" >
                    Action           
               </th>
            </tr>
        </thead>
        <slot></slot>
    </table>
    </div>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
    
    export default {
        props: ['columns', 'sortKey', 'sortOrders'],
        mounted() {
        },
        data() {
            return {
                checkAll: false
            }
        },
        created() {
            this.$root.$on("add_arrows",(c, d)=>{
                $("#shipment_id").removeClass("up").removeClass("down");
                $("#"+c).removeClass("up").removeClass("down");
                if(d=="asc")
                    $("#"+c).addClass("down");
                else
                    $("#"+c).addClass("up");
            })

            this.$root.$on("on_get_shipment",(e)=>{
                this.checkAll = e;
                $("#CheckAllId"). prop("checked", false);
            })

        },
        methods:{
            onChange(state){
                console.log(state)
                this.$root.$emit("check_on_off", state);
            }
        }

    };


</script>
<style  scoped>
.table thead th, .table th {
    font-weight: 600 !important;
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
}
.up {
    padding-right: 0px;
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjI8Bya2wnINUMopZAQA7);
    background-repeat: no-repeat;
    background-position: right 0px top 10px;
    color: #26c5da;
}
.down {
    padding-right: 0px;
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7);
    background-repeat: no-repeat;
    background-position: right 0px top 10px;
    color: #26c5da;
}

.fixed-table-container th, .fixed-table-container td {
    padding: 12px 15px 12px 7px;
}

.form-group {
    margin-bottom: 40px !important;
}
.floating-labels .focused label {
    top: -15px;
}

</style>
