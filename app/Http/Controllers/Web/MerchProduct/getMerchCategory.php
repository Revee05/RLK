<?php

namespace App\Http\Controllers\Web\MerchProduct;

use App\Http\Controllers\Controller;
use App\models\MerchCategory;
use Illuminate\Http\Request;

class GetMerchCategory extends Controller
{
    public function __invoke()
    {
        $categories = \Cache::remember('merch_categories_list', 3600, function () {
            return MerchCategory::select('id', 'name', 'slug')->orderBy('name')->get();
        });

        $categories_version = \Cache::remember('merch_categories_version', 3600, function () {
            return MerchCategory::max('updated_at') ?: now();
        });

        return response()->json([
            'categories' => $categories,
            'categories_version' => $categories_version,
        ]);
    }
}