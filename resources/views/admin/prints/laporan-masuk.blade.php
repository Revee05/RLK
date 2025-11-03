<!DOCTYPE html>
<html>
	<head>
		<title>Report</title>
		<link href="{{asset('assets/css/sb-admin-2.min.css')}}" rel="stylesheet">
		<style type="text/css">
			table {
			    border-collapse: collapse;
			}
			.table {
			  width: 100%;
			  margin-bottom: 1rem;
			  color: black;
			}

			.table th,
			.table td {
			  padding: 0.75rem;
			  vertical-align: top;
			  border-top: 1px solid black;
			}

			.table thead th {
			  vertical-align: bottom;
			  border-bottom: 2px solid black;
			}

			.table tbody + tbody {
			  border-top: 2px solid black;
			}

			.table-sm th,
			.table-sm td {
			  padding: 0.3rem;
			}

			.table-bordered {
			  border: 1px solid black;
			}

			.table-bordered th,
			.table-bordered td {
			  border: 1px solid black;
			}

			.table-bordered thead th,
			.table-bordered thead td {
			  border-bottom-width: 2px;
			}
			.table-sm th,
			.table-sm td {
			  padding: 0.3rem;
			}
			.pb-2 {
				padding-bottom: .8rem !important;
			}
			.pb-4 {
				padding-bottom: 1.25rem !important;
			}
			.d-block {
			  display: block !important;
			}
			.d-flex {
			  display: flex;
			}
			.d-inline-flex {
			  display: inline-flex !important;
			}
			.alert {
			  position: relative;
			  padding: 0.75rem 1.25rem;
			  margin-bottom: 1rem;
			  border: 1px solid transparent;
			  border-radius:0;
			  margin-top: 10px;
			}
			.alert-secondary {
			  color: #45464e;
			  background-color: #e7e7ea;
			  border-color: #dddde2;
			}

			.alert-secondary hr {
			  border-top-color: #cfcfd6;
			}

			.alert-secondary .alert-link {
			  color: #2d2e33;
			}
			.justify-content-between {
			  justify-content: space-between !important;
			}
		</style>
	</head>
	<body>
		<center>
		<h3>{{$setting->title}}</h3>
		<div>{{$setting->address}}</div>
		<div>Phone: {{$setting->phone}} WA: {{$setting->wa}}</div>
		<div>Email : {{$setting->email}}</div>
		</center>
		<hr>
		<center class="pb-4">
			<b>LAPORAN MASUK</b>
		</center>
		<div class="d-block pb-2">
			<div>Tanggal : {{date('Y-m-d')}}</div>
		</div>
		<div class="d-block border-bottom">
			 <table class="table table-bordered table-sm" id="dataTable">
                    <thead>
                        <tr>
                            <th>Tanggal</th>
                            <th>Supplier</th>
                            <th>Kode Barang</th>
                            <th>Nama Barang</th>
                            <th>Jumlah</th>
                            <th>Satuan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($masuks as $masuk)
                            <tr style="background: #efefef;">
                                <td>{{$masuk->date_of_entry}}</td>
                                <td>{{ucwords($masuk->supplier)}}</td>
                                <td></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            @php
                            $no=1;
                            @endphp
                            @foreach($masuk->transaksis as $trans)
                            <tr class="event">
                                <td></td>
                                <td align="right">{{$no++}}.</td>
                                <td>{{$trans->barang->code}}</td>
                                <td>{{ucfirst($trans->barang->name)}}</td>
                                <td>{{$trans->amount}}</td>
                                <td>1</td>
                            </tr>
                            @endforeach
                        @endforeach
                    </tbody>
                </table>
		</div>
		<div style="width: 100%;">
		  	<div style="text-align:right; padding-top: 10px; font-size: 14px;">
		  		Dilaporkan pada tanggal, {{date('Y-m-d')}}
		  	</div>
		</div>
	</body>
</html>