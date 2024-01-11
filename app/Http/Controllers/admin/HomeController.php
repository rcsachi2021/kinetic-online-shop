<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index()
    {
        $totalOrders = Order::where('status', '!=', 'canceled')->count();
        $totalProducts = Product::count();
        $totalCustomers = User::where('role', 1)->count();
        $totalRevanue = Order::where('status', '!=', 'canceled')->sum('grand_total');

        //This month revanue
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $currentMonthRevenue = Order::where('status', '!=', 'canceled')
                               ->whereDate('created_at', '>=', $startOfMonth)
                               ->whereDate('created_at', '<=', $currentDate)
                               ->sum('grand_total');
        
        //Last month revanue
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $endOfMonth = Carbon::now()->subMonth()->lastOfMonth()->format('Y-m-d');
        $lastMonthRevanue = Order::where('status','!=', 'canceled')
                            ->whereDate('created_at', '>=', $lastMonthStartDate)
                            ->whereDate('created_at', '<=', $endOfMonth)
                            ->sum('grand_total');

        // Last 30 days's revanue
        $satrtDate = Carbon::now()->subDays(30)->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $revanueLastThirtyDays = Order::where('status', '!=', 'canceled')
                                 ->whereDate('created_at', '>=', $satrtDate)
                                 ->whereDate('created_at', '<=', $currentDate)
                                 ->sum('grand_total');
       
        return view('admin.dashboard', [
            'totalOrders' => $totalOrders,
            'totalProducts' => $totalProducts,
            'totalCustomers' => $totalCustomers,
            'totalRevanue' => $totalRevanue,
            'revanueThisMOnth' => $currentMonthRevenue,
            'lastMonthRevanue' => $lastMonthRevanue,
            'revanueLastThirtyDays' => $revanueLastThirtyDays
        ]);
    }

    public function logout()
    {
        auth()->guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
