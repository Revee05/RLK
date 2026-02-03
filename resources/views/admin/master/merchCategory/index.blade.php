{{-- filepath: resources/views/admin/master/merchCategory/index.blade.php --}}
@extends('admin.partials._layout')
@section('title','Merch Categories')
@section('collapseMerch','show')
@section('merchcategory','active')
@section('merch','active')
@section('content')
<div class="container">
    <h1>Merchandise Categories</h1>
    <a href="{{ route('master.merchCategory.create') }}" class="btn btn-success mb-3">Add Category</a>
    <!-- DataTables CSS -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/jquery.dataTables.min.css">
    <table class="table table-bordered align-middle" id="categoryTable">
        <thead>
            <tr>
                <th class="text-center" style="width: 60px;">No</th>
                <th>Name</th>
                <th>Slug</th>
                <th class="text-center" style="width: 140px;">Action</th>
            </tr>
        </thead>
        <tbody>
            @forelse($categories as $cat)
            <tr>
                <td class="text-center">{{ $loop->iteration }}</td>
                <td class="text-start">{{ $cat->name }}</td>
                <td class="text-start">{{ $cat->slug }}</td>
                <td class="text-center">
                    <a href="{{ route('master.merchCategory.edit', $cat->id) }}" class="btn btn-warning btn-sm me-1">Edit</a>
                    <form action="{{ route('master.merchCategory.destroy', $cat->id) }}" method="POST" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button class="btn btn-danger btn-sm ms-1" onclick="return confirm('Delete this category?')">Delete</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="4" class="text-center">No categories found.</td>
            </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection

@push('scripts')
<!-- DataTables JS -->
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script>
function debounce(func, wait) {
    let timeout;
    return function(...args) {
        clearTimeout(timeout);
        timeout = setTimeout(() => func.apply(this, args), wait);
    };
}

$(document).ready(function() {
    const table = $('#categoryTable').DataTable({
        "columnDefs": [
            { "orderable": false, "targets": 3 } // Kolom Action tidak bisa sorting
        ]
    });

    // Debounce untuk search input DataTables
    const searchInput = $('div.dataTables_filter input');
    const debouncedSearch = debounce(function() {
        table.search(this.value).draw();
    }, 1000);

    
    searchInput.off('keyup.DT input.DT');
    
    searchInput.on('input', debouncedSearch);
});
</script>
@endpush