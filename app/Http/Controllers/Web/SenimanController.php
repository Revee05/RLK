<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Karya;

class SenimanController extends Controller
{
    public function index(Request $request)
    {
        $query = Karya::query();

        // Filter berdasarkan pencarian
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('bio', 'like', "%{$search}%")
                  ->orWhere('address', 'like', "%{$search}%");
            });
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
                'slug' => $item->slug,
                'address' => $item->address,
                'bio' => $item->bio,
                'image' => $item->image,
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

        return view('web.Seniman.seniman', compact('senimans', 'request'));
    }

    public function detail($slug)
    {
        $seniman = Karya::where('slug', $slug)->firstOrFail();
        
        // Ambil produk dari seniman ini dengan eager load imageUtama
        $products = $seniman->product()->with('imageUtama')->paginate(12);

        // Hitung statistik
        $totalProducts = $seniman->product()->count();
        
        // Data seniman yang lebih lengkap
        $senimanData = [
            'id' => $seniman->id,
            'name' => $seniman->name,
            'slug' => $seniman->slug,
            'address' => $seniman->address,
            'bio' => $seniman->bio,
            'description' => $seniman->description,
            'social' => $seniman->social,
            'image' => $seniman->image,
            'created_at' => $seniman->created_at,
            'total_products' => $totalProducts,
        ];

        \Log::info('SenimanController@detail response', [
            'seniman' => $senimanData,
            'products' => $products->items(),
            'pagination' => [
                'current_page' => $products->currentPage(),
                'last_page' => $products->lastPage(),
                'per_page' => $products->perPage(),
                'total' => $products->total(),
            ],
        ]);

        return view('web.Seniman.seniman-detail', compact('seniman', 'products'));
    }
}
