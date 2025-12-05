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

        // Set file_path = null
        $panduan->update(['file_path' => null]);

        return back()->with('success', 'File panduan berhasil dihapus.');
    }
}
