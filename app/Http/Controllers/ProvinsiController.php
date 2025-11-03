<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provinsi;
class ProvinsiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $provinsis = Provinsi::all();
        return view('admin.master.provinsi.index',compact('provinsis'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.provinsi.create');
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
            'nama_provinsi'=>'required',
        ],[
            'nama_provinsi.required' => 'Nama provinsi wajib di isi',
        ]);
        try {
            Provinsi::create([
                'nama_provinsi'=> $request->nama_provinsi,
            ]);
            return redirect()->route('master.provinsi.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Provinsi save error ".$e->getMessage());
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
         $provinsi = Provinsi::findOrFail($id);
        return view('admin.master.provinsi.edit',compact('provinsi'));
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
            'nama_provinsi'=>'required',
        ],[
            'nama_provinsi.required' => 'Nama provinsi wajib di isi',
        ]);
        try {
            $provinsi = Provinsi::findOrFail($id);
            $provinsi->update([
                'nama_provinsi'=> $request->nama_provinsi,
            ]);
            return redirect()->route('master.provinsi.index')->with('message', 'Data berhasil di perbaharui');    
        } catch (Exception $e) {
            Log::error("Provinsi update error ".$e->getMessage());
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
            $provinsi = Provinsi::findOrFail($id);
            $provinsi->delete();
            return redirect()->route('master.provinsi.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Provinsi delete error ".$e->getMessage());
        }
    }
}
