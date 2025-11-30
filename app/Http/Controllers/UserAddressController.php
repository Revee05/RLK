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
        try {
            $validated = $request->validate([
                'name'          => 'required|string',
                'phone'         => 'required|string',
                'address'       => 'required|string',
                'province_id'   => 'required|integer',
                'city_id'       => 'required|integer',
                'district_id'   => 'required|integer',
                'label_address' => 'nullable|string',
            ]);

            $data = [
                'user_id'       => auth()->id(),
                'name'          => $validated['name'],
                'phone'         => $validated['phone'],
                'address'       => $validated['address'],
                'province_id'   => $validated['province_id'],
                'city_id'       => $validated['city_id'],
                'district_id'   => $validated['district_id'],
                'label_address' => $validated['label_address'] ?? null,
                'kodepos'       => null,
            ];

            $save = UserAddress::create($data);

            return response()->json([
                "status" => "success",
                "message" => "Alamat berhasil disimpan",
                "data" => $save
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'status' => 'error',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // fallback untuk semua error lain
            return response()->json([
                'status' => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }


    public function refreshList()
    {
        $addresses = UserAddress::with(['province','city','district'])->where('user_id', auth()->id())->get();

        $html = "";
        foreach ($addresses as $addr) {
            $html .= '
            <div class="address-card p-3 border rounded mb-2 pointer" data-id="'.$addr->id.'">
                <label class="d-flex justify-content-between w-100">
                    <div>
                        <div class="fw-bold">'.$addr->label_address.'</div>
                        <div class="small text-muted">
                            '.$addr->name.' • '.$addr->phone.' <br>
                            '.$addr->address.', 
                            '.($addr->district->name ?? '-').'<br>
                            '.($addr->city->name ?? '-').',
                            '.($addr->province->name ?? '-').'
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
