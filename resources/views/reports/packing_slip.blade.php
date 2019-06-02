<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Packing Slip</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->
</head>
<body>

    <!-- 'shipment','address_from','recipient','recipient_address', 'sender', 'items' ,'barcode_png' -->
    <?php

        if($sender_business->business_name)
        {

            $business_info = $sender_business->business_name .'<br>'.$sender_business->address_1;
            $city = $sender_business->city;
            $province = $sender_business->province;
            $postal = $sender_business->postal;
            $country = $sender_business->country;
        }
        else
        {
            $business_info = $address_from->address_1;;
            $city = $address_from->city;
            $province = $address_from->province;
            $postal = $address_from->postal;
            $country = $address_from->country;

        }

    ?>

    <table width="100%">
        <tr>
            <td style="text-align: right; font-size: 18px; padding-bottom: 0px;" colspan="4">
                <strong>PACKING SLIP</strong>
            </td>
        </tr>
        <tr>
            <td style="text-align: right; font-size: 12px; padding-bottom: 10px" colspan="4">
                {{ date('F j, Y') }}
            </td>
        </tr>

        <tr>
            <td style="text-align: left; font-size: 28px; padding-bottom: 50px; color: #7DACCE" colspan="4">
                Crossing Borders Management Inc.
            </td>
        </tr>


        <tr>
            
            <td style="text-align: left; font-size: 13px; width: 18%" valign="top">
                <strong>Shipper:</strong>
            </td>
            <td style="text-align: left; font-size: 13px; width: 32%" valign="top">
                {{ $business_info }}<br>
                {{ $city }}, {{ $province }} {{ $postal }}<br>
                {{ $country }}
            </td>

            <td style="text-align: left; font-size: 13px; width: 18%" valign="top">
                <strong>Ship To:</strong>
            </td>
            <td style="text-align: left; font-size: 13px; width: 32%" valign="top">
                {{ $recipient->first_name }} {{ $recipient->last_name }}<br>
                {{ $recipient->company }}<br>
                {{ $recipient_address->address_1 }}<br>
                {{ $address_from->city }}, {{ $address_from->province }} {{ $address_from->postal }}<br>
                {{ $recipient_address->country }}<br>
                {{ $recipient->contact_no }}<br>
                
            </td>


        </tr>

        <tr>

            <td style="text-align: left; font-size: 13px; width: 18%" valign="top">
                <table width="100%" cellpadding="0"  cellspacing="0">
                    <tr>
                        <td>
                            <strong>Order Date: </strong> 
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Order Number: </strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <strong>Shipment Id: </strong>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            
                        </td>
                    </tr>                    
                </table>
            </td>
            <td style="text-align: left; font-size: 13px; width: 32%" valign="top">
                <table width="100%" cellpadding="0"  cellspacing="0">
                    <tr>
                        <td>
                            {{ \Carbon\Carbon::parse($shipment->created_at)->format('d/m/Y')}}
                        </td>
                    </tr>
                    <tr>
                        <td>
                           {{ $shipment->internal_order_id }}
                        </td>
                    </tr>
                    <tr>
                        <td>
                             {{ $shipment->internal_ship_id }}
                        </td>
                    </tr>                    
                    <tr>
                        <td>
                             
                        </td>
                    </tr>
                </table>

            </td>

            <td style="text-align: left; font-size: 13px; width: 18%" valign="top">
                <strong>Bill To: </strong> 
            </td>
            <td style="text-align: left; font-size: 13px; width: 32%" valign="top">
                {{ $recipient->first_name }} {{ $recipient->last_name }}<br>
                {{ $recipient->company }}<br>
                {{ $recipient_address->address_1 }}<br>
                {{ $recipient_address->city }}, {{ $recipient_address->province }} {{ $recipient_address->postal }}<br>
                {{ $recipient_address->country }}<br>
                {{ $recipient->contact_no }}<br>
            </td>

        </tr>
        <tr>
            <td style="text-align: left; width: 100%;" colspan="4">
                <table width="100%" cellpadding="0"  cellspacing="0">
                    <tr>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600; background-color: #878787; color: #fff" align="center">Item Description</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600; background-color: #878787; color: #fff" align="center">Value</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600; background-color: #878787; color: #fff" align="center">Quantity</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600; background-color: #878787; color: #fff" align="center">Amount</td>
                    </tr>
                    <?php
                        $total = 0;
                        $totalValue = 0;
                        $ctr = 0
                    ?>
                    @foreach($items as $item)
                    <?php
                        $total += $item->qty;
                        $totalValue += ($item->value * $item->qty);
                        $ctr++;
                    ?>
                    <tr>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px" align="center">{{ $item->description }}</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px" align="center">{{ $item->value }}</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px" align="center">{{ $item->qty }}</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px" align="center">{{ $item->value * $item->qty }}</td>
                    </tr>
                    @endforeach
                    <tr>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600" colspan="2" align="right">Total</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600" align="center">{{ $total }}</td>
                        <td style="font-size: 13px; border: solid 1px #ccc; padding: 2px; font-weight: 600" align="center">{{ $totalValue }}</td>
                    </tr>                       
                </table>
            </td>
        </tr>
        <!--         
        <tr>
            <td style="text-align: left; width: 100%; padding-top: 100px;" colspan="4">
                <table width="100%">
                   <tr>
                        <td style="text-align: center; padding-bottom: 30px; font-size: 14px">
                            <strong>Received in Apparent Good Order</strong>
                        </td>
                    </tr>                 
                    <tr>
                        <td style="font-size: 13px; padding: 10px">Recieved By: ______</td>
                    </tr>
                    <tr>
                        <td style="font-size: 13px; padding: 10px">Date:______</td>
                    </tr>                    
                </table>
            </td>
        </tr>  
        -->       
    </table>

</body>
</html>