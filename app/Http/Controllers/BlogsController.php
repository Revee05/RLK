<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class BlogsController extends Controller
{
    public function index()
    {
        Log::info('[BLOG] INDEX accessed', ['user_id' => Auth::id()]);

        $blogs = DB::table('posts as p')
            ->leftJoin('kategori as k', 'k.id', '=', 'p.kategori_id')
            ->where('p.post_type', 'blog')
            ->select('p.*', 'k.name as kategori_name')
            ->orderByDesc('p.id')
            ->get();

        return view('admin.blogs.index', compact('blogs'));
    }

    public function create()
    {
        Log::info('[BLOG] CREATE page opened', ['user_id' => Auth::id()]);

        $cats = DB::table('kategori')->where('cat_type', 'blog')->pluck('name', 'id');
        $tags = DB::table('tags')->pluck('name', 'id');

        return view('admin.blogs.create', compact('cats', 'tags'));
    }

    public function store(Request $request)
    {
        Log::info('[BLOG] STORE start', ['user_id' => Auth::id()]);

        $request->validate([
            'title'  => 'required',
            'body'   => 'required',
            'status' => 'required|in:DRAFT,PUBLISHED',
        ]);

        DB::beginTransaction();
        try {

            /* === COVER === */
            $coverName = null;
            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = uniqid() . '.' . $cover->extension();
                $cover->move(public_path('uploads/blogs'), $coverName);

                Log::info('[BLOG] COVER uploaded', [
                    'filename' => $coverName,
                    'user_id'  => Auth::id()
                ]);
            }

            /* === POST === */
            $postId = DB::table('posts')->insertGetId([
                'user_id'     => Auth::id(),
                'title'       => $request->title,
                'kategori_id' => $request->kategori_id,
                'slug'        => Str::slug($request->title, '-'),
                'body'        => $request->body,
                'status'      => $request->status,
                'image'       => $coverName,
                'post_type'   => 'blog',
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);

            Log::info('[BLOG] POST created', [
                'post_id' => $postId,
                'status'  => $request->status
            ]);

            /* === IMAGE ARTIKEL === */
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $file) {
                    $name = uniqid() . '.' . $file->extension();
                    $file->move(public_path('uploads/blogs'), $name);

                    DB::table('blog_images')->insert([
                        'post_id'    => $postId,
                        'filename'   => $name,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('[BLOG] IMAGE added', [
                        'post_id'  => $postId,
                        'filename' => $name
                    ]);
                }
            }

            /* === TAGS === */
            if (!empty($request->tagger)) {
                foreach ($request->tagger as $tagName) {

                    DB::table('tags')->updateOrInsert(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName, 'slug' => Str::slug($tagName)]
                    );

                    $tag = DB::table('tags')
                        ->where('slug', Str::slug($tagName))
                        ->first();

                    DB::table('posts_tags')->insert([
                        'posts_id'   => $postId,
                        'tags_id'    => $tag->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    Log::info('[BLOG] TAG saved', ['tag' => $tag]);
                }
            }

            /* ===== SYNC IMAGE KE POST ===== */
            $this->attachImagesToPost($postId, $request->body);
            $this->syncUnusedImages($postId, $request->body);

            DB::commit();
            Log::info('[BLOG] STORE success', ['post_id' => $postId]);

            return redirect()->route('admin.blogs.index')->with('message', 'Blog berhasil disimpan!');
        
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Blog Store Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menyimpan blog.');
        }
    }

    public function edit($id)
    {
        Log::info('[BLOG] EDIT opened', ['post_id' => $id]);

        $blog   = DB::table('posts')->find($id);
        $cats   = DB::table('kategori')->where('cat_type', 'blog')->pluck('name', 'id');
        
        $selectedTags = DB::table('posts_tags as pt')
            ->join('tags as t', 't.id', '=', 'pt.tags_id')
            ->where('pt.posts_id', $id)
            ->pluck('t.name');
            
        $images = DB::table('blog_images')->where('post_id', $id)->get();

        return view('admin.blogs.edit', compact('blog', 'cats', 'selectedTags', 'images'));
    }

    public function update(Request $request, $id)
    {
        Log::info('[BLOG] UPDATE start', ['post_id' => $id]);

        $request->validate([
            'title'  => 'required',
            'body'   => 'required',
            'status' => 'required|in:DRAFT,PUBLISHED',
        ]);

        DB::beginTransaction();
        try {

            $data = [
                'user_id'     => Auth::id(),
                'title'       => $request->title,
                'kategori_id' => $request->kategori_id,
                'slug'        => Str::slug($request->title, '-'),
                'body'        => $request->body,
                'status'      => $request->status,
                'updated_at'  => now(),
            ];

            /* === COVER === */
            if ($request->hasFile('cover')) {
                $cover = $request->file('cover');
                $coverName = uniqid() . '.' . $cover->extension();
                $cover->move(public_path('uploads/blogs'), $coverName);
                $data['image'] = $coverName;

                Log::info('[BLOG] COVER updated', [
                    'post_id'  => $id,
                    'filename' => $coverName
                ]);
            }

            DB::table('posts')->where('id', $id)->update($data);

            /* ===== UPDATE TAGS ===== */
            DB::table('posts_tags')->where('posts_id', $id)->delete();

            if (!empty($request->tagger)) {
                foreach ($request->tagger as $tagName) {

                    DB::table('tags')->updateOrInsert(
                        ['slug' => Str::slug($tagName)],
                        ['name' => $tagName, 'slug' => Str::slug($tagName)]
                    );

                    $tag = DB::table('tags')
                        ->where('slug', Str::slug($tagName))
                        ->first();

                    DB::table('posts_tags')->insert([
                        'posts_id'   => $id,
                        'tags_id'    => $tag->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
            
            $this->attachImagesToPost($id, $request->body);
            $this->syncUnusedImages($id, $request->body);

            DB::commit();
            Log::info('[BLOG] UPDATE success', ['post_id' => $id]);

            return redirect()->route('admin.blogs.index')->with('message', 'Blog berhasil diperbarui!');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('[BLOG] UPDATE failed', [
                'post_id' => $id,
                'error'   => $e->getMessage()
            ]);
            return back()->with('error', 'Terjadi kesalahan saat memperbarui blog.');
        }
    }

    public function destroy($id)
    {
        Log::warning('[BLOG] DELETE start', ['post_id' => $id]);

        DB::beginTransaction();
        try {
            $images = DB::table('blog_images')->where('post_id', $id)->get();
            foreach ($images as $img) {
                $path = public_path('uploads/blogs/' . $img->filename);
                if (file_exists($path)) unlink($path);
            }

            DB::table('blog_images')->where('post_id', $id)->delete();
            DB::table('posts')->where('id', $id)->delete();

            DB::commit();
            Log::warning('[BLOG] DELETE success', ['post_id' => $id]);

            return back()->with('message', 'Blog berhasil dihapus!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Blog Delete Error: ' . $e->getMessage());
            return back()->with('error', 'Terjadi kesalahan saat menghapus blog.');
        }
    }

    public function getTag(Request $request)
    {
        Log::info('[BLOG] TAG search start', [
            'keyword' => $request->search,
            'user_id' => Auth::id()
        ]);
        
        $search = $request->search;
        $tags = DB::table('tags')
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%"))
            ->select('name as id', 'name as text')
            ->orderBy('name')
            ->get();

        Log::info('[BLOG] TAG search result', [
            'count' => $tags->count()
        ]);
        return response()->json($tags);
    }

    /* ================= IMAGE UPLOAD ================= */
    public function uploadContentImage(Request $request)
    {
        $request->validate([
            'image'   => 'required|image|max:3072',
        ]);

        $file = $request->file('image');
        $name = uniqid() . '.' . $file->getClientOriginalExtension();
        $file->move(public_path('uploads/blogs'), $name);

        $id = DB::table('blog_images')->insertGetId([
            'post_id'    => $request->post_id ?? null,
            'filename'   => $name,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json([
            'success'  => true,
            'id'       => $id,
            'filename' => $name,
            'url'      => asset('uploads/blogs/'.$name)
        ]);
    }

    /* ================= ATTACH IMAGE TO POST ================= */
    private function attachImagesToPost($postId, $body)
    {
        $blocks = json_decode($body, true);
        if (!is_array($blocks)) return;

        foreach ($blocks as $block) {
            if ($block['type'] === 'image' && !empty($block['image_id'])) {
                DB::table('blog_images')
                    ->where('id', $block['image_id'])
                    ->update(['post_id' => $postId]);
            }
        }
    }

    private function syncUnusedImages($postId, $body)
    {
        $blocks = json_decode($body, true);
        if (!is_array($blocks)) return;

        $usedIds = collect($blocks)
            ->where('type', 'image')
            ->pluck('image_id')
            ->filter()
            ->map(fn ($v) => (int) $v)
            ->toArray();

        $images = DB::table('blog_images')->where('post_id', $postId)->get();

        foreach ($images as $img) {
            if (!in_array($img->id, $usedIds)) {
                $path = public_path('uploads/blogs/' . $img->filename);
                if (file_exists($path)) unlink($path);
                DB::table('blog_images')->where('id', $img->id)->delete();
            }
        }
    }

    public function status($id)
    {
        $post = DB::table('posts')->find($id);
        if (!$post) {
            Log::warning('[BLOG] STATUS failed - not found', ['post_id' => $id]);
            return back()->with('error', 'Blog tidak ditemukan.');
        }

        $newStatus = ($post->status === 'PUBLISHED') ? 'DRAFT' : 'PUBLISHED';

        DB::table('posts')->where('id', $id)->update([
            'status'     => $newStatus,
            'updated_at' => now(),
        ]);

        Log::info('[BLOG] STATUS changed', [
            'post_id' => $id,
            'from'    => $post->status,
            'to'      => $newStatus
        ]);

        return back()->with('message', 'Status blog diperbarui!');
    }    
}