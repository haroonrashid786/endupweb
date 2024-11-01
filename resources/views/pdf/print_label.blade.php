<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>

</head>

<body style="width: 100%">
    <div style="border-bottom: 2px solid;">
    </div>
    <div style="height: 20.5%; border-left: 2px solid;position: absolute;"></div>
    <table style="float:left; width: 50%; position:absolute; top:24;">
        <tr style="">
            <img src="https://enduptech.tijarah.ae/images/logo.png"
                style="text-align:center;position:absolute; left: 25; width: 100px" alt="">
        </tr>
    </table>
    <table style="float: right; width:40%; border: 1px solid">
        <tr>
            <td style="border-bottom: 1px solid; text-align: center; "><span style="font-size:8px">{{ strtoupper($items[0]['order']['order_type']) }}</span></td>
        </tr>
        <tr>
            <td style="border-bottom: 1px solid; text-align: center;">{{ (isset($items[0]['order']['zone']['name'])) ? $items[0]['order']['zone']['name'] : 'N/A' }}</td>
        </tr>
        @if($items[0]['order']['dropoff_postal'])
        <tr>
            <td style="border-bottom: 1px solid; text-align: center">{{ $items[0]['order']['dropoff_postal'] }}</td>
        </tr>
        @endif
         @if($items[0]['order']['is_grocery'] == 0)
        <tr>

            <td style=" text-align: center">GBP {{ $items[0]['order']['shipping_charges'] }}</td>

        </tr>
        @endif
    </table>
    <div style="border-bottom: 2px solid; margin-top:33.5%">
    </div>
    <table style="border: 1px solid; width: 100%; height: 15px">
        <tr>
            <td style="text-align: center"> Order Number: {{ $items[0]['order']['order_number'] }}</td>
        </tr>
    </table>
    <div style="position: relative">
        <table style=" width: 100%; position:absolute;">
            <tr style="">
                <img src="data:image/png;base64, {!! base64_encode(QrCode::format('svg')->size(100)->generate($qr)) !!} "
                    style="text-align:center;position:absolute; left:20%; width: 60%" alt="">
            </tr>
        </table>

    </div>
    <div style="position: relative">
    <table style="width:100%; border: 1px solid;position: absolute; top:37%">
        <tr>
            <td style=""><b>Recipient</b><br>
                <small>{{ $items[0]['order']['enduser_name'] }} <br>
                {{ $items[0]['order']['enduser_address']  }}</small>
            </td>
        </tr>


    </table>
    </div>
    <div style="position: relative">
    <table style="width:100%; border: 1px solid;position: absolute; top:55%">
        <tr>
            <td style=""><b>Sender</b><br>
                <small>{{ $items[0]['order']['retailer']['user']['name'] }} <br>
                {{ $items[0]['order']['retailer']['address']  }}</small>
            </td>
        </tr>


    </table>
    </div>
    {{-- <div style="border-bottom: 2px solid; margin-top:3%">
    </div> --}}
</body>


</html>
