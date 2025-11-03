<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Carbon\Carbon;
use App\Products;
use App\User;
use App\Karya;
use App\Order;

class DashboardController extends Controller
{
    public function index()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');

        //All
        $dashboard['product'] = Products::count(); 
        $dashboard['seniman'] = Karya::count(); 
        $dashboard['users'] = User::count();
        $dashboard['member'] = User::member()->count();

        //Produk
        $lelang['aktiv'] = Products::active()->count();
        $lelang['expired'] = Products::where('end_date','<',$now)->count();
        $lelang['expired'] = Products::where('end_date','<',$now)->count();

        //Table
        $daftar['produk'] = Products::orderBy('id','desc')->take('5')->get();
        $daftar['transaksi'] = Order::orderBy('id','desc')->take('5')->get();
        return view('admin.dashboard',compact('dashboard','lelang','daftar'));
    }
}
