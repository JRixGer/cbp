import { AbilityBuilder } from '@casl/ability'

export default AbilityBuilder.define(can => {

    var a = window.abilities;
    var atemp = [];

    //console.log('==>> '+JSON.stringify(a['user_access']));

    if(a['user_access'])
    {
	    console.log(a['user_access']);
        if(a['user_access']['isAbove800'].length > 0)
	    	can(a['user_access']['isAbove800'],'isAbove800');

        if(a['user_access']['isPrintPostage'].length > 0)
        	can(a['user_access']['isPrintPostage'],'isPrintPostage');

        if(a['user_access']['isPrintInvoice'].length > 0)
        	can(a['user_access']['isPrintInvoice'],'isPrintInvoice');

        if(a['user_access']['isPrintBillOfLading'].length > 0)
        	can(a['user_access']['isPrintBillOfLading'],'isPrintBillOfLading');

        if(a['user_access']['isDownload'].length > 0)
        	can(a['user_access']['isDownload'],'isDownload');
    }

})