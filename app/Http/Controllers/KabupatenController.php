<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kabupaten;
use App\Provinsi;
class KabupatenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kabupatens = Kabupaten::all();
        return view('admin.master.kabupaten.index',compact('kabupatens'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        return view('admin.master.kabupaten.create',compact('provinsis'));
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
            'provinsi_id'=>'required',
            'nama_kabupaten'=>'required',
        ],[
            'provinsi_id.required' => 'Nama provinsi wajib di isi',
            'nama_kabupaten.required' => 'Nama Kabupaten wajib di isi',
        ]);
        try {
            Kabupaten::create([
                'provinsi_id'=> $request->provinsi_id,
                'nama_kabupaten'=> $request->nama_kabupaten,
            ]);
            return redirect()->route('master.kabupaten.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kabupaten save error ".$e->getMessage());
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
        $kabupaten = Kabupaten::findOrFail($id);
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        return view('admin.master.kabupaten.edit',compact('kabupaten','provinsis'));
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
            'provinsi_id'=>'required',
            'nama_kabupaten'=>'required',
        ],[
            'provinsi_id.required' => 'Nama provinsi wajib di isi',
            'nama_kabupaten.required' => 'Nama Kabupaten wajib di isi',
        ]);
        try {
            $kabupaten = Kabupaten::findOrFail($id);
            $kabupaten->update([
                'provinsi_id'=> $request->provinsi_id,
                'nama_kabupaten'=> $request->nama_kabupaten,
            ]);
            return redirect()->route('master.kabupaten.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kabupaten save error ".$e->getMessage());
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
            $kabupaten = Kabupaten::findOrFail($id);
            $kabupaten->delete();
            return redirect()->route('master.kabupaten.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Kabupaten delete error ".$e->getMessage());
        }
    }
}
