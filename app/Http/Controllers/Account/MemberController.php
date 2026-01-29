<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Desa;
use App\UserAddress;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Product\Uploads;
use Illuminate\Support\Facades\Log;
use Exception;

class MemberController extends Controller
{
    public function profile()
    {
        try {
            $user = Auth::user();
            $response = view('account.profile_new', compact('user'));
            if (app()->environment(['local', 'development'])) {
                Log::info('MemberController@profile response', ['user_id' => $user->id]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MemberController@profile error', ['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }
    public function updateProfile(Request $request)
    {
        $id = $request->id;
        if ($request->password == "" && $request->katasandi == FALSE) {
            $this->validate($request, [
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $id,
                'hp' => 'required|digits_between:1,15',
            ], [
                'name.required' => 'Nama wajib diisi',
                'name.max' => 'Nama maksimal 255 karakter',
                'email.required' => 'Email wajib diisi',
                'email.email' => 'Format email tidak valid',
                'email.unique' => 'Email sudah digunakan',
                'hp.required' => 'Nomor Telepon wajib di isi',
                'hp.digits_between' => 'Nomor Telepon harus berupa angka dan maksimal 15 digit',
            ]);
            $input = $request->only(['name', 'email', 'jenis_kelamin', 'hp']);
        } else {
            $this->validate($request, [
                'password' => 'required|min:6|confirmed',
            ], [
                'password.required' => 'Password harus diisi',
                'password.confirmed' => 'Konfirmasi Password harus diisi dan sama dengan Password',
                'password.min' => 'Password minimal 6 karakter',
            ]);
            $input['password'] = Hash::make($request->password);
        }
        try {
            $user = User::findOrFail($id);
            if ($id == Auth::user()->id) {
                $user->fill($input)->save();
                if (app()->environment(['local', 'development'])) {
                    Log::info('MemberController@updateProfile response', ['user_id' => $id]);
                }
                return redirect()->back()->with('success', 'Data berhasil diupdate');
            }
            Auth::logout();
            return redirect('/login');
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MemberController@updateProfile error', ['error' => $e->getMessage(), 'request' => $request->all()]);
            }
            return redirect()->back()->with('error', 'Gagal memperbarui profil. Silakan coba lagi.');
        }
    }

    public function kataSandi()
    {
        try {
            $user = User::findOrFail(Auth::user()->id);
            $response = view('account.password.password_new', compact('user'));
            if (app()->environment(['local', 'development'])) {
                Log::info('MemberController@kataSandi response', ['user_id' => $user->id]);
            }
            return $response;
        } catch (Exception $e) {
            if (app()->environment(['local', 'development'])) {
                Log::error('MemberController@kataSandi error', ['error' => $e->getMessage()]);
            }
            throw $e;
        }
    }

    /**
     * Upload profile avatar separately (AJAX or normal POST)
     */
    public function uploadAvatar(Request $request)
    {
        $this->validate($request, [
            'avatar' => 'required|image|max:4096',
        ], [
            'avatar.required' => 'Silakan pilih gambar',
            'avatar.image' => 'File harus berupa gambar',
            'avatar.max' => 'Ukuran gambar maksimal 4MB',
        ]);

        try {
            $user = Auth::user();
            if (! $user) {
                return redirect()->route('login');
            }

            $uploads = new Uploads();
            $path = $uploads->handleUpload($request->file('avatar'));

            // Save directly to model to avoid fillable issues
            $user->foto = $path;
            $user->save();

            if ($request->ajax()) {
                if (app()->environment(['local', 'development'])) {
                    Log::info('MemberController@uploadAvatar response (ajax)', ['user_id' => $user->id]);
                }
                return response()->json(['success' => true, 'path' => $path]);
            }

            if (app()->environment(['local', 'development'])) {
                Log::info('MemberController@uploadAvatar response', ['user_id' => $user->id]);
            }

            return redirect()->back()->with('message', 'Foto profil berhasil diperbarui');
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            if ($request->ajax()) {
                return response()->json(['success' => false, 'message' => 'Gagal mengunggah gambar'], 500);
            }
            return redirect()->back()->with('error', 'Gagal mengunggah gambar');
        }
    }
}
