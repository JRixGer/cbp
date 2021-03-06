<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>
        <!-- <link href="https://fonts.googleapis.com/css?family=lucida:200,600" rel="stylesheet" type="text/css"> -->
    </head>
    <style>
        body{
            padding: 10px;
            font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
        }
        p{
            line-height: normal;
        }
        .heading{
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        article p{
            font-size: 9px;
            line-height: normal;
            text-align: justify;
        }

        table{
            border-collapse: collapse;
            font-size: 9px;

        }
        table.main-tbl td{
                border: 1px solid #000;
                vertical-align: top;
            }

        table.no-border td{
            border: 0;
        }
    </style>
    <body>
        <table style="width: 100%">
            <tr>
                <td><img src="{{ public_path() . '/img/canada-header.png' }}" alt="Logo" width="200px"></td>
                <td style="text-align: right"><b>Protected B</b> when completed</td>
            </tr>
        </table>
        
        <div class="heading">
            <p>Business Number - Import-Export Program Account Information </p>
        </div>
        <p style="text-align: justify; font-size: 10px">
            Fill in this form if you have a business number (BN) and you need to open an import-export program account for commercial purposes. (You do not need to register for an import-export program account for personal importations). Fill in a separate form for each branch or division of your business ·that requires an import-export program account for commercial purposes. Once filled in, send this form to your tax centre. The tax centres are listed at www.cra.gc.ca/taxcentre and in Booklet RC2, The Business Number and Your Canada Revenue Agency Program Accounts. For more information, go to www.cra.gc.ca/bn or call 1·800-959-5525. 
        </p>

        <article>
            <?php 


                //////////////////////////
                $blank = "data:image/jpeg;base64,/9j/4AAQSkZJRgABAQEAYABgAAD/2wBDAAIBAQIBAQICAgICAgICAwUDAwMDAwYEBAMFBwYHBwcGBwcICQsJCAgKCAcHCg0KCgsMDAwMBwkODw0MDgsMDAz/2wBDAQICAgMDAwYDAwYMCAcIDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAwMDAz/wAARCAAZAEMDASIAAhEBAxEB/8QAHwAAAQUBAQEBAQEAAAAAAAAAAAECAwQFBgcICQoL/8QAtRAAAgEDAwIEAwUFBAQAAAF9AQIDAAQRBRIhMUEGE1FhByJxFDKBkaEII0KxwRVS0fAkM2JyggkKFhcYGRolJicoKSo0NTY3ODk6Q0RFRkdISUpTVFVWV1hZWmNkZWZnaGlqc3R1dnd4eXqDhIWGh4iJipKTlJWWl5iZmqKjpKWmp6ipqrKztLW2t7i5usLDxMXGx8jJytLT1NXW19jZ2uHi4+Tl5ufo6erx8vP09fb3+Pn6/8QAHwEAAwEBAQEBAQEBAQAAAAAAAAECAwQFBgcICQoL/8QAtREAAgECBAQDBAcFBAQAAQJ3AAECAxEEBSExBhJBUQdhcRMiMoEIFEKRobHBCSMzUvAVYnLRChYkNOEl8RcYGRomJygpKjU2Nzg5OkNERUZHSElKU1RVVldYWVpjZGVmZ2hpanN0dXZ3eHl6goOEhYaHiImKkpOUlZaXmJmaoqOkpaanqKmqsrO0tba3uLm6wsPExcbHyMnK0tPU1dbX2Nna4uPk5ebn6Onq8vP09fb3+Pn6/9oADAMBAAIRAxEAPwD9/KKKKACiiigAooooAKKKKACiiigAooooAKKKKACiiigAooooAKKKKAP/2Q==";


                $business_name = (@$userInfo['sender_power_of_atty']['business_name']) ? strtoupper($userInfo['sender_power_of_atty']['business_name']) : "";
                $business_number = (@$userInfo['sender_power_of_atty']['business_number']) ? strtoupper($userInfo['sender_power_of_atty']['business_number']) : "";
                $business_location = (@$userInfo['sender_power_of_atty']['physical_business_location']) ? strtoupper($userInfo['sender_power_of_atty']['physical_business_location']) : "";
                $address_1 = (@$userInfo['sender_power_of_atty']['address_1']) ? strtoupper($userInfo['sender_power_of_atty']['address_1']) : "";
                $city = (@$userInfo['sender_power_of_atty']['city']) ? strtoupper($userInfo['sender_power_of_atty']['city']) : "";
                $province = (@$userInfo['sender_power_of_atty']['province']) ? strtoupper($userInfo['sender_power_of_atty']['province']) : "";
                $postal = (@$userInfo['sender_power_of_atty']['postal']) ? strtoupper($userInfo['sender_power_of_atty']['postal']) : "";              
                $country = (@$userInfo['sender_power_of_atty']['country']) ? strtoupper($userInfo['sender_power_of_atty']['country']) : "";   

                $province_state = (@$userInfo['sender_power_of_atty']['province_state']) ? strtoupper($userInfo['sender_power_of_atty']['province_state']) : "";  
                $full_name_of_corp_client = (@$userInfo['sender_power_of_atty']['full_name_of_corp_client']) ? strtoupper($userInfo['sender_power_of_atty']['full_name_of_corp_client']) : ""; 
                $name_of_municipality = (@$userInfo['sender_power_of_atty']['name_of_municipality']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_municipality']) : ""; 
                $name_of_province_state = (@$userInfo['sender_power_of_atty']['name_of_province_state']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_province_state']) : ""; 
                $name_of_country = (@$userInfo['sender_power_of_atty']['name_of_country']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_country']) : ""; 
                $name_of_signing_authority_1 = (@$userInfo['sender_power_of_atty']['name_of_signing_authority_1']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_signing_authority_1']) : ""; 
                $office_held_by_signing_authority_1 = (@$userInfo['sender_power_of_atty']['office_held_by_signing_authority_1']) ? strtoupper($userInfo['sender_power_of_atty']['office_held_by_signing_authority_1']) : ""; 
                $signature_1 = (@$userInfo['sender_power_of_atty']['signature_1']) ? strtoupper($userInfo['sender_power_of_atty']['signature_1']) : ""; 
                $name_of_signing_authority_2 = (@$userInfo['sender_power_of_atty']['name_of_signing_authority_2']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_signing_authority_2']) : ""; 
                $office_held_by_signing_authority_2 = (@$userInfo['sender_power_of_atty']['office_held_by_signing_authority_2']) ? strtoupper($userInfo['sender_power_of_atty']['office_held_by_signing_authority_2']) : ""; 
                $signature_2 = (@$userInfo['sender_power_of_atty']['signature_2']) ? strtoupper($userInfo['sender_power_of_atty']['signature_2']) : ""; 
                $import_number = (@$userInfo['sender_power_of_atty']['import_number']) ? strtoupper($userInfo['sender_power_of_atty']['import_number']) : ""; 
                $language = (@$userInfo['sender_power_of_atty']['language']) ? strtoupper($userInfo['sender_power_of_atty']['language']) : ""; 
                $operating_trade = (@$userInfo['sender_power_of_atty']['operating_trade']) ? strtoupper($userInfo['sender_power_of_atty']['operating_trade']) : ""; 
                $import_export_program_account_name = (@$userInfo['sender_power_of_atty']['import_export_program_account_name']) ? strtoupper($userInfo['sender_power_of_atty']['import_export_program_account_name']) : ""; 
                $physical_business_location = (@$userInfo['sender_power_of_atty']['physical_business_location']) ? strtoupper($userInfo['sender_power_of_atty']['physical_business_location']) : ""; 
                $physical_city = (@$userInfo['sender_power_of_atty']['physical_city']) ? strtoupper($userInfo['sender_power_of_atty']['physical_city']) : ""; 
                $physical_province_state = (@$userInfo['sender_power_of_atty']['physical_province_state']) ? strtoupper($userInfo['sender_power_of_atty']['physical_province_state']) : ""; 
                $physical_postal_zip_code = (@$userInfo['sender_power_of_atty']['physical_postal_zip_code']) ? strtoupper($userInfo['sender_power_of_atty']['physical_postal_zip_code']) : ""; 
                $physical_country = (@$userInfo['sender_power_of_atty']['physical_country']) ? strtoupper($userInfo['sender_power_of_atty']['physical_country']) : ""; 
                $contact_person_title = (@$userInfo['sender_power_of_atty']['contact_person_title']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_title']) : ""; 
                $contact_person_first_name = (@$userInfo['sender_power_of_atty']['contact_person_first_name']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_first_name']) : ""; 
                $contact_person_last_name = (@$userInfo['sender_power_of_atty']['contact_person_last_name']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_last_name']) : ""; 
                $contact_person_work_tel_no = (@$userInfo['sender_power_of_atty']['contact_person_work_tel_no']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_work_tel_no']) : ""; 
                $contact_person_ext = (@$userInfo['sender_power_of_atty']['contact_person_ext']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_ext']) : ""; 
                $contact_person_work_fax_no = (@$userInfo['sender_power_of_atty']['contact_person_work_fax_no']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_work_fax_no']) : ""; 
                $contact_person_mobile_no = (@$userInfo['sender_power_of_atty']['contact_person_mobile_no']) ? strtoupper($userInfo['sender_power_of_atty']['contact_person_mobile_no']) : ""; 
                $transport = (@$userInfo['sender_power_of_atty']['transport']) ? strtoupper($userInfo['sender_power_of_atty']['transport']) : ""; 
                $type_of_goods = (@$userInfo['sender_power_of_atty']['type_of_goods']) ? strtoupper($userInfo['sender_power_of_atty']['type_of_goods']) : ""; 
                $estimated_annual_value = (@$userInfo['sender_power_of_atty']['estimated_annual_value']) ? strtoupper($userInfo['sender_power_of_atty']['estimated_annual_value']) : ""; 
                $major_bus_description = (@$userInfo['sender_power_of_atty']['major_bus_description']) ? strtoupper($userInfo['sender_power_of_atty']['major_bus_description']) : ""; 
                $major_bus_product_services_1 = (@$userInfo['sender_power_of_atty']['major_bus_product_services_1']) ? strtoupper($userInfo['sender_power_of_atty']['major_bus_product_services_1']) : ""; 
                $revenue_from_product_services_1 = (@$userInfo['sender_power_of_atty']['revenue_from_product_services_1']) ? strtoupper($userInfo['sender_power_of_atty']['revenue_from_product_services_1']) : ""; 
                $major_bus_product_services_2 = (@$userInfo['sender_power_of_atty']['major_bus_product_services_2']) ? strtoupper($userInfo['sender_power_of_atty']['major_bus_product_services_2']) : ""; 
                $revenue_from_product_services_2 = (@$userInfo['sender_power_of_atty']['revenue_from_product_services_2']) ? strtoupper($userInfo['sender_power_of_atty']['revenue_from_product_services_2']) : ""; 
                $major_bus_product_services_3 = (@$userInfo['sender_power_of_atty']['major_bus_product_services_3']) ? strtoupper($userInfo['sender_power_of_atty']['major_bus_product_services_3']) : ""; 
                $revenue_from_product_services_3 = (@$userInfo['sender_power_of_atty']['revenue_from_product_services_3']) ? strtoupper($userInfo['sender_power_of_atty']['revenue_from_product_services_3']) : ""; 
                $partner_type = (@$userInfo['sender_power_of_atty']['partner_type']) ? strtoupper($userInfo['sender_power_of_atty']['partner_type']) : ""; 
                $sign_title = (@$userInfo['sender_power_of_atty']['sign_title']) ? strtoupper($userInfo['sender_power_of_atty']['sign_title']) : ""; 
                $sign_first_name = (@$userInfo['sender_power_of_atty']['sign_first_name']) ? strtoupper($userInfo['sender_power_of_atty']['sign_first_name']) : ""; 
                $sign_last_name = (@$userInfo['sender_power_of_atty']['sign_last_name']) ? strtoupper($userInfo['sender_power_of_atty']['sign_last_name']) : ""; 
                $sign_tel_no = (@$userInfo['sender_power_of_atty']['sign_tel_no']) ? strtoupper($userInfo['sender_power_of_atty']['sign_tel_no']) : ""; 
                $sign_signature = empty(@$userInfo['sender_power_of_atty']['sign_signature']) ? $blank:$userInfo['sender_power_of_atty']['sign_signature'];             
                
                $engLang = ($language=='ENGLISH')? "checked='checked'":'';
                $freLang = ($language=='FRENCH')? "checked='checked'":'';

                $importer = ($transport=='IMPORTER')? "checked='checked'":'';
                $exporter = ($transport=='EXPORTER')? "checked='checked'":'';
                $both_imp_exp = ($transport=='BOTH IMPORTER-EXPORTER')? "checked='checked'":'';


                $anOwner = ($partner_type =='AN OWNER')? "checked='checked'":'';
                $corporateDirector = ($partner_type=='A CORPORATE DIRECTOR')? "checked='checked'":'';
                $trusteeOfEstate = ($partner_type=='A TRUSTEE OF AN ESTATE')? "checked='checked'":'';
                $partnerOfPartnership = ($partner_type=='A PARTNER OF A PARTNERSHIP')? "checked='checked'":'';
                $officerOfNonProfitOrg = ($partner_type=='AN OFFICER OF A NON-PROFIT ORGANIZATION')? "checked='checked'":'';
                $thirdPartyRequestor = ($partner_type=='A THIRD PARTY REQUESTOR')? "checked='checked'":'';
                
                $created_at = date('Y-m-j', strtotime(@$userInfo['sender_power_of_atty']['created_at']));
                
             ?>
            <table class="main-tbl" style="width: 100%">
                <tr>
                    <td style="width:10px">1</td>
                    <td colspan="11"><b>Business information </b>(for a corporation, enter the name and address of the head office)</td>
                </tr>
                <tr>
                    <td colspan="8">Business name (Legal name) 
                        <br><br> {{ $business_name }}
                    </td>
                    <td colspan="2">Business number
                        <br><br> {{ $business_number }}
                    </td>
                    <td colspan="2">
                        Language of correspondence <br>
                        <div >
                            <input type="checkbox" <?php echo $engLang ?>> English <input type="checkbox" <?php echo $freLang ?>> French
                        </div>
                    </td>
                </tr>

                <tr>
                    <td colspan="12">
                        Operating, trade, or partnership name (ii different from name above). If you have more than one business or if_ your ousine.ss operates under more than one name, enter the names here. If you need more space, include the information on a separate piece of paper.
                        <br><br>
                        {{ $operating_trade  }}
                    </td>
                </tr>

                <tr>
                    <td colspan="12">
                        If you want to use a separate name for your import-export program account, enter that name here.
                        <br><br>
                        {{ $import_export_program_account_name  }}
                    </td>
                </tr>

                <tr>
                    <td colspan="8">
                        Physical business location
                        <br> {{ $business_location }}
                    </td>
                    <td colspan="4">
                        City
                        <br> {{ $city}}
                    </td>
                </tr>

                <tr>
                    <td colspan="6">
                        Province, territory, or state
                        <br> {{ $province }}
                    </td>
                    <td colspan="4">
                        Country
                        <br> {{ $country}}
                    </td>

                    <td colspan="2">
                        Postal or ZIP Code
                        <br> {{ $postal }}
                    </td>
                </tr>


                <tr>
                    <td colspan="10">
                        Mailing address (ii different from physical business location) for import-export purposes.
                        <br>c/o
                    </td>
                    <td colspan="2">
                        City
                        <br> {{ $city}}
                    </td>
                </tr>

                <tr>
                    <td colspan="6">
                        Province, territory, or state
                        <br> {{ $province}}
                    </td>
                    <td colspan="4">
                        Country
                        <br> {{ $country}}
                    </td>

                    <td colspan="2">
                        Postal or ZIP Code
                        <br> {{ $postal}}
                    </td>
                </tr>

                 <tr>
                    <td colspan="12">
                        <b>Contact Person - </b> Please provide the name of a contact for <b>registration purposes only</b> (the contact name provided will not be considered authorized representative). A contact person does not have authority unless they are also an authorized representative or a delegated authority. If the contact person does not have authority authority in the business number program account, they cannot change information and we cannot share information. If you wish to authorized the representative to deal with the Canada Revenue Agency (CRA) about your BN program accounts, fill in Form RC59; <i>Business Consent</i> or Form RC321, <i>Delegation of Authority.</i> For more information, see Booklet RC2, <i>The Business Number of your Canada Revenue Agency Program Accounts.</i>
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        Title
                        <br><br>
                        {{ $contact_person_title }}
                    </td>
                    <td colspan="4">
                        First Name
                        <br><br>
                        {{ $contact_person_first_name }}
                    </td>

                    <td colspan="4">
                        Last Name
                        <br><br>
                        {{ $contact_person_last_name }}
                    </td>
                </tr>

                <tr>
                    <td colspan="4">
                        Work telephone number
                        <br><br>
                        {{ $contact_person_work_tel_no }}
                    </td>
                    <td colspan="1">
                        Ext.
                        <br><br>
                        {{ $contact_person_ext }}
                    </td>

                    <td colspan="4">
                        Work fax number
                        <br><br>
                        {{ $contact_person_work_fax_no }}
                    </td>

                    <td colspan="3">
                        Mobile telephone number
                        <br><br>
                        {{ $contact_person_mobile_no }}
                    </td>
                </tr>

                <tr>
                    <td>2</td>
                    <td colspan="11"><b>Import-export information</b></td>
                </tr>

                <tr>
                    <td colspan="12">
                        <table class="no-border" width="100%">
                            <tr>
                                <td>Type of account: </td>
                                <td><input type="checkbox" <?php echo $importer ?>> Importer</td>
                                <td><input type="checkbox" <?php echo $exporter ?>> Exporter</td>
                                <td><input type="checkbox" <?php echo $both_imp_exp ?>> Both Importer-exporter</td>
                                <td><input type="checkbox"> Meeting, convention, and incentive travel</td>
                            </tr>
                        </table>
                        If you are applying for an exporter account, you must enter all of the following information: <br>
                        Enter the type of goods you are or will be exporting:
                        <br>
                        <br>
                        {{ $type_of_goods }}
                        <hr>
                        &nbsp;
                        <hr>
                        Enter the estimated annual value of goods you are or will be exporting: {{ $estimated_annual_value }} <br>
                    </td>
                </tr>


                 <tr>
                    <td>3</td>
                    <td colspan="11"><b>Major business activity</b></td>
                </tr>

                <tr>
                    <td colspan="12">
                        Describe your major business activity with as much detail as possible. Use at least a noun, a verb, and an adjective to describe your activity. <br>
                        Example: Construction - Installing residential hardwood flooring.
                        <br><br>
                        {{ $major_bus_description }}
                        <hr>
                        Specify up to three main products or services that you provide and the estimated percentage of revenue they each represent. 
                        <br>
                        {{ $major_bus_product_services_1 }}
                        <br>
                        {{ $major_bus_product_services_2 }}
                        <hr>
                        {{ $major_bus_product_services_3 }}
                    </td>
                </tr>


                <tr>
                    <td>4</td>
                    <td colspan="11"><b>Certification</b></td>
                </tr>

                <tr>
                    <td colspan="12">
                        All businesses must fill in and sign this part in order for the form to be processed. After you register your CRA program account we may contact you to confirm the information you provided. At that time we may ask you to provide more information. We can server you better when you have complete and valid information on file for your business. 
                        <br>
                        <br>

                        The individual signing this form is:
                        <br>
                        <table class="no-border" style="width: 100%">
                            <tr>
                                <td><input type="checkbox" <?php echo $anOwner ?>> an owner</td>
                                <td><input type="checkbox" <?php echo $corporateDirector ?>> a corporate director</td>
                                <td><input type="checkbox" <?php echo $trusteeOfEstate ?>> a trustee of an estate</td>
                            </tr>

                            <tr>
                                <td><input type="checkbox" <?php echo $partnerOfPartnership ?>> a partner of a partnership</td>
                                <td><input type="checkbox" <?php echo $officerOfNonProfitOrg ?>> an officer of a non-profit organization</td>
                                <td><input type="checkbox" <?php echo $thirdPartyRequestor ?>> a third party requestor</td>
                            </tr>
                        </table>

                        <table class="no-border" style="width: 100%">
                            <tr>
                                <td style="width: 70px">First Name:</td>
                                <td style="border-bottom: 1px solid #000">{{ $sign_first_name }}</td>
                                <td style="width: 70px">Last Name:</td>
                                <td style="border-bottom: 1px solid #000">{{ $sign_last_name }}</td>
                            </tr>

                            <tr>
                                <td  style="width: 40px">Title:</td>
                                <td style="border-bottom: 1px solid #000">{{ $sign_title }}</td>
                                <td  style="width: 100px">Telephone number:</td>
                                <td style="border-bottom: 1px solid #000">{{ $sign_tel_no }}</td>
                            </tr>
                        </table>

                        I certify that the information given on this form is correct and complete. <br>

                        <table class="no-border" style="width: 100%">
                            <tr>
                                <td style="width: 50px; vertical-align: bottom;">Signature:</td>
                                <td style="border-bottom: 1px solid #000"><img height="50px" src="<?php echo $sign_signature ?>"></td>
                                <td style="width: 120px; vertical-align: bottom;" >Date (YYYY-MM-DD):</td>
                                <td style="border-bottom: 1px solid #000; vertical-align: bottom;"> {{ $created_at }} </td>
                            </tr>
                        </table>
                        <br>


                       
                    </td>
                </tr>
            </table>
            
            <p style="font-size: 9px">
                Personal information is collected under the  <i>Income Tax Act</i> and <i>Customs Act</i> to administer tax, benefits, and related programs. It may also be used for any purpose related to the administration or enforcement of these Ads such audit, compliance and the payment of debts owed to the Crown. It may be shared or verified with other federal, provincial/territorial government institutions to the extent authorized by law. Failured to provide this information may result in interest payable, penaltyies or other actions. Under <i>Privacy Act,</i> individuals have the right to access their personal information and request correction if there are errors or omissions. Refer to Info Source www.cra-arc.gc.ca/gyncy/tp/nfsrc/nfsrc-eng.html, personal information bank CRA PPU 223.
            </p>
            <br>

            <table style="width: 100%; font-size: 9px">
                <tr>
                    <td style="width: 100px">RC1C E (15)</td>
                    <td>(Vous pouvez obtenir ce formufaire en francais a www.arc.gc.ca/formulaires ou en composant le 1-800-959-7775.)</td>
                    <td style="width: 70px"><img src="{{ public_path() . '/img/canada-logo-footer.png' }}" alt="Logo" width="70px"></td>
                </tr>
            </table>


        </article>

    </body>
</html>
