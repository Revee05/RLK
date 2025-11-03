<?php


namespace App\Product;

use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class Uploads
{
    /**
     * Directory path for store uploaded images.
     *
     * @var string
     */
    private $uploadPath;

    /**
     * Instantiate a new controller instance.
     * @return void
     */
    public function __construct()
    {
        if (config('app.env') == 'prod'){
            $this->uploadPath = 'uploads/' . date('Y/m/');
        }else{
            $this->uploadPath = 'uploads/' . date('Y/m/');
            if (! File::isDirectory($this->uploadPath)) {
                File::makeDirectory($this->uploadPath, 0755, true);
            }
        }
    }

    /**
     * Save uploaded file to upload directory.
     *
     * @param  Illuminate\Http\UploadedFile  $file
     * @return string
     */
    public function handleUpload($file)
    {

        $image = Image::make($file);
        $quality = 80;

        $fileName = now()->timestamp . '_' . uniqid() . '.' . $file->getClientOriginalExtension();
        if (config('app.env') == 'prod'){
            //upload ke s3
            $image_stream = $image->stream($file->getClientOriginalExtension(), $quality);
            // Storage::put($this->uploadPath . $fileName, file_get_contents($file), [
            //     'ResponseContentType' => 'application/'.$file->getClientOriginalExtension(),
            // ]);
            Storage::put($this->uploadPath . $fileName, $image_stream, [
                'ResponseContentType' => 'application/'.$file->getClientOriginalExtension(),
            ]);
        }else{
            //upload ke local dir
            // $file->move($this->uploadPath, $fileName);
            $image->save($this->uploadPath . $fileName, $quality);
        }


        return 'uploads/' . date('Y/m/') . $fileName;
    }

    /**
     * Save uploaded file to upload directory.
     *
     * @param Illuminate\Http\UploadedFile $file
     * @param $extension
     * @return string
     */
    public function handleUploadProduct($file, $extension = '')
    {
        $img    = Image::make($file);

        $width  = $img->width();
        $height = $img->height();

        $dimension = 500;
        $quality = 80;

        $vertical   = (($width < $height) ? true : false);
        $horizontal = (($width > $height) ? true : false);
        $square     = (($width = $height) ? true : false);

        if ($vertical) {
            $img->resize(null, $dimension, function ($constraint) {
                $constraint->aspectRatio();
            });

        } else if ($horizontal) {
            $img->resize($dimension, null, function ($constraint) {
                $constraint->aspectRatio();
            });

        } else if ($square) {
            $img->resize($dimension, null, function ($constraint) {
                $constraint->aspectRatio();
            });

        }

        $image = $img->resizeCanvas($dimension, $dimension, 'center', false, '#ffffff');

        $ext = $extension != '' ? $extension : $file->getClientOriginalExtension();

        $fileName = now()->timestamp . '_' . uniqid() . '.' . $ext;
        if (config('app.env') == 'prod'){
            //upload ke s3
            $image_stream = $image->stream($ext, $quality);
            Storage::put($this->uploadPath . $fileName, $image_stream, [
                'ResponseContentType' => 'application/'.$ext,
            ]);
        }else{
            //upload ke local dir
            $image->save($this->uploadPath . $fileName, $quality);
        }


        return 'uploads/' . date('Y/m/') . $fileName;
    }
}
