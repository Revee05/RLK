<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Validator;
use Exception;
use Carbon\Carbon;

// --- MODELS ---
use App\Products;             // Model Lelang
use App\ProductImage;
use App\Bid;
use App\user;
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
    // 1. QUERY DATA
    $products = Products::active()->orderBy('id', 'desc')->take(5)->get();
    $sliders = Sliders::active()->get();
    $blogs = Posts::Blog()->orderBy('id', 'desc')->where('status', 'PUBLISHED')->take(5)->get();
    $featuredEvent = Event::whereIn('status', ['active', 'coming_soon'])
      ->latest()
      ->first();

    // 2. RESPON JSON
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

    // 3. RESPON VIEW
    return view('web.home', compact('products', 'sliders', 'blogs', 'featuredEvent'));
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

      $highestBid = $bidList->first() ? $bidList->first()->price : $product->price;
      $step = intval($product->kelipatan);

      $nominals = [];
      for ($i = 1; $i <= 5; $i++) {
        $nominals[] = $highestBid + ($step * $i);
      }

      $related = Products::where('kategori_id', $product->kategori_id)
        ->where('id', '!=', $product->id)
        ->active()
        ->take(4)
        ->with('imageUtama')
        ->get();

      return view('web.detail_lelang.detail', [
        'product'     => $product,
        'bids'        => $bidList,
        'highestBid'  => $highestBid,
        'nominals'    => $nominals,
        'related'     => $related,
      ]);
    } catch (Exception $e) {
      Log::error('Detail Product Error: ' . $e->getMessage());
      abort(404);
    }
  }

  /**
   * =================================================================
   * PENCARIAN (SEARCH) - SENIMAN REMOVED
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
        ->with(['defaultVariant.images' => function ($query) {
          $query->orderBy('id', 'asc');
        }, 'defaultVariant.sizes'])
        ->orderBy('created_at', 'desc')
        ->take(8)
        ->get();

      // --- 3. SEARCH SENIMAN (RESTORED) ---
      // Logika disesuaikan dengan SenimanController
      $seniman = Karya::query()
        ->where(function ($query) use ($q) {
          $query->where('name', 'LIKE', "%$q%")
            ->orWhere('julukan', 'LIKE', "%$q%")
            ->orWhere('bio', 'LIKE', "%$q%")
            ->orWhere('address', 'LIKE', "%$q%");
        })
        ->with(['city']) // Eager load kota untuk ditampilkan di card
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

      // =============================================================
      // LOGGING
      // =============================================================
      try {
        $logData = [
          'meta' => [
            'keyword'    => $q,
            'user'       => Auth::check() ? Auth::user()->name . ' (ID:' . Auth::user()->id . ')' : 'Guest',
            'time'       => now()->toDateTimeString(),
          ],
          'results' => [
            'lelang' => $lelang->count(),
            'merchandise' => $merchandise->count(),
            'seniman' => $seniman->count(), // Log jumlah seniman
            'blog' => $blogs->count(),
          ]
        ];
        // Log logic (bisa diaktifkan jika perlu)
        // Log::channel('search')->info(json_encode($logData));

      } catch (Exception $logError) {
        // Silent error
      }

      // Kembalikan ke view dengan data lengkap
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

      if ($products->count() > 0 || $products) {
        return view('web.category', compact('products'));
      }

      abort(404);
    } catch (Exception $e) {
      Log::error('By Kategori Error :' . $e->getMessage());
      abort(404);
    }
  }

  public function blogDetail($slug)
  {
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
