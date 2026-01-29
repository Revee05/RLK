<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

// 1. TAMBAHKAN IMPORT MODEL SLIDER (SESUAI FILE BARU ANDA)
use App\Sliders;

class RegisterController extends Controller
{
    use RegistersUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest');
    }

    protected function validator(array $data)
    {
        return Validator::make($data, [
            'username' => ['required', 'string', 'max:255', 'unique:users'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'number' => ['required', 'digits_between:1,15'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ], [
            'username.required' => 'Username wajib di isi',
            'username.unique' => 'Username sudah ada, ganti dengan deskripsi lain',
            'username.max' => 'Username maksimal 255 karakter',
            'name.required' => 'Nama wajib di isi',
            'name.max' => 'Nama maksimal 255 karakter',
            'email.required' => 'Email wajib di isi',
            'email.unique' => 'Email sudah ada, ganti dengan deskripsi lain',
            'email.email' => 'Format email tidak valid',
            'email.max' => 'Email maksimal 255 karakter',
            'number.required' => 'Nomor Telepon wajib di isi',
            'number.digits_between' => 'Nomor Telepon harus berupa angka dan maksimal 15 digit',
            'password.required' => 'Password wajib di isi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi Password tidak sama',
        ]);
    }

    protected function create(array $data)
    {
        return User::create([
            'username' => $data['username'],
            'name' => $data['name'],
            'email' => $data['email'],
            'hp' => $data['number'],
            'password' => Hash::make($data['password']),
        ]);
    }

    // FUNGSI INI YANG DIMODIFIKASI
    public function showRegistrationForm()
    {
        // 2. AMBIL DATA SLIDER (SESUAI FILE BARU ANDA)
        $sliders = Sliders::all();

        // 3. KIRIM DATA KE VIEW
        return view('auth.register', ['sliders' => $sliders]);
    }
}
