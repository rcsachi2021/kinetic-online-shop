<?php

namespace App\Http\Controllers;

use App\Events\OrderCreated;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\DiscountCoupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\ShippingCharge;
use Illuminate\Http\Request;
use Gloudemans\Shoppingcart\Facades\Cart;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;



class CartController extends Controller
{
    public function cart()
    {
        $cartItems = Cart::content();
        return view('front.cart', compact('cartItems'));
    }

    public function addToCart(Request $request)
    {
        $id = $request->id;
        $product = Product::with('product_images')->find($id);
        if(empty($product))
        {
            return response()->json([
                'status' => false,
                'message' => 'Product not found'
            ]);
            
        }
        if(Cart::count() > 0){

            $cartItems = Cart::content();
            foreach($cartItems as $item)
            {
                if($item->id == $product->id){
                    $status = false;
                    $message = $item->name.' already exists in cart';
                }
                else{
                    Cart::add($product->id, $product->title, 1, $product->price,['product_image'=>(!empty($product->product_images)) ? $product->product_images->first() : '']);
                    $status = true;
                    $message = $product->title.' Product added';
                }
            }
            
        }else{
            Cart::add($product->id, $product->title, 1, $product->price,['product_image'=>(!empty($product->product_images)) ? $product->product_images->first() : '']);
            $status = true;
            $message = $product->title.' Product added';
        }
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
        //Cart::add('293ad', 'Product 1', 1, 9.99);      
        
    }

    public function updateCart(Request $request)
    {
        $rowId  = $request->rowId;
        $qty    = $request->qty;
        $itemDet = Cart::get($rowId);
        $itemID  = $itemDet->id;
        $product = Product::find($itemID);
        if($product->track_qty == 'Yes')
        {
            if($qty <= $product->qty){
                Cart::update($rowId, $qty);
                $message = 'Cart updated successfully!';
                $status  = true;
                session()->flash('success',$message);
            }else{
                $status = false;
                $message = 'Requested quantity('.$qty.') not available in the stock';
                session()->flash('error',$message);
            }
        }else{
            Cart::update($rowId, $qty);
            $status = true;
            $message = 'Cart updated successfully!';
            session()->flash('success',$message);
        }        
        
        return response()->json([
            'status' => $status,
            'message' => $message
        ]);
    }

    public function deleteItem(Request $request)
    {
        $itemInfo = Cart::get($request->rowId);
        if($itemInfo == null){
            session()->flash('error', 'Item not found in cart');
            return response()->json([
                'status' => false,
                'error' => 'Item not found in cart'
            ]);
        }
        Cart::remove($request->rowId);
        session()->flash('success', 'Item removed from cart successfully');
        return response()->json([
            'status' => true,
            'error' => 'Item removed from cart successfully'
        ]);
    }

    public function checkout()
    {
        $discount = 0;
        if(Cart::count() == 0){
            return redirect()->route('front.cart');
        }
        if(Auth::check() == false)
        {
            session(['url.intended' => url()->current()]);
            return redirect()->route('account.login');
        }
        session()->forget('url.intended');
        $customerAddress = CustomerAddress::where('user_id', Auth::user()->id)->first();
        //Cart::store(Auth::user()->id);
        $countries = Country::orderBy('name', 'ASC')->get();
        $subTotal = Cart::subtotal(2,'.','');
        //Appy Discount Here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }
        }
        //Calculate shipping here
        if($customerAddress != ''){
            $userCountry = $customerAddress->country_id;
            $shippingInfo = ShippingCharge::where('country_id', $userCountry)->first();
            if($shippingInfo == ''){
                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
            }
            $shippingAmount =  $shippingInfo->amount;
            $totalQty = 0;
            $totalShippingCharge = 0;
            $grandTotal = 0;
            foreach(Cart::content() as $item)
            {
                $totalQty += $item->qty;
            }
            $totalShippingCharge =  $totalQty*$shippingAmount;
            $grandTotal = ($subTotal-$discount) + $totalShippingCharge;              
        }else{
            $totalShippingCharge = 0;
            $grandTotal = ($subTotal-$discount) + $totalShippingCharge;  
        }
        
        return view('front.checkout', [
            'countries'=>$countries,
            'customerAddress'=>$customerAddress,
            'totalShippingCharge'=>$totalShippingCharge,
            'grandTotal'=>$grandTotal,
            'discount'=>$discount
        ]);
    }

    public function processCheckout(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => 'required|min:3',
            'last_name'  => 'required',
            'email' => 'required|email',
            'mobile' => 'required',
            'address' => 'required|min:15',
            'country' => 'required',
            'state' => 'required',
            'city' => 'required',
            'zip' => 'required'
        ]);

        if($validator->fails()){
            return response()->json([
                'message'   => 'Please correct the errors',
                'status'    => false,
                'errors'    => $validator->errors()
            ]);
        }else{
            // Save customer addresses 

            $user = Auth::user();
            CustomerAddress::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'user_id' => $user->id,
                'first_name' => $request->first_name,
                'last_name'  => $request->last_name,
                'email' => $request->email,
                'mobile' => $request->mobile,
                'country_id' => $request->country,
                'address' => $request->address,
                'apartment' => $request->appartment,
                'city' => $request->city,
                'state' => $request->state,
                'zip' => $request->zip,
            ]);

            // Store data in orders table

                if($request->payment_method == 'cod'){

                //Calculating shipping
                $shipping = 0;
                $discount = 0;
                $subTotal   =   Cart::subtotal(2, '.', '');
                $shippingInfo = ShippingCharge::where('country_id', $request->country)->first();
                $totalQty = 0;
                $couponCode = null;
                $couponCodeId = null;
                if(session()->has('code')){
                    $code = session()->get('code');
                    $couponCode = $code->code;
                    $couponCodeId = $code->id;
                    if($code->type == 'percent'){
                        $discount = ($code->discount_amount/100)*$subTotal;
                    }else{
                        $discount = $code->discount_amount;
                    }        
                }
                
                foreach(Cart::content() as $item)
                {
                    $totalQty += $item->qty; 
                }
                if($shippingInfo != '')
                {
                    
                    $shipping =  $shippingInfo->amount * $totalQty; 
                    $grandTotal     =  ($subTotal-$discount) + $shipping;
                
                 }else{
                    $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                    $shipping =  $shippingInfo->amount * $totalQty; 
                    $grandTotal     =  ($subTotal-$discount) + $shipping;               
                }


                
                $order = new Order();
                $order->user_id =   $user->id;
                $order->subtotal =   $subTotal;
                $order->shipping =   $shipping;
                $order->coupon_code = $couponCode;
                $order->coupon_code_id = $couponCodeId;
                $order->payment_status = 'not paid';
                $order->status = 'pending';
                $order->discount = $discount;
                $order->grand_total =   $grandTotal;
                $order->first_name =   $request->first_name;
                $order->last_name =   $request->last_name;
                $order->email =   $request->email;
                $order->mobile =   $request->mobile;
                $order->country_id =   $request->country;
                $order->address =   $request->address;
                $order->apartment =   $request->appartment;
                $order->city =   $request->city;
                $order->state =   $request->state;
                $order->zip =   $request->zip;
                $order->notes =   $request->notes;
                $order->save();

               // event::Broadcast::channel('order-notify', OrderCreated::class);
                event(new OrderCreated($order->id));

                // Store Order Items in Order Items table

                $cartItems = Cart::content();
                foreach($cartItems as $item)
                {
                    $orderItem = new OrderItem();
                    $orderItem->order_id = $order->id;
                    $orderItem->product_id	= $item->id;
                    $orderItem->name	= $item->name;
                    $orderItem->qty	= $item->qty;
                    $orderItem->price	= $item->price;
                    $orderItem->total	= $item->total;
                    $orderItem->save();

                    $productDet = Product::find($item->id);
                    if($productDet->track_qty == 'Yes'){
                        $productDet->qty = $productDet->qty - $item->qty;
                        $productDet->save();
                    }
                }

                //Send order email
                orderEmail($order->id,'customer');
                session()->flash('success', 'You have successfully placed your order.');
                Cart::destroy();
                session()->forget('code');
                return response()->json([
                    'message'   => 'Order saved successfully',
                    'status'    => true,
                    'orderId'   => $order->id
                ]);
            }else{

            }
        }
    }

    public function thankyou($id)
    {
        return view('front.thanks', ['id' => $id]);
    }

    public function getOrderSummery(Request $request)
    {
        $subTotal = Cart::subtotal();
        $discount = 0;
        $discountString = '';
        //Appy Discount Here
        if(session()->has('code')){
            $code = session()->get('code');
            if($code->type == 'percent'){
                $discount = ($code->discount_amount/100)*$subTotal;
            }else{
                $discount = $code->discount_amount;
            }

           $discountString =  '<div class="mt-4" id="discount-response">
                        <strong>'.session()->get('code')->code.'</strong>
                        <a class="btn btn-sm btn-danger" type="button" id="remove-discount"><i class="fa fa-times"></i></a>
                    </div>';
        }
        if($request->country_id > 0)
        {            
            $shippingInfo = ShippingCharge::where('country_id', $request->country_id)->first();
            $totalQty = 0;
            foreach(Cart::content() as $item)
            {
                $totalQty += $item->qty; 
            }

            if($shippingInfo != '')
            {
                $shippingCharge =  $shippingInfo->amount * $totalQty; 
                $grandTotal     =  ($subTotal-$discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2),
                    'grandTotal' => number_format($grandTotal,2)
                ]);
            }else{
                $shippingInfo = ShippingCharge::where('country_id', 'rest_of_world')->first();
                $shippingCharge =  $shippingInfo->amount * $totalQty; 
                $grandTotal     =  ($subTotal-$discount) + $shippingCharge;
                return response()->json([
                    'status' => true,
                    'discount' => number_format($discount,2),
                    'discountString' => $discountString,
                    'shippingCharge' => number_format($shippingCharge,2),
                    'grandTotal' => number_format($grandTotal,2)
                ]);
            }
        }else{
            return response()->json([
                'status' => true,
                'discount' => number_format($discount,2),
                'discountString' => $discountString,
                'shippingCharge' => 0,
                'grandTotal' => number_format(($subTotal-$discount),2)
            ]);
        }
    }

    public function applyDiscount(Request $request)
    {
        $code = DiscountCoupon::where('code', $request->code)->first(); 
        if($code == ''){
            return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon'
            ]);
        }

        //Check coupon start date is valid or not
        $now = Carbon::now();
        
        if($code->starts_at != ''){
            $startDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->starts_at);
            if($now->lt($startDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon'
                ]);
            }
        }
        if($code->expires_at != ''){
            $expireDate = Carbon::createFromFormat('Y-m-d H:i:s', $code->expires_at);
            if($now->gt($expireDate)){
                return response()->json([
                    'status' => false,
                    'message' => 'Invalid discount coupon'
                ]);
            }
        }

        //Check max coupon used or not
        if($code->max_uses > 0){
            $couponUsed = Order::where('coupon_code_id',$code->id)->count();
            if($couponUsed >= $code->max_uses){
               return response()->json([
                'status' => false,
                'message' => 'Invalid discount coupon'
               ]);
            }
        }      

        //Check max uses by a single user
        if($code->max_uses_user > 0){
            $couponUsedByUser = Order::where(['coupon_code_id'=> $code->id,'user_id'=>Auth::user()->id])->count();
            if($couponUsedByUser >= $code->max_uses_user){
                return response()->json([
                    'status' => false,
                    'message' => 'You already used this coupon'
                ]);
            }    
        }      
        $subTotal = Cart::subtotal(2,'.','');
        //Min amount condition check
        if($code->min_amount > 0){
            if($subTotal < $code->min_amount){
                return response()->json([
                    'status' => false,
                    'message' => 'Your min amount must be greater than $'.$code->min_amount.'.'
                ]);
            }
        }

        Session()->put('code',$code);
        return $this->getOrderSummery($request);       
        
    }

    public function removeCoupon(Request $request){
        session()->forget('code');
        return $this->getOrderSummery($request);
    }
}
