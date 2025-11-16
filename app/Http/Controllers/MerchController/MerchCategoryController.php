<?php

namespace App\Http\Controllers\MerchController;

use App\Http\Controllers\Controller;
use App\models\MerchCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Exception;

class MerchCategoryController extends Controller
{
    public function index()
    {
        try {
            $categories = MerchCategory::select('id', 'name', 'slug')->get();
            $response = view('admin.master.merchCategory.index', compact('categories'));
            if (app()->environment(['local', 'development'])) {
                // Ambil hanya id dan name untuk log
                $logCategories = $categories->map(function ($cat) {
                    return [
                        'id' => $cat->id,
                        'name' => $cat->name,
                        'slug' => $cat->slug,
                    ];
                });
                Log::info('MerchCategoryController@index response', ['categories' => $logCategories]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@index error', ['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    public function create()
    {
        try {
            $response = view('admin.master.merchCategory.create');
            if (app()->environment(['local', 'development'])) {
                Log::info('MerchCategoryController@create response');
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@create error', ['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'categories' => 'required|array|min:1',
                'categories.*.name' => 'required|string|distinct|unique:merch_categories,name',
            ]);

            $created = [];
            foreach ($request->categories as $category) {
                $created[] = MerchCategory::create([
                    'name' => $category['name'],
                    'slug' => \Illuminate\Support\Str::slug($category['name']),
                ]);
            }

            $response = redirect()->route('master.merchCategory.index')->with('success', 'Categories created!');
            if (app()->environment(['local', 'development'])) {
                Log::info('MerchCategoryController@store response', ['created' => $created]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@store error', ['error' => $e->getMessage(), 'request' => $request->all()]);
            }
            throw $e;
        }
    }

    public function edit($id)
    {
        try {
            $category = MerchCategory::findOrFail($id);
            $response = view('admin.master.merchCategory.edit', compact('category'));
            if (app()->environment(['local', 'development'])) {
                Log::info('MerchCategoryController@edit response', ['category' => $category]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@edit error', ['error' => $e->getMessage(), 'id' => $id]);
            }
            throw $e;
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $category = MerchCategory::findOrFail($id);
            $request->validate([
                'name' => 'required|unique:merch_categories,name,'.$id,
            ]);
            $category->update([
                'name' => $request->name,
                'slug' => Str::slug($request->name),
            ]);
            $response = redirect()->route('master.merchCategory.index')->with('success', 'Category updated!');
            if (app()->environment(['local', 'development'])) {
                Log::info('MerchCategoryController@update response', ['updated' => $category]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@update error', ['error' => $e->getMessage(), 'id' => $id, 'request' => $request->all()]);
            }
            throw $e;
        }
    }

    public function destroy($id)
    {
        try {
            $category = MerchCategory::findOrFail($id);
            $category->delete();
            $response = redirect()->route('master.merchCategory.index')->with('success', 'Category deleted!');
            if (app()->environment(['local', 'development'])) {
                Log::info('MerchCategoryController@destroy response', ['deleted_id' => $id]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MerchCategoryController@destroy error', ['error' => $e->getMessage(), 'id' => $id]);
            }
            throw $e;
        }
    }
}