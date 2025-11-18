<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kabupaten;
use App\Provinsi;
class KabupatenController extends Controller
{
    public function index()
    {
        $kabupatens = Kabupaten::all();
        return view('admin.master.kabupaten.index',compact('kabupatens'));
    }

    public function getByProvinsi($provinsi_id)
    {
        return response()->json(
            Kabupaten::where('provinsi_id', $provinsi_id)
                ->select('id', 'nama_kabupaten')
                ->get()
        );
    }

    public function create()
    {
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        return view('admin.master.kabupaten.create',compact('provinsis'));
    }

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

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $kabupaten = Kabupaten::findOrFail($id);
        $provinsis = Provinsi::pluck('nama_provinsi','id');
        return view('admin.master.kabupaten.edit',compact('kabupaten','provinsis'));
    }

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
