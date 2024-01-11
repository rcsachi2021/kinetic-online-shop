<?php

namespace App\Http\Controllers;

use App\Mail\ContactMail;
use App\Models\Page;
use App\Models\Product;
use App\Models\User;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class FrontController extends Controller
{
    public function index()
    {
        $featuredProducts = Product::where('is_featured','Yes')->where('status', 1)->orderBy('id', 'DESC')->take(8)->get();
        $latestProducts   = Product::where('status', 1)->orderBy('id', 'DESC')->take(8)->get();
        $data['featuredProducts']   = $featuredProducts;
        $data['latestProducts']     = $latestProducts;
        return view('front.home',$data);
    }

    public function addToWishList(Request $request)
    {
        if(Auth::check() == false)
        {
            session(['url.intended' => url()->previous()]);
            return response()->json([
                'status' => false,

            ]);
        }
        $product = Product::where('id', $request->id)->first();
        if($product == null){
            return response()->json([
                'status' => true,
                'message' => '<div class="alert alert-success">Product not found!</div>'
            ]);
        }

        $wishList = Wishlist::updateOrCreate(
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ],
            [
                'user_id' => Auth::user()->id,
                'product_id' => $request->id
            ] 
        );

        // $wishList = new Wishlist();
        // $wishList->user_id = Auth::user()->id;
        // $wishList->product_id = $request->id;
        // $wishList->save();

        return response()->json([
            'status' => true,
            'message' => '<div class="alert alert-success"><strong>'.$product->title.'</strong> added in your wishlist</div>'
        ]);
    }

    public function page($slug)
    {
        $page = Page::where('slug', $slug)->first();
        if($page == null)
        {
            abort(404);
        }
        return view('front.page', compact('page'));
    }

    public function sendContactEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email',
            'subject' => 'required|min:10'
        ]);

        if($validator->passes())
        {
            $mailData = [
                'name' => $request->name,
                'email' => $request->email,
                'subject' => $request->subject,
                'message' => $request->message,
                'mail_subject' => 'You have a contact mail'
            ];
            $admin = User::find(1);
            Mail::to($admin->email)->send(new ContactMail($mailData));
            session()->flash('success', 'Thanks for contcat us. We will get back to you soon');
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
}
