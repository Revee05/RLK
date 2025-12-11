<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Products;
use App\ProductImage;
use App\Bid;
use App\user;
use App\Kategori;
use App\Posts;
use App\Karya;
use App\Events\MessageSent;
use Validator;
use Illuminate\Support\Facades\Log;
use Hash;
use App\Kelengkapan;
use App\Sliders;
use Carbon\Carbon;
use App\Event; // <-- 1. TAMBAHKAN INI

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // 1. QUERY DATA
        $products = Products::active()->orderBy('id', 'desc')->take(5)->get();
        $sliders = Sliders::active()->get();
        $blogs = Posts::Blog()->orderBy('id', 'desc')->where('status', 'PUBLISHED')->take(3)->get();

        $featuredEvent = Event::whereIn('status', ['active', 'coming_soon'])
            ->latest()
            ->first();

        // 2. LOGIKA RESPON JSON (Dual Mode)
        // Jika request datang dari AJAX atau meminta JSON (misal dari Postman/Mobile App)
        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Data Home berhasil diambil',
                'data' => [
                    'sliders' => $sliders,
                    'featured_event' => $featuredEvent,
                    'products' => $products,
                    'blogs' => $blogs,
                ],
                'meta' => [
                    'total_products' => $products->count(),
                    'timestamp' => now()->toDateTimeString()
                ]
            ]);
        }

        // 3. RESPON VIEW BIASA (Untuk Browser)
        return view('web.home', compact('products', 'sliders', 'blogs', 'featuredEvent'));
    }

    public function detail($slug)
    {
        // ... (sisa kode Anda tetap sama) ...
        //cek data is exist
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:products,slug']
        ]);

        //jika tidak ada redirect ke halaman 404
        if ($validator->fails()) {
            abort('404');
        }

        try {

            $product = Products::where('slug', $slug)
                ->with(['imageUtama', 'images', 'kategori', 'karya', 'kelengkapans'])
                ->firstOrFail();

            // Ambil semua bidding - PENTING: sort numeric bukan string
            $bidList = Bid::where('product_id', $product->id)
                ->with('user')
                ->orderByRaw('CAST(price AS UNSIGNED) DESC')
                ->get();

            // Highest bidding
            $highestBid = $bidList->first() ? $bidList->first()->price : $product->price;

            // Kelipatan bidding
            $step = intval($product->kelipatan);

            // Dropdown kelipatan 5x
            $nominals = [];
            for ($i = 1; $i <= 5; $i++) {
                $nominals[] = $highestBid + ($step * $i);
            }

            // Related products
            $related = Products::where('kategori_id', $product->kategori_id)
                ->where('id', '!=', $product->id)
                ->active()
                ->take(4)
                ->with('imageUtama')
                ->get();

            // Pass to view (gunakan template detail lelang yang ada)
            return view('web.detail_lelang.detail', [
                'product'     => $product,
                'bids'        => $bidList,
                'highestBid'  => $highestBid,
                'nominals'    => $nominals,
                'related'     => $related,
            ]);
        } catch (Exception $e) {
            Log::error('Detail Product :' . $e->getMessage());
        }
    }

    public function category($slug)
    {
        // ... (sisa kode Anda tetap sama) ...
        //cek data is exist
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:kategori,slug']
        ]);

        //jika tidak ada redirect ke halaman 404
        if ($validator->fails()) {
            abort('404');
        }

        try {

            $products = Products::whereHas('kategori', function ($query) use ($slug) {
                return $query->where('slug', $slug);
            })->active()->orderBy('id', 'desc')->paginate(16);

            if ($products) {
                return view('web.category', compact('products'));
            }

            abort(404);
        } catch (Exception $e) {
            Log::error('By Kategori :' . $e->getMessage());
        }
    }

    public function page($slug)
    {
        // ... (sisa kode Anda tetap sama) ...
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:posts,slug']
        ]);

        //jika tidak ada redirect ke halaman 404
        if ($validator->fails()) {
            abort('404');
        }

        try {

            $page = Posts::where('slug', $slug)->first();

            if ($page) {
                return view('web.page', compact('page'));
            }

            abort(404);
        } catch (Exception $e) {
            Log::error('Page :' . $e->getMessage());
        }
    }

    public function search(Request $request)
    {
        // ... (sisa kode Anda tetap sama) ...
        try {

            $q = $request->input('q');

            $validator = Validator::make(['q' => $q], [
                'q' => ['required', 'string', 'min:1', 'max:90']
            ]);

            if ($validator->fails()) {

                return redirect()->route('home');
            }


            $products = Products::active()->where('title', 'LIKE', "%$q%")->paginate(16);
            $products->appends(['q' => $q]);

            return view('web.search', compact('q', 'products'));
        } catch (Exception $e) {
            Log::error('Search :' . $e->getMessage());
        }
    }

    public function perusahaan()
    {
        return view('web.tentang.tentang');
    }
    public function tim()
    {
        return view('web.tentang.tim');
    }

    public function seniman($slug)
    {
        // ... (sisa kode Anda tetap sama) ...
        //cek data is exist
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:karya,slug']
        ]);
        //jika tidak ada redirect ke halaman 404
        if ($validator->fails()) {
            abort('404');
        }

        try {

            $products = Products::whereHas('karya', function ($query) use ($slug) {
                return $query->where('slug', $slug);
            })->active()->orderBy('id', 'desc')->paginate(16);

            if ($products) {
                return view('web.seniman', compact('products'));
            }

            abort(404);
        } catch (Exception $e) {
            Log::error('By Seniman :' . $e->getMessage());
        }
    }
}
