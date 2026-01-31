<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File; // Tambahan untuk hapus file manual

class EventController extends Controller
{
    /**
     * Menampilkan daftar semua event.
     */
    public function index()
    {
        $events = Event::latest()->get();
        return view('admin.master.events.index', compact('events'));
    }

    /**
     * Menampilkan form untuk membuat event baru.
     */
    public function create()
    {
        return view('admin.master.events.create');
    }

    /**
     * Menyimpan event baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi Input
        $this->validateRequest($request);

        // 2. Siapkan data selain gambar
        $data = $request->except(['image', 'image_mobile']);

        // 3. Proses Upload Gambar Desktop (Landscape) ke public/uploads/events
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            // Buat nama unik: timestamp_namapli.jpg
            $filename = time() . '_desktop_' . $file->getClientOriginalName();
            
            // Pindahkan file ke public/uploads/events
            $file->move(public_path('uploads/events'), $filename);
            
            // Simpan path relatif ke database
            $data['image'] = 'uploads/events/' . $filename;
        }

        // 4. Proses Upload Gambar Mobile (Portrait) ke public/uploads/events
        if ($request->hasFile('image_mobile')) {
            $file = $request->file('image_mobile');
            $filename = time() . '_mobile_' . $file->getClientOriginalName();
            
            $file->move(public_path('uploads/events'), $filename);
            
            $data['image_mobile'] = 'uploads/events/' . $filename;
        }

        // 5. Simpan ke Database
        Event::create($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil ditambahkan.');
    }

    /**
     * Menampilkan form edit event.
     */
    public function edit(Event $event)
    {
        return view('admin.master.events.edit', compact('event'));
    }

    /**
     * Mengupdate data event yang sudah ada.
     */
    public function update(Request $request, Event $event)
    {
        // 1. Validasi Input
        $this->validateRequest($request);

        // 2. Siapkan data update
        $data = $request->except(['image', 'image_mobile']);

        // 3. Cek & Update Gambar Desktop
        if ($request->hasFile('image')) {
            // Hapus gambar lama dulu
            $this->deleteImage($event->image);
            
            // Upload yang baru
            $file = $request->file('image');
            $filename = time() . '_desktop_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/events'), $filename);
            
            $data['image'] = 'uploads/events/' . $filename;
        }

        // 4. Cek & Update Gambar Mobile
        if ($request->hasFile('image_mobile')) {
            $this->deleteImage($event->image_mobile);
            
            $file = $request->file('image_mobile');
            $filename = time() . '_mobile_' . $file->getClientOriginalName();
            $file->move(public_path('uploads/events'), $filename);
            
            $data['image_mobile'] = 'uploads/events/' . $filename;
        }

        // 5. Update Database
        $event->update($data);

        return redirect()->route('admin.events.index')
            ->with('success', 'Event berhasil diperbarui.');
    }

    /**
     * Fitur Tambahan: Update Status Cepat.
     */
    public function updateStatus(Request $request, Event $event)
    {
        $request->validate([
            'status' => 'required|in:active,coming_soon,inactive'
        ]);

        $event->update(['status' => $request->status]);

        return redirect()->back()->with('success', 'Status publikasi berhasil diubah.');
    }

    /**
     * Menghapus event dan gambarnya.
     */
    public function destroy(Event $event)
    {
        // Hapus kedua file gambar dari folder uploads
        $this->deleteImage($event->image);
        $this->deleteImage($event->image_mobile);

        // Hapus data dari database
        $event->delete();

        return redirect()->back()->with('success', 'Event berhasil dihapus.');
    }

    // =========================================================================
    // PRIVATE HELPER FUNCTIONS
    // =========================================================================

    /**
     * Validasi request.
     */
    private function validateRequest(Request $request)
    {
        $request->validate([
            'title'         => 'required|string|max:255',
            'link'          => 'nullable|url',
            'status'        => 'required|in:active,coming_soon,inactive',
            'online_period' => 'nullable|string|max:255',
            'offline_date'  => 'nullable|string|max:255',
            'location'      => 'nullable|string|max:255',
            'image'         => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'image_mobile'  => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
        ]);
    }

    /**
     * Fungsi hapus gambar manual dari folder public/uploads.
     */
    private function deleteImage($path)
    {
        // $path contohnya: 'uploads/events/gambar.jpg'
        if ($path) {
            $fullPath = public_path($path); // Menjadi C:\laragon\www\RLK\public\uploads\events\gambar.jpg
            if (file_exists($fullPath)) {
                unlink($fullPath); // Hapus file fisik
            }
        }
    }
}