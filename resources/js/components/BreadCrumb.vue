<template>
    <div>
        <div class="row page-titles">
            <div class="col-md-5 col-8 align-self-center">
                <h3 class="">{{ module }}
                    <!-- <button class="btn btn-success btn-sm" v-on:click="showForm">Add New</button>
                    <button class="btn btn-info btn-sm" v-on:click="showModal">Search Filter</button> -->
                </h3>
               <!--  <h3 v-else class="text-themecolor">{{ module }} 
                    <button class="btn btn-default btn-sm" v-on:click="hideForm">Return</button>
                </h3> -->
                <!-- <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/">Home</a></li>
                    <li class="breadcrumb-item active">Dashboard</li>
                </ol> -->
            </div>
            <div class="col-md-7 col-4 align-self-center">
                <div class="d-flex m-t-10 justify-content-end">
                    <!-- <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                        <div class="chart-text m-r-10">
                            <h6 class="m-b-0"><small>THIS MONTH</small></h6>
                            <h4 class="m-t-0 text-info">$58,356</h4></div>
                        <div class="spark-chart">
                            <div id="monthchart"></div>
                        </div>
                    </div>
                    <div class="d-flex m-r-20 m-l-10 hidden-md-down">
                        <div class="chart-text m-r-10">
                            <h6 class="m-b-0"><small>LAST MONTH</small></h6>
                            <h4 class="m-t-0 text-primary">$48,356</h4></div>
                        <div class="spark-chart">
                            <div id="lastmonthchart"></div>
                        </div>
                    </div> -->

                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
       
        data(){
            return {
                model:{},
                websiteName:'',
            }
        },
        created(){
            // this.getInitialContents();
        },
        methods:{
            getInitialContents: function(){

            },

        }
    }
</script>
