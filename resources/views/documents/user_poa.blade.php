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
            padding: 5px 5px 0px 5px;
            font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
        }
        p{
            line-height: 0.4;
        }
        .heading{
            text-align: center;
            font-size: 12px;
            margin-bottom: 10px;
            font-weight: bold;
        }

        article p{
            font-size: 11px;
            line-height: normal;
            text-align: justify;
        }

        table{
            font-size: 11px;

        }
        table td{
                /*border: 1px solid #000;*/
            }
    </style>
    <body>
        <div class="heading">
            <p>CONTINUOUS GENERAL AGENCY AGREEMENT AND POWER OF ATTORNEY </p>
            <p>WITH POWER TO APPOINT A SUB-AGENT ("Agency Agreement and Power of Attorney")</p>
        </div>

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
                
                //$signature_1 = (@$userInfo['sender_power_of_atty']['signature_1']) ? strtoupper($userInfo['sender_power_of_atty']['signature_1']) : ""; 

                $signature_1 = empty(@$userInfo['sender_power_of_atty']['signature_1']) ? $blank:$userInfo['sender_power_of_atty']['signature_1'];   


                $name_of_signing_authority_2 = (@$userInfo['sender_power_of_atty']['name_of_signing_authority_2']) ? strtoupper($userInfo['sender_power_of_atty']['name_of_signing_authority_2']) : ""; 
                $office_held_by_signing_authority_2 = (@$userInfo['sender_power_of_atty']['office_held_by_signing_authority_2']) ? strtoupper($userInfo['sender_power_of_atty']['office_held_by_signing_authority_2']) : ""; 

                //$signature_2 = (@$userInfo['sender_power_of_atty']['signature_2']) ? strtoupper($userInfo['sender_power_of_atty']['signature_2']) : ""; 

                $signature_2 = empty(@$userInfo['sender_power_of_atty']['signature_2']) ? $blank:$userInfo['sender_power_of_atty']['signature_2'];   

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
                $sign_signature = (@$userInfo['sender_power_of_atty']['sign_signature']) ? strtoupper($userInfo['sender_power_of_atty']['sign_signature']) : ""; 

                $d = date('\t\h\i\s jS \d\a\y \o\f\ F Y');  

                

                
                if($import_number){
                    $import_number_and_biz_num = strtoupper($import_number.", ".$business_name); 
                }else{
                    $import_number_and_biz_num = "__________________________________________________________________";
                }

                if($address_1){
                    $address = strtoupper($address_1." ".$city." ".$province." ".$postal." ".$country);
                }else{
                    $address = "________________________________________________________________________";
                }


                if($name_of_province_state){
                    $province_country = strtoupper($name_of_province_state.", ".$name_of_country);
                }else{
                    $province_country = "_______________________________";
                }

                if($name_of_municipality){
                    $name_of_mun = strtoupper($name_of_municipality);
                }else{
                    $name_of_mun = "____________________________________________________________";
                }


             ?>
            <p style="text-align: left">
                I/We (Name of Client and Business Number) <u>{{ $import_number_and_biz_num}}</u> of 
                (Address) <u>{{ $address }}</u> ("Client") does/do hereby constitute and appoint (Name of Customs Broker and Business Number) <u>WILLIAM L. RUTHERFORD LIMITED 892673971</u> a Customs Broker licensed under the Customs Act, of (Address) <u>3350 AIRWAY DRIVE, MISSISSAUGA. ONTARIO, CANADA, L4V 1T3</u> ("Customs Broker") as and to be Client's true and lawful agent and attorney, and Client hereby authorizes and directs Customs Broker to transact business on Client's behalf on all matters relating to the import and export of goods, including but not restricted to: <br>

                &nbsp;(a) advance data filing for admissibility purposes, the release of and accounting for goods, document and data preparation, payment of, and receipt of refunds of, all government duties, taxes, penalties, interest or other levies in respect of imported and exported goods reported or released or to be reported or released; and <br>

                &nbsp;(b) arrangement of or the transportation, warehousing and distribution of such goods, <br> and Client does hereby engage Customs Broker to perform such services.
            </p>

            <p>
                AND IN CONNECTION THEREWITH, Client further authorizes and directs Customs Broker, as Client's agent and attorney, to:  <br>
                &nbsp;(a) obtain, sign, seal, endorse and deliver for Client all bonds, entries, permits, bills of lading, bills of exchange, declarations, claims of any nature, or other means of payment or collateral security which comes into Customs Broker's possession and to use same, including drawbacks and claims of any nature, for reimbursement of duties, taxes, penalties, interests or other levies; <br>
                &nbsp;(b) receive all such payments and sums of money as are now due or may hereafter become due and payable to Client relative to the foregoing; and to endorse on Client's behalf and as Client's agent and attorney and to deposit to and for Customs Broker's own account all such payments; and <br>
                &nbsp;(c) obtain from the Canada Border Services Agency ("CBSA") and review Client's CBSA importer profile and other data related to Client's import and export transactions.
            </p>

            <p>
                Client confirms that this Agency Agreement and Power of Attorney (a) constitutes all notices and authorizations required by the Minister of Foreign Affairs and the Trade Controls Bureau in Global Affairs Canada with respect to all matters for which such notices and authorizations are required for an agent or attorney to act on Client's behalf; and (b} authorizes Customs Broker to act on Client's behalf with respect to documentary compliance with all Federal Government programs involving the import or export of goods. 
            </p>

            <p>
                Client further grants Customs Broker, as Client's agent and attorney, full power and authority to (a) appoint as Customs Broker's sub­agent any other person to whom a license to transact business as a customs broker has been issued under the Customs Act (such licensed person being herein called a "Sub-Agent") to transact the aforesaid business, or part thereof, as an agent of Customs Broker and on Client's behalf, (b) revoke any such appointment; and (c} appoint another Sub-Agent in the place of any Sub-Agent whose appointment has been revoked, as Customs Broker, as Client's agent and attorney, shall from time to time think fit. 
            </p>

            <p>
                Client acknowledges that any duties, taxes, penalties, interests, levies or other amounts paid on Client's behalf or to Client's account by Customs Broker, as Client's agent and attorney, or by Sub-Agent for Customs Broker, shall be a debt due by Client to Customs Broker as Client's agent and attorney, and any refund, rebate or remission of such duties, taxes, penalties, interest, levies or other amounts shall be the property of Customs Broker, as Client's agent and attorney, and Client directs and authorizes any governmental agencies collecting same to deliver such rebate, refund or remission to Customs Broker, as Client's agent and attorney. 
            </p>

            <p>
                Client hereby undertakes that, to the best of Client's knowledge, all documents and/or information that will be provided to Customs Broker, as Client's agent and attorney, by Client or on Client's behalf in connection with this mandate, will be true, accurate and complete. 
            </p>

            <p>
                Client hereby agrees that this Agency Agreement and Power of Attorney and all transactions hereunder, are governed by the Standard Trading Conditions attached hereto as Schedule A and forming part of, and incorporated by reference into, this Agency Agreement and Power of Attorney. By signing this Agency Agreement and Power of Attorney, Client acknowledges and agrees to all the terms and conditions set out in the attached Standard Trading Conditions. 
            </p>

            <p>
                Client hereby ratifies and confirms, and agrees to ratify and confirm, all that Customs Broker, as Client's agent and attorney, may do by virtue hereof. 
            </p>

            <p>
                This Agency Agreement and Power of Attorney remains in full force and effect, until due written notice of its revocation has been given to Customs Broker, subject to Section 8 of the attached Standard Trading Conditions. 
            </p>

            <p>
                In witness whereof each of Client and Customs Broker has caused these presents to be sealed with its corporate seal and signed by the signatures of its duly authorized officers or signatories at 
            </p>

            <p>
                (Name of Municipality) {{ $name_of_municipality }} in <br>
                (Name of Province/State and Country) <u>{{ $province_country }}</u>, {{$d}}.
            </p>


            <p>
                <br>
                
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td>{{ $full_name_of_corp_client }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>WILLIAM L. RUTHERFORD LIMITED </td>
                </tr>
                <tr>
                    <td style="border-top: 1px solid #000">Full Corporate Name of Client </td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="border-top: 1px solid #000">Full Corporate Name of Customs Broker</td>
                </tr>
            </table>
            </p>

            <p>
                <br>
            </p>

            <p>
            <table style="width:100%">
                <tr>
                    <td style="width:20px">By:</td>
                    <td>{{ $name_of_signing_authority_1 }}</td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right">Accepted By: </td>
                    <td>JACKIE GREENBERG, CCS </td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Name of Signing Authority (Please print) </td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="border-top: 1px solid #000">Name of Signing Authority (Please print)</td>
                </tr>
            </table>
            </p>
            <p><br></p>
            <p>
            <table style="width:100%">
                <tr>
                    <td style="width:20px"></td>
                    <td>{{ $office_held_by_signing_authority_1 }}</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td>H.O. CUSTOMS MANAGER</td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Office Held by Signing Authority</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="border-top: 1px solid #000">Office Held by Signing Authority </td>
                </tr>
            </table>
            </p>
            <p style="height: 5px">
            </p>

            <p>
            <table style="width:100%">
                <tr>
                    <td style="width:20px"></td>
                    <td><img height="50px" src="<?php echo $signature_1 ?>"></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Signature of Signing Authority</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td style="border-top: 1px solid #000">Signature of Signing Authority </td>
                </tr>
            </table>
            </p>


            <p style="height: 5px">
            </p>



            <p>
            <table style="width:100%">
                <tr>
                    <td style="width:20px">By:</td>
                    <td>{{ $name_of_signing_authority_2 }}</td>
                    <td style="color:#fff">_______________________________________________________</td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Name of Signing Authority (Please print) </td>
                    <td></td>

                </tr>
            </table>
            </p>


            <p style="height: 5px">
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td style="color:#fff; width:20px"></td>
                    <td>{{ $office_held_by_signing_authority_2 }}</td>
                    <td style="color:#fff">_______________________________________________________</td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Office Held by Signing Authority </td>
                    <td></td>

                </tr>
            </table>
            </p>


            <p style="height: 5px">
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td style="color:#fff; width:20px"></td>
                    <td><img height="50px" src="<?php echo $signature_2 ?>"></td>
                    <td style="color:#fff">_______________________________________________________</td>
                </tr>
                <tr>
                    <td style="width:20px"></td>
                    <td style="border-top: 1px solid #000">Signature of Signing Authority  </td>
                    <td></td>

                </tr>
            </table>
            </p>
        </article>
    </body>
</html>
