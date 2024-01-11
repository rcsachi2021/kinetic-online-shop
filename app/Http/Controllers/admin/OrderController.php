<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::latest('orders.created_at')->select('orders.*','users.name', 'users.email');
        $orders = $orders->leftJoin('users', 'users.id', 'orders.user_id');
        if($request->get('keyword') != ''){
            $orders = $orders->where('users.name', 'LIKE','%'.$request->get('keyword').'%');
            $orders = $orders->orWhere('users.email', 'LIKE','%'.$request->get('keyword').'%');
            $orders = $orders->orWhere('orders.id', 'LIKE','%'.$request->get('keyword').'%');
        }
        $orders = $orders->paginate(5);
        //dd($orders);
        return view('admin.orders.list', [
            'orders'=>$orders
        ]);
    }

    public function detail($orderID)
    {
        $order = Order::select('orders.*','countries.name as countryName')
        ->where('orders.id', $orderID)
        ->leftJoin('countries', 'orders.country_id', 'countries.id')
        ->first();
        // dd($order);
        $orderItems = OrderItem::where('order_id', $orderID)->get();

        return view('admin.orders.detail', [
            'order' => $order,
            'orderItems' => $orderItems
        ]);
    }

    public function changeOrderStatus(Request $request, $orderID)
    {
        $order = Order::find($orderID);
        $order -> status = $request->status;
        $order->shipped_date = $request->shipped_date;
        $order->save();
        $message = 'Order status changed successfully';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }

    public function sendInvoiceEmail(Request $request, $orderID)
    {
        $userType = $request->userType;
        orderEmail($orderID, $userType);
        $message = 'Order email sent successfully';
        session()->flash('success', $message);
        return response()->json([
            'status' => true,
            'message' => $message
        ]);
    }
}
