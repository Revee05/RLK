<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use App\Kategori;
use App\Karya;
use App\Products;
use App\ProductImage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use App\Product\Uploads as Upload;
use App\Kelengkapan;
use App\Bid;

class ProductsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Products::all();
        return view('admin.master.product.index',compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $kategoris = Kategori::pluck('name','id');
        $karyas = Karya::pluck('name','id');
        $kelengkapans = Kelengkapan::orderBy('id','asc')->get();
        return view('admin.master.product.create',compact('kategoris','karyas','kelengkapans'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // Tambah context global untuk setiap log pada request ini
        $logContext = [
            'request_id' => (string) Str::uuid(),
            'user_id'    => optional(Auth::user())->id,
            'ip'         => $request->ip(),
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
        ];
        if (app()->environment('local', 'testing')) {
            Log::info('=== DATA REQUEST ===', $logContext);
            Log::info(var_export($request->all(), true), $logContext);
            Log::info('=== END DATA REQUEST ===', $logContext);
        }
            
        $this->validate($request,[
            'title'=> 'required',
            'description'=> 'required',
            'price'=> 'required|numeric',
            'kategori_id'=> 'required|exists:kategori,id',
            'karya_id'=> 'required|exists:karya,id',
            'stock'=> 'required|numeric',
            'sku'=> 'required|unique:products',
            'asuransi'=> 'nullable',
            'long'=> 'required|numeric',
            'width'=> 'required|numeric',
            'height'=> 'required|numeric',
            'kondisi'=> 'required',
            'kelipatan'=> 'required|numeric',
            'end_date'=> 'nullable|date_format:Y-m-d H:i:s',
        ],[
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi produk harus diisi',
            'price.required' => 'Harga produk harus diisi',
            'kategori_id.required' => 'Kategori produk harus diisi',
            'karya_id.required' => 'Karya produk harus diisi',
            'stock.required' => 'Stok produk harus diisi',
            'sku.required' => 'SKU/Kode Unik produk harus diisi',
            'sku.unique' => 'SKU/Kode Unik produk sudah ada, ganti kode yang lain',
            'long.required' => 'Panjang produk harus diisi',
            'width.required' => 'Lebar produk harus diisi',
            'height.required' => 'Tinggi produk harus diisi',
            'kondisi.required' => 'Kondisi produk harus diisi',
            'kelipatan.required' => 'Kelipatan produk harus diisi',
            'end_date.required' => 'Tanggal berakhir harus diisi',
            'end_date.date_format' => 'Format tanggal harus Y-m-d H:i:s',
        ]);

        // save product
        try {
            
            $harga = str_replace(".","", $request->price);
            $kelipatan = str_replace(".","", $request->kelipatan);

            // Normalisasi end_date
            $endDate = $request->end_date;
            if ($endDate && strlen($endDate) === 16) { // format Y-m-d H:i
                $endDate .= ':00';
            }

            $product = Products::create([
                'user_id'=> Auth::user()->id,
                'kategori_id'=> $request->kategori_id,
                'karya_id'=> $request->karya_id,
                'title'=> $request->title,
                'slug'=> Str::slug($request->title, '-'),
                'description'=> $request->description,
                'price'=> $harga,
                'diskon'=> $request->diskon,
                'stock'=> $request->stock,
                'sku'=> $request->sku,
                'weight'=> $request->weight,
                'asuransi'=> $request->asuransi ? 1 : 0,
                'long'=> $request->long,
                'height'=> $request->height,
                'width'=> $request->width,
                'status'=> $request->status ?? '1',
                'kondisi'=> $request->kondisi,
                'kelipatan'=> $kelipatan,
                'end_date' => $endDate,
            ]);
            //save kelengkapan karya
            if(!empty($request->kelengkapan_id)){
                if ($kelengkapan_id = $request->input('kelengkapan_id', [])) {
                    $product->kelengkapans()->sync($kelengkapan_id);
                }            
            }
            //save images
            try {
                $image = [];
                $allImage = [
                    'img_utama' => $request->fotosatu, 
                    'img_depan' => $request->fotodua,
                    'img_samping' => $request->fototiga,
                    'img_atas' => $request->fotoempat,
                ];
                foreach ($allImage as $key => $val) {
                    if ($key) {
                        if (!empty($val)) {
                            $path = (new Upload)->handleUploadProduct($val);
                            $image[] = [
                                'products_id' => $product->id,
                                'name' => $key,
                                'path' => $path,
                            ];
                        }
                    }
                }
                ProductImage::insert($image);
                
            } catch (Exception $e) {
                $this->logException('Save images error', $e, ['phase' => 'store', 'product_id' => $product->id ?? null]);
            }
        } catch (Exception $e) {
            $this->logException('Save product error', $e, ['phase' => 'store']);
        }

        return redirect()->route('master.product.index');
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
        $product = Products::findOrFail($id);
        $kategoris = Kategori::pluck('name','id');
        $karyas = Karya::pluck('name','id');
        $kelengkapans = Kelengkapan::orderBy('id','asc')->get();
        return view('admin.master.product.edit',compact('product','kategoris','karyas','kelengkapans'));
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
        // Tambah context global untuk setiap log pada request ini
        $logContext = [
            'request_id' => (string) Str::uuid(),
            'user_id'    => optional(Auth::user())->id,
            'ip'         => $request->ip(),
            'method'     => $request->method(),
            'url'        => $request->fullUrl(),
        ];
        if (app()->environment('local', 'testing')) {
            Log::info('=== DATA REQUEST ===', $logContext);
            Log::info(var_export($request->all(), true), $logContext);
            Log::info('=== END DATA REQUEST ===', $logContext);
        }

        $this->validate($request,[
            'title'=> 'required',
            'description'=> 'required',
            'price'=> 'required|numeric',
            'kategori_id'=> 'required|exists:kategori,id',
            'karya_id'=> 'required|exists:karya,id',
            'stock'=> 'required|numeric',
            'sku'=> 'required',
            'asuransi'=> 'nullable',
            'long'=> 'required|numeric',
            'height'=> 'required|numeric',
            'width'=> 'required|numeric',
            'height'=> 'required|numeric',
            'kondisi'=> 'required',
            'kelipatan'=> 'required|numeric',
            'end_date'=> 'nullable|date_format:Y-m-d H:i:s',
        ],[
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi produk harus diisi',
            'price.required' => 'Harga produk harus diisi',
            'kategori_id.required' => 'Kategori produk harus diisi',
            'karya_id.required' => 'Karya produk harus diisi',
            'stock.required' => 'Stok produk harus diisi',
            'sku.required' => 'SKU/Kode Unik produk harus diisi',
            'long.required' => 'Panjang produk harus diisi',
            'width.required' => 'Lebar produk harus diisi',
            'height.required' => 'Tinggi produk harus diisi',
            'kondisi.required' => 'Kondisi produk harus diisi',
            'kelipatan.required' => 'Kelipatan produk harus diisi',
            'end_date.required' => 'Tanggal berakhir harus diisi',
            'end_date.date_format' => 'Format tanggal harus Y-m-d H:i:s',
        ]);

        // save product
        try {
            $product = Products::findOrFail($id);

            // Normalisasi end_date
            $endDate = $request->end_date;
            if ($endDate && strlen($endDate) === 16) { // format Y-m-d H:i
                $endDate .= ':00';
            }

            $product->update([
                'user_id'=> Auth::user()->id,
                'kategori_id'=> $request->kategori_id,
                'karya_id'=> $request->karya_id,
                'title'=> $request->title,
                'slug'=> Str::slug($request->title, '-'),
                'description'=> $request->description,
                'price'=> $request->price,
                'diskon'=> $request->diskon,
                'stock'=> $request->stock,
                'sku'=> $request->sku,
                'weight'=> $request->weight,
                'asuransi'=> $request->asuransi ? 1 : 0,
                'long'=> $request->long,
                'height'=> $request->height,
                'width'=> $request->width,
                'status'=> $request->status ?? '1',
                'kondisi'=> $request->kondisi,
                'kelipatan'=> $request->kelipatan,
                'end_date' => $endDate,
            ]);
            // if(!empty($request->kelengkapan_id)){
            //     //hapus dulu
            //     KelengkapanProduct::where('product_id',$product->id)->delete();

            //     foreach ($request->kelengkapan_id as $kelengkapans) {
            //         KelengkapanProduct::create([
            //             'kelengkapan_id'=> $kelengkapans,
            //             'product_id' => $product->id
            //         ]);
            //     }
            // }
            //save kelengkapan karya
            if(!empty($request->kelengkapan_id)){
                if ($kelengkapan_id = $request->input('kelengkapan_id', [])) {
                    $product->kelengkapans()->sync($kelengkapan_id);
                }            
            }
            // $product->kelengkapans()->sync($request->kelengkapan_id);
            //save images
            try {
                $allImage = [
                    'img_utama' => $request->fotosatu, 
                    'img_depan' => $request->fotodua,
                    'img_samping' => $request->fototiga,
                    'img_atas' => $request->fotoempat,
                ];
                foreach ($allImage as $key => $val) {
                    if ($key) {
                        if (!empty($val)) {
                            $path = (new Upload)->handleUploadProduct($val);
                            $image = [
                                'products_id' => $product->id,
                                'name' => $key,
                                'path' => $path,
                            ];
                            $check = ProductImage::where(['products_id' => $product->id, 'name' => $key])->first();
                            if ($check === null) {
                                ProductImage::create($image);
                            } else {
                                $check->update($image);
                            }
                        }
                    }
                }
                
            } catch (Exception $e) {
                $this->logException('Save images error', $e, ['phase' => 'update', 'product_id' => $id]);
            }
        } catch (Exception $e) {
            $this->logException('Save product error', $e, ['phase' => 'update', 'product_id' => $id]);
        }

        return redirect()->route('master.product.index');
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
             $product = Products::findOrFail($id);
             $product->delete();
             return back();
        } catch (Exception $e) {
            $this->logException('Destroy product error', $e, ['product_id' => $id]);
        }
    }
    public function status($id)
    {
        $product = Products::findOrFail($id);
        $product->status = $product->status == '0' ? '1' : '0';
        $product->save();
        return back();
    }
    public function resetBid($id)
    {
        try {
            $cekBid = Bid::where('product_id',$id)->first();
            if($cekBid) {
                Bid::where('product_id',$id)->delete();
                return back()->with('message', 'Data Bid berhasil direset');
            }
            return back()->with('message', 'Data Bid belum ada!');
        } catch (Exception $e) {
            $this->logException('Reset Bid error', $e, ['product_id' => $id]);
        }
    }

    /**
     * Utility: log exception lengkap dengan context dan stack trace
     */

    private function logException(string $message, Exception $e, array $context = []): void
    {
        if (app()->environment('local', 'testing')) {
            Log::error($message, array_merge($context, [
                'exception_class' => get_class($e),
                'exception_msg'   => $e->getMessage(),
                'file'            => $e->getFile(),
                'line'            => $e->getLine(),
                'trace'           => $e->getTraceAsString(),
            ]));
        } else {
            Log::error($message, array_merge($context, [
                'exception_class' => get_class($e),
                'exception_msg'   => $e->getMessage(),
            ]));
        }
    }

}