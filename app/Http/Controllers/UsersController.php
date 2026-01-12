<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon;
use Auth;
use Illuminate\Support\Facades\Hash;
use App\Favorite;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UsersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $users = User::all();
        return view('admin.users.index',compact('users'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.users.create');
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
            'name'=>'required',
            'username'=>'nullable|unique:users,username',
            'access'=>'required',
            'email'=>'required|unique:users',
            'password'=>'required|min:6|confirmed',
        ],[
            'name.required' => 'Nama wajib di isi',
            'email.required' => 'Email wajib di isi',
            'email.unique' => 'Email sudah ada, ganti dengan deskripsi lain',
            'password.required' => 'Password wajib di isi',
            'password.min' => 'Password minimal 6 karakter',
            'password.confirmed' => 'Konfirmasi Password tidak sama',
        ]);
        try {
            // ensure username exists (generate from name/email if not provided)
            $username = $request->username ?? null;
            if(!$username) {
                $base = Str::slug($request->name ?: explode('@', $request->email)[0], '-');
                $base = substr($base, 0, 50) ?: 'user';
                $candidate = $base;
                $i = 0;
                while(User::where('username', $candidate)->exists()){
                    $i++;
                    $candidate = $base . $i;
                }
                $username = $candidate;
            }

            // create user then ensure email_verified_at is set (force auto-verify)
            $user = User::create([
                'name'=> $request->name,
                'username'=> $username,
                'email'=> $request->email,
                'password'=> Hash::make($request->password),
                'access'=> $request->access,
            ]);

            // Force mark as verified to guarantee admin-created users are active
            $user->email_verified_at = Carbon\Carbon::now()->toDateTimeString();
            $user->save();
            return redirect()->route('admin.user.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("User save error ".$e->getMessage());
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
        $user = User::findOrFail($id);
        return view('admin.users.edit',compact('user'));
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
         $this->validate($request, [
            'email'=>'required|email|unique:users,email,'.$id,
        ]);
        if($request->password==""){
            $input = $request->only(['name','email','access']);
        }else{
            $this->validate($request, [
                'password'=>'required|min:6|confirmed',
                'email'=>'required|email|unique:users,email,'.$id,
            ]);
            $input = $request->only(['name','email', 'access', 'password']);
            $input['password'] = Hash::make($input['password']);
        }
        try {
            $user = User::findOrFail($id);
            $user->fill($input)->save();

            //jika akses user sebagai operator, redirect ke edit
            if(Auth::user()->access == 'operator') {
                return redirect()->route('admin.user.edit',Auth::user()->id)->with('message', 'Data berhasil diupdate');
            }
                        
            //jika akses user sebagai admin, redirect ke daftar user
            return redirect()->route('admin.user.index')->with('message', 'Data berhasil diupdate');
        } catch (Exception $e) {
            Log::error("User save error ".$e->getMessage());
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
        $user = User::findOrFail($id);
        $user->delete();
        return back()->with('message', 'Data berhasil dihapus');
    }
    public function status($id)
    {
        $user = User::findOrFail($id);
        $user->email_verified_at = $user->email_verified_at == NULL ? \Carbon\Carbon::now() : NULL;
        $user->save();
        return back();
    }

    

public function favorites()
{
    $favorites = Favorite::with('product')
        ->where('user_id', auth()->id())
        ->get();

    return view('account.favorites.favorites', compact('favorites'));
}


}
