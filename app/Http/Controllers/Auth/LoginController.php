<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Route;

// 1. TAMBAHKAN IMPORT MODEL SLIDER (SESUAI FILE BARU ANDA)
use App\Sliders; 

class LoginController extends Controller 
{
    use AuthenticatesUsers;

    protected $redirectTo = RouteServiceProvider::HOME;

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    // FUNGSI INI YANG DIMODIFIKASI
    public function showLoginForm()
    {
        // 2. AMBIL DATA SLIDER (SESUAI FILE BARU ANDA)
        $sliders = Sliders::all(); 

        // 3. KIRIM DATA KE VIEW
        return view('auth.login', ['sliders' => $sliders]);
    }

    protected function sendFailedLoginResponse(Request $request)
    {
        throw ValidationException::withMessages([
            'email' => 'opps ada yang salah, ulangi lagi',
        ]);
    }

    public function postLogin(Request $request)
    {   
        $input = $request->all();
    
        $this->validate($request, [
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        if(auth()->attempt(array('email' => $input['email'], 'password' => $input['password'])))
        {
            if (auth()->user()->access == 'admin') {
                return redirect()->route('admin.dashboard');
            }else{
                return redirect()->route('home');
            }
        }else{
            return redirect()->route('login')
                ->with('error','Login gagal. Periksa kembali email dan password Anda.');
        }
          
    }
}