<?php

namespace App\Http\Controllers\MerchController;

use App\Http\Controllers\Controller;
use App\models\MerchCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MerchCategoryController extends Controller
{
    public function index()
    {
        $categories = MerchCategory::all();
        return view('admin.master.merchCategory.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.master.merchCategory.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|unique:merch_categories,name',
        ]);
        MerchCategory::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return redirect()->route('master.merchCategory.index')->with('success', 'Category created!');
    }

    public function edit($id)
    {
        $category = MerchCategory::findOrFail($id);
        return view('admin.master.merchCategory.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $category = MerchCategory::findOrFail($id);
        $request->validate([
            'name' => 'required|unique:merch_categories,name,'.$id,
        ]);
        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);
        return redirect()->route('master.merchCategory.index')->with('success', 'Category updated!');
    }

    public function destroy($id)
    {
        $category = MerchCategory::findOrFail($id);
        $category->delete();
        return redirect()->route('master.merchCategory.index')->with('success', 'Category deleted!');
    }
}