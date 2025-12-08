<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Panduan;

class PanduanController extends Controller
{
    public function index()
    {
        $semuaPanduan = Panduan::orderBy('title')->get();

        // Jika database masih kosong
        if ($semuaPanduan->count() == 0) {
            return view('web.panduan.index', [
                'panduan' => null,
                'semuaPanduan' => []
            ]);
        }

        // Jika ada data, tampilkan data pertama
        $panduan = $semuaPanduan->first();

        return view('web.panduan.index', compact('panduan', 'semuaPanduan'));
}


    public function loadPanduan($slug)
    {
        $panduan = Panduan::where('slug', $slug)->firstOrFail();

        return response()->json([
            'title'     => $panduan->title,
            'file_path' => $panduan->file_path ? asset($panduan->file_path) : null
        ]);
    }
}