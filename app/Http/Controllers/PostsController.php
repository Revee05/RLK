<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Posts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class PostsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Posts::all();
        return view('admin.posts.index',compact('posts'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.posts.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->except);
        $request->validate([
            'title'=>'required',
            'body'=>'required',
            'status'=>'required',
        ],[
            'title.required' => 'Judul wajib di isi',
            'body.required' => 'Content wajib di isi',
            'status.required' => 'status wajib di isi',
        ]);
        try {
            Posts::create([
                'user_id'=> Auth::user()->id,
                'title'=> $request->title,
                'slug'=> Str::slug($request->title,'-'),
                'body'=> $request->body,
                'status'=> $request->status,
                'post_type'=> 'page',
            ]);
            return redirect()->route('admin.posts.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Post save error ".$e->getMessage());
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
        $post = Posts::findOrFail($id);
        return view('admin.posts.edit',compact('post'));
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
            'title'=>'required',
            'body'=>'required',
            'status'=>'required',
        ],[
            'title.required' => 'Judul wajib di isi',
            'body.required' => 'Content wajib di isi',
            'status.required' => 'status wajib di isi',
        ]);
        try {
            $post = Posts::findOrFail($id);
            $post->update([
                'user_id'=> Auth::user()->id,
                'title'=> $request->title,
                'slug'=> Str::slug($request->title,'-'),
                'body'=> $request->body,
                'status'=> $request->status,
                'post_type'=> 'page',
            ]);
            return redirect()->route('admin.posts.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Post save error ".$e->getMessage());
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
            
            $post = Posts::findOrFail($id);
            $post->delete();
            return back()->with('message','Artikel berhasil di hapus');
        
        } catch (Exception $e) {
            Log::error("Post delete error ".$e->getMessage());
        }
    }
}
