<?php

namespace App\Http\Controllers;

use App\TeamMember;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class TeamMembersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $teamMembers = TeamMember::orderBy('id')->get();
        return view('admin.master.team.index', compact('teamMembers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.team.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
            'role' => 'required',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'avatar' => 'required|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $avatarPath = null;
            if ($request->hasFile('avatar')) {
                $dir = public_path('uploads/team');
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $extension = strtolower($request->file('avatar')->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $extension;
                $request->file('avatar')->move($dir, $fileName);
                $avatarPath = 'uploads/team/' . $fileName;
            }

            TeamMember::create([
                'name' => $request->name,
                'role' => $request->role,
                'email' => $request->email,
                'instagram' => $request->instagram,
                'avatar' => $avatarPath,
            ]);

            return redirect()->route('master.team.index')->with('message', 'Data berhasil disimpan');
        } catch (\Exception $e) {
            Log::error('Team member save error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menyimpan data');
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        try {
            $teamMember = TeamMember::findOrFail($id);
            return view('admin.master.team.edit', compact('teamMember'));
        } catch (\Exception $e) {
            Log::error('Team member edit error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Data tidak ditemukan');
        }
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
            'name' => 'required',
            'role' => 'required',
            'email' => 'nullable|email',
            'instagram' => 'nullable|url',
            'avatar' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ]);

        try {
            $teamMember = TeamMember::findOrFail($id);
            $avatarPath = $teamMember->avatar;

            if ($request->hasFile('avatar')) {
                $dir = public_path('uploads/team');
                if (!File::exists($dir)) {
                    File::makeDirectory($dir, 0755, true);
                }

                $extension = strtolower($request->file('avatar')->getClientOriginalExtension());
                $fileName = uniqid() . '.' . $extension;
                $request->file('avatar')->move($dir, $fileName);
                $avatarPath = 'uploads/team/' . $fileName;

                $this->deleteUploadedAvatar($teamMember->avatar);
            }

            $teamMember->update([
                'name' => $request->name,
                'role' => $request->role,
                'email' => $request->email,
                'instagram' => $request->instagram,
                'avatar' => $avatarPath,
            ]);

            return redirect()->route('master.team.index')->with('message', 'Data berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Team member update error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui data');
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
            $teamMember = TeamMember::findOrFail($id);
            $this->deleteUploadedAvatar($teamMember->avatar);
            $teamMember->delete();

            return redirect()->route('master.team.index')->with('message', 'Data berhasil dihapus');
        } catch (\Exception $e) {
            Log::error('Team member delete error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus data');
        }
    }

    private function deleteUploadedAvatar($avatarPath)
    {
        if (!$avatarPath) {
            return;
        }

        if (strpos($avatarPath, 'uploads/team/') === 0) {
            $fullPath = public_path($avatarPath);
            if (File::exists($fullPath)) {
                File::delete($fullPath);
            }
        }
    }
}
