<?php

namespace App\Prints;
use PDF;
use App\Masuk;
use App\Keluar;
use App\Barang;

/**
 * Print Transaksi
 */
class TransaksiPrint
{
	
	public function masuk($id)
	{
		$masuk = Masuk::findOrFail($id);
        $pdf = PDF::loadview('admin.prints.masuk',['masuk'=>$masuk]);
        return $pdf->stream();
	}
	public function keluar($id)
	{
		$keluar = Keluar::findOrFail($id);
        $pdf = PDF::loadview('admin.prints.keluar',['keluar'=>$keluar]);
        return $pdf->stream();
	}
	public function stok()
	{
		$stoks = Barang::all();
        $pdf = PDF::loadview('admin.prints.stok',['stoks'=>$stoks]);
        return $pdf->stream();
	}
	public function reportMasuk($start_date, $end_date)
	{
        $query = Masuk::with('transaksis');

        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereBetween('date_of_entry', [$start_date, $end_date]);
        }
        $masuks = $query->get();
        $pdf = PDF::loadview('admin.prints.laporan-masuk',['masuks'=>$masuks]);
        return $pdf->stream();
	}
	public function reportKeluar($start_date, $end_date)
	{
        $query = Keluar::with('transaksis');

        if (!empty($start_date) && !empty($end_date)) {
            $query = $query->whereBetween('out_date', [$start_date, $end_date]);
        }
        $keluars = $query->get();
        $pdf = PDF::loadview('admin.prints.laporan-keluar',['keluars'=>$keluars]);
        return $pdf->stream();
	}
	

}