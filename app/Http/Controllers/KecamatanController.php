<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Kabupaten;
use App\Kecamatan;
class KecamatanController extends Controller
{
    public function index()
    {
        $kecamatans = Kecamatan::all();
        return view('admin.master.kecamatan.index',compact('kecamatans'));
    }

    public function getByKabupaten($kabupaten_id)
    {
        return response()->json(
            Kecamatan::where('kabupaten_id', $kabupaten_id)
                ->select('id', 'nama_kecamatan')
                ->get()
        );
    }

    public function create()
    {
        $kabupatens = Kabupaten::pluck('nama_kabupaten','id');
        return view('admin.master.kecamatan.create',compact('kabupatens'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'kabupaten_id'=>'required',
            'nama_kecamatan'=>'required',
        ],[
            'kabupaten_id.required' => 'Nama provinsi wajib di isi',
            'nama_kecamatan.required' => 'Nama Kabupaten wajib di isi',
        ]);
        try {
            Kecamatan::create([
                'kabupaten_id'=> $request->kabupaten_id,
                'nama_kecamatan'=> $request->nama_kecamatan,
            ]);
            return redirect()->route('master.kecamatan.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kecamatan save error ".$e->getMessage());
        }
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        $kecamatan = Kecamatan::findOrFail($id);
        $kabupatens = Kabupaten::pluck('nama_kabupaten','id');
        return view('admin.master.kecamatan.edit',compact('kecamatan','kabupatens'));
    }

    public function update(Request $request, $id)
    {
         $request->validate([
            'kabupaten_id'=>'required',
            'nama_kecamatan'=>'required',
        ],[
            'kabupaten_id.required' => 'Nama provinsi wajib di isi',
            'nama_kecamatan.required' => 'Nama Kabupaten wajib di isi',
        ]);
        try {
            $kecamatan = Kecamatan::findOrFail($id);
            $kecamatan->update([
                'kabupaten_id'=> $request->kabupaten_id,
                'nama_kecamatan'=> $request->nama_kecamatan,
            ]);
            return redirect()->route('master.kecamatan.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Kecamatan save error ".$e->getMessage());
        }
    }

    public function destroy($id)
    {
         try {
            $kecamatan = Kecamatan::findOrFail($id);
            $kecamatan->delete();
            return redirect()->route('master.kecamatan.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("kecamatan delete error ".$e->getMessage());
        }
    }
}
