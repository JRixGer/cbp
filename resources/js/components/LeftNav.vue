<template>
    <div>
        <aside class="left-sidebar">
            <!-- Sidebar scroll-->
            <div class="scroll-sidebar">
                <!-- Sidebar navigation-->
                <nav class="sidebar-nav">
                    <ul id="sidebarnav">
                        <!-- <li class="add-credits"><span class="hide-menu"><a href="/#/shipment/addmoney/">ADD CREDITS</a></span> </li> -->

			             <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">PAYMENTS </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <router-link to="/shipment/addmoney/" class=" waves-effect waves-dark" aria-expanded="false">
                                        Purchase Credit
                                    </router-link>
                                </li>
                                
                                <li>
                                    <router-link to="/shipment/creditcard/" class=" waves-effect waves-dark" aria-expanded="false">
                                        Credit Cards
                                    </router-link>
                                </li>
                                <li>
                                    <router-link to="/shipment/wallet/" class=" waves-effect waves-dark" aria-expanded="false">
                                        History
                                    </router-link>
                                </li>                                
                            </ul>
                        </li>

                        <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">REPORTS </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <router-link to="/shipment/report/all" class=" waves-effect waves-dark" aria-expanded="false">
                                        All Shipments
                                    </router-link>
                                </li>
                            </ul>
                        </li>


                        <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">NEW ORDER </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li v-on:click="showRequirePostageModal" class=" waves-effect waves-dark" aria-expanded="false">
                                    <a>
                                        Single Upload
                                    </a>
                   
                                </li>
                                <router-link to="/shipment/batchbulk/upload" class=" waves-effect waves-dark" aria-expanded="false">
                                        Batch Upload
                                </router-link>
                                <!-- <li v-on:click="loadBatchBulkUpload" class=" waves-effect waves-dark" aria-expanded="false">
                                    <a>
                                        
                                        Batch Upload
                                    </a>
                   
                                </li> -->

                            </ul>
                        </li>

                        <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">CBP </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <router-link to="/" class=" waves-effect waves-dark" aria-expanded="false">
                                        LINKS
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <!-- <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">PICKUP REQUEST </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <router-link to="/" class=" waves-effect waves-dark" aria-expanded="false">
                                        LINKS
                                    </router-link>
                                </li>
                            </ul>
                        </li>

                        <li > <a class="has-arrow waves-effect waves-dark" href="#" aria-expanded="false"><span class="hide-menu">ORDER SUPPLIES </span></a>
                            <ul aria-expanded="false" class="collapse">
                                <li>
                                    <router-link to="/" class=" waves-effect waves-dark" aria-expanded="false">
                                        LINKS
                                    </router-link>
                                </li>
                            </ul>
                        </li> -->
                        
                    </ul>
                </nav>
                <!-- End Sidebar navigation -->
            </div>
            <!-- End Sidebar scroll-->

        </aside>

    </div>
</template>


<script>

import moment from 'moment';

export default {
    data(){
        return{
        }
    },
    mounted() {
        
    },
    created:function(){
    },
    watch:{
        
    },

    methods:{
        showRequirePostageModal: function(){

            if($cookies.get("nevershowReqPostage") == "true"){

                if($cookies.get("reqPostage") == "y"){
                    window.location.href="/#/shipment/single/pd";

                }else if($cookies.get("reqPostage") == "n"){
                    window.location.href="/#/shipment/single/do";
                    
                }else{

                    var el = $('.require-postage-modal')
                    el.modal({backdrop: 'static', keyboard: false});
                }
            }else{


                var el = $('.require-postage-modal')
                    el.modal({backdrop: 'static', keyboard: false});
            }
        },
        loadBatchBulkUpload: function(){
            window.location.href="/#/shipment/batchbulk/upload";
        }    
    }
}

</script>
