<?php

namespace App\Http\Controllers;

use App\Kategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
class KategoriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {   
        $getType = $request->input('cat_type');
        if ($getType == 'product') {
            $kategoris = Kategori::product()->get();
        } else {
            $kategoris = Kategori::blog()->get();
        }
        return view('admin.master.kategori.index',compact('kategoris','getType'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $getType = $request->input('cat_type');
        return view('admin.master.kategori.create',compact('getType'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required|unique:kategori',
        ],[
            'name.required' => 'Nama kategori wajib di isi',
            'name.unique' => 'Nama kategori sudah ada, ganti dengan nama lain',
        ]);
        try {
            $getType = $request->input('cat_type');

            Kategori::create([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name,'-'),
                'cat_type'=> $getType,
            ]);
            return redirect()->route('master.kategori.index',['cat_type'=>$getType])->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kategori save error ".$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Kategori  $kategori
     * @return \Illuminate\Http\Response
     */
    public function show(Kategori $kategori)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Kategori  $kategori
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $kategori = Kategori::findOrFail($id);
        $getType = $request->input('cat_type');
        return view('admin.master.kategori.edit',compact('kategori','getType'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Kategori  $kategori
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
        ],[
            'name.required' => 'Nama kategori wajib di isi',
        ]);
        try {
            $getType = $request->input('cat_type');

            $kategori = Kategori::findOrFail($id);
            $kategori->update([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name,'-'),
                'cat_type'=> $getType,
            ]);
            return redirect()->route('master.kategori.index',['cat_type'=>$getType])->with('message', 'Data berhasil di perbaharui');    
        } catch (Exception $e) {
            Log::error("Kategori update error ".$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Kategori  $kategori
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        try {
            $getType = $request->input('cat_type');

            $kategori = Kategori::findOrFail($id);
            $kategori->delete();
            return redirect()->route('master.kategori.index',['cat_type'=>$getType])->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Kategori delete error ".$e->getMessage());
        }
    }
}
