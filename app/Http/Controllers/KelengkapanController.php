<?php

namespace App\Http\Controllers;

use App\Kelengkapan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KelengkapanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kelengkapans = Kelengkapan::all();
        return view('admin.master.kelengkapan.index',compact('kelengkapans'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.kelengkapan.create');
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
            'name'=>'required|unique:kelengkapan',
        ],[
            'name.required' => 'Nama kelengkapan wajib di isi',
            'name.unique' => 'Nama kelengkapan sudah ada, ganti dengan nama lain',
        ]);
        try {
            Kelengkapan::create([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name,'-')
            ]);
            return redirect()->route('master.kelengkapan.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kelengkapan save error ".$e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $kelengkapan = Kelengkapan::findOrFail($id);
        return view('admin.master.kelengkapan.edit',compact('kelengkapan'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'=>'required',
        ],[
            'name.required' => 'Nama kelengkapan wajib di isi',
        ]);
        try {
            $kelengkapan = Kelengkapan::findOrFail($id);
            $kelengkapan->update([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name,'-')
            ]);
            return redirect()->route('master.kelengkapan.index')->with('message', 'Data berhasil di perbaharui');    
        } catch (Exception $e) {
            Log::error("Kelengkapan update error ".$e->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $kelengkapan = Kelengkapan::findOrFail($id);
            $kelengkapan->delete();
            return redirect()->route('master.kelengkapan.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Kelengkapan delete error ".$e->getMessage());
        }
    }
}
