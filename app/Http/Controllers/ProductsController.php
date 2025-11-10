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
        // dd($request->all());
        $this->validate($request,[
            'title'=> 'required',
            'description'=> 'required',
            'price'=> 'required|numeric',
            'kategori_id'=> 'required|exists:kategori,id',
            'karya_id'=> 'required|exists:karya,id',
            'stock'=> 'required|numeric',
            'sku'=> 'required|unique:products',
            'asuransi'=> 'required',
            'long'=> 'required|numeric',
            'width'=> 'required|numeric',
            'height'=> 'required|numeric',
            'kondisi'=> 'required',
            'kelipatan'=> 'required|numeric',
            'end_date'=> '',
        ],[
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi produk harus diisi',
            'price.required' => 'Harga produk harus diisi',
            'kategori_id.required' => 'Kategori produk harus diisi',
            'karya_id.required' => 'Karya produk harus diisi',
            'stock.required' => 'Stok produk harus diisi',
            'sku.required' => 'SKU/Kode Unik produk harus diisi',
            'sku.unique' => 'SKU/Kode Unik produk sudah ada, ganti kode yang lain',
            'asuransi.required' => 'Asuransi produk harus diisi',
            'long.required' => 'Panjang produk harus diisi',
            'width.required' => 'Lebar produk harus diisi',
            'height.required' => 'Tinggi produk harus diisi',
            'kondisi.required' => 'Kondisi produk harus diisi',
            'kelipatan.required' => 'Kelipatan produk harus diisi',
            'end_date.required' => 'Tanggal berakhir harus diisi',
        ]);

        // save product
        try {
            
            $harga = str_replace(".","", $request->price);
            $kelipatan = str_replace(".","", $request->kelipatan);

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
                'width'=> $request->width,
                'status'=> $request->status,
                'kondisi'=> $request->kondisi,
                'kelipatan'=> $kelipatan,
                'end_date'=> $request->end_date,
                'status'=> $request->status ?? '1',
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
                 Log::error("Save images error ".$e->getMessage());   
            }
        } catch (Exception $e) {
            Log::error("Save product error ".$e->getMessage());   
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
        $this->validate($request,[
            'title'=> 'required',
            'description'=> 'required',
            'price'=> 'required|numeric',
            'kategori_id'=> 'required|exists:kategori,id',
            'karya_id'=> 'required|exists:karya,id',
            'stock'=> 'required|numeric',
            'sku'=> 'required',
            'asuransi'=> 'required',
            'long'=> 'required|numeric',
            'width'=> 'required|numeric',
            'height'=> 'required|numeric',
            'kondisi'=> 'required',
            'kelipatan'=> 'required|numeric',
            'end_date'=> '',
        ],[
            'title.required' => 'Judul harus diisi',
            'description.required' => 'Deskripsi produk harus diisi',
            'price.required' => 'Harga produk harus diisi',
            'kategori_id.required' => 'Kategori produk harus diisi',
            'karya_id.required' => 'Karya produk harus diisi',
            'stock.required' => 'Stok produk harus diisi',
            'sku.required' => 'SKU/Kode Unik produk harus diisi',
            'asuransi.required' => 'Asuransi produk harus diisi',
            'long.required' => 'Panjang produk harus diisi',
            'width.required' => 'Lebar produk harus diisi',
            'height.required' => 'Tinggi produk harus diisi',
            'kondisi.required' => 'Kondisi produk harus diisi',
            'kelipatan.required' => 'Kelipatan produk harus diisi',
            'end_date.required' => 'Tanggal berakhir harus diisi',
        ]);

        // save product
        try {
            $product = Products::findOrFail($id);
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
                'width'=> $request->width,
                'status'=> $request->status,
                'kondisi'=> $request->kondisi,
                'kelipatan'=> $request->kelipatan,
                'end_date'=> $request->end_date,
                'status'=> $request->status ?? '1',
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
                 Log::error("Save images error ".$e->getMessage());   
            }
        } catch (Exception $e) {
            Log::error("Save product error ".$e->getMessage());   
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
            Log::error("destroy ".$e->getMessage());  
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
            Log::error("Reset Bid ".$e->getMessage());
        }
       
    }

}
