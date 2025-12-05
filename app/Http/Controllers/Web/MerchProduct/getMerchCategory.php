<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchCategory;
use Illuminate\Http\Request;

class GetMerchCategory extends Controller
{
    public function __invoke(Request $request)
    {
        $cacheKey = 'merch_categories_list';

        if (app()->environment(['local', 'testing', 'development'])) {
            if (\Cache::has($cacheKey)) {
                \Log::info('GetMerchCategory cache hit', ['key' => $cacheKey]);
            } else {
                \Log::info('GetMerchCategory cache miss', ['key' => $cacheKey]);
            }
        }

        $categories_version = \Cache::remember('merch_categories_version', 3600, function () {
            return MerchCategory::max('updated_at') ?: now();
        });

        // jika hanya diminta version, kembalikan versi saja (lebih ringan)
        if ($request->boolean('version_only') || $request->query('version_only')) {
            if (app()->environment(['local', 'testing', 'development'])) {
                \Log::info('GetMerchCategory version_only response', ['categories_version' => $categories_version]);
            }
            return response()->json(['categories_version' => $categories_version]);
        }

        $categories = \Cache::remember($cacheKey, 3600, function () {
            return MerchCategory::select('id', 'name', 'slug')->orderBy('name')->get();
        });

        $responseData = [
            'categories' => $categories,
            'categories_version' => $categories_version,
        ];

        if (app()->environment(['local', 'testing', 'development'])) {
            \Log::info('GetMerchCategory response', [
                'categories' => $categories instanceof \Illuminate\Support\Collection ? $categories->toArray() : $categories,
                'categories_version' => (string)$categories_version,
            ]);
        }

        return response()->json($responseData);
    }
}