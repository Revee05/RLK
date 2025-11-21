<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Provinsi;
class ProvinsiController extends Controller
{
    public function index()
    {
        $provinsis = Provinsi::all();
        return view('admin.master.provinsi.index',compact('provinsis'));
    }

    public function getAll()
    {
        $provinsi = Provinsi::select('id', 'nama_provinsi')->get();
        return response()->json($provinsi);
    }

    public function create()
    {
        return view('admin.master.provinsi.create');
    }

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

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
         $provinsi = Provinsi::findOrFail($id);
        return view('admin.master.provinsi.edit',compact('provinsi'));
    }

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
