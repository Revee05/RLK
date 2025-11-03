<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Posts;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Kategori;
use App\Tags;

class BlogsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $blogs = Posts::Blog()->orderBy('id','desc')->get();
        return view('admin.blogs.index',compact('blogs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $cats = Kategori::Blog()->pluck('name','id');
        return view('admin.blogs.create',compact('cats'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // dd($request->all());
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
                try {
                    if ($request->hasFile('fotoblog')) {
                        $dir = 'uploads/blogs/';
                        $extension = strtolower($request->file('fotoblog')->getClientOriginalExtension()); // get image extension
                        $fileName = uniqid() . '.' . $extension; // rename image
                        $request->file('fotoblog')->move($dir, $fileName);
                        $save['fotoblog'] =  $fileName;
                    }
                } catch (Exception $e) {
                     Log::error("Upload foto blog error ".$e->getMessage());
                }
            $blog = Posts::create([
                'user_id'=> Auth::user()->id,
                'title'=> $request->title,
                'kategori_id'=> $request->kategori_id,
                'slug'=> Str::slug($request->title,'-'),
                'body'=> $request->body,
                'status'=> $request->status,
                'post_type'=> 'blog',
                'image'=> $save['fotoblog'] ?? NULL,
            ]);
            if(!empty($request->tagger)){
                $arrayImplode = implode(",",$request->tagger);
                $tags = explode(",",$arrayImplode);
                $tagsId = collect($tags)->map(function($tag) {
                    if(!empty($tag) || $tag != ""){
                        return Tags::firstOrCreate(['name' => trim($tag),'slug'=>Str::slug($tag,'-')])->id;
                    }
                });

                $blog->tags()->attach($tagsId);
            }
            return redirect()->route('admin.blogs.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Blog save error ".$e->getMessage());
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
        $blog = Posts::findOrFail($id);
        $cats = Kategori::Blog()->pluck('name','id');
        return view('admin.blogs.edit',compact('blog','cats'));
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
            try {
                if ($request->hasFile('fotoblog')) {
                    $dir = 'uploads/blogs/';
                    $extension = strtolower($request->file('fotoblog')->getClientOriginalExtension()); // get image extension
                    $fileName = uniqid() . '.' . $extension; // rename image
                    $request->file('fotoblog')->move($dir, $fileName);
                    $save['fotoblog'] =  $fileName;
                }
            } catch (Exception $e) {
                 Log::error("Upload foto blog error ".$e->getMessage());
            }
            $blog = Posts::findOrFail($id);
            $blog->update([
                'user_id'=> Auth::user()->id,
                'title'=> $request->title,
                'slug'=> Str::slug($request->title,'-'),
                'body'=> $request->body,
                'status'=> $request->status,
                'post_type'=> 'blog',
                'image'=> $save['fotoblog'] ?? $blog->image,
            ]);

            if(!empty($request->tagger)){
                $arrayImplode = implode(",",$request->tagger);
                $tags = explode(",",$arrayImplode);
                $tagsId = collect($tags)->map(function($tag) {
                    if(!empty($tag) || $tag != ""){
                        return Tags::firstOrCreate(['name' => trim($tag),'slug'=>Str::slug($tag,'-')])->id;
                    }
                });
                 $blog->tags()->sync($tagsId);
            }
            return redirect()->route('admin.blogs.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Blog save error ".$e->getMessage());
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
        //
    }
    public function getTag(Request $request){

      $search = $request->search;

      if($search == ''){
         $tag = Tags::orderby('name','asc')->select('id','name')->get();
      }else{
         $tag = Tags::orderby('name','asc')->select('id','name')->where('name', 'like', '%' .$search . '%')->get();
      }

      $response = array();
      foreach($tag as $tags){
         $response[] = array(
              "id"=>$tags->name,
              "text"=>$tags->name
         );
      }

      echo json_encode($response);
      exit;
   }
   public function status($id)
    {
        $blog = Posts::findOrFail($id);
        $blog->status = $blog->status == 'DRAFT' ? 'PUBLISHED' : 'DRAFT';
        $blog->save();
        return back();
    }
}
