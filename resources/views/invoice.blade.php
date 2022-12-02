<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1" />
    <link rel="stylesheet" type="text/css" href="http://cdnjs.cloudflare.com/ajax/libs/sweetalert/1.1.3/sweetalert.css">
    <meta content='width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=0, shrink-to-fit=no' name='viewport' />
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <!--     Fonts and icons     -->

    <link href="https://fonts.googleapis.com/css?family=Montserrat:400,700,200" rel="stylesheet" />
    <link rel="stylesheet" type="text/css" href="{{asset('css/bootstrap.min.css')}}">    
    <link rel="stylesheet" type="text/css" href="{{asset('css/voucher.css')}}">    
    <link rel="stylesheet" type="text/css" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://pro.fontawesome.com/releases/v5.10.0/css/all.css" integrity="sha384-AYmEC3Yw5cVb3ZcuHtOA93w35dYTsvhLPVnYs9eStHfGJvOvKxVfELGroGkvsg+p" crossorigin="anonymous"/>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@500&display=swap" rel="stylesheet">
    <style type="text/css">
        *{
            font-family: 'Roboto', sans-serif;
        }
        body{
            color: #005EBE;
            -webkit-print-color-adjust:exact;
        }
        .dark{
            color: black;
            font-weight: bold;
        }
        /*.card-header h2,h4{
            text-align: center;
        }*/
        .signature{
            width:200px;
            height: 100px;
            border: 2px solid black;
            border-radius: 10px;
        }
        .head-top{
            text-align: center;
            background-color: #0060C4;
            color:white;
            width: 83px;
            margin: 0;
        }
        .head-top th{
            padding: 2px;
        }
        .head-top p{
            margin: 0 auto;
        }
        .head-bottom{
            text-align: center;
        }
        .head-bottom p{
            margin: 0 auto;
        }
        .head-bottom th{
            padding:2px;
        }
        .number{
            width:5%;
        }
        .particular{
            width:40%;
        }
        .qty{
            width:10%;
        }
        .price{
            width: 20%;
        }
        table{
            height: 500px;
        }
    </style>

</head>
<body style="background-color: #473C70;">
 
 <div class="offset-xl-2 col-xl-8 col-lg-12 col-md-12 col-sm-12 col-12 padding">
     <div class="card printableArea shadow" style="border: 7px solid #005EBE;height:100%;border-radius: 20px">
         <div class="card-header p-4">
             <div style="display:flex;justify-content: space-between;">
                 <div>
                     <img src="{{asset('image/logo.jpg')}}" width="140" height="140" />
                 </div>
                 <div>
                     <h1 class="text-center font-weight-bold"><img src="{{asset('image/waihinoo-name.png')}}" /> <i> (Mobile)</i></h1>
                     <h4 class="text-center">Sales & Service Center</h4>
                     
                 </div>
                 <div>
                     <img src="{{asset('image/logo.jpg')}}" width="140" height="140" />
                 </div>
             </div>
             <p class="text-center">အမှတ် (၂၇၆)၊ အလောင်းဘုရားလမ်းမနှင့် (၁၁)လမ်းထောင့်၊ (၁)ရပ်ကွက်၊မရမ်းကုန်းမြို့နယ်၊ရန်ကုန်မြို့။</p>
            <p class="text-center font-weight-bold"><i class="fas fa-mobile-alt"></i> : 09-965100514, 09-448008522, 09-795505099</p>
         </div>
         <div class="card-body">
             <div class="row">
                <div class="col-sm-12 py-3">
                    <p style="display:inline;" class="dark">Invoice No : </p>
                    <span>{{$voucher->voucher_number}}</span>
                </div>
             </div>
             <div style="display:flex;">
                 <div class="py-0">
                     <p class="dark">Name &nbsp; &nbsp;  : ................................................................................................................</p> 
                 </div>
                 <div class="py-0 ml-5">
                    <p class="dark">Date : {{date('d-m-Y')}}</p> 
                 </div>
             </div>
             <div style="display:flex;justify-content:space-between" class="mb-4 py-0">
                 <div>
                     <p class="dark">Address : ...................................................................................................................</p> 
                 </div>
                 <div class="py-0">
                    <p class="dark"><i class="fas fa-2x fa-phone-square-alt"></i> ..................................................</p> 
                 </div>
             </div>
             <div class="table-responsive-sm">
                 <table class="table table-bordered" cellspacing="1" bordercolor="#0060C4" style="width: 100%;height=1000px;border-spacing: 2px;border-collapse: separate;">
                     <thead>
                         <tr class="head-top">
                             <th><p  style="text-align: center;margin:0;padding:2px;">No</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">Particular</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">Quantity</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">Unit Price</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">Amount</p></th>
                         </tr>
                         <tr class="head-bottom">
                             <th><p  style="text-align: center;margin:0;padding:2px;">စဥ်</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">အမျိုးအမည်</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">အရေအတွက်</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">နှုန်း</p></th>
                             <th><p  style="text-align: center;margin:0;padding:2px;">သင့်ငွေ</p></th>
                         </tr>
                     </thead>
                     <tbody>
                        <?php $i=1; ?>
                        @if( $voucher->item_list != null )
                        @foreach( $voucher->item_list as $item_list )
                        <tr>
                             <td style="text-align: center;" class="number">{{ $i++ }}</td>
                             <td style="text-align: center;" class="particular left strong">{{ $item_list->name }}</td>
                             <td style="text-align: center;" class=" qty center">{{ $item_list->order_qty }}</td>
                             <td style="text-align: center;" class="price right">{{ $item_list->selling_price }}</td>
                             <td style="text-align: center;" class="price right">{{ $item_list->selling_price * $item_list->order_qty }}</td>
                        </tr>
                         @endforeach
                         @endif
                         @if( $voucher->accessory_list != null )
                         @foreach( $voucher->accessory_list as $accessory_list )
                         <tr>
                             <td style="text-align: center;" class="number center">{{ $i++ }}</td>
                             <td style="text-align: center;" class="particular left strong">{{ $accessory_list->name }}</td>
                             <td style="text-align: center;" class="qty center">{{ $accessory_list->order_qty }}</td>
                             <td style="text-align: center;" class="price right">{{ ($accessory_list->selling_price != 0) ? $accessory_list->selling_price : "FOC" }}</td>
                             <td style="text-align: center;" class="price right">{{ ($accessory_list->selling_price != 0) ? ($accessory_list->selling_price * $accessory_list->order_qty) : "FOC" }}</td>
                         </tr>
                         @endforeach
                         @endif
                         <?php $j = 21 - ($i*2) -3 ?>
                         @for($b = 0;$j>$b;$b++)
                         <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                         @endfor
                         <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                             <td class="text-center text-primary">
                                 <strong>Total Amount</strong>
                             </td>
                             <td class="text-center">
                                 <strong>{{ $voucher->total_amount }}</strong>
                             </td>
                         </tr>
                         <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center text-primary">
                                <strong>Discount({{($voucher->cashback_type == 1) ? "Phone" : "Accessories"}})</strong>
                            </td>
                            <td class="text-center">
                                <strong>{{$voucher->cashback_amount}}</strong>
                            </td>
                        </tr>
                       <!-- <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center text-primary">
                                <strong>Tax</strong>
                            </td>
                            <td class="text-center">
                                <strong>{{$voucher->tax}}</strong>
                            </td>
                        </tr>-->
                        <tr>
                            <td></td>
                            <td></td>
                            <td></td>
                            <td class="text-center text-primary">
                                <strong>Net Amount</strong>
                            </td>
                            <td class="text-center">
                                <strong>{{ $voucher->voucher_grand_total }}</strong>
                            </td>
                        </tr>
                     </tbody>
                 </table>
             </div>
        </div>

        <!--<div class="container">-->
        <!--    <div class="row justify-content-between">-->
        <!--            <h2 style="margin-left:100px">Sales Person</h2>-->
        <!--            <h2 style="margin-right:165px">Owner</h2>-->
        <!--     </div>-->
        <!--           <br><br>-->
        <!--    <div class="row">-->
        <!--        <hr style="background-color: blue;width: 200px;margin-left: 100px;">-->
        <!--        <hr style="background-color: blue;width: 200px;margin-right: 100px;">-->
        <!--    </div>-->
        <!--</div>-->

         <div class="card-footer bg-white" style="display:flex;justify-content:space-between;">
            <div>
                <p><i class="fas fa-cannabis"></i> ဝယ်ယူသည့် ပစ္စည်းစမ်းသပ်စစ်ဆေးပါ</p>
                <p><i class="fas fa-cannabis"></i> ဝယ်ပြီးပစ္စည်း ငွေပြန်မအမ်းပါ</p>
                <p><i class="fas fa-cannabis"></i> အားပေးမှုကို ကျေးဇူးတင်ပါသည်</p>
            </div>
            <div style="display:flex;flex-direction:column;">
                <h3 style="text-align:center;">ရောင်းသူလက်မှတ်<h3>
                <div class="signature"></div>
            </div>
         </div>
     </div>
     </div>
     
 </div>

    

 <script src="{{asset('js/jquery.min.js')}}"></script>
 <script src="{{asset('js/bootstrap.min.js')}}"></script>
 <script src="{{asset('js/jquery.PrintArea.js')}}" type="text/JavaScript"></script>

 <script type="text/javascript">
     $("#print").click(function() {
            
            var modes = { iframe : "iframe", popup : "popup" };
            var standards = { strict : "strict", loose : "loose", html5 : "html5" };
            var defaults = { mode: modes.iframe,
             standard   : standards.html5,
             popHt      : 10,
             popWd      : 10,
             popX       : 100,
             popY       : 100,
             popTitle   : '',
             popClose   : false,
             extraCss   : '',
             extraHead  : '',
             retainAttr : ["id","class","style"] };
            $("div.printableArea").printArea(standards);
        });
 </script>
</body>
</html>