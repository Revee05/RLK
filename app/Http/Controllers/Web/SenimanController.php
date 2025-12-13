<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Karya;
use App\Province;
use App\City;
use App\District;

class SenimanController extends Controller
{
    public function index(Request $request)
    {
        // eager load relations and product count
        $query = Karya::with(['province', 'city', 'district'])->withCount('product');

        // Ambil daftar kota untuk filter dropdown
        $cities = City::orderBy('name')->get();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%")
                  ->orWhereHas('province', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('city', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('district', function($query) use ($search) {
                      $query->where('name', 'like', "%{$search}%");
                  });
            });
        }

        // Filter berdasarkan kota (pastikan integer dan bukan empty)
        if ($request->filled('city')) {
            $cityId = (int) $request->city;
            if ($cityId > 0) {
                $query->where('city_id', $cityId);
            }
        }

        // Sorting
        switch ($request->sort) {
            case 'name_asc':
                $query->orderBy('name', 'asc');
                break;
            case 'name_desc':
                $query->orderBy('name', 'desc');
                break;
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $senimans = $query->paginate(12);

        // Ambil hanya data penting
        $senimans->getCollection()->transform(function($item) {
            return (object)[
                'id' => $item->id,
                'name' => $item->name,
                'julukan' => $item->julukan,
                'slug' => $item->slug,
                'address' => $item->address,
                'bio' => $item->bio,
                'image' => $item->image,
                'province' => $item->province ? $item->province->name : null,
                'city' => $item->city ? $item->city->name : null,
                'district' => $item->district ? $item->district->name : null,
                'total_products' => $item->product_count ?? 0,
            ];
        });

        \Log::info('SenimanController@index response', [
            'senimans' => $senimans->items(),
            'pagination' => [
                'current_page' => $senimans->currentPage(),
                'last_page' => $senimans->lastPage(),
                'per_page' => $senimans->perPage(),
                'total' => $senimans->total(),
            ],
        ]);

        return view('web.Seniman.seniman', compact('senimans', 'request', 'cities'));
    }

    public function detail($slug)
    {
        $seniman = Karya::with(['province', 'city', 'district'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Ambil produk dari seniman ini dengan eager load imageUtama
        $products = $seniman->product()->with('imageUtama')->paginate(12);

        // Hitung statistik
        $totalProducts = $seniman->product()->count();

        // Data seniman yang lebih ringkas (tanpa created_at)
        $senimanData = [
            'id' => $seniman->id,
            'name' => $seniman->name,
            'julukan' => $seniman->julukan,
            'slug' => $seniman->slug,
            'address' => $seniman->address,
            'profession' => $seniman->profession,
            'bio' => $seniman->bio,
            'description' => $seniman->description,
            'art_projects' => $seniman->art_projects,
            'achievement' => $seniman->achievement,
            'exhibition' => $seniman->exhibition,
            'social' => $seniman->social,
            'image' => $seniman->image,
            'province' => $seniman->province,
            'city' => $seniman->city,
            'district' => $seniman->district,
            'total_products' => $totalProducts,
            'created_at' => $seniman->created_at,
        ];

        // Slice data produk: hanya ambil field penting
        $productsData = collect($products->items())->map(function($product) {
            return [
                'id' => $product->id,
                'title' => $product->title,
                'slug' => $product->slug,
                'price' => $product->price,
                // 'description' => $product->description,
                'image_utama' => $product->imageUtama ? $product->imageUtama->path : null,
            ];
        });

        \Log::info('SenimanController@detail response', [
            'seniman' => $senimanData,
            'products' => $productsData,
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);

        // Kirim data ke view
        return view('web.Seniman.seniman-detail', [
            'seniman' => (object)$senimanData,
            'products' => $products, // tetap kirim paginator untuk links()
            'productsData' => $productsData,
        ]);
    }
}
