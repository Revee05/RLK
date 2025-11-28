<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Karya;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Facades\Image;
use Exception;
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
            'bio'=>'nullable',
            'social'=>'nullable',
        ],[
            'name.required' => 'Nama karya wajib di isi',
        ]);
        try {
            $imageName = '';
            if ($request->hasFile('fotoseniman')) {
                $dir = 'uploads/senimans/';
                $extension = strtolower($request->file('fotoseniman')->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $extension;
                $request->file('fotoseniman')->move($dir, $fileName);
                $imageName = $fileName;
            }
            
            Karya::create([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name, '-'),
                'description'=> $request->description,
                'bio'=> $request->bio,
                'social'=> $request->social,
                'address'=> $request->address,
                'image'=> $imageName,
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
        $request->validate([
            'name'=>'required',
            'description'=>'nullable',
            'social'=>'nullable',
        ],[
            'name.required' => 'Nama karya wajib di isi',
        ]);
        try {
            $karya = Karya::findOrFail($id);

            // Handle image upload
            $imageName = $karya->image; // Keep old image by default
            if ($request->hasFile('fotoseniman')) {
                $dir = 'uploads/senimans/';
                $extension = strtolower($request->file('fotoseniman')->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $extension;
                $request->file('fotoseniman')->move($dir, $fileName);

                // Hapus gambar lama jika ada dan berbeda
                if ($karya->image && file_exists(public_path($dir . $karya->image))) {
                    @unlink(public_path($dir . $karya->image));
                }

                $imageName = $fileName; // Use new image
            }

            $karya->update([
                'name'=> $request->name,
                'slug'=> Str::slug($request->name, '-'),
                'description'=> $request->description,
                'bio'=> $request->bio,
                'address'=> $request->address,
                'social'=> $request->social,
                'image'=> $imageName,
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

            // Cek jika masih punya produk
            if ($karya->product->count() > 0) {
                return redirect()->route('master.karya.index')->with('message', 'Jangan dihapus, seniman ini masih mempunyai produk!');
            }

            // Hapus file gambar jika ada
            if ($karya->image && file_exists(public_path('uploads/senimans/' . $karya->image))) {
                @unlink(public_path('uploads/senimans/' . $karya->image));
            }

            // Hapus relasi lain jika ada (contoh: hapus produk, gambar produk, dsb)
            // Contoh jika ingin hapus produk juga:
            // foreach ($karya->product as $product) {
            //     // Hapus gambar produk jika ada
            //     foreach ($product->images as $img) {
            //         if ($img->path && file_exists(public_path($img->path))) {
            //             @unlink(public_path($img->path));
            //         }
            //         $img->delete();
            //     }
            //     $product->delete();
            // }

            $karya->delete();

            return redirect()->route('master.karya.index')->with('message', 'Data berhasil dihapus');
        } catch (Exception $e) {
            Log::error("Karya delete error " . $e->getMessage());
        }
    }
}
