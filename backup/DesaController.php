<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Desa;
use App\Kecamatan;

class DesaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $desas = Desa::all();
        return view('admin.master.desa.index',compact('desas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kecamatans = Kecamatan::pluck('nama_kecamatan','id');
        return view('admin.master.desa.create',compact('kecamatans'));
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
            'kecamatan_id'=>'required',
            'nama_desa'=>'required',
            'kodepos'=>'required',
        ],[
            'kecamatan_id.required' => 'Nama kecamatan wajib di isi',
            'nama_desa.required' => 'Nama desa wajib di isi',
            'kodepos.required' => 'kodepos wajib di isi',
        ]);
        try {
            Desa::create([
                'kecamatan_id'=> $request->kecamatan_id,
                'nama_desa'=> $request->nama_desa,
                'kodepos'=> $request->kodepos,
            ]);
            return redirect()->route('master.desa.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Desa save error ".$e->getMessage());
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
        $desa = Desa::findOrFail($id);
        $kecamatans = Kecamatan::pluck('nama_kecamatan','id');
        return view('admin.master.desa.edit',compact('desa','kecamatans'));
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
            'kecamatan_id'=>'required',
            'nama_desa'=>'required',
            'kodepos'=>'required',
        ],[
            'kecamatan_id.required' => 'Nama kecamatan wajib di isi',
            'nama_desa.required' => 'Nama desa wajib di isi',
            'kodepos.required' => 'kodepos wajib di isi',
        ]);
        try {
            $desa = Desa::findOrFail($id);
            $desa->update([
                'kecamatan_id'=> $request->kecamatan_id,
                'nama_desa'=> $request->nama_desa,
                'kodepos'=> $request->kodepos,
            ]);
            return redirect()->route('master.desa.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Desa save error ".$e->getMessage());
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
            $desa = Desa::findOrFail($id);
            $desa->delete();
            return redirect()->route('master.desa.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Desa delete error ".$e->getMessage());
        }
    }
}
