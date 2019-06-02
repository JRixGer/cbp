<template>
    <div>
        <div class="page-wrapper">

            <div class="container-fluid">
                <router-view></router-view>
            </div>

            <RequirePostageModal></RequirePostageModal>
            

        </div>
    </div>
</template>

<script>
    export default {
        props: [],
        mounted() {
            console.log('Component mounted.')
        },
        data(){
            return {
                model:{},
            }
        },
        created(){
            this.getInitialContents();
        },
        methods:{
            getInitialContents: function(){

            }
        }
    }
</script>
