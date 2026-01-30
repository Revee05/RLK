@extends('admin.partials._layout')
@section('title', 'Daftar Tim')
@section('team', 'active')
@section('css')
    <link href="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.css') }}" rel="stylesheet">
    <style>
        .img-team {
            max-width: 120px;
            width: 100%;
            height: auto;
            object-fit: cover;
            border-radius: 6px;
        }

        @media (max-width: 576px) {
            .img-team {
                max-width: 90px;
            }
        }
    </style>
@endsection

@section('content')
    <div class="container-fluid">
        <h1 class="h5 mb-4 text-gray-800">Master
            <small>Tim</small>
            <a href="{{ route('master.team.create') }}" class="btn btn-primary btn-sm float-right">
                <i class="fa fa-plus-circle"></i> <span class="d-none d-sm-inline">Tambah Tim</span>
            </a>
        </h1>

        <div class="card shadow mb-4">
            <div class="card-body">
                @include('admin.partials._success')

                <div class="table-responsive">
                    <table class="table table-bordered table-sm table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th width="5%" class="d-none d-md-table-cell text-center">No</th>
                                <th width="18%" class="text-center">Avatar</th>
                                <th>Nama</th>
                                <th>Role</th>
                                <th>Email</th>
                                <th>Instagram</th>
                                <th width="10%" class="text-center text-nowrap">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @php $no = 1; @endphp
                            @foreach ($teamMembers as $member)
                                <tr>
                                    <td class="align-middle text-center d-none d-md-table-cell">{{ $no++ }}</td>
                                    <td class="text-center align-middle">
                                        @if ($member->avatar)
                                            <img src="{{ asset($member->avatar) }}" alt="{{ $member->name }}"
                                                class="img-fluid img-team">
                                        @else
                                            <span class="badge badge-secondary">No Image</span>
                                        @endif
                                    </td>
                                    <td class="align-middle text-break">{{ $member->name }}</td>
                                    <td class="align-middle text-break">{{ $member->role }}</td>
                                    <td class="align-middle text-break">{{ $member->email }}</td>
                                    <td class="align-middle text-break">{{ $member->instagram }}</td>
                                    <td class="text-center align-middle text-nowrap">
                                        <a href="{{ route('master.team.edit', $member->id) }}"
                                            class="btn btn-sm btn-info rounded-0" title="Edit">
                                            <i class="fa fa-pencil-alt"></i>
                                        </a>
                                        <form action="{{ route('master.team.destroy', [$member->id]) }}" method="post"
                                            class="d-inline">
                                            @method('delete')
                                            @csrf
                                            <button onclick="return confirm('Apakah Anda yakin ingin menghapus data ini?')"
                                                type="submit" class="btn btn-danger btn-sm rounded-0" title="Hapus">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('js')
    <script src="{{ asset('assets/vendor/datatables/jquery.dataTables.min.js') }}"></script>
    <script src="{{ asset('assets/vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#dataTable').DataTable({
                "responsive": true,
                "autoWidth": false,
                "columnDefs": [{
                    "orderable": false,
                    "targets": [1, 6]
                }]
            });
        });
    </script>
@endsection
