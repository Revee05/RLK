<?php 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

if (!function_exists('kode_transaksi')) {
    function kode_transaksi()
    {
    	$kode = time();

    	if (session()->exists('kode_transaksi')) {
            $kode_transaksi = session()->get('kode_transaksi');
        } else {

	        Session::put('kode_transaksi', $kode);
	        Session::save();
	        //get kode
	        $kode_transaksi = session()->get('kode_transaksi');
        }

        return $kode_transaksi;
        
    }
}