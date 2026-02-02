<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use \Illuminate\Support\Facades\Schema;
use App\Setting;
use App\Kategori;
use App\Posts;
use Illuminate\Support\Collection;
use Illuminate\Pagination\Paginator;
use Illuminate\Pagination\LengthAwarePaginator;
class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // check table exists and share safe defaults to views
        if (Schema::hasTable('setting')) {
            try {
                $setting = Setting::first() ?: new Setting();
                $social = $setting->social ?? [];
                View::share(['setting' => $setting, 'social' => $social]);
            } catch (\Exception $e) {
                View::share(['setting' => new Setting(), 'social' => []]);
            }
        } else {
            // ensure keys exist to avoid undefined variable/offsets in Blade
            View::share(['setting' => new Setting(), 'social' => []]);
        }
        if (Schema::hasTable('kategori')) {
            $kategori = Kategori::all();
            View::share(['kategori' => $kategori]);
        }
        if (Schema::hasTable('posts')) {
            $about = Posts::where('slug','about-us')->first();
            $pages = Posts::where('post_type','page')->get();
            View::share(['about' => $about,'pages'=> $pages]);
        }

        //Add custom pagination
        if (!Collection::hasMacro('paginated')) {

            Collection::macro('paginated', function ($perPage = 15, $page = null, $options = []) {
                $page = $page ?: (Paginator::resolveCurrentPage() ?: 1);
                return (new LengthAwarePaginator($this->forPage($page, $perPage), $this->count(), $perPage, $page, $options))->withPath('');
            });
        }
        
    }
}
