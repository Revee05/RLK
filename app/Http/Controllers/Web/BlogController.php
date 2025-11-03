<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Posts;
use Validator;
class BlogController extends Controller
{
    public function index()
    {
        $blogs = Posts::Blog()->where('status','PUBLISHED')->orderBy('id','desc')->paginate(10);
        return view('web.blogs',compact('blogs'));
    }
    public function detail($slug)
    {

        $validator = Validator::make(['slug'=>$slug], [
            'slug'=>['required','exists:posts,slug']
        ]);
        
        //jika tidak ada redirect ke halaman 404
        if ($validator->fails()) {
            abort('404');
        }

        try {
            
            $blog = Posts::Blog()->where('slug',$slug)->where('status','PUBLISHED')->first();
            
            if ($blog) {
                return view('web.blog-detail',compact('blog'));
            }
            
            abort(404);

        } catch (Exception $e) {
            Log::error('Page :'. $e->getMessage());
        }
    }
}
