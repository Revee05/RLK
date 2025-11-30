<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Province;
use App\City;
use App\District;

class LocationController extends Controller
{
    public function province()
    {
        return Province::select('id', 'name')->get();
    }

    public function city($province_id)
    {
        return City::where('province_id', $province_id)
                   ->select('id', 'name')
                   ->get();
    }

    public function district($city_id)
    {
        return District::where('city_id', $city_id)
                       ->select('id', 'name')
                       ->get();
    }
}
