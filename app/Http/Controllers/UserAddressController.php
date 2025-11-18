<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserAddress;

class UserAddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'          => 'required|string',
            'phone'         => 'required|string',
            'address'       => 'required|string',
            'provinsi_id'   => 'required|integer',
            'kabupaten_id'  => 'required|integer',
            'kecamatan_id'  => 'required|integer',
            'label_address' => 'nullable|string',
            'note'          => 'nullable|string',
            'pinpoint'      => 'nullable|string',
        ]);

        $data = [
            'user_id'       => auth()->id(),
            'name'          => $validated['name'],
            'phone'         => $validated['phone'],
            'address'       => $validated['address'],
            'provinsi_id'   => $validated['provinsi_id'],
            'kabupaten_id'  => $validated['kabupaten_id'],
            'kecamatan_id'  => $validated['kecamatan_id'],
            'label_address' => $validated['label_address'] ?? null,
            'desa_id'       => null,
            'kodepos'       => null,
        ];

        $save = UserAddress::create($data);

        return response()->json([
            "status" => "success",
            "message" => "Alamat berhasil disimpan",
            "data" => $save
        ]);
    }

    public function refreshList()
    {
        $addresses = UserAddress::where('user_id', auth()->id())->get();

        $html = "";
        foreach ($addresses as $addr) {
            $html .= '
            <div class="address-card p-3 border rounded mb-2 pointer" data-id="'.$addr->id.'">
                <label class="d-flex justify-content-between w-100">
                    <div>
                        <div class="fw-bold">'.$addr->label_address.'</div>
                        <div class="small text-muted">
                            '.$addr->name.' • '.$addr->phone.' <br>
                            '.$addr->address.' <br>
                            '.($addr->kabupaten->nama_kabupaten ?? '-').',
                            '.($addr->provinsi->nama_provinsi ?? '-').'
                        </div>
                    </div>
                    <input type="radio" name="address_id">
                </label>
            </div>';
        }

        return response()->json([
            'html' => $html
        ]);
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
