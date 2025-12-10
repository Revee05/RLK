<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Products;
use App\User;
use App\Karya;
use App\Order;
use App\Models\MerchProduct;
use App\Posts;
use App\Bid;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        /* ============================================================
         *  DASHBOARD COUNTERS
         * ============================================================ */
        $dashboard = [
            'member'        => User::where('access', 'member')->count(),
            'seniman'       => Karya::count(),
            'products'      => Products::count(),
            'merchandise'   => MerchProduct::count(),
        ];

        /* ============================================================
         *  PRODUK LELANG TERBARU
         * ============================================================ */
        $produkAktif = Products::with(['imageUtama', 'karya'])
            ->where('status', 1)
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

        /* ============================================================
         *  LIST TRANSAKSI & BLOG TERBARU
         * ============================================================ */
        $daftar = [
            'transaksi' => Order::latest()->take(5)->get(),
            'blog'      => Posts::with('author')->latest()->take(5)->get(),
        ];

        /* ============================================================
         *  BID CHART — per bulan
         * ============================================================ */
        $selectedYear = $request->year ?? Carbon::now()->year;

        $bidRaw = Bid::selectRaw('MONTH(created_at) as bulan, COUNT(*) as total')
            ->whereYear('created_at', $selectedYear)
            ->groupBy('bulan')
            ->orderBy('bulan')
            ->get();

        $labels = [];
        $bidData = [];

        for ($i = 1; $i <= 12; $i++) {
            $labels[] = Carbon::create()->month($i)->format('M');
            $bidData[] = $bidRaw->firstWhere('bulan', $i)->total ?? 0;
        }

        $chart = [
            'labels' => $labels,
            'data'   => $bidData,
        ];

        $years = Bid::selectRaw('YEAR(created_at) as year')
            ->distinct()->orderBy('year','desc')
            ->pluck('year');

        $totalBidYear = array_sum($bidData);

        /* ============================================================
         *  TRANSAKSI — Informasi Tambahan
         * ============================================================ */

        $trxSummary = [
            'expired_total' => Order::where('payment_status', 3)->sum('total_tagihan'),
            'pending_total' => Order::where('payment_status', 1)->sum('total_tagihan'),
            'paid_total'    => Order::where('payment_status', 2)->sum('total_tagihan'),
        ];

        /* ============================================================
         *  RETURN VIEW
         * ============================================================ */
        return view('admin.dashboard', compact(
            'dashboard',
            'produkAktif',
            'daftar',
            'chart',
            'years',
            'selectedYear',
            'totalBidYear',
            'trxSummary'
        ));
    }
}
