<?php

namespace App\Http\Controllers;

use App\Mail\ResetPasswordMail;
use App\Models\Country;
use App\Models\CustomerAddress;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthController extends Controller
{
    public function login()
    {
        return view('front.account.login');
    }

    public function authenticate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);
        if($validator->passes()){

            if(Auth::attempt(['email' => $request->email, 'password' => $request->password],$request->get('remember'))){
                if(session()->has('url.intended'))
                {
                    return redirect(session()->get('url.intended'));
                }
                return redirect()->route('account.profile');
            }else{
                return redirect()->back()->withInput($request->only('email'))->with('error', 'Incorrect email/password');
            }

        }else{
            return redirect()->back()->withErrors($validator)->withInput($request->only('email'));
        }

    }

    public function register()
    {
        return view('front.account.register');
    }

    public function processRegister(Request $request)
    {
        $validator  = Validator::make($request->all(), [
            'name' => 'required|min:3',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6|confirmed'
        ]);

        if($validator->passes()){
            $user  = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->password = Hash::make($request->password);
            $user->save();
            session()->flash('success', 'Registered successfully!');
            return response()->json([
                'status' => true,
                'success' => 'Registered successfully!'
            ]);
            
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
        
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('account.login');
    }

    public function dashboard(){
        $userID = Auth::user()->id;
        $user = User::where('id',Auth::user()->id)->first();
        $countries = Country::orderBy('name', 'asc')->get();
        $customerAddress = CustomerAddress::where('user_id', $userID)->first();
        return view('front.account.profile', [
            'user' => $user,
            'countries' => $countries,
            'customerAddress' => $customerAddress
        ]);
    }

    public function orders()
    {
        $user = Auth::user();
        $orders = Order::where('user_id',$user->id)->orderBy('created_at', 'DESC')->get();
        $data['orders'] = $orders;
        return view('front.account.order', $data);
    }

    public function ordersDetail($id){
        $user = Auth::user();
        $data = [];
        $order = Order::where(['id' => $id, 'user_id' => $user->id])->first();
        $orderItems = OrderItem::where('order_id', $order->id)->get();
        $data['order'] = $order;
        $orderItemsCount = OrderItem::where('order_id', $order->id)->count();
        $data['orderItemsCount'] = $orderItemsCount;
        $data['orderItems'] = $orderItems;
        return view('front.account.order-details', $data);
    }

    public function wishList()
    {
        $data = [];
        $wishLists = Wishlist::with('product')->where('user_id', Auth::user()->id)->get();
       // dd($wishLists);
        $data['wishlists'] = $wishLists;
        return view('front.account.wish-list', $data);
    }

    public function removeProductFromWishList(Request $request)
    {
        $wishList = Wishlist::where('product_id',$request->id)->where('user_id', Auth::user()->id)->first();
        if($wishList == null){
            session()->flash('error', 'Product already removed!');
            return response()->json([
                'status' => true
            ]);
        }
        else{
            $wishList->delete();
            session()->flash('success', 'Product removed from wishlist!');
            return response()->json([
                'status' => true
            ]);
        }
    }

    public function updateProfile(Request $request)
    {
        $userID = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users,email,'.$userID.',id',
            'phone' => 'required|numeric'
        ]);

        if($validator->passes()){
            $user = User::find($userID);
            $user->name = $request->name;
            $user->email = $request->email;
            $user->phone = $request->phone;
            $user->save();
            session()->flash('success', 'Profile updated successfully');
            return response()->json([
                'status' => true,
                'message' => 'Profile updated successfully'                
            ]);

        }else{
            
            return response()->json([
                'status' => false,
                'errors' => $validator->errors(),
                'message' => 'Please fix validation errors'                
            ]);

        }
    }

    public function updateAddress(Request $request)
    {
        $userID = Auth::user()->id;
        $validator = Validator::make($request->all(), [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|email',
            'mobile' => 'required|numeric',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'state' => 'required',
            'zip' => 'required'
        ]);

        if($validator->passes()){
            
            $address = CustomerAddress::updateOrCreate(
                [
                    'user_id' => $userID
                ],
                [
                    'user_id' => $userID,
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'mobile' => $request->mobile,
                    'country_id' => $request->country,
                    'address' => $request->address,
                    'apartment' => $request->apartment,
                    'city' => $request->city,
                    'state' => $request->state,
                    'zip' => $request->zip
                ]
            );
            session()->flash('success', 'Address updated successfully');
            return response()->json([
                'status' => true
            ]);
        }else{
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }
    }
    public function showChangePassword()
    {
        return view('front.account.chnage-password');
    }

    public function changePassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required',
            'new_password' => 'required',
            'confirm_password' => 'required|same:new_password'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => false,
                'errors' => $validator->errors()
            ]);
        }else{
            $user = Auth::user();
            if(!Hash::check($request->old_password,$user->password))
            {
                session()->flash('error', 'Entered password is not correct. Please try again.');
                return response()->json([
                    'status' => true
                ]);
            }

            User::where('id', $user->id)->update([
                'password' => Hash::make($request->new_password)
            ]);
            $message = 'Password updated successfully';
            session()->flash('success', $message);
            return response()->json([
                'status' => true,
                'message' => $message
            ]);

        }
    }

    public function forgotPassword()
    {
        return view('front.account.forgot-password');
    }

    public function processForgotPassword(Request $request)
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|email|exists:users,email'
        ]);
        if($validator->fails()){
            return redirect()->back()->withInput()->withErrors($validator);
        }
        DB::table('password_reset_tokens')->where('email', $request->email)->delete();
        $token = Str::random(60);
        DB::table('password_reset_tokens')->insert([
            'email' => $request->email,
            'token' => $token,
            'created_at' => now()
        ]);

        $user = User::where('email', $request->email)->first();
        $mailData = [
            'user' => $user,
            'token' => $token,
            'mailSubject' => 'You have requested to reset your password'
        ];

        Mail::to($request->email)->send(new ResetPasswordMail($mailData));
        return redirect()->back()->with('success', 'Please check your inbox to reset password');
    }

    public function resetPassword($token)
    {   
        $checkToken = DB::table('password_reset_tokens')->where('token', $token)->first();
        if($checkToken == null){
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid token');
        }
        return view('front.account.reset-password', ['token' => $token]);
    }

    public function processResetPassword(Request $request)
    {
        $token = $request->token;
        $tokenObj = DB::table('password_reset_tokens')->where('token', $token)->first();
        if($tokenObj == null){
            return redirect()->route('front.forgotPassword')->with('error', 'Invalid token');
        }
        $user = User::where('email', $tokenObj->email)->first();
        $validator = Validator::make($request->all(), [
            'password' => 'required|confirmed',
            'password_confirmation' => 'required'
        ]);
        if($validator->fails()){
            return redirect()->back()->withErrors($validator);
        }
        // $user = User::where('email', $tokenObj->email)->update([
        //     'password' => Hash::make($request->pasword)
        // ]);

        User::where('id',$user->id)->update([
            'password' => Hash::make($request->password)
        ]);
        
        DB::table('password_reset_tokens')->where('email', $tokenObj->email)->delete();
        return redirect()->route('account.login')->with('success', 'Password reset successfully');
    }


}
