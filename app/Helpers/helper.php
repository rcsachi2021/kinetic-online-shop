<?php

use App\Mail\OrderEmail;
use App\Models\Category;
use App\Models\Country;
use App\Models\Order;
use App\Models\Page;
use App\Models\ProductImage;
use Illuminate\Support\Facades\Mail;

    function getCategories()
    {
        return Category::with('subCategories')
        ->orderBY('name', 'ASC')
        ->where('showhome', 'Yes')
        ->where('status', 1)
        ->get();
    }

    function getProductImage($productID){
        return ProductImage::where('product_id', $productID)->first();
    }

    function orderEmail($orderID, $userType='customer')
    {
        $order = Order::with('items')->where('id',$orderID)->first();
        if($userType=='customer'){
            $subject = 'Thanks for your order';
            $email = $order->email;
        }else{
            $subject = 'You have received an order';
            $email = env('ADMIN_EMAIL');
        }
        $mailData = [
            'subject' => $subject,
            'order' => $order,
            'userType' => $userType
        ];
        Mail::to($email)->send(new OrderEmail($mailData));
        
    }

    function countryInfo($id){
       return  Country::where('id', $id)->first();
    }

    function staticPages()
    {
       $pages =   Page::orderBy('name', 'ASC')->get();
       return $pages;
        
    }

?>