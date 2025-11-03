<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Karya;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;
class KaryaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $karyas = Karya::all();
        return view('admin.master.karya.index',compact('karyas'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.karya.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
        $request->validate([
            'name'=>'required',
            'description'=>'nullable',
            'social'=>'nullable',
        ],[
            'name.required' => 'Nama karya wajib di isi',
        ]);
        try {
            if ($request->hasFile('fotoseniman')) {
                $dir = 'uploads/senimans/';
                $extension = strtolower($request->file('fotoseniman')->getClientOriginalExtension()); // get image extension
                $fileName = uniqid() . '.' . $extension; // rename image
                $request->file('fotoseniman')->move($dir, $fileName);
                $data['fotoseniman'] =  $fileName;
            }
            Karya::create([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name, '-'),
                'description'=> $request->description,
                'social'=> $request->social,
                'address'=> $request->address,
                'image'=> $data['fotoseniman'] ?? '',
            ]);
            return redirect()->route('master.karya.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Karya save error ".$e->getMessage());
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
        $karya = Karya::findOrFail($id);
        return view('admin.master.karya.edit',compact('karya'));
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
        // dd($request->all());
        $request->validate([
            'name'=>'required',
            'description'=>'nullable',
            'social'=>'nullable',
        ],[
            'name.required' => 'Nama karya wajib di isi',
        ]);
        if ($request->hasFile('fotoseniman')) {
            $dir = 'uploads/senimans/';
            $extension = strtolower($request->file('fotoseniman')->getClientOriginalExtension()); // get image extension
            $fileName = uniqid() . '.' . $extension; // rename image
            $request->file('fotoseniman')->move($dir, $fileName);
            $data['fotoseniman'] =  $fileName;
        }
        
        try {
            $karya = Karya::findOrFail($id);

            if(empty($karya->image) AND $karya->image != NULL) {
                $profile = $karya->image;
            } else {
                $profile = $data['fotoseniman'];
            }
            $karya->update([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name, '-'),
                'description'=> $request->description,
                'address'=> $request->address,
                'social'=> $request->social,
                'image'=> $profile,
            ]);
            return redirect()->route('master.karya.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Karya save error ".$e->getMessage());
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
            $karya = Karya::findOrFail($id);
            if($karya->product->count() > 0){
                return redirect()->route('master.karya.index')->with('message', 'Jangan dihapus, seniman ini masih mempunyai produk!');   
            }
            $karya->delete();
            return redirect()->route('master.karya.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Karya delete error ".$e->getMessage());
        }
    }
}
