<?php

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run()
    {
        $csvPath = database_path('seeds/data/blogs.csv');
        if (!file_exists($csvPath)) {
            $this->command->error("CSV file not found: {$csvPath}");
            return;
        }

        $imgSourceDir = database_path('seeds/seed_images');
        $imgDestDir   = public_path('uploads/blogs');
        if (!file_exists($imgDestDir)) mkdir($imgDestDir, 0777, true);

        $handle = fopen($csvPath, 'r');
        if ($handle === false) {
            $this->command->error("Unable to open CSV: {$csvPath}");
            return;
        }

        // Read header
        $header = fgetcsv($handle);
        if ($header === false) {
            fclose($handle);
            $this->command->error("CSV is empty");
            return;
        }

        while (($row = fgetcsv($handle)) !== false) {
            $data = array_combine($header, $row);

            DB::beginTransaction();
            try {
                $title = trim($data['title'] ?? 'Untitled');
                // slug otomatis jika kosong
                $slugRaw = trim($data['slug'] ?? '');
                $slug = $slugRaw !== '' ? $slugRaw : Str::slug($title) . '-' . uniqid();
                $body  = $data['body'] ?? '';
                $status = strtoupper(trim($data['status'] ?? 'PUBLISHED'));
                $userId = $data['user_id'] ? (int)$data['user_id'] : DB::table('users')->value('id') ?? 1;
                $kategoriId = $data['kategori_id'] ? (int)$data['kategori_id'] : (DB::table('kategori')->value('id') ?? null);
                // updated_at samakan dengan created_at (jika created_at tidak diberikan, gunakan now())
                $createdAt = !empty($data['created_at']) ? $data['created_at'] : now();
                $updatedAt = $createdAt;

                $postId = DB::table('posts')->insertGetId([
                    'user_id'     => $userId,
                    'title'       => $title,
                    'kategori_id' => $kategoriId,
                    'slug'        => $slug,
                    'body'        => $body,
                    'status'      => $status,
                    'post_type'   => 'blog',
                    'created_at'  => $createdAt,
                    'updated_at'  => $updatedAt,
                ]);

                // Images: expect filenames separated by ';'
                $firstImage = null;
                if (!empty($data['images'])) {
                    $images = array_filter(array_map('trim', explode(';', $data['images'])));
                    foreach ($images as $imgName) {
                        $src = $imgSourceDir . DIRECTORY_SEPARATOR . $imgName;
                        $dst = $imgDestDir . DIRECTORY_SEPARATOR . $imgName;
                        if (file_exists($src)) {
                            copy($src, $dst);
                        } else {
                            // create empty placeholder to avoid missing-file error (optional)
                            if (!file_exists($dst)) file_put_contents($dst, '');
                        }
                        DB::table('blog_images')->insert([
                            'post_id'    => $postId,
                            'filename'   => $imgName,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]);
                        if ($firstImage === null) $firstImage = $imgName;
                    }
                    if ($firstImage) {
                        DB::table('posts')->where('id', $postId)->update(['image' => $firstImage]);
                    }
                }

                // Tags: expect tag names separated by ';'
                if (!empty($data['tags']) && Schema::hasTable('tags')) {
                    $tagNames = array_filter(array_map('trim', explode(';', $data['tags'])));
                    foreach ($tagNames as $tname) {
                        if (empty($tname)) continue;
                        $tagId = DB::table('tags')->where('name', $tname)->value('id');
                        if (!$tagId) {
                            $tagId = DB::table('tags')->insertGetId([
                                'name'       => $tname,
                                'slug'       => Str::slug($tname),
                                'created_at' => now(),
                                'updated_at' => now(),
                            ]);
                        }
                        if (Schema::hasTable('post_tag')) {
                            DB::table('post_tag')->insert([
                                'post_id' => $postId,
                                'tag_id'  => $tagId,
                            ]);
                        }
                    }
                }

                DB::commit();
                $this->command->info("Seeded post: {$postId} - {$title}");
            } catch (\Exception $e) {
                DB::rollBack();
                $this->command->error("Failed seeding row (title: {$title}): " . $e->getMessage());
            }
        }

        fclose($handle);
    }
}