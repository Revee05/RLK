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
        $provinces = \App\Province::orderBy('name', 'asc')->get();
        return view('admin.master.karya.create', compact('provinces'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Temporary log incoming request for debugging profession persistence
        try {
            if (app()->environment(['local', 'testing', 'development'])) {
                Log::info('Karya.store payload', $request->all());
            }
        } catch (Exception $e) {
            // ignore logging errors
        }
        $request->validate([
            'name'=>'required|string|max:255',
            'julukan'=>'nullable|string|max:255',
            'profession'=>'required|string|max:255',
            'description'=>'nullable|string',
            'bio'=>'nullable|string',
            'address'=>'nullable|string|max:500',
            'province_id'=>'nullable|exists:provinces,id',
            'city_id'=>'nullable|exists:cities,id',
            'district_id'=>'nullable|exists:districts,id',
            'social'=>'nullable|array',
            'social.email' => 'nullable|email|max:255',
            'fotoseniman'=>'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ],[
            'name.required' => 'Nama seniman wajib diisi',
            'name.max' => 'Nama seniman maksimal 255 karakter',
            'julukan.max' => 'Julukan maksimal 255 karakter',
            'profession.required' => 'Profesi wajib diisi',
            'profession.max' => 'Profesi maksimal 255 karakter',
            'address.max' => 'Alamat maksimal 500 karakter',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid',
            'city_id.exists' => 'Kota yang dipilih tidak valid',
            'district_id.exists' => 'Kecamatan yang dipilih tidak valid',
            'fotoseniman.image' => 'File harus berupa gambar',
            'fotoseniman.mimes' => 'Format gambar harus jpeg, jpg, png, atau webp',
            'fotoseniman.max' => 'Ukuran gambar maksimal 2MB',
            'social.email' => 'Format email tidak valid',
        ]);
        try {
            $imageName = '';
            if ($request->hasFile('fotoseniman')) {
                $dir = 'uploads/senimans/';
                
                // Create directory if not exists
                if (!file_exists(public_path($dir))) {
                    mkdir(public_path($dir), 0755, true);
                }
                
                $extension = strtolower($request->file('fotoseniman')->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $extension;
                $request->file('fotoseniman')->move($dir, $fileName);
                $imageName = $fileName;
            }
            
            Karya::create([
                'name'=> $request->name,
                'julukan'=> $request->julukan,
                'slug'=> Str::slug($request->name, '-'),
                'profession'=> $request->profession,
                'description'=> $request->description,
                'art_projects'=> $request->art_projects,
                'achievement'=> $request->achievement,
                'exhibition'=> $request->exhibition,
                'bio'=> $request->bio,
                'social'=> $request->social,
                'address'=> $request->address,
                'province_id'=> $request->province_id,
                'city_id'=> $request->city_id,
                'district_id'=> $request->district_id,
                'image'=> $imageName,
            ]);
            return redirect()->route('master.karya.index')->with('message', 'Data seniman berhasil ditambahkan');
        } catch (Exception $e) {
            Log::error("Karya save error ".$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat menyimpan data');
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
        $provinces = \App\Province::orderBy('name', 'asc')->get();
        $cities = $karya->province_id ? \App\City::where('province_id', $karya->province_id)->orderBy('name', 'asc')->get() : collect();
        $districts = $karya->city_id ? \App\District::where('city_id', $karya->city_id)->orderBy('name', 'asc')->get() : collect();
        return view('admin.master.karya.edit',compact('karya', 'provinces', 'cities', 'districts'));
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
        // Temporary log incoming request for debugging profession persistence
        try {
            if (app()->environment(['local', 'testing', 'development'])) {
                Log::info('Karya.update payload', $request->all());
            }
        } catch (Exception $e) {
            // ignore logging errors
        }
        $request->validate([
            'name'=>'required|string|max:255',
            'julukan'=>'nullable|string|max:255',
            'profession'=>'required|string|max:255',
            'description'=>'nullable|string',
            'art_projects'=>'nullable|string',
            'achievement'=>'nullable|string',
            'exhibition'=>'nullable|string',
            'bio'=>'nullable|string',
            'address'=>'nullable|string|max:500',
            'province_id'=>'nullable|exists:provinces,id',
            'city_id'=>'nullable|exists:cities,id',
            'district_id'=>'nullable|exists:districts,id',
            'social'=>'nullable|array',
            'social.email' => 'nullable|email|max:255',
            'fotoseniman'=>'nullable|image|mimes:jpeg,jpg,png,webp|max:2048',
        ], [
            'name.required' => 'Nama seniman wajib diisi',
            'name.max' => 'Nama seniman maksimal 255 karakter',
            'julukan.max' => 'Julukan maksimal 255 karakter',
            'profession.required' => 'Profesi wajib diisi',
            'profession.max' => 'Profesi maksimal 255 karakter',
            'address.max' => 'Alamat maksimal 500 karakter',
            'province_id.exists' => 'Provinsi yang dipilih tidak valid',
            'city_id.exists' => 'Kota yang dipilih tidak valid',
            'district_id.exists' => 'Kecamatan yang dipilih tidak valid',
            'fotoseniman.image' => 'File harus berupa gambar',
            'fotoseniman.mimes' => 'Format gambar harus jpeg, jpg, png, atau webp',
            'fotoseniman.max' => 'Ukuran gambar maksimal 2MB',
            'social.email' => 'Format email tidak valid',
        ]);
        try {
            $karya = Karya::findOrFail($id);

            // Handle image upload
            $imageName = $karya->image; // Keep old image by default
            if ($request->hasFile('fotoseniman')) {
                $dir = 'uploads/senimans/';
                
                // Create directory if not exists
                if (!file_exists(public_path($dir))) {
                    mkdir(public_path($dir), 0755, true);
                }
                
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
                'julukan'=> $request->julukan,
                'slug'=> Str::slug($request->name, '-'),
                'profession'=> $request->profession,
                'description'=> $request->description,
                'art_projects'=> $request->art_projects,
                'achievement'=> $request->achievement,
                'exhibition'=> $request->exhibition,
                'bio'=> $request->bio,
                'address'=> $request->address,
                'province_id'=> $request->province_id,
                'city_id'=> $request->city_id,
                'district_id'=> $request->district_id,
                'social'=> $request->social,
                'image'=> $imageName,
            ]);
            return redirect()->route('master.karya.index')->with('message', 'Data seniman berhasil diperbarui');
        } catch (Exception $e) {
            Log::error("Karya update error ".$e->getMessage());
            return redirect()->back()->withInput()->with('error', 'Terjadi kesalahan saat memperbarui data');
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
                return redirect()->route('master.karya.index')->with('error', 'Tidak dapat menghapus! Seniman ini masih memiliki ' . $karya->product->count() . ' produk. Hapus produk terlebih dahulu.');
            }

            // Hapus file gambar jika ada
            if ($karya->image && file_exists(public_path('uploads/senimans/' . $karya->image))) {
                @unlink(public_path('uploads/senimans/' . $karya->image));
            }

            $karya->delete();

            return redirect()->route('master.karya.index')->with('message', 'Data seniman berhasil dihapus');
        } catch (Exception $e) {
            Log::error("Karya delete error " . $e->getMessage());
            return redirect()->route('master.karya.index')->with('error', 'Terjadi kesalahan saat menghapus data');
        }
    }
}
