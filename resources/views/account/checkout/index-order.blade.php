@extends('account.partials.layout')
@section('css')

@endsection
@section('content')
<section class="py-4" id="customer-account">
    <div class="container">
         <table class="table table-bordered table-sm" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="5%">No</th>
                            <th>Nama Kabupaten</th>
                            <th>Provinsi</th>
                            <th width="10%"></th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                        $no=1;
                        @endphp
                        @foreach($orders as $order)
                        <tr>
                            <td>{{$no++}}</td>
                            <td>{{ucfirst($order->number)}}</td>
                            <td>{{ucfirst($order->total_price)}}</td>
                            <td class="text-center">
                                <a href="{{route('account.orders.edit',$order->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-pencil-alt"></i>
                                </a>
                                <a href="{{route('account.orders.show',$order->id)}}" class="btn btn-sm btn-info rounded-0">
                                    <i class="fa fa-eye"></i>
                                </a>
                                
                               {{--  <form action="{{route('account.orders.destroy',[$order->id])}}" method="post" class="d-inline">
                                    @method('delete')
                                    @csrf
                                    <button onclick="return confirm('Are you sure want to delete this record?')"
                                        type="submit" class="btn btn-danger btn-sm rounded-0">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form> --}}

                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
    </div>
</section>
