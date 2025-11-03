<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Carbon;
use Auth; 
use Illuminate\Support\Facades\Hash;

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
            User::create([
                'name'=> $request->name,
                'email'=> $request->email,
                'email_verified_at'=> Carbon\Carbon::now()->toDateTimeString(),
                'password'=> Hash::make($request->password),
                'access'=> $request->access,
            ]);
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
}
