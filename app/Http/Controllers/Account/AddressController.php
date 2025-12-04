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
use Illuminate\Validation\ValidationException; // added

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
        // Ensure primary address (is_primary) appears first, then fallback to default ordering by id
        $userAddress = UserAddress::where('user_id', $user->id)
            ->orderByDesc('is_primary')
            ->orderBy('id')
            ->get();
        Log::info('AddressController@index response', ['user_address' => $userAddress]);
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
        try {
            // validation moved inside try so ValidationException dapat ditangani di bawah
            $this->validate($request, [
                'name' => 'required',
                'user_id' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'province_id' => 'required',
                'city_id' => 'required',
                'district_id' => 'required',
                // 'desa_id'=>'required',
                'label_address' => 'required',
                // 'kodepos'=>'',
            ], [
                'name.required' => 'Nama wajib di isi',
                'user_id.required' => 'wajib di isi',
                'phone.required' => 'Nomer hp wajib di isi',
                'address.required' => 'Alamat wajib di isi',
                'province_id.required' => 'Provinsi wajib di isi',
                'city_id.required' => 'Kabupaten wajib di isi',
                'district_id.required' => 'Kecamtan wajib di isi',
                // 'desa_id.required'=>'Desa wajib di isi',
                // 'kodepos'=>'Kodepost wajib di isi',
                'label_address' => 'Label alamat wajib di isi',
            ]);

            // If new address is marked primary, reset other addresses for this user
            if ($request->has('is_primary') && $request->boolean('is_primary')) {
                UserAddress::where('user_id', $request->user_id)->update(['is_primary' => false]);
            }
            $userAddress = UserAddress::create([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'label_address' => $request->label_address,
                'is_primary' => $request->boolean('is_primary'),
            ]);
            return redirect()->route('account.address.index')->with('success', 'Alamat berhasil ditambahkan!');
        } catch (ValidationException $e) {
            // handle validation errors and return with validation messages
            return redirect()->back()->withErrors($e->validator)->withInput();
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
        $user_address = UserAddress::with(['province', 'city', 'district'])->findOrFail($id);
        Log::info('AddressController@edit response', ['user_address' => $user_address]);
        // If request expects JSON (AJAX), return the address as JSON for modal population
        if (request()->ajax() || request()->wantsJson()) {
            return response()->json($user_address);
        }

        // $provinsis = Cache::remember('provinsis', 180, function () {
        //     return Provinsi::pluck('nama_provinsi', 'id');
        // });
        // $kabupatens = Cache::remember('kabupatens', 180, function () {
        //     return Kabupaten::all();
        // });
        // $kecamatans = Cache::remember('kecamatans', 180, function () {
        //     return  Kecamatan::all();
        // });

        // return view(
        //     'account.address.edit',
        //     compact('user_address', 'provinsis', 'kabupatens', 'kecamatans')
        // );
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
        try {
            $this->validate($request, [
                'name' => 'required',
                'user_id' => 'required',
                'phone' => 'required',
                'address' => 'required',
                'province_id' => 'required',
                'city_id' => 'required',
                'district_id' => 'required',
                // 'desa_id' => 'required',
                'label_address' => 'required',
                // 'kodepos' => '',
            ], [
                'name.required' => 'Nama wajib di isi',
                'user_id.required' => 'wajib di isi',
                'phone.required' => 'Nomer hp wajib di isi',
                'address.required' => 'Alamat wajib di isi',
                'province_id.required' => 'Provinsi wajib di isi',
                'city_id.required' => 'Kabupaten wajib di isi',
                'district_id.required' => 'Kecamtan wajib di isi',
                // 'desa_id.required' => 'Desa wajib di isi',
                // 'kodepos' => 'Kodepost wajib di isi',
                'label_address' => 'Label alamat wajib di isi',
            ]);

            // If updated address is marked primary, reset other addresses for this user
            if ($request->has('is_primary') && $request->boolean('is_primary')) {
                UserAddress::where('user_id', $request->user_id)->where('id', '<>', $id)->update(['is_primary' => false]);
            }

            $userAddress = UserAddress::findOrFail($id);
            $userAddress->update([
                'name' => $request->name,
                'user_id' => $request->user_id,
                'phone' => $request->phone,
                'address' => $request->address,
                'province_id' => $request->province_id,
                'city_id' => $request->city_id,
                'district_id' => $request->district_id,
                'label_address' => $request->label_address,
                'is_primary' => $request->boolean('is_primary'),
            ]);
            return redirect()->route('account.address.index')->with('success', 'Alamat berhasil diperbarui!');
        } catch (ValidationException $e) {
            return redirect()->back()->withErrors($e->validator)->withInput();
        } catch (Exception $e) {
            Log::error('AddressController@update failed: ' . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->withInput()->with('error', 'Gagal memperbarui alamat. Silakan coba lagi.');
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
            return back()->with('success', 'Alamat berhasil dihapus!');
        } catch (Exception $e) {
            Log::error('AddressController@destroy failed: ' . $e->getMessage(), ['exception' => $e]);
            return back()->with('error', 'Gagal menghapus alamat. Silakan coba lagi.');
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
