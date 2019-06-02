<template>
   <div id="fixed-table_go" class="fixed-table fixed-table-container h-w horiz-scroll"> 
    <table class="table table-striped">
        <thead>
            <tr>
                <th v-for="column in columns" :key="column.name" :id="column.name" @click="$emit('sort', column.name)"
                    :class="sortKey === column.name ? (sortOrders[column.name] > 0 ? 'sorting_asc' : 'sorting_desc') : 'sorting'"
                    :style="'width:'+column.width+';'+'cursor:pointer;'">
                    {{column.label}}
                </th>
                <th style="text-align:center">
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
                $("#id").removeClass("up").removeClass("down");
                $("#"+c).removeClass("up").removeClass("down");
                if(d=="asc")
                    $("#"+c).addClass("down");
                else
                    $("#"+c).addClass("up");
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
    font-weight: 700 !important;
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
}
.down {
    padding-right: 0px;
    background-image: url(data:image/gif;base64,R0lGODlhFQAEAIAAACMtMP///yH5BAEAAAEALAAAAAAVAAQAAAINjB+gC+jP2ptn0WskLQA7);
    background-repeat: no-repeat;
    background-position: right 0px top 10px;
}

.table thead th, .table th{
    font-weight: 600 !important; padding: 5px;
}  
</style>
