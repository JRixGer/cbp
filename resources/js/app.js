
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

// require('./bootstrap');


window.Vue = require('vue');
window.$ = window.jQuery = require('jquery');
Vue.use(require('vue-resource'));
Vue.use(require('vue-cookies'))

// import { StripeCheckout } from 'vue-stripe'
import Autocomplete from 'vue2-autocomplete-js';
import VueRouter from 'vue-router';
import VueCookies from 'vue-cookies'

import { abilitiesPlugin } from '@casl/vue'
import Vue from 'vue';
import ability from './ability';
import Vuelidate from 'vuelidate';
import VueSignaturePad from 'vue-signature-pad';
 
Vue.use(VueSignaturePad);
Vue.use(Vuelidate);
Vue.use(abilitiesPlugin, ability);
Vue.use(VueCookies)
Vue.use(VueRouter)

// import DatePicker from 'vue-md-date-picker';
// Vue.use(DatePicker);

// set default config
VueCookies.config('7d')


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

Vue.component('contentbody', require('./components/ContentBody.vue'));
Vue.component('contentfooter', require('./components/ContentFooter.vue'));
Vue.component('topnav', require('./components/TopNav.vue'));
Vue.component('leftnav', require('./components/LeftNav.vue'));
Vue.component('breadcrumb', require('./components/BreadCrumb.vue'));
Vue.component('profileregistration', require('./components/ProfileRegistration.vue'));
Vue.component('GoogleAddress', require('./components/GoogleAddress.vue'));
Vue.component('SenderBusinessFormModal', require('./components/SenderBusinessFormModal.vue'));
Vue.component('SenderMailingAddressFormModal', require('./components/SenderMailingAddressFormModal.vue'));
Vue.component('SingleShipmentToolbar', require('./components/SingleShipmentToolbar.vue'));
Vue.component('RecipientInfoForm', require('./components/RecipientInfoForm.vue'));
Vue.component('SignatureRequireForm', require('./components/SignatureRequireForm.vue'));
Vue.component('ParcelDimensionsForm', require('./components/ParcelDimensionsForm.vue'));
Vue.component('ParcelTypesForm', require('./components/ParcelTypesForm.vue'));
Vue.component('ShipFromAddress', require('./components/ShipFromAddress.vue'));
Vue.component('PostageOptions', require('./components/PostageOptions.vue'));
Vue.component('ShipFromAddressFormModal', require('./components/ShipFromAddressFormModal.vue'));
Vue.component('ShipFromAddressFormModalSS', require('./components/ShipFromAddressFormModalSS.vue'));
Vue.component('InsuranceModal', require('./components/InsuranceModal.vue'));
Vue.component('RequirePostageModal', require('./components/RequirePostageModal.vue'));
Vue.component('ItemInformation', require('./components/ItemInformation.vue'));
Vue.component('ItemInformationModal', require('./components/ItemInformationModal.vue'));
Vue.component('ConfirmationTOSModal', require('./components/ConfirmationTOSModal.vue'));
Vue.component('ConfirmationPrintModal', require('./components/ConfirmationPrintModal.vue'));
Vue.component('USPSboxOptions', require('./components/USPSboxOptions.vue'));
Vue.component('USPSletterOptions', require('./components/USPSletterOptions.vue'));
Vue.component('ShowMessageModal', require('./components/ShowMessageModal.vue'));


Vue.component('BatchBulkShipmentToolbar', require('./components/BatchBulkShipmentToolbar.vue'));
Vue.component('ConfirmationPrintBatchModal', require('./components/ConfirmationPrintBatchModal.vue'));

Vue.component('AllShipmentsToolbar', require('./components/AllShipmentsToolbar.vue'));
Vue.component('GroupedOrdersToolbar', require('./components/GroupedOrdersToolbar.vue'));
Vue.component('UserProfileToolbar', require('./components/UserProfileToolbar.vue'));

Vue.component('CreditCardToolbar', require('./components/CreditCardToolbar.vue'));
Vue.component('CreditCard', require('./components/CreditCard.vue'));
Vue.component('CreditCardForm', require('./components/CreditCardForm.vue'));
Vue.component('CreditCardListModal', require('./components/CreditCardListModal.vue'));


Vue.component('ImportShipmentsForm', require('./components/ImportShipmentsForm.vue'));
Vue.component('AllShipmentsForm', require('./components/AllShipmentsForm.vue'));
Vue.component('GroupedOrdersForm', require('./components/GroupedOrdersForm.vue'));
Vue.component('UserProfileForm', require('./components/UserProfileForm.vue'));

Vue.component('ShipmentsAllModal', require('./components/ShipmentsAllModal.vue'));
Vue.component('GroupedOrderModal', require('./components/GroupedOrderModal.vue'));
Vue.component('ConfirmationModal', require('./components/ConfirmationModal.vue'));
Vue.component('ConfirmationCreditCardModal', require('./components/ConfirmationCreditCardModal.vue'));
Vue.component('DrawPadModal', require('./components/DrawPadModal.vue'));

Vue.component('InfoModal', require('./components/InfoModal.vue'));
Vue.component('PrintOptionModal', require('./components/PrintOptionModal.vue'));
Vue.component('CheckoutModal', require('./components/CheckoutModal.vue'));
Vue.component('CreditCardModal', require('./components/CreditCardModal.vue'));
Vue.component('PaywithUserwalletModal', require('./components/PaywithUserwalletModal.vue'));
Vue.component('BatchBulkEditFormModal', require('./components/BatchBulkEditFormModal.vue'));

Vue.component('PostageOptionsModal', require('./components/PostageOptionsModal.vue'));
Vue.component('DeliveryFee', require('./components/DeliveryFee.vue'));
Vue.component('IsLetterForm', require('./components/IsLetterForm.vue'));
Vue.component('InsuranceForm', require('./components/InsuranceForm.vue'));
Vue.component('AccountInfo', require('./components/AccountInfo.vue'));
Vue.component('UserProfile', require('./components/UserProfile.vue'));
Vue.component('BusinessAddress', require('./components/BusinessAddress.vue'));
Vue.component('MailingAddress', require('./components/MailingAddress.vue'));
Vue.component('ImportNumber', require('./components/ImportNumber.vue'));


const dashboard = Vue.component('dashboard', require('./components/Dashboard.vue'));
const SingleShipmentPD = Vue.component('SingleShipmentPD', require('./components/SingleShipmentPD.vue'));
const SingleShipmentDO = Vue.component('SingleShipmentDO', require('./components/SingleShipmentDO.vue'));
const BatchBulkUpload = Vue.component('BatchBulkUpload', require('./components/BatchBulkUpload.vue'));
const AllShipments = Vue.component('AllShipments', require('./components/AllShipments.vue'));
const GroupedOrders = Vue.component('GroupedOrders', require('./components/GroupedOrders.vue'));
const CreditCard = Vue.component('CreditCard', require('./components/CreditCard.vue'));
const Addmoney = Vue.component('Addmoney', require('./components/Addmoney.vue'));
const Userwallet = Vue.component('Userwallet', require('./components/Userwallet.vue'));
const UserProfile = Vue.component('UserProfile', require('./components/UserProfile.vue'));
const AccountProfile = Vue.component('AccountProfile', require('./components/Profile.vue'));


/////////////////////////////////////////
// staff portal 

// Vue.component('Portalcontentbody', require('./staffPortal/PortalContentBody.vue'));
// Vue.component('Portalcontentfooter', require('./staffPortal/PortalContentFooter.vue'));
// Vue.component('Portaltopnav', require('./staffPortal/PortalTopNav.vue'));
// Vue.component('Portalleftnav', require('./staffPortal/PortalLeftNav.vue'));
// Vue.component('Portalbreadcrumb', require('./staffPortal/PortalBreadCrumb.vue'));


// const PortalDashboard = Vue.component('PortalDashboard', require('./staffPortal/PortalDashboard.vue'));
// Vue.component('UserManagmentToolbar', require('./staffPortal/UserManagmentToolbar.vue'));
// Vue.component('UserManagmentForm', require('./staffPortal/UserManagmentForm.vue'));

// const UserManagment = Vue.component('UserManagment', require('./staffPortal/UserManagment.vue'));
//const StaffPortal = Vue.component('StaffPortal', require('./components/staffPortal/StaffPortal.vue'));


/////////////////////////////////////////


const notFound ={
	template:`<div>Not found</div>`
};

const routes = [
    { path: '/', component: dashboard, meta:{conditionalRoute:true}},
    { path: '/shipment/single/pd', component: SingleShipmentPD, meta:{conditionalRoute:true}},
    { path: '/shipment/single/do', component: SingleShipmentDO, meta:{conditionalRoute:true}},
    { path: '/shipment/batchbulk/upload', component: BatchBulkUpload, meta:{conditionalRoute:true}},
    { path: '/shipment/report/all', component: AllShipments, meta:{conditionalRoute:true}},
    { path: '/shipment/report/grouped', component: GroupedOrders, meta:{conditionalRoute:true}},
    { path: '/shipment/creditcard', component: CreditCard, meta:{conditionalRoute:true}},
	{ path: '/shipment/addmoney', component: Addmoney, meta:{conditionalRoute:true}},
    { path: '/shipment/wallet', component: Userwallet, meta:{conditionalRoute:true}},
    { path: '/user/account_profile', component: AccountProfile, meta:{conditionalRoute:true}},
    { path: '/user/profile', component: UserProfile, meta:{conditionalRoute:true}},
    //{ path: '/shipment/checkout', component: CheckoutPage, meta:{conditionalRoute:true}},
	//{ path: '/user/management', component: UserManagment, meta:{conditionalRoute:true}},
    { path:'*', component: notFound},

];

const router = new VueRouter({
    routes, // short for routes: routes

});


const app = new Vue({
    router,
}).$mount('#app');


Vue.http.headers.common['X-CSRF-TOKEN'] = Laravel.csrfToken;
