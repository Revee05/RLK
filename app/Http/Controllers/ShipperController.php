<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Shipper;
class ShipperController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $shippers = Shipper::all();
        return view('admin.master.shipper.index',compact('shippers'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.master.shipper.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'=>'required',
        ],[
            'name.required' => 'Nama shipper wajib di isi',
        ]);
        try {
            Shipper::create([
                'name'=> $request->name,
            ]);
            return redirect()->route('master.shipper.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Shipper save error ".$e->getMessage());
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
        $shipper = Shipper::findOrFail($id);
        return view('admin.master.shipper.edit',compact('shipper'));
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
            'name'=>'required',
        ],[
            'name.required' => 'Nama shipper wajib di isi',
        ]);
        try {
            $shipper = Shipper::findOrFail($id);
            $shipper->update([
                'name'=> $request->name,
            ]);
            return redirect()->route('master.shipper.index')->with('message', 'Data berhasil disimpan');
        } catch (Exception $e) {
            Log::error("Shipper save error ".$e->getMessage());
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
        try {
            $shipper = Shipper::findOrFail($id);
            $shipper->delete();
            return redirect()->route('master.shipper.index')->with('message', 'Data berhasil dihapus');   
        } catch (Exception $e) {
            Log::error("Shipper delete error ".$e->getMessage());
        }
    }
}
