<template>
    <div>
    <header class="topbar">
        <nav class="navbar top-navbar navbar-toggleable-sm navbar-light">
            <!-- ============================================================== -->
            <!-- Logo -->
            <!-- ============================================================== -->
            <div class="navbar-header">
                <a class="navbar-brand" href="#">
                    <!-- Logo icon -->

                        <!--You can put here icon as well // <i class="wi wi-sunset"></i> //-->
                        <!-- Dark Logo icon -->
                        <!-- <img src="images/logo-icon.png" alt="homepage" class="dark-logo" /> -->
                        <!-- Light Logo icon -->
                        <!-- <img src="images/logo-light-icon.png" alt="homepage" class="light-logo" /> -->
                        

                    <!--End Logo icon -->
                    <!-- Logo text -->
                    <span>
                        <b>
                        Credits: ${{credits}}
                        </b>
                         <!-- dark Logo text -->
                         <!-- <img src="images/logo-text.png" alt="homepage" class="dark-logo" /> -->
                         <!-- Light Logo text -->    
                         <!-- <img src="images/logo-light-text.png" class="light-logo" alt="homepage" /> -->
                     </span> 
                 </a>
            </div>

            <!-- ============================================================== -->
            <!-- End Logo -->
            <!-- ============================================================== -->
            <div class="navbar-collapse">
                <!-- ============================================================== -->
                <!-- toggle and nav items -->
                <!-- ============================================================== -->
                <ul class="navbar-nav mr-auto mt-md-0">
                    <!-- This is  -->
                    <li class="nav-item"> <a class="nav-link nav-toggler hidden-md-up text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="mdi mdi-menu"></i></a> </li>
                    <li class="nav-item"> <a class="nav-link sidebartoggler hidden-sm-down text-muted waves-effect waves-dark" href="javascript:void(0)"><i class="ti-menu"></i></a> </li>
                    </li>
                </ul>


                <!-- <div v-bind:class="{'show-ticker': notifications.length == 0}"> -->
                <div style="margin-top:15px" class="modern-ticker mt-round show-hide-ticker">
                    <div class="mt-body">
                        <div class="mt-label">Announcements:</div>
                        <div class="mt-news">
                            <ul>

                                <li v-for="(d, index) in notifications">
                                    <a href="#" target="_self" v-on:click="showMessage(d)">

                                         <span v-html="removeHTML(d.title, d.body)"></span>           
                                    
                                    </a>
                                </li>
                                

                            </ul>
                        </div>
                    </div>
                </div>
                <!-- </div> -->

                <!-- ============================================================== -->
                <!-- User profile and search -->
                <!-- ============================================================== -->
                <ul class="navbar-nav my-lg-0">
                    <!-- ============================================================== -->
                    <!-- Comment -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-message"></i>
                            <div class="notify"> <span class="heartbit" v-if="notifications.length > 0"></span> <span class="point" v-if="notifications.length > 0"></span> </div>
                        </a>
                        <div class="dropdown-menu dropdown-menu-right mailbox scale-up">
                            <ul>
                                <li>
                                    <div class="drop-title">Announcements</div>
                                </li>
                                <li>
                                    <div class="message-center">
                                        <a href="#" v-for="d in notifications">
                                            <div class="btn btn-danger btn-circle"><i class="mdi mdi-message"></i></div>
                                            <div class="mail-content" v-on:click="showMessage(d)">
                                                <h5>{{ d.title }}</h5> <span class="mail-desc"><span v-html="d.body"> </span></span> <span class="time">9:30 AM</span> 
                                            </div>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <a class="nav-link text-center" href="javascript:void(0);"> <strong>Check all notifications</strong> <i class="fa fa-angle-right"></i> </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- ============================================================== -->
                    <!-- End Comment -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Messages -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" id="2" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> <i class="mdi mdi-email"></i>
                            <div class="notify"> <span class="heartbit" v-if="emails.length > 0"></span> <span class="point" v-if="emails.length > 0"></span> </div>
                        </a>
                        <div class="dropdown-menu mailbox dropdown-menu-right scale-up" aria-labelledby="2"  v-if="emails.length > 0">
                            <ul>
                                <li>
                                    <div class="drop-title">You have 4 new messages</div>
                                </li>
                                <li>
                                    <div class="message-center">
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="images/users/1.jpg" alt="user" class="img-circle"> <span class="profile-status online pull-right"></span> </div>
                                            <div class="mail-content">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:30 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="images/users/2.jpg" alt="user" class="img-circle"> <span class="profile-status busy pull-right"></span> </div>
                                            <div class="mail-content">
                                                <h5>Sonu Nigam</h5> <span class="mail-desc">I've sung a song! See you at</span> <span class="time">9:10 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="images/users/3.jpg" alt="user" class="img-circle"> <span class="profile-status away pull-right"></span> </div>
                                            <div class="mail-content">
                                                <h5>Arijit Sinh</h5> <span class="mail-desc">I am a singer!</span> <span class="time">9:08 AM</span> </div>
                                        </a>
                                        <!-- Message -->
                                        <a href="#">
                                            <div class="user-img"> <img src="images/users/4.jpg" alt="user" class="img-circle"> <span class="profile-status offline pull-right"></span> </div>
                                            <div class="mail-content">
                                                <h5>Pavan kumar</h5> <span class="mail-desc">Just see the my admin!</span> <span class="time">9:02 AM</span> </div>
                                        </a>
                                    </div>
                                </li>
                                <li>
                                    <a class="nav-link text-center" href="javascript:void(0);"> <strong>See all e-Mails</strong> <i class="fa fa-angle-right"></i> </a>
                                </li>
                            </ul>
                        </div>
                    </li>
                    <!-- ============================================================== -->
                    <!-- End Messages -->
                    <!-- ============================================================== -->
                    <!-- ============================================================== -->
                    <!-- Profile -->
                    <!-- ============================================================== -->
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle text-muted waves-effect waves-dark" href="" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><img src="images/users/1.jpg" alt="user" class="profile-pic" /></a>
                        <div class="dropdown-menu dropdown-menu-right scale-up">
                            <ul class="dropdown-user">
                                <li>
                                    <div class="dw-user-box">
                                        <div class="u-img"><img src="images/users/1.jpg" alt="user"></div>
                                        <div class="u-text">
                                            <h4>{{ profile.name }}</h4>
                                            <p class="text-muted">{{ profile.email }}</p><a href="/#/user/profile" class="btn btn-rounded btn-danger btn-sm">View Profile</a></div>
                                    </div>
                                </li>
                                <li role="separator" class="divider"></li>
                                <!-- <li><a href="/#/user/account_profile"><i class="ti-user"></i> My Profile</a></li> -->
                                <li><a href="/#/user/profile"><i class="ti-user"></i> User Profile</a></li>
                                <li><a href="/#/shipment/wallet"><i class="ti-wallet"></i> My Balance</a></li>
                                <li><a href="#"><i class="ti-email"></i> Inbox</a></li>
                                <li role="separator" class="divider"></li>
                                <li><a href="#"><i class="ti-settings"></i> Account Setting</a></li>
                                <li role="separator" class="divider"></li>
                                <!-- <li><a href="#"><i class="fa fa-power-off"></i> Logout</a></li> -->
                            </ul>
                        </div>
                    </li>
                    <!-- ============================================================== -->
                    <!-- Language -->
                    <!-- ============================================================== -->
                    <li class="nav-item">
                        <a v-on:click="logout" class="nav-link dropdown-toggle text-muted waves-effect waves-dark"  data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"> 
                            <i class="mdi mdi-logout"></i>
                        </a>
                    </li>

                </ul>
            </div>
        </nav>
    </header>
    <ShowMessageModal></ShowMessageModal>
</div>
</template>


<script>
    import {showErrorMsg, showSuccessMsg, handleErrorResponse, fixedThisTable, showLoading, hideLoading, stripAllHtml, loadTicker} from '../helpers/helper';
    // import { mixin as clickaway } from 'vue-clickaway';
    export default {
        // mixins: [ clickaway ],
        mounted() {
        },
        props:[],
        data(){
            return{
                credits: 0,
                notifications: [],
                profile:{},
                emails: [],
                tickerLocation: 0,
                news: [
                  true,
                  false,
                  false,
                ]
            }
        },
        created:function(id){
    	    this.fetchWalletdata();
            this.fetchNotification();
            this.fetchProfile();

            setInterval(this.updateTicker, 5000);
            
            this.init();
        },

        methods:{
            init:function(){
                //loadTicker(this.notifications.length);
            },
            showMessage: function(d) {
                this.$root.$emit("show_message",d);
                var el = $('.show_message-modal')
                el.modal({backdrop: 'static', keyboard: false}); 
            },   
            fetchProfile: function() {
                this.$http.get("/profile/user/info/").then(res=>{

                    this.profile = res.data;


                });
            },          
            fetchNotification: function() {
                this.$http.get("/site/notifications/").then(res=>{

                    this.notifications = res.data.noti;

                    loadTicker(this.notifications.length);

                    console.log(this.notifications);

                });
            },
            logout:function(){
                this.$http.post('/logout').then(response=>{
                    window.location.href="/login";
                    // window.location.reload();
                }, response=>{
                    window.location.reload();
                });
            },
    	    fetchWalletdata: function() {
        		this.$http.get("/addmoney/walletdetail/All").then(res=>{
        		    this.credits = (res.data.cash) ? parseFloat(res.data.cash).toFixed(2) : 0;
        		});
    	    },
            updateTicker: function() {
                if(this.notifications.lenght > 0)
                {
                    var removed = this.news.pop();
                    this.news.unshift(removed);
                }
            },
            removeHTML: function(t, h) {
                return t+', '+stripAllHtml(h); 
            },
        }
    };
</script>
<style scoped>
    
    .mailbox .message-center {
        height: auto !important;
    }
    .mailbox .message-center a .mail-content {
        height: 56px !important;
        overflow: hidden;
    }

    .breaking-news {
      background-color: #1fb5ad;
      color: #ffffff;
      border-radius: 10px 50px 50px 10px;
      padding: 5px;
    }

    .media-content {
      padding: 6px;
    }

    .time {
      color: #1fb5ad;
    }

    .news {
      color: #ccc;
    }

    .fade-enter-active, .fade-leave-active {
      transition: opacity 1s
    }
    .fade-enter, .fade-leave-to {
      opacity: 0
    }
    .media {
        margin-bottom: 0px; 
    }
    .box {
        border-radius: 4px;
        padding: 0px;
    }

    .media {
        border: 1px solid #1fb5ad;
        padding: 0px;
        margin-top: 15px;
        height: 37px;
        overflow: hidden;
     }    

    .breaking-news {
        padding: 5px 7px 5px 5px;
    }

    .point-btn 
    { 
        width: 148px;
        height: 37px;
        background: #1fb5ad;
        position: relative;
        border-radius: 0px;
        margin-left: 0px;
        min-width: 148px;
    } 
/*    .point-btn:before
    { 
        content:"";
        position: absolute;
        left: 148px;
        width: 0;
        height: 0; 
        border-top: 17px solid transparent;
        border-left: 14px solid #1fb5ad; 
        border-bottom: 17px solid transparent;
    }*/

    .point-btn:after {
        content: "";
        position: absolute;
        left: 147px;
        width: 0;
        height: 0;
        top: 8px;
        border-top: 10px solid transparent;
        border-left: 10px solid #1fb5ad;
        border-bottom: 10px solid transparent;
    }
    .point-btn span {
        width: 142px;
        background: #1fb5ad;
        height: 25px;
        position: absolute;
        top: 4px;
        left: 5px;
    }

    .media-content {
        margin: 0px 5px 0px 15px;
    }
    .an-title {
        position: absolute;
        padding: 5px;
        color: #fff;
    }
    .limit-text {
        text-align:left;
        margin-top:1px; 
        width: auto;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
        -webkit-transition: all 0.35s ease;
        -moz-transition: all 0.35s ease;
        -o-transition: all 0.35s ease;
        -ms-transition: all 0.35s ease;
        transition: all 0.35s ease;
    }  
    .show-ticker {
        display: none;
    } 
</style>
<style>

    .modern-ticker {
        color: #fafafa !important;
    }

    .modern-ticker .mt-label {
        background-color: #26c5da !important;
    }

    .modern-ticker .mt-news a {
        background-color: #fff !important;
    }   

    .modern-ticker .mt-news a:active {
        background-color: #26c5da !important;
    }    

    .modern-ticker .mt-news a:hover {
        background-color: #26c5da !important;
    }

    .modern-ticker.mt-round {
        border-radius: 0px !important;
    }

    .modern-ticker.mt-round .mt-label {
        border-radius: 0px !important;
    }

    .modern-ticker.mt-scroll .mt-news {
        background-color: #fff !important;
    }

    .slimScrollDiv {
        height: auto !important;
    }

</style>