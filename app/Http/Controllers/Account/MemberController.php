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

class MemberController extends Controller
{
    public function profile()
    {
        $user = Auth::user();
        return view('account.profile_new', compact('user'));
    }
    public function updateProfile(Request $request)
    {
        $id = $request->id;
        if ($request->password == "" && $request->katasandi == FALSE) {
            $this->validate($request, [
                'email' => 'required|email|unique:users,email,' . $id,
            ], [
                'email.required' => 'Email wajib diisi'
            ]);
            $input = $request->only(['name', 'email', 'jenis_kelamin']);
        } else {
            $this->validate($request, [
                'password' => 'required|min:6|confirmed',
            ], [
                'password.required' => 'Password harus diisi',
                'password.confirmed' => 'Konfirmasi Password harus diisi',
                'password.min' => 'Password minimal 6 karakter',
            ]);
            $input['password'] = Hash::make($request->password);
        }
        try {

            $user = User::findOrFail($id);
            if ($id == Auth::user()->id) {
                $user->fill($input)->save();
                return redirect()->back()->with('message', 'Data berhasil diupdate');
            }
            Auth::logout();
            return redirect('/login');
        } catch (Exception $e) {
            Log::error("User save error " . $e->getMessage());
        }
    }
    public function kataSandi()
    {

        $user = User::findOrFail(Auth::user()->id);
        return view('account.password.password_new', compact('user'));
    }
}
