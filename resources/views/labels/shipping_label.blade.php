<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CBP Delivery</title>
    <!-- <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous"> -->

    <style scoped>
        html{
            font-family: "Arial";
        }
        h5{
            padding: 3px !important;
            margin: 3px;
        }

        table{
            width: 100%;
        }

        
    </style>
</head>
<body>

    <table width="90%">
        <tr>
            
            <td style="text-align: center">
                Actual Weight ({{ strtolower($shipment->weight_unit) }}): {{ $shipment->weight}} <br>
                @if($shipment->length && $shipment->width && $shipment->height)
                Dimension ({{ strtolower($shipment->size_unit) }}): {{ $shipment->length }} X {{ $shipment->width }} X {{ $shipment->height }}
                @endif 
            </td>
        </tr>
        <tr>
            <td style="text-align: left; width: 60%;" >
                <h3>CBP Delivery</h3>
                <h5>www.crossborderpickups.ca</h5>
                <h5>{{ $address_from->address_1 }} {{ $address_from->address_2 }}</h5>
                <h5>{{ $address_from->city }}, {{ $address_from->province }} {{ $address_from->postal }}</h5>
                <h5>{{ $address_from->country }}</h5>


                <table style="margin-top:20px">
                    <tr>
                        <td style="width: 100px"><h5>Shipment ID:</h5></td>
                        <td><h5>{{ $shipment->internal_ship_id }}</h5></td>
                    </tr>

                    <tr>
                        <td style="width: 100px"><h5>Order ID:</h5></td>
                        <td><h5>{{ $shipment->internal_order_id }}</h5></td>
                    </tr>
                    <tr>
                        <td style="width: 100px"><h5>Customer ID:</h5></td>
                        <td><h5>{{ $recipient->id }}</h5></td>
                    </tr>
                    <tr>
                        <td><h5>Sender:</h5></td>
                        <td><h5>{{ $sender->first_name }} {{ $sender->last_name }}</h5></td>
                    </tr>

                    <tr>
                        <td><h5>Recipient: </h5></td>
                        <td><h5>{{ $recipient->first_name }} {{ $recipient->last_name }}</h5></td>
                    </tr>
                    <tr>
                        <td><h5>Address:</h5></td>
                        <td>
                            <h5>{{ $recipient_address->address_2 }} {{ $recipient_address->address_2 }}</h5>
                            <h5>{{ $recipient_address->city }}, {{ $recipient_address->province }} {{ $recipient_address->postal }}</h5>
                            <h5>{{ $recipient_address->country }}</h5>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td colspan="2" style="padding-top: 15pt;">

                <div class="text-center">
                    <img src="data:image/png;base64,{{$barcode_png}}" style="margin-top: 30px; width: 300pt; height: 72pt;" />
                    <br>
                    {{ $shipment->id }} <span style="font-size:30px">{{ $shipment->shipment_code}}</span>
                </div>
            </td>
        </tr>
    </table>

</body>
</html>