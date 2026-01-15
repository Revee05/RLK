<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Panduan;

class PanduanAdminController extends Controller
{
    public function index()
    {
        $panduan = Panduan::all();
        return view('admin.panduan.index', compact('panduan'));
    }

    public function upload(Request $request, $id)
    {
        $request->validate([
            'pdf' => 'required|mimes:pdf|max:20480', // maksimal 20 MB
        ]);

        $panduan = Panduan::findOrFail($id);

        // Hapus file lama jika ada
        if ($panduan->file_path && file_exists(public_path($panduan->file_path))) {
            unlink(public_path($panduan->file_path));
        }

        // Upload file baru
        $filename = time() . '-' . $request->file('pdf')->getClientOriginalName();
        $path = $request->file('pdf')->move('uploads/panduan', $filename);

        // Simpan ke database
        $panduan->update([
            'file_path' => $path
        ]);

        return back()->with('success', 'File panduan berhasil diupload.');
    }

    public function hapus($id)
    {
        $panduan = Panduan::findOrFail($id);

        // Hapus file fisik
        if ($panduan->file_path && file_exists(public_path($panduan->file_path))) {
            unlink(public_path($panduan->file_path));
        }

        // Hapus seluruh record dari database
        $panduan->delete();

        return back()->with('success', 'Panduan berhasil dihapus.');
    }

    public function create()
    {
        return view('admin.panduan.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|unique:panduan,title',
            'slug'  => 'required|unique:panduan,slug',
            'pdf'   => 'nullable|mimes:pdf|max:5000',
        ], [
            'title.unique' => 'Judul panduan sudah ada. Silahkan upload ulang file pada panduan tersebut.',
        ]);

        $panduan = new Panduan();
        $panduan->title = $request->title;
        $panduan->slug = $request->slug;

        if ($request->hasFile('pdf')) {
            $file = $request->file('pdf');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move('uploads/panduan/', $filename);
            $panduan->file_path = 'uploads/panduan/' . $filename;
        }

        $panduan->save();

        return redirect()->route('admin.panduan.index')->with('success', 'Panduan baru berhasil ditambahkan.');
    }
    
    public function edit($id)
    {
        $panduan = Panduan::findOrFail($id);
        return view('admin.panduan.edit', compact('panduan'));
    }

    public function update(Request $request, $id)
    {
        $panduan = Panduan::findOrFail($id);

        // 1. Validasi: Title wajib, PDF opsional (nullable)
        $request->validate([
            'title' => 'required|unique:panduan,title,' . $panduan->id,
            'pdf'   => 'nullable|mimes:pdf|max:20480', // Max 20MB
        ]);

        // 2. Update data teks
        $panduan->title = $request->title;
        // Jika ada slug, update juga (opsional, tergantung kebutuhan)
        // $panduan->slug = Str::slug($request->title); 

        // 3. Cek apakah user mengupload file baru?
        if ($request->hasFile('pdf')) {
            
            // Hapus file lama jika ada
            if ($panduan->file_path && file_exists(public_path($panduan->file_path))) {
                unlink(public_path($panduan->file_path));
            }

            // Upload file baru
            $file = $request->file('pdf');
            $filename = time() . '_' . $file->getClientOriginalName();
            $file->move('uploads/panduan/', $filename);
            
            // Simpan path baru ke database
            $panduan->file_path = 'uploads/panduan/' . $filename;
        }

        $panduan->save();

        return redirect()->route('admin.panduan.index')
            ->with('success', 'Panduan berhasil diperbarui.');
    }

// Catatan: Function public function upload(...) BOLEH DIHAPUS karena sudah tidak dipakai.


}
