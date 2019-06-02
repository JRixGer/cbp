export function showSuccessMsg(msg, headingTxt, timeout) {
    if (typeof(timeout) == 'undefined') {
        timeout = 5000;
    }

    $.toast({
        heading: typeof (headingTxt) != 'undefined' ? headingTxt : 'Success',
        text: msg,
        position: 'top-right',
        loaderBg:'#26c6da',
        icon: 'success',
        hideAfter: timeout
    });
}

export function showErrorMsg(msg, headingTxt, timeout) {
    if (typeof(timeout) == 'undefined') {
        timeout = 5000;
    }

    $.toast({
        heading: typeof (headingTxt) != 'undefined' ? headingTxt : 'Error',
        text: msg,
        position: 'top-right',
        loaderBg:'#ff6849',
        icon: 'error',
        hideAfter: timeout
    });
}

export function handleErrorResponse(status, err)
{
    switch (status) {
        case 422:
            if (typeof(err) != 'undefined' && err.hasOwnProperty('body')) {
                $.each(err.body, (i, row) => {
                    showErrorMsg(row[0]);
                });
            } else {
                showErrorMsg("The given data was invalid.");
            }
            break;
        case 401:
        case 419:
            showErrorMsg("Sorry, your session seems to have expired. Please login again.");
            break;
        default:
            showErrorMsg("Something went wrong while processing your request.");
            break;
    }
}

export function identifyParcelStatus(data)
{
    if (data.status_id == '3') //Incomplete
        return '<span class="mdi mdi-alert-circle red"></span> Incomplete';
    if (data.status_id == '4') //Ready
        return '<span class="mdi mdi-check-circle green"></span> Ready';
    if (data.status_id == '2') //Postage_expired
        return '<span class="mdi mdi-star-circle" style="color: yellow"></span> Postage Expired';
    if (data.status_id == '1') //Unpaid
        return '<span class="mdi mdi-star-circle red"></span> Unpaid';
    if (data.status_id == '5') //Pending
        return '<span class="mdi mdi-pause-circle" style="color: orange"></span> Pending';
    if (data.status_id == '6') //Refunded
        return '<span class="mdi mdi-google-circles-group green"></span> Refunded';
    if (!data.status_id) return '<span>NA</span>';

}

export function getClientOS() {
    var os = null; 

    if(navigator.appVersion.indexOf("Win")!=-1) os="windows";
    if(navigator.appVersion.indexOf("Mac")!=-1) os="mac";
    if(navigator.appVersion.indexOf("X11")!=-1) os="unix";
    if(navigator.appVersion.indexOf("Linux")!=-1) os="linux";

    return os;
}

export function CSVValidator_before(results) {

    var fileInfo = [];
    var t1ctr =  0; // PD
    var t2ctr =  0; // PO
    var ctr =  0;  

    $('.csvfiledrop').hide();
    $('.download-div').hide();
    $('.parsed-csv').show();
    $('.content-div').removeClass('col-md-6');
    $('.content-div').addClass('col-md-12');   
    $('.back-csv').show();   
    $('.import-button').show();

    // PD
    const t1csvHeader = ['FirstName','LastName','BusinessName','AddressLine1','AddressLine2','City','ProvState','IntlProvState','PostalZipCode','Country','Email','Phone','Length','Width','Height','Weight','isSignatureReq','LetterMail','Item1','Qty1','ItemValue1','OriginCountry1','Item2','Qty2','ItemValue2','OriginCountry2','Item3','Qty3','ItemValue3','OriginCountry3','Item4','Qty4','ItemValue4','OriginCountry4','Item5','Qty5','ItemValue5','OriginCountry5','Item6','Qty6','ItemValue6','OriginCountry6','Item7','Qty7','ItemValue7','OriginCountry7','Item8','Qty8','ItemValue8','OriginCountry8','Item9','Qty9','ItemValue9','OriginCountry9','Item10','Qty10','ItemValue10','OriginCountry10','Item11','Qty11','ItemValue11','OriginCountry11','Item12','Qty12','ItemValue12','OriginCountry12','Item13','Qty13','ItemValue13','OriginCountry13','Item14','Qty14','ItemValue14','OriginCountry14','Item15','Qty15','ItemValue15','OriginCountry15','Item16','Qty16','ItemValue16','OriginCountry16','Item17','Qty17','ItemValue17','OriginCountry17','Item18','Qty18','ItemValue18','OriginCountry18','Item19','Qty19','ItemValue19','OriginCountry19','Item20','Qty20','ItemValue20','OriginCountry20'];
    
    // DO
    const t2csvHeader = ['FirstName','LastName','BusinessName','AddressLine1','AddressLine2','City','ProvState','IntlProvState','PostalZipCode','Country','Weight','isSignatureReq','LetterMail','Item1','Qty1','ItemValue1','OriginCountry1','Item2','Qty2','ItemValue2','OriginCountry2','Item3','Qty3','ItemValue3','OriginCountry3','Item4','Qty4','ItemValue4','OriginCountry4','Item5','Qty5','ItemValue5','OriginCountry5','Item6','Qty6','ItemValue6','OriginCountry6','Item7','Qty7','ItemValue7','OriginCountry7','Item8','Qty8','ItemValue8','OriginCountry8','Item9','Qty9','ItemValue9','OriginCountry9','Item10','Qty10','ItemValue10','OriginCountry10','Item11','Qty11','ItemValue11','OriginCountry11','Item12','Qty12','ItemValue12','OriginCountry12','Item13','Qty13','ItemValue13','OriginCountry13','Item14','Qty14','ItemValue14','OriginCountry14','Item15','Qty15','ItemValue15','OriginCountry15','Item16','Qty16','ItemValue16','OriginCountry16','Item17','Qty17','ItemValue17','OriginCountry17','Item18','Qty18','ItemValue18','OriginCountry18','Item19','Qty19','ItemValue19','OriginCountry19','Item20','Qty20','ItemValue20','OriginCountry20'];

    const isEmailValid = function (email) {
        const reqExp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/
        return reqExp.test(email)
    }

    var header = [];
    var headerErrMess = [];
    var dataErrMess = [];
    var isCSVValid = false;

    // validate header here this is using t1csvHeader, need more work later
    document.getElementById('invalidMessages').innerHTML = "";

    $.each(results.meta['fields'], function(i) {
        header.push(results.meta['fields'][i]);
        ctr++;
        if(t1csvHeader.indexOf(results.meta['fields'][i]) < 0)
        {
            console.log(">> 1 ");
            headerErrMess.push(results.meta['fields'][i]+', wrong header.');
            t1ctr++;

        }
        
        if(t2csvHeader.indexOf(results.meta['fields'][i]) < 0)
        {
            console.log(">> 2 ");
            headerErrMess.push(results.meta['fields'][i]+', wrong header.');
            t2ctr++;
        }
        //console.log(results.meta['fields'][i]+' >> '+t1ctr+' >> '+t2ctr);
    });

    if(ctr == 98) // PD
    {

        fileInfo['fileName'] = 'PD';
        fileInfo['isValid'] = true;
        return fileInfo;
    }   
    else if(ctr == 93) // DO
    {

        fileInfo['fileName'] = 'DO';
        fileInfo['isValid'] = true;
        return fileInfo;
    }
    // else if(t1ctr > 0 || t2ctr > 0)
    // {

    //     showErrorMsg("Invalid CSV headers", "Error!", 10000);
    //     fileInfo['fileName'] = 'invalid';
    //     fileInfo['isValid'] = false;
    //     return fileInfo;
    // }


    if(results.data.length == 0)
    {
       showErrorMsg("Invalid CSV -  empty", "Error!", 10000);
       fileInfo['fileName'] = 'invalid';
       fileInfo['isValid'] = false;
       return fileInfo;
    }       

}

export function showHide() {

    if($("#invalidMessages").is(':visible'))
    {
        $("#invalidMessages").hide();
        $("#sh_error").html("click here to see details");
    }
    else  
    {
        $("#invalidMessages").show();
        $("#sh_error").html("click here to hide details");
        $("html, body").animate({ scrollTop: $('#errDiv').offset().top-130 }, 500);
    }
    $("#showErrors_id").show();
}

export function CSVValidator_after(results, f) {

    const check = ['Y','N','y','n','YES','NO','Yes','No','yes','no'];
    const checkCountry = ['US','Us','usa','USA','America','america','CA','ca','Ca','Canada','canada'];
    const checkCACountry = ['CA','ca','Ca','Canada','canada'];
    
    // var address = "";
    // var i = "";
    // console.log(results);
    // for (var key in results) {
    //     if (results.hasOwnProperty(key)) {
    //         address = results[key]['AddressLine1']+', '+results[key]['AddressLine2']+', '+results[key]['City']+', '+results[key]['ProvState']+', '+results[key]['PostalZipCode']+', '+results[key]['Country'];
    //         i = results[key]['#'];
    //         doGeocode(key, address); disabled temporarily

    //     }
    // }

    const isEmailValid = function (email) {
        const reqExp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/
        return reqExp.test(email)
    }

    const isValidPhonenumber = function(value) {
        return (/^\d{10,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
    }


    const isAlpha = function (input) {
        const reqExp = /^[A-Za-z]+$/
        return reqExp.test(input)
    }


    const isAlphaNumeric = function (input) {
        const reqExp = /^[A-Za-z\d\s]+$/
        return reqExp.test(input)
    }

    const isNumeric2Decimal = function (input) {
        const reqExp = /^\d+(\.\d{0,2})?$/
        return reqExp.test(input)
    }

    const isNumeric1Decimal = function (input) {
        const reqExp = /^\d+(\.\d{0,1})?$/
        return reqExp.test(input)
    }

    const isNumeric = function (input) {
        const reqExp = /^\d*$/
        return reqExp.test(input)
    }

    var dataErrMess = [];
    var isCSVValid = false;


    if(results.length == 0)
    {
        showErrorMsg("Invalid CSV -  empty", "Error!", 10000);
        isCSVValid = false;        
        return isCSVValid;
    }       

    // validate data here
    for (var key in results) {
        
        console.log(key +"=="+ (results.length-1))
        if (results.hasOwnProperty(key)) {
          
            var innerObj = results[key];
            var col = 1;
            var cr;
            var country;

            for (var innerKey in innerObj) {

                cr = 'at column:'+col+'/row:'+(parseInt(key)+1);

                if (innerKey == 'FirstName' || innerKey == 'LastName') {
                    
                    if(innerObj[innerKey].length > 64)
                        dataErrMess.push(innerKey+ ': ['+innerObj[innerKey]+'] max limit is 64 characters '+cr);

                    if(!isAlpha(innerObj[innerKey]))
                            dataErrMess.push('Special character / numbers are not allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                }

                if (innerKey == 'BusinessName' || innerKey == 'AddressLine1' || innerKey == 'City' || innerKey == 'ProvState' || innerKey == 'IntlProvState' || innerKey == 'PostalZipCode' || innerKey == 'Country') {

                    if(innerKey == 'Country')
                        country = innerObj[innerKey];

                    if(!isAlphaNumeric(innerObj[innerKey]) && innerObj[innerKey].length > 0)
                        dataErrMess.push('Special characters are not allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                    if ((!innerObj[innerKey] || 0 === innerObj[innerKey].length) && (innerKey != 'BusinessName')) {
                        dataErrMess.push(innerKey+' cannot be empty at '+cr);
                    }
                }

                if (innerKey == 'AddressLine2' && innerObj[innerKey].length > 0) {

                    if(!isAlphaNumeric(innerObj[innerKey]))
                            dataErrMess.push('Special characters are not allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                }


                if (innerKey == 'Length' || innerKey == 'Width' || innerKey == 'Height' || innerKey == 'Weight') 
                {
                    
                    if(!isNumeric1Decimal(innerObj[innerKey]))
                        dataErrMess.push('Only number is allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                    if(innerObj[innerKey].length > 4)
                        dataErrMess.push(innerKey+ ': ['+innerObj[innerKey]+'] max limit is 3 digits '+cr);

                }

                if (innerKey == 'Qty1' || innerKey == 'ItemValue1' || innerKey == 'OriginCountry1' || innerKey == 'Item1' && (jQuery.inArray(country, checkCACountry) === -1)) {

                    if (!innerObj[innerKey] || 0 === innerObj[innerKey].length) {
                        dataErrMess.push(innerKey+' cannot be empty at '+cr);
                    }
                }

                if (innerKey == 'Qty1' && innerObj[innerKey].length > 0)
                {
                    if(!isNumeric(innerObj[innerKey]))
                        dataErrMess.push('Only number is allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                }

                if (innerKey == 'ItemValue1' && innerObj[innerKey].length > 0)
                {
                    if(!isNumeric2Decimal(innerObj[innerKey]))
                        dataErrMess.push('Only number is allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                }

                if(/Qty/.test(innerKey) || /ItemValue/.test(innerKey) || /OriginCountry/.test(innerKey) || /Item/.test(innerKey))
                {
                    if(innerKey!="Qty1" && innerKey!="ItemValue1" && innerKey!="OriginCountry1" && innerKey!="Item1")
                    {
                        if (/Qty/.test(innerKey) && innerObj[innerKey].length > 0)
                        {
                            if(!isNumeric(innerObj[innerKey]))
                                dataErrMess.push('Only number is allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                        }

                        if (/ItemValue/.test(innerKey) && innerObj[innerKey].length > 0)
                        {
                            if(!isNumeric2Decimal(innerObj[innerKey]))
                                dataErrMess.push('Only number is allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                        }

                        if ((/OriginCountry/.test(innerKey) || /Item/.test(innerKey)) && innerObj[innerKey].length > 0)
                        {
                            if(!isAlphaNumeric(innerObj[innerKey]))
                                dataErrMess.push('Special characters are not allowed in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                        }

                    }
                }

                if(f == "PD")
                {
                    if (innerKey == 'Email') 
                    {
                        if(!isEmailValid(innerObj[innerKey]))
                            dataErrMess.push('Invalid email address in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                    }    

                    if (innerKey == 'Phone') 
                    {
                        
                        if(!isValidPhonenumber(innerObj[innerKey]))
                            dataErrMess.push('Invalid Phone format in '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                        if(innerObj[innerKey].length > 13)
                            dataErrMess.push('Phone is not more than 13 digits '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                        if(innerObj[innerKey].length > 0 && innerObj[innerKey].length <10)
                            dataErrMess.push('Phone is at least 10 digits '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);

                        if(innerObj[innerKey].length == 0 && (jQuery.inArray(country, checkCountry) === -1))
                            dataErrMess.push('Phone number cannot be empty '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);
                    }    
                }


                if (innerKey == 'isSignatureReq' && (innerObj[innerKey].length >  0)) 
                {

                    if(jQuery.inArray(innerObj[innerKey], check) === -1)
                        dataErrMess.push('Invalid isSignatureReq value '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);          
                }

                if (innerKey == 'LetterMail' && (innerObj[innerKey].length >  0)) 
                {

                    if(jQuery.inArray(innerObj[innerKey], check) === -1)
                        dataErrMess.push('Invalid LetterMail value '+innerKey+ ': ['+innerObj[innerKey]+'] '+cr);           
                }

               col++;
            }

        }
    }

    if(dataErrMess.length > 0)
    {
          
        var m = '';
        for (var i in dataErrMess) {
            m += '&bull; '+dataErrMess[i]+'<br>';
        }

        document.getElementById('notify-error-id').innerHTML = 'Your file has '+ dataErrMess.length  +' erros, <span id="sh_error">click here to see details</span>';
        document.getElementById('invalidMessages').innerHTML = '<strong>Import Error Details Below:</strong><br><br>'+m;
        $("#notify-error-id").show();
        $("#invalidMessages").hide();
        $("#showErrors_id").hide();
        showErrorMsg("Please fix the error in red text", "Valid CSV with errors!", 10000);
    }
    else
    {
        showSuccessMsg("This is a valid CSV file.", "Valid CSV", 10000);
        document.getElementById('invalidMessages').innerHTML = "";
        $("#invalidMessages").hide();
        $("#showErrors_id").hide();
        $("#notify-error-id").hide();
    }
    $('.csvfiledrop').hide();
    $('.download-div').hide();
    $('.content-div').removeClass('col-md-6');
    $('.content-div').addClass('col-md-12');
    $('.parsed-csv').show();
    $('.back-csv').show();
    $('.import-button').show();
    isCSVValid = true; 
    setTimeout(function(){ var fixedTable = fixTable(document.getElementById('fixed-table-csv')); }, 500);
    //var fixedTable = fixTable(document.getElementById('fixed-table-csv'));
    return isCSVValid;

}

export function orderSummary(results, items) {
    var fixedTable = fixTable(document.getElementById('fixed-table2'));
   
    // console.log(results);
    // var trackingTemp = "";
    // var ctr = 0;
    // var k = 0;
    // var subTotal = 0;
    // for (var key in results) {
    //     if (results.hasOwnProperty(key)) {

    //         if(key > 0 && results[key]['Tracking']==trackingTemp)
    //         {
                
    //             $("#s_fname"+key).css({'display':'none'});
    //             //$("#s_row"+(key-1)).addClass("row-border");
    //             ctr += 1;
    //         }
    //         else
    //         {
    //             k = parseInt(key);
    //             if(k > 0)
    //                 k = k - 1;

    //             subTotal = (ctr+1)*parseFloat((results[k]['TotalFee']));

    //             $('#s_fname'+(key-(ctr+1))).addClass("td-bg"); 
    //             $('#s_fname'+(key-(ctr+1))).attr("data-value",subTotal);
    //             $('#s_fname'+(key-(ctr+1))).attr("data-count",ctr+1); 

    //             $("#s_row"+key).addClass("row-border");
    //             $('#s_fname'+(key-(ctr+1))).attr('rowspan', (ctr+1));
                
    //             if(key > 0 && results[key]['Tracking']!=trackingTemp)
    //             {
    //                 if(ctr == 0)
    //                     $("#td_h"+(key-1)).addClass("row-height-170");
    //                 else if(ctr == 1)
    //                     $("#td_h"+(key-1)).addClass("row-height-140");
    //                 else if(ctr == 2)
    //                     $("#td_h"+(key-1)).addClass("row-height-110");
    //                 else if(ctr == 3)
    //                     $("#td_h"+(key-1)).addClass("row-height-80");
    //             }

    //             ctr = 0;
    //         }
    //         trackingTemp = results[key]['Tracking'];
    //     }
    // }
    // k = parseInt(key);
    // subTotal = (ctr+1)*parseFloat((results[k]['TotalFee']));
    // $('#s_fname'+(key-ctr)).attr("data-value",subTotal); 
    // $('#s_fname'+(key-ctr)).attr("data-count",ctr+1);         
    // $('#s_fname'+(key-ctr)).attr('rowspan', ctr+1);
    // var sid = [];
    

    for (var key in results) {
        if (results.hasOwnProperty(key)) {
            if(results[key]['note']=='Imported with error')
                $("#s_row"+key).addClass("import-error");
            else
                $("#s_row"+key).removeClass("import-error");
        }
    }


    for (var key in items)
       $('#item'+(key)).html(items[key]);  

}

export function doGeocode(index, data) {

    var completeAddress = data;
    var geocoder = new google.maps.Geocoder();
    var css = '';
    geocoder.geocode({
        'address': completeAddress
    }, function(results, status) {
        if (status === google.maps.GeocoderStatus.OK && results.length > 0) 
        {

            console.log('country found: '+data); 
          
            // $("#addressLine1_"+index).removeAttr('style');
            // $("#addressLine2_"+index).removeAttr('style');
            // $("#city_"+index).removeAttr('style');
            // $("#provState_"+index).removeAttr('style');
            // $("#intlProvState_"+index).removeAttr('style');
            // $("#postalZipCode_"+index).removeAttr('style');
            // $("#country_"+index).removeAttr('style');

            css = "'font-size':'12px'";
            $("#addressLine1_"+index).css({css});
            $("#addressLine2_"+index).css({css});
            $("#city_"+index).css({css});
            $("#provState_"+index).css({css});
            $("#intlProvState_"+index).css({css});
            $("#postalZipCode_"+index).css({css});
            $("#country_"+index).css({css});     
        }
        else
        {
            css = "'background-color':'#fffe8363','color':'#ff0202','border-bottom-color':'#ff0202', 'font-weight':'400'";
            console.log('country not found: '+completeAddress); 
            $("#addressLine1_"+index).css({css});
            $("#addressLine2_"+index).css({css});
            $("#city_"+index).css({css});
            $("#provState_"+index).css({css});
            $("#intlProvState_"+index).css({css});
            $("#postalZipCode_"+index).css({css});
            $("#country_"+index).css({css});
        }
    });
}

export function initDropify() {
    $(document).ready(function() {
        $('.dropify').dropify();
    });
}

export function backToCSV() {
    $("#notify-error-id").hide();
    $("#invalidMessages").hide();
    $('.csvfiledrop').show();
    $('.download-div').show();
    $('.content-div').removeClass('col-md-12');
    $('.content-div').addClass('col-md-6');
    $('.parsed-csv').hide();
    $('.back-csv').hide();
    $('.import-button').hide();
    $('.summary-div').hide();
    $('.parsed-csv').hide();
    $('.import-div').show();
    $('.errors-button').hide();
    $('.on-parsing').hide();
    
}

export function validate_data(c, v, f, country) {

    const check = ['Y','N','y','n','YES','NO','Yes','No','yes','no'];
    const checkCountry = ['US','Us','usa','USA','America','america','CA','ca','Ca','Canada','canada'];
    const checkCACountry = ['CA','ca','Ca','Canada','canada'];

    const isEmailValid = function (email) {
        const reqExp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/
        return reqExp.test(email)
    }

    const isValidPhonenumber = function(value) {
        return (/^\d{10,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
    }


    const isAlpha = function (input) {
        const reqExp = /^[A-Za-z]+$/
        return reqExp.test(input)
    }


    const isAlphaNumeric = function (input) {
        const reqExp = /^[A-Za-z,\d\s]+$/
        return reqExp.test(input)
    }

    const isNumeric2Decimal = function (input) {
        const reqExp = /^\d+(\.\d{0,2})?$/
        return reqExp.test(input)
    }

    const isNumeric = function (input) {
        const reqExp = /^\d*$/
        return reqExp.test(input)
    }

    


    var err = 0;
    var c = c;
    var v = v;
   
    if (!c || 0 === c.length) 
        err++;
    
    if (c == 'FirstName' || c == 'LastName') 
    {
        if(!isAlpha(v))
            err++;

        if (v.length > 64)
            err++;
    }
    
 
    if (c == 'BusinessName' || c == 'AddressLine1' || c == 'City' || c == 'ProvState' || c == 'IntlProvState' || c == 'PostalZipCode' || c == 'Country') 
    {
        if(!isAlphaNumeric(v) && v.length > 0)
            err++;
        
        if ((!v || 0 === v.length) && (c != 'BusinessName')) {
            err++;
        }

    }
    
    if (c == 'AddressLine2') 
        if(!isAlphaNumeric(v) && v.length > 0)
            err++;

   
    if (c == 'Length' || c == 'Width' || c == 'Height' || c == 'Weight') 
    {
        if(!isNumeric2Decimal(v))
            err++;

        if(v.length > 4 || v.length == 0)
            err++;

    }

    if (c == 'Qty2' || c == 'Qty3' || c == 'Qty4' || c == 'Qty5' || c == 'Qty6' || c == 'Qty7' || c == 'Qty8' || c == 'Qty9' || c == 'Qty10' || c == 'Qty11' || c == 'Qty12' || c == 'Qty13' || c == 'Qty14' || c == 'Qty15' || c == 'Qty16' || c == 'Qty17' || c == 'Qty18' || c == 'Qty19' || c == 'Qty20') 
    {

        if(!isNumeric(v) && v.length > 0)
            err++;            

        if(v.length > 4)
            err++;
    }

    
    if (c == 'Item2' || c == 'Item3' || c == 'Item4' || c == 'Item5' || c == 'Item6' || c == 'Item7' || c == 'Item8' || c == 'Item9' || c == 'Item10' || c == 'Item11' || c == 'Item12' || c == 'Item13' || c == 'Item14' || c == 'Item15' || c == 'Item16' || c == 'Item17' || c == 'Item18' || c == 'Item19' || c == 'Item20')
    {
        if(!isAlphaNumeric(v) && v.length > 0)
            err++;            
    }


    if (c == 'OriginCountry2' || c == 'OriginCountry3' || c == 'OriginCountry4' || c == 'OriginCountry5' || c == 'OriginCountry6' || c == 'OriginCountry7' || c == 'OriginCountry8' || c == 'OriginCountry9' || c == 'OriginCountry10' || c == 'OriginCountry11' || c == 'OriginCountry12' || c == 'OriginCountry13' || c == 'OriginCountry14' || c == 'OriginCountry15' || c == 'OriginCountry16' || c == 'OriginCountry17' || c == 'OriginCountry18' || c == 'OriginCountry19' || c == 'OriginCountry20')
    {
        if(!isAlphaNumeric(v) && v.length > 0)
            err++;            
    }



    if (c == 'Qty1') 
    {

        if(v.length == 0 && (jQuery.inArray(country, checkCACountry) === -1))
            err++;  
        if(!isNumeric(v))
            err++;            
    }
    
    if (c == 'Item1') 
    {
        if(v.length == 0 && (jQuery.inArray(country, checkCACountry) === -1))
            err++;  

        if(!isAlphaNumeric(v) && v.length > 0)
            err++;             
    }

    if (c == 'OriginCountry1') 
    {
        if(v.length == 0 && (jQuery.inArray(country, checkCACountry) === -1))
            err++;  

        if(!isAlphaNumeric(v) && v.length > 0)
            err++;             
    }

    if (c == 'ItemValue1') 
    {

        if(v.length == 0 && (jQuery.inArray(country, checkCACountry) === -1))
            err++;  

        if(!isNumeric2Decimal(v))
            err++;            
    }

    if (c == 'ItemValue2' || c == 'ItemValue3' || c == 'ItemValue4' || c == 'ItemValue5' || c == 'ItemValue6' || c == 'ItemValue7' || c == 'ItemValue8' || c == 'ItemValue9' || c == 'ItemValue10' || c == 'ItemValue11' || c == 'ItemValue12' || c == 'ItemValue13' || c == 'ItemValue14' || c == 'ItemValue15' || c == 'ItemValue16' || c == 'ItemValue17' || c == 'ItemValue18' || c == 'ItemValue19' || c == 'ItemValue20')
    {

        if(!isNumeric2Decimal(v) && v.length > 0)
            err++;            

        if(v.length > 7)
            err++;
    }


    if (c == 'isSignatureReq' && (v.length >  0)) 
    {

        if(jQuery.inArray(v, check) === -1)
            err++;            
    }

    if (c == 'LetterMail' && (v.length >  0)) 
    {

        if(jQuery.inArray(v, check) === -1)
            err++;            
    }

    
    if(f=="PD")
    {
        if (c == 'Email') 
        {
            if(!isEmailValid(v))
                err++;
        }        

        if (c == 'Phone') 
        {
            
            if(!isValidPhonenumber(v) && v.length > 0)
                err++;
            
            if(v.length > 0 && v.length < 10)
                err++;

            if(v.length > 0 && v.length > 13)
                err++;

            if(v.length == 0 && (jQuery.inArray(country, checkCountry) === -1))
                err++;

        } 
    }

    if(err > 0)
        return 1;
    else
        return 0;
}


export function poaValidateData(c, v) {

    const Alphanumerical = ["business_name","address_1","full_name","name_of_signing_authority_1","office_held_by_signing_authority_1","name_of_signing_authority_2","office_held_by_signing_authority_2","contact_person_title","contact_person_first_name","contact_person_last_name","physical_business_location","physical_city","physical_province_state","physical_postal_zIP_code","type_of_goods","import_number","estimated_annual_value","major_bus_description","major_bus_product_services_1","revenue_from_product_services_1","major_bus_product_services_2","revenue_from_product_services_2","major_bus_product_services_3","revenue_from_product_services_3","business_number","operating_trade","import_export_program_account_name","sign_title","sign_first_name","sign_last_name","sign_tel_no"];
    const Alphabetical = ["city","province_state","country","name_of_municipality","name_of_province_state","name_of_country","physical_country"];
    const Numerical = ["contact_person_work_tel_no","contact_person_ext","contact_person_work_fax_no","contact_person_mobile_no"];
    //const Custom = [""];


    const isEmailValid = function (email) {
        const reqExp = /[a-z0-9._%+-]+@[a-z0-9.-]+\.[a-z]{2,3}$/
        return reqExp.test(email)
    }

    const isValidPhonenumber = function(value) {
        return (/^\d{10,}$/).test(value.replace(/[\s()+\-\.]|ext/gi, ''));
    }


    const isAlpha = function (input) {
        const reqExp = /^[A-Za-z]+$/
        return reqExp.test(input)
    }


    const isAlphaNumeric = function (input) {
        const reqExp = /^[A-Za-z,\d\s]+$/
        return reqExp.test(input)
    }

    const isNumeric2Decimal = function (input) {
        const reqExp = /^\d+(\.\d{0,2})?$/
        return reqExp.test(input)
    }

    const isNumeric = function (input) {
        const reqExp = /^\d*$/
        return reqExp.test(input)
    }

    


    var err = 0;
    var c = c;
    var v = v;
   
    if (!c || 0 === c.length) 
        err++;
      

    if(v.length > 0 && (jQuery.inArray(c, Alphanumerical) !== -1))
    {
        console.log('went here');
        if(!isAlphaNumeric(v))
            err++;
    }

    
    // if (c == 'Email') 
    // {
    //     if(!isEmailValid(v))
    //         err++;
    // }        

    // if (c == 'Phone') 
    // {
        
    //     if(!isValidPhonenumber(v) && v.length > 0)
    //         err++;
        
    //     if(v.length > 0 && v.length < 10)
    //         err++;

    //     if(v.length > 0 && v.length > 13)
    //         err++;

    //     if(v.length == 0 && (jQuery.inArray(country, checkCountry) === -1))
    //         err++;
    // } 

    if(err > 0)
        return 1;
    else
        return 0;
} 
 
export function fixedThisTable(ele = null) {
    $(document).ready(function() {

        console.log('>> here >'+ele)
        if($("#fixed-table0").length > 0)
            var fixedTable = fixTable(document.getElementById('fixed-table0'));
        else if($("#fixed-table1").length > 0)
            var fixedTable = fixTable(document.getElementById('fixed-table1'));
        else if($("#fixed-table2").length > 0)
            var fixedTable = fixTable(document.getElementById('fixed-table2'));
        else if($("#fixed-table3").length > 0)
            var fixedTable = fixTable(document.getElementById('fixed-table3'));
        else if($("#fixed-table4").length > 0)
            var fixedTable = fixTable(document.getElementById('fixed-table4'));

        if($("#"+ele).length > 0)
            var fixedTable = fixTable(document.getElementById(ele));


        $(".btn-primary").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });
        $(".btn-default").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });
        $(".btn-secondary").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });        
    });
}        

 
export function toggleTtooltip() {

    $(document).ready(function(){
        $(".btn-primary").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });
        $(".btn-default").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });
        $(".btn-secondary").click(function(){
            $("[data-toggle='tooltip']").tooltip('hide');
        });        
    });
}        


export function showLoading() {
    $(document).ready(function() {
        $.blockUI({ message: '<div class="overlay"><svg class="circular" viewBox="25 25 50 50"><circle class="path" cx="50" cy="50" r="20" fill="none" stroke-width="2" stroke-miterlimit="10" /></svg></div>',
            css: { 
                border: 'none', 
            }
        });
    });   

}

export function hideLoading() {
    $(document).ready(function() {
        $.unblockUI();
    });   
}

export function checkYN(d){
    const check = ['Y','YES','Yes','yes', '1'];

    if(jQuery.inArray(""+d, check) === -1)
        return 'N';
    else
        return 'Y';

}


export function removeTrash(d,f) {

    if (f == 'FirstName' || f == 'LastName') 
    {
        return (d.replace(/[^a-zA-Z ]/g, ""));
    }        
    else if (f == 'BusinessName' || f == 'AddressLine1' || f == 'AddressLine2' || f == 'City' || f == 'ProvState' || f == 'IntlProvState' || f == 'PostalZipCode' || f == 'Country') 
    {
        return (d.replace(/[^0-9a-zA-Z, ]/g, ""));
    }  
    else if (f == 'OriginCountry1' || f == 'OriginCountry2' || f == 'OriginCountry3' || f == 'OriginCountry4' || f == 'OriginCountry5' || f == 'OriginCountry6' || f == 'OriginCountry7' || f == 'OriginCountry8' || f == 'OriginCountry9' || f == 'OriginCountry10' || f == 'OriginCountry11' || f == 'OriginCountry12' || f == 'OriginCountry13' || f == 'OriginCountry14' || f == 'OriginCountry15' || f == 'OriginCountry16' || f == 'OriginCountry17' || f == 'OriginCountry18' || f == 'OriginCountry19' || f == 'OriginCountry20')
    {
        return (d.replace(/[^0-9a-zA-Z, ]/g, ""));
    }  
    else if (f == 'Qty1' || f == 'Qty2' || f == 'Qty3' || f == 'Qty4' || f == 'Qty5' || f == 'Qty6' || f == 'Qty7' || f == 'Qty8' || f == 'Qty9' || f == 'Qty10' || f == 'Qty11' || f == 'Qty12' || f == 'Qty13' || f == 'Qty14' || f == 'Qty15' || f == 'Qty16' || f == 'Qty17' || f == 'Qty18' || f == 'Qty19' || f == 'Qty20') 
    {
        return (d.replace(/[^0-9]/g, ""));
    }
    else if (f == 'ItemValue1' || f == 'ItemValue2' || f == 'ItemValue3' || f == 'ItemValue4' || f == 'ItemValue5' || f == 'ItemValue6' || f == 'ItemValue7' || f == 'ItemValue8' || f == 'ItemValue9' || f == 'ItemValue10' || f == 'ItemValue11' || f == 'ItemValue12' || f == 'ItemValue13' || f == 'ItemValue14' || f == 'ItemValue15' || f == 'ItemValue16' || f == 'ItemValue17' || f == 'ItemValue18' || f == 'ItemValue19' || f == 'ItemValue20')
    {
        return (d.replace(/[^.0-9]/g, ""));
    }
    else if (f == 'Item1' || f == 'Item2' || f == 'Item3' || f == 'Item4' || f == 'Item5' || f == 'Item6' || f == 'Item7' || f == 'Item8' || f == 'Item9' || f == 'Item10' || f == 'Item11' || f == 'Item12' || f == 'Item13' || f == 'Item14' || f == 'Item15' || f == 'Item16' || f == 'Item17' || f == 'Item18' || f == 'Item19' || f == 'Item20')
    {
        return (d.replace(/[^0-9a-zA-Z ]/g, ""));
    }
    else if (f == 'Length' || f == 'Width' || f == 'Height' || f == 'Weight') 
    {
        return (d.replace(/[^.0-9]/g, ""));
    }
    else if (f == 'Phone') 
    {
        return (d.replace(/[^0-9+]/g, ""));
    }
    else if (f == 'Email') 
    {
        return (d.replace(/[^0-9a-zA-Z@.-]/g, ""));
    }    

} 


export function stripAllHtml(str) {
  if (!str || !str.length) return ''

  str = str.replace(/<script.*?>.*?<\/script>/igm, '')

  let tmp = document.createElement("DIV");
  tmp.innerHTML = str;

  return tmp.textContent || tmp.innerText || "";
}

export function loadTicker(d) {

    jQuery(document).ready(function() {

        if(d == 0)
        {
            $('.show-hide-ticker').hide();
            return;
        }

        jQuery(".modern-ticker").modernTicker({
            effect: "scroll",
            scrollType: "continuous",
            scrollStart: "inside",
            scrollInterval: 20,
            transitionTime: 500,
            linksEnabled: true,
            pauseOnHover: true,
            autoplay: true
        });
        
    });  
}

