<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Sliders;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Facades\Image;

class SlidersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sliders = Sliders::all();
        return view('admin.master.sliders.index',compact('sliders'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.sliders.create');
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
            'name'=> 'required',
            'fotoslider'=> 'required',
        ]);
        try {
            
            try {
                if ($request->hasFile('fotoslider')) {
                    $dir = 'uploads/sliders/';
                    $extension = strtolower($request->file('fotoslider')->getClientOriginalExtension()); // get image extension
                    $fileName = uniqid() . '.' . $extension; // rename image
                    $request->file('fotoslider')->move($dir, $fileName);
                    $data['fotoslider'] =  $fileName;
                }
            } catch (Exception $e) {
                Log::error("Upload foto sliders".$e->getMessage());
            }
            Sliders::create([
                'name'=>$request->name,
                'slug'=> Str::slug($request->name, '-'),
                'image'=> $data['fotoslider']
            ]);
            return redirect()->route('master.sliders.index')->with('message', 'Data berhasil disimpan');
        
        } catch (Exception $e) {
            Log::error("Sliders save error ".$e->getMessage());
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
        try {
            
            $slider = Sliders::findOrFail($id);
            return view('admin.master.sliders.edit',compact('slider'));
            
        } catch (Exception $e) {
            
        }
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
            'name'=> 'required',
        ]);
        try {
            
            try {
                if ($request->hasFile('fotoslider')) {
                    $dir = 'uploads/sliders/';
                    $extension = strtolower($request->file('fotoslider')->getClientOriginalExtension()); // get image extension
                    $fileName = uniqid() . '.' . $extension; // rename image
                    $request->file('fotoslider')->move($dir, $fileName);
                    $data['fotoslider'] =  $fileName;
                }
            } catch (Exception $e) {
                Log::error("Upload foto sliders".$e->getMessage());
            }
            $slider = Sliders::findOrFail($id);
            $slider->update([
                'name'=>$request->name,
                'slug'=> Str::slug($request->name, '-'),
                'image'=> $data['fotoslider'] ?? $slider->image 
            ]);
            return redirect()->route('master.sliders.index')->with('message', 'Data berhasil disimpan');
        
        } catch (Exception $e) {
            Log::error("Sliders save error ".$e->getMessage());
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
}
