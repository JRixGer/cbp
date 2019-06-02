<style lang="scss">
.pagination {
    justify-content: flex-end !important;

    .page-stats {
        align-items: center;
        margin: 5px;
    }
    i {
        color: #fff !important;
    }

}
.btn-sm {
    /*color:#fff !important; */
    margin-right: 5px !important;
}
</style>

<template>
    <nav class="pagination" v-if="!client">
        <span class="page-stats">{{pagination.from}} - {{pagination.to}} of {{pagination.total}}</span>
        <a v-if="pagination.prevPageUrl" class="btn btn-secondary btn-sm pagination-previous" @click="$emit('prev');">
            Prev
        </a>
        <a class="btn btn-secondary btn-sm pagination-previous" v-else :disabled="true">
           Prev
        </a>

        <a v-if="pagination.nextPageUrl" class="btn btn-secondary btn-sm pagination-next" @click="$emit('next');">
            Next
        </a>
        <a class="btn btn-secondary btn-sm pagination-next" v-else :disabled="true">
            Next
        </a>
    </nav>

    <nav class="pagination" v-else>
        <span class="page-stats">
            {{pagination.from}} - {{pagination.to}} of {{filtered.length}}
            <span v-if="filtered.length < pagination.total">(filtered from {{pagination.total}} total entries)</span>
        </span>
        <a v-if="pagination.prevPage" class="btn btn-secondary btn-sm pagination-previous" @click="$emit('prev');">
            Prev
        </a>
        <a class="btn btn-secondary btn-sm pagination-previous" v-else :disabled="true">
           Prev
        </a>

        <a v-if="pagination.nextPage" class="btn btn-secondary btn-sm pagination-next" @click="$emit('next');">
            Next
        </a>
        <a class="btn btn-secondary btn-sm pagination-next" v-else :disabled="true">
            Next
        </a>
    </nav>
</template>

<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading} from '../helpers/helper';
    export default {
        props: ['pagination', 'client', 'filtered']
    };
</script>
<style scoped>
    .btn {
        padding: .3rem .3rem 0rem .3rem !important;
        margin: 0rem .3rem 0rem .3rem !important;
        font-size: 12px !important;
    }     
</style>
