<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Exception;
use Carbon\Carbon;

// --- MODELS ---
use App\Products;             // Model Lelang
use App\ProductImage;
use App\Bid;
use App\User;
use App\Kategori;
use App\Posts;                // Model Blog
use App\Karya;                // Model Seniman
use App\Kelengkapan;
use App\Sliders;
use App\Event;
use App\Models\MerchProduct;  // Model Merchandise
use App\TeamMember;

class HomeController extends Controller
{
    /**
     * =================================================================
     * HALAMAN UTAMA (HOME)
     * =================================================================
     */
    public function index(Request $request)
    {
        try {
            // 1. QUERY SLIDER (LOGIKA PRIORITAS)
            // A. Ambil Event yang statusnya ACTIVE untuk Slider Utama
            $eventSliders = Event::where('status', 'active')->latest()->get();

            // B. Ambil Slider Biasa (Cadangan jika tidak ada event)
            $defaultSliders = Sliders::active()->get();

            // 2. QUERY DATA UTAMA
            $products = Products::active()->orderBy('id', 'desc')->take(5)->get();
            $blogs = Posts::Blog()->orderBy('id', 'desc')->where('status', 'PUBLISHED')->take(5)->get();

            // 3. QUERY DATA MERCHANDISE (Eager Loading Optimization)
            $merchProducts = MerchProduct::with([
                'defaultVariant.images' => fn($q) => $q->orderBy('id'),
                'defaultVariant.sizes' => fn($q) => $q->orderBy('price')
            ])
            ->where('status', 'active')
            ->orderBy('created_at', 'desc')
            ->take(5)
            ->get();

            // 4. RESPON JSON (API / AJAX)
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Data Home berhasil diambil',
                    'data'    => [
                        'event_sliders'   => $eventSliders,
                        'default_sliders' => $defaultSliders,
                        'products'        => $products,
                        'merchandise'     => $merchProducts,
                        'blogs'           => $blogs,
                    ],
                ]);
            }

            // 5. RESPON VIEW
            return view('web.home', compact(
                'products', 
                'eventSliders', 
                'defaultSliders', 
                'blogs', 
                'merchProducts'
            ));

        } catch (Exception $e) {
            Log::error("Error Home Index: " . $e->getMessage());
            abort(500);
        }
    }

    /**
     * =================================================================
     * DETAIL PRODUK LELANG
     * =================================================================
     */
    public function detail($slug)
    {
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:products,slug']
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        try {
            $product = Products::where('slug', $slug)
                ->with(['imageUtama', 'images', 'kategori', 'karya', 'kelengkapans'])
                ->firstOrFail();

            $bidList = Bid::where('product_id', $product->id)
                ->with('user')
                ->orderByRaw('CAST(price AS UNSIGNED) DESC')
                ->get();

            // Hitung Suggestion Bid
            $highestBid = $bidList->first() ? $bidList->first()->price : $product->price;
            $step       = intval($product->kelipatan);
            $nominals   = [];

            for ($i = 1; $i <= 5; $i++) {
                $nominals[] = $highestBid + ($step * $i);
            }

            // Produk Terkait
            $related = Products::where('kategori_id', $product->kategori_id)
                ->where('id', '!=', $product->id)
                ->active()
                ->take(4)
                ->with('imageUtama')
                ->get();

            return view('web.detail_lelang.detail', [
                'product'    => $product,
                'bids'       => $bidList,
                'highestBid' => $highestBid,
                'nominals'   => $nominals,
                'related'    => $related,
            ]);

        } catch (Exception $e) {
            Log::error('Detail Product Error: ' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * =================================================================
     * PENCARIAN (SEARCH)
     * =================================================================
     */
    public function search(Request $request)
    {
        try {
            $q = $request->input('q');

            $validator = Validator::make(['q' => $q], [
                'q' => ['required', 'string', 'min:1', 'max:90']
            ]);

            if ($validator->fails()) {
                return redirect()->route('home');
            }

            // --- 1. SEARCH LELANG ---
            $searchNoSpace = preg_replace('/\s+/', '', strtolower($q));

            $lelang = Products::query()
                ->whereIn('status', [1, 2])
                ->where(function ($query) use ($q, $searchNoSpace) {
                    $query->where('title', 'LIKE', "%$q%")
                        ->orWhereRaw("REPLACE(LOWER(title), ' ', '') LIKE ?", ['%' . $searchNoSpace . '%']);
                })
                ->with('imageUtama', 'kategori')
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();

            // --- 2. SEARCH MERCHANDISE ---
            $merchandise = MerchProduct::where('status', 'active')
                ->where('name', 'LIKE', "%$q%")
                ->with(['defaultVariant.images' => fn($query) => $query->orderBy('id', 'asc'), 'defaultVariant.sizes'])
                ->orderBy('created_at', 'desc')
                ->take(8)
                ->get();

            // --- 3. SEARCH SENIMAN ---
            $seniman = Karya::query()
                ->where(function ($query) use ($q) {
                    $query->where('name', 'LIKE', "%$q%")
                        ->orWhere('julukan', 'LIKE', "%$q%")
                        ->orWhere('bio', 'LIKE', "%$q%")
                        ->orWhere('address', 'LIKE', "%$q%");
                })
                ->with(['city'])
                ->orderBy('name', 'asc')
                ->take(8)
                ->get();

            // --- 4. SEARCH BLOG ---
            $blogs = Posts::Blog()
                ->where('status', 'PUBLISHED')
                ->where('title', 'LIKE', "%$q%")
                ->orderBy('id', 'desc')
                ->take(8)
                ->get();

            return view('web.search', compact('q', 'lelang', 'merchandise', 'blogs', 'seniman'));

        } catch (Exception $e) {
            Log::error('Search Error :' . $e->getMessage());
            return redirect()->route('home')->with('error', 'Terjadi kesalahan saat mencari.');
        }
    }

    /**
     * =================================================================
     * HALAMAN KATEGORI LELANG
     * =================================================================
     */
    public function category($slug)
    {
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:kategori,slug']
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        try {
            $products = Products::whereHas('kategori', function ($query) use ($slug) {
                return $query->where('slug', $slug);
            })->active()->orderBy('id', 'desc')->paginate(16);

            if ($products->count() > 0) {
                return view('web.category', compact('products'));
            }

            // Jika kategori ada tapi produk kosong, tetap tampilkan view (opsional) atau 404
            // Disini saya biarkan 404 sesuai logic lama, tapi idealnya return view empty state
            abort(404); 

        } catch (Exception $e) {
            Log::error('By Kategori Error :' . $e->getMessage());
            abort(404);
        }
    }

    public function blogDetail($slug)
    {
        try {
            $blog = Posts::where('slug', $slug)
                ->where('post_type', 'blog')
                ->where('status', 'PUBLISHED')
                ->with(['author', 'images'])
                ->firstOrFail();

            $relatedBlogs = Posts::where('post_type', 'blog')
                ->where('status', 'PUBLISHED')
                ->where('id', '!=', $blog->id)
                ->inRandomOrder()
                ->take(5)
                ->get();

            return view('web.blog_detail', compact('blog', 'relatedBlogs'));
        } catch (Exception $e) {
            abort(404);
        }
    }

    public function page($slug)
    {
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:posts,slug']
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        try {
            $page = Posts::where('slug', $slug)->where('status', 'PUBLISHED')->firstOrFail();
            return view('web.page', compact('page'));
        } catch (Exception $e) {
            Log::error('Page Error :' . $e->getMessage());
            abort(404);
        }
    }

    /**
     * =================================================================
     * HALAMAN FILTER BY SENIMAN (LELANG)
     * =================================================================
     */
    public function seniman($slug)
    {
        $validator = Validator::make(['slug' => $slug], [
            'slug' => ['required', 'exists:karya,slug']
        ]);

        if ($validator->fails()) {
            abort(404);
        }

        try {
            // Ambil Data Profil Seniman + Relasi Lokasi
            $senimanData = Karya::where('slug', $slug)
                ->with(['city', 'province'])
                ->firstOrFail();

            // Ambil Produk milik seniman
            $products = Products::where('karya_id', $senimanData->id)
                ->active()
                ->orderBy('id', 'desc')
                ->paginate(16);

            return view('web.seniman', [
                'products' => $products,
                'seniman'  => $senimanData
            ]);
        } catch (Exception $e) {
            Log::error('By Seniman Error :' . $e->getMessage());
            abort(404);
        }
    }

    public function perusahaan()
    {
        $teamMembers = TeamMember::orderBy('id')->get();
        return view('web.tentang.tentang', compact('teamMembers'));
    }

    public function tim()
    {
        $teamMembers = TeamMember::orderBy('id')->get();
        return view('web.tentang.tim', compact('teamMembers'));
    }
}