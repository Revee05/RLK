{{-- filepath: c:\Users\HP\Documents\GitHub\RLK\resources\views\admin\master\merchCategory\form.blade.php --}}
@if(isset($mode) && $mode == 'edit')
    <form action="{{ route('master.merchCategory.update', $category->id) }}" method="POST">
        @csrf
        @method('PUT')
        <label class="form-label mb-2">Nama Kategori</label>
        <div class="mb-2">
            <input type="text" name="name" class="form-control w-75 d-inline-block"
                   value="{{ old('name', $category->name) }}" placeholder="Nama Kategori" required>
        </div>
        <button type="submit" class="btn btn-primary">Update</button>
    </form>
@else
    <form action="{{ route('master.merchCategory.store') }}" method="POST">
        @csrf
        <label class="form-label mb-2">Nama Kategori</label>
        <small class="text-muted d-block mb-2">
            Isi nama kategori, klik <b>Tambah Kategori</b> untuk menambah baris. Klik <b>Simpan</b> jika sudah selesai.
        </small>
        <div id="categories-wrapper">
            <div class="input-group mb-2 category-input">
                <input type="text" name="categories[0][name]" class="form-control" placeholder="Contoh: Kaos" required>
                <button type="button" class="btn btn-outline-danger remove-category" style="display:none;">Hapus</button>
            </div>
        </div>
        <div class="mb-3">
            <button type="button" class="btn btn-primary me-2" id="add-category">
                <i class="fas fa-plus"></i> Tambah Kategori
            </button>
            <button type="submit" class="btn btn-success">
                <i class="fas fa-save"></i> Simpan
            </button>
        </div>
    </form>

    @push('scripts')
    <script>
    let index = 1;
    document.getElementById('add-category').onclick = function() {
        const wrapper = document.getElementById('categories-wrapper');
        const div = document.createElement('div');
        div.className = 'input-group mb-2 category-input';
        div.innerHTML = `<input type="text" name="categories[${index}][name]" class="form-control" placeholder="Contoh: Topi" required>
            <button type="button" class="btn btn-outline-danger remove-category">Hapus</button>`;
        wrapper.appendChild(div);
        div.querySelector('input').focus();
        index++;
    };

    document.getElementById('categories-wrapper').addEventListener('click', function(e) {
        if (e.target.classList.contains('remove-category')) {
            e.target.parentElement.remove();
        }
    });
    </script>
    @endpush
@endif