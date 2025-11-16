{{-- filepath: resources/views/admin/master/merchProduct/form.blade.php --}}
<form
    action="{{ isset($mode) && $mode == 'edit' ? route('master.merchProduct.update', $merchProduct->id) : route('master.merchProduct.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($mode) && $mode == 'edit')
    @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $merchProduct->name ?? '') }}"
            required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $merchProduct->price ?? '') }}"
            required>
    </div>
    <div class="mb-3">
        <label for="discount" class="form-label">Discount</label>
        <input type="number" name="discount" class="form-control"
            value="{{ old('discount', $merchProduct->discount ?? 0) }}">
    </div>
    <div class="mb-3">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', $merchProduct->stock ?? 0) }}"
            required>
    </div>
    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
        <textarea name="description" id="deskripsi" class="form-control" required>
            {{ old('description', $merchProduct->description ?? '') }}
        </textarea>
    </div>
    <div class="mb-3">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" class="form-control" multiple>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @if(isset($merchProduct) && $merchProduct->categories->contains($cat->id))
                selected @endif>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
        <small class="text-muted">Hold CTRL/Command untuk memilih lebih dari satu kategori.</small>
    </div>

    <div class="form-group">
        <label for="type">Tipe Produk</label>
        <select name="type" id="type" class="form-control" required>
            <option value="normal" {{ old('type', $merchProduct->type ?? '') == 'normal' ? 'selected' : '' }}>Normal
            </option>
            <option value="featured" {{ old('type', $merchProduct->type ?? '') == 'featured' ? 'selected' : '' }}>
                Featured</option>
        </select>
        <small class="form-text text-muted">
            <b>Normal:</b> Produk tampil di cell biasa.<br>
            <b>Featured:</b> Produk tampil di cell besar (span 2 kolom).
        </small>
        <div style="margin-top:8px;">
            <div style="display:grid;grid-template-columns:repeat(4,20px);gap:8px;align-items:center;">
                <div style="background:#007bff;height:20px;grid-column:span 2;border-radius:3px;" title="Featured">
                </div>
                <div style="background:#6c757d;height:20px;border-radius:3px;" title="Normal"></div>
                <div style="background:#6c757d;height:20px;border-radius:3px;" title="Normal"></div>
            </div>
            <div style="font-size:11px;color:#888;margin-top:2px;">
                <span style="display:inline-block;width:40px;text-align:center;color:#007bff;">Featured</span>
                <span
                    style="display:inline-block;width:40px;text-align:center;margin-left:16px;color:#6c757d;">Normal</span>
            </div>
        </div>
    </div>
    <div class="mb-3">
        <label for="images" class="form-label">Product Images - bisa upload lebih dari satu - max 2MB/img</label>
        <small class="text-muted d-block mb-1">
            <b>Syarat ukuran gambar dengan type:</b>
            <br>
            <span style="color:#007bff;">Normal:</span> <b>400 x 300 (px)</b> &nbsp;|&nbsp;
            <span style="color:#ff9800;">Featured:</span> <b>800 x 300 (px)</b>
        </small>
        <input type="file" name="images[]" class="form-control" multiple onchange="previewImages(event)">
        <div class="row mt-2 g-3" id="preview-container"></div>
        @if(isset($merchProduct) && $merchProduct->images)
        <div class="row mt-3 g-3">
            @foreach($merchProduct->images as $img)
            <div class="col-auto">
                <div class="card shadow-sm" style="width: 120px;">
                    <img src="{{ asset($img->image_path) }}" alt="Image" class="card-img-top mt-2"
                        style="height: 90px; object-fit: cover;">
                    <div class="card-body p-2">
                        <input type="text" name="existing_image_labels[{{ $img->id }}]"
                            class="form-control form-control-sm mb-1" placeholder="Label img"
                            value="{{ old('existing_image_labels.'.$img->id, $img->label) }}">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" name="delete_images[]"
                                value="{{ $img->id }}" id="delimg{{ $img->id }}">
                            <label class="form-check-label small" for="delimg{{ $img->id }}">Hapus</label>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="active"
                {{ (old('status', $merchProduct->status ?? 'inactive') == 'active') ? 'selected' : '' }}>Publish
            </option>
            <option value="inactive"
                {{ (old('status', $merchProduct->status ?? 'inactive') == 'inactive') ? 'selected' : '' }}>Draft
            </option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">
        {{ isset($mode) && $mode == 'edit' ? 'Update' : 'Create' }}
    </button>
</form>

{{-- Summernote CSS --}}
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
{{-- jQuery --}}
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
{{-- Summernote JS --}}
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>

<script>

function previewImages(event) {
    const files = event.target.files;
    const preview = document.getElementById('preview-container');
    preview.innerHTML = '';
    if (files) {
        Array.from(files).forEach((file, idx) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-auto';
                const card = document.createElement('div');
                card.className = 'card shadow-sm';
                card.style.width = '120px';
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'card-img-top';
                img.style.height = '90px';
                img.style.objectFit = 'cover';
                const body = document.createElement('div');
                body.className = 'card-body p-2';
                const input = document.createElement('input');
                input.type = 'text';
                input.name = 'image_labels[' + idx + ']';
                input.placeholder = 'Label';
                input.className = 'form-control form-control-sm mb-1';
                body.appendChild(input);
                card.appendChild(img);
                card.appendChild(body);
                col.appendChild(card);
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        });
    }
}
</script>