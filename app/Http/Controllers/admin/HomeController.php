<?php

namespace App\Http\Controllers\admin;

use App\Http\Controllers\Controller;
use App\Models\order;
use App\Models\Product;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{

    public function index()
    {
        $totalOrders = order::where('status', '!=', 'cancelled')->count();
        $totalProducts = Product::count();
        $totalUsers = User::where('role', 1)->count();
        $totalRevenue = order::where('status', '!=', 'cancelled')->sum('grand_total');
        //revenue of this month
        $startOfMonth = Carbon::now()->startOfMonth()->format('Y-m-d');
        $currentDate = Carbon::now()->format('Y-m-d');
        $revenueThisMonth = order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $startOfMonth)->whereDate('created_at', '>=', $currentDate)->sum('grand_total');
        //revenue last month
        $lastMonthStartDate = Carbon::now()->subMonth()->startOfMonth()->format('Y-m-d');
        $lastMonthEndDate = Carbon::now()->subMonth()->endOfMonth()->format('Y-m-d');
        $lastMonthName = Carbon::now()->subMonth()->startOfMonth()->format('M');
        $revenueLastMonth = order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $lastMonthStartDate)->whereDate('created_at', '>=', $lastMonthEndDate)->sum('grand_total');
        //revenue 30 day  only
        $lastThirtyDayStartDate = Carbon::now()->subDays(30)->startOfMonth()->format('Y-m-d');
        $revenueLastThirtyDays = order::where('status', '!=', 'cancelled')->whereDate('created_at', '>=', $lastThirtyDayStartDate)->whereDate('created_at', '>=', $currentDate)->sum('grand_total');


        return view('admin.dashboard', ['totalOrders' => $totalOrders, 'totalProducts' => $totalProducts, 'totalUsers' => $totalUsers, 'totalRevenue' => $totalRevenue, 'revenueThisMonth' => $revenueThisMonth, 'revenueLastMonth' => $revenueLastMonth, 'revenueLastThirtyDays' => $revenueLastThirtyDays, 'lastMonthName' => $lastMonthName]);
    }


    public function logout()
    {
        Auth::guard('admin')->logout();
        return redirect()->route('admin.login');
    }
}
