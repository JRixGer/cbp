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
            padding: 20px;
            font-family: "Lucida Sans Unicode", "Lucida Grande", sans-serif;
        }
        p{
            line-height: 0.5;
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


                if(@$business_name){
                    $client_and_biz_num = strtoupper($business_name." ".$business_number); 
                }else{
                    $client_and_biz_num = "__________________________________________________________________";
                }

                if(@$address_1){
                    $address = strtoupper($address_1." ".$address_2." ".$city." ".$province." ".$postal." ".$country);
                }else{
                    $address = "________________________________________________________________________";
                }

                if(@$province){
                    $province_country = strtoupper($province.", ".$country);
                }else{
                    $province_country = "_______________________________";
                }



             ?>
            <p style="text-align: left">
                I/We (Name of Client and Business Number) <u>{{ $client_and_biz_num}}</u> of 
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
                (Name of Municipality) ____________________________________________________________________ in <br>
                (Name of Province/State and Country) <u>{{ $province_country }}</u>, this ____ day of _____ ___J 20 
            </p>

            <p>
                <br>
                
            </p>
            <p>
            <table style="width:100%">
                <tr>
                    <td></td>
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
                <br><br>
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td>By:</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td style="text-align: right">Accepted By: </td>
                    <td>JACKIE GREENBERG, CCS </td>
                </tr>
                <tr>
                    <td></td>
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
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td></td>
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
                    <td></td>
                    <td></td>
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



            <p><br></p>
            <p>
            <table style="width:100%">
                <tr>
                    <td>&nbsp;</td>
                    <td>&nbsp;</td>
                    <td></td>
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
                    <td></td>
                    <td></td>
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


            <p>
                <br>
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td>By:</td>
                    <td></td>
                    <td style="color:#fff">_______________________________________</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="border-top: 1px solid #000">Name of Signing Authority (Please print) </td>
                    <td></td>

                </tr>
            </table>
            </p>


            <p>
                <br>
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td style="color:#fff">___</td>
                    <td></td>
                    <td style="color:#fff">_______________________________________</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="border-top: 1px solid #000">Office Held by Signing Authority </td>
                    <td></td>

                </tr>
            </table>
            </p>


            <p>
                <br>
            </p>


            <p>
            <table style="width:100%">
                <tr>
                    <td style="color:#fff">___</td>
                    <td></td>
                    <td style="color:#fff">_______________________________________</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="border-top: 1px solid #000">Signature of Signing Authority  </td>
                    <td></td>

                </tr>
            </table>
            </p>





        </article>

    </body>
</html>
