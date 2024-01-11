<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Order Email</title>
</head>
<body style="font-family: Arial, Helvetica, sans-serif; font-size: 16px;">
@if($mailData['userType'] == 'customer')
    <h1>{{$mailData['subject']}}</h1>
    <h2>Your order id is: #{{ $mailData['order']->id}}</h2>
@else
    <h1>{{$mailData['subject']}}</h1>
    <h2>Order id: #{{ $mailData['order']->id}}</h2>
@endif
    <div class="card-body table-responsive p-3">
        
    <h2 class="h5 mb-3">Shipping Address</h2>
                                            <address>
                                                <strong>{{$mailData['order']->first_name.' '.$mailData['order']->last_name}}</strong><br>
                                                {{$mailData['order']->address}}<br>
                                                {{$mailData['order']->city}}, {{$mailData['order']->zip}},{{countryInfo($mailData['order']->country_id)->name}}<br>
                                                Phone: {{$mailData['order']->mobile}}<br>
                                                Email: {{$mailData['order']->email}}
                                            </address>
                                            <h2>Products</h2>
    <table class="table table-striped" style="width:800px;">
                                            <thead>
                                                <tr style="background:#ccc">                                                
                                                    <th>Product</th>
                                                    <th>Price</th>
                                                    <th>Qty</th>                                        
                                                    <th>Total</th>
                                                </tr>
                                            </thead>
                                            <tbody>

                                                @foreach($mailData['order']->items as $item)
                                                <tr>
                                                    <td>{{$item->name}}</td>
                                                    <td>${{ number_format($item->price,2) }}</td>                                        
                                                    <td>{{$item->qty}}</td>
                                                    <td>${{ number_format($item->total,2) }}</td>
                                                </tr>
                                                @endforeach
                                                <tr>
                                                    <th colspan="3" align="right">Subtotal:</th>
                                                    <td>${{ number_format($mailData['order']->subtotal,2) }}</td>
                                                </tr>

                                                <tr>
                                                    <th colspan="3" align="right">Discount:{{ (!empty($order->coupon_code)) ? '('.$order->coupon_code.')' : '' }}</th>
                                                    <td>${{ number_format($mailData['order']->discount,2) }}</td>
                                                </tr>
                                                
                                                <tr>
                                                    <th colspan="3" align="right">Shipping:</th>
                                                    <td>${{ number_format($mailData['order']->shipping,2) }}</td>
                                                </tr>
                                                <tr>
                                                    <th colspan="3" align="right">Grand Total:</th>
                                                    <td>${{ number_format($mailData['order']->grand_total,2) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
    </div>
</body>
</html>