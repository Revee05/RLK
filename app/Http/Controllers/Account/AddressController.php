<?php

namespace App\Http\Controllers\Account;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Provinsi;
use App\Kabupaten;
use App\Kecamatan;
use App\Desa;
use App\UserAddress;
use Auth;
use DB;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class AddressController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $user = Auth::user();
        $userAddress = UserAddress::where('user_id', $user->id)->get();
        return view('account.address.address', compact('userAddress'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $user = Auth::user();
        $provinsis = Cache::remember('provinsis', 180, function () {
            return Provinsi::pluck('nama_provinsi', 'id');
        });
        $kabupatens = Cache::remember('kabupatens', 180, function () {
            return Kabupaten::all();
        });
        $kecamatans = Cache::remember('kecamatans', 180, function () {
            return  Kecamatan::all();
        });

        return view(
            'account.address.create',
            compact('user', 'provinsis', 'kabupatens', 'kecamatans')
        );
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
        $this->validate($request, [
            'name' => 'required',
            'user_id' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'provinsi_id' => 'required',
            'kabupaten_id' => 'required',
            'kecamatan_id' => 'required',
            // 'desa_id'=>'required',
            'label_address' => 'required',
            // 'kodepos'=>'',
        ], [
            'name.required' => 'Nama wajib di isi',
            'user_id.required' => 'wajib di isi',
            'phone.required' => 'Nomer hp wajib di isi',
            'address.required' => 'Alamat wajib di isi',
            'provinsi_id.required' => 'Provinsi wajib di isi',
            'kabupaten_id.required' => 'Kabupaten wajib di isi',
            'kecamatan_id.required' => 'Kecamtan wajib di isi',
            // 'desa_id.required'=>'Desa wajib di isi',
            // 'kodepos'=>'Kodepost wajib di isi',
            'label_address' => 'Label alamat wajib di isi',
        ]);
        try {
            $userAddress = UserAddress::create([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kecamatan_id' => $request->kecamatan_id,
                // 'desa_id'=>$request->desa_id,
                // 'kodepos'=>$request->kodepos,
                'label_address' => $request->label_address,
            ]);
            return redirect()->route('account.address.index')->with('success', 'Alamat berhasil ditambahkan!');
        } catch (Exception $e) {
            Log::error('AddressController@store failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Gagal menyimpan alamat. Silakan coba lagi.');
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
        $user_address = UserAddress::findOrFail($id);
        $provinsis = Cache::remember('provinsis', 180, function () {
            return Provinsi::pluck('nama_provinsi', 'id');
        });
        $kabupatens = Cache::remember('kabupatens', 180, function () {
            return Kabupaten::all();
        });
        $kecamatans = Cache::remember('kecamatans', 180, function () {
            return  Kecamatan::all();
        });

        return view(
            'account.address.edit',
            compact('user_address', 'provinsis', 'kabupatens', 'kecamatans')
        );
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
        // dd($request->all());
        $this->validate($request, [
            'name' => 'required',
            'user_id' => 'required',
            'phone' => 'required',
            'address' => 'required',
            'provinsi_id' => 'required',
            'kabupaten_id' => 'required',
            'kecamatan_id' => 'required',
            'desa_id' => 'required',
            'label_address' => 'required',
            'kodepos' => '',
        ], [
            'name.required' => 'Nama wajib di isi',
            'user_id.required' => 'wajib di isi',
            'phone.required' => 'Nomer hp wajib di isi',
            'address.required' => 'Alamat wajib di isi',
            'provinsi_id.required' => 'Provinsi wajib di isi',
            'kabupaten_id.required' => 'Kabupaten wajib di isi',
            'kecamatan_id.required' => 'Kecamtan wajib di isi',
            'desa_id.required' => 'Desa wajib di isi',
            'kodepos' => 'Kodepost wajib di isi',
            'label_address' => 'Label alamat wajib di isi',
        ]);
        try {
            $userAddress = UserAddress::findOrFail($id);
            $userAddress->update([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'provinsi_id' => $request->provinsi_id,
                'kabupaten_id' => $request->kabupaten_id,
                'kecamatan_id' => $request->kecamatan_id,
                'desa_id' => $request->desa_id,
                'kodepos' => $request->kodepos,
                'label_address' => $request->label_address,
            ]);
            return redirect()->route('account.address.index');
        } catch (Exception $e) {
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
            $userAddress = UserAddress::findOrFail($id);
            $userAddress->delete();
            return back();
        } catch (Exception $e) {
        }
    }
    public function getDesa(Request $request, $id)
    {
        if ($request->ajax()) {

            $term = trim($request->term);
            $posts = DB::table('desa')->select('id', 'nama_desa as text')
                ->where('nama_desa', 'LIKE',  '%' . $term . '%')
                ->where('kecamatan_id', $id)
                ->orderBy('nama_desa', 'asc')->simplePaginate(10);

            $morePages = true;
            $pagination_obj = json_encode($posts);
            if (empty($posts->nextPageUrl())) {
                $morePages = false;
            }
            $results = array(
                "results" => $posts->items(),
                "pagination" => array(
                    "more" => $morePages
                )
            );

            return \Response::json($results);
        }
    }
}
