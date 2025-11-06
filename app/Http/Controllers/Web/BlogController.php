<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Posts;
use Validator;
class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Posts::Blog()
            ->where('status', 'PUBLISHED');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                ->orWhere('body', 'like', "%{$search}%");
            });
        }

        if ($request->filled('filter')) {
            $filter = strtolower($request->filter);
            $query->where(function ($q) use ($filter) {
                $q->where('title', 'like', "%{$filter}%")
                ->orWhere('body', 'like', "%{$filter}%");
            });
        }

        switch ($request->sort) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;

            case 'title_asc':
                $query->orderBy('title', 'asc');
                break;

            case 'title_desc':
                $query->orderBy('title', 'desc');
                break;

            case 'author_asc':
                $query->join('users', 'posts.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'asc')
                    ->select('posts.*');
                break;

            case 'author_desc':
                $query->join('users', 'posts.user_id', '=', 'users.id')
                    ->orderBy('users.name', 'desc')
                    ->select('posts.*');
                break;

            default:
                $query->orderBy('created_at', 'desc'); // default = Terbaru
                break;
        }

        $blogs = $query->paginate(10);

        return view('web.blogs', compact('blogs', 'request'));
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
