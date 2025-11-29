<style>
.note-editor .note-editable table,
.size-guide-content-preview table {
    border-collapse: collapse !important;
    width: 100%;
}

.note-editor .note-editable th,
.note-editor .note-editable td,
.size-guide-content-preview th,
.size-guide-content-preview td {
    border: 1px solid #333 !important;
    padding: 6px 10px;
}
</style>

{{-- ========================= FORM UTAMA ========================= --}}
<form
    action="{{ isset($mode) && $mode == 'edit' ? route('master.merchProduct.update', $merchProduct->id) : route('master.merchProduct.store') }}"
    method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($mode) && $mode == 'edit')
    @method('PUT')
    @endif

    {{-- ====== DATA PRODUK UTAMA ====== --}}
    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $merchProduct->name ?? '') }}"
            required>
    </div>
    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
        <textarea name="description" id="deskripsi" class="form-control"
            required>{{ old('description', $merchProduct->description ?? '') }}</textarea>
    </div>
    {{-- ====== PRODUCT GUIDE (HTML + IMAGE + BUTTON LABEL) ====== --}}
    <div class="mb-3">
        <label for="size-guide-content" class="form-label">Panduan Produk (HTML/Table)</label>
        <textarea name="size_guide_content" id="size-guide-content"
            class="form-control">{{ old('size_guide_content', $merchProduct->size_guide_content ?? '') }}</textarea>
        <small class="text-muted">Bisa diisi tabel ukuran, dimensi, atau info lain.</small>
    </div>
    <div class="mb-3">
        <label for="size-guide-image" class="form-label">Gambar Panduan Produk (opsional)</label>
        <input type="file" name="size_guide_image" id="size-guide-image" class="form-control" accept="image/*">
        @if(isset($mode) && $mode == 'edit' && !empty($merchProduct->size_guide_image))
        <div class="mt-2">
            <img src="{{ asset($merchProduct->size_guide_image) }}" alt="Size Guide" id="size-guide-preview"
                class="img-thumbnail" style="max-width:240px;">
            <div class="small text-muted">Gambar saat ini</div>
            <div class="form-check mt-1">
                <input class="form-check-input" type="checkbox" name="remove_size_guide_image"
                    id="remove_size_guide_image" value="1">
                <label class="form-check-label" for="remove_size_guide_image">
                    Hapus gambar panduan
                </label>
            </div>
        </div>
        @else
        <img id="size-guide-preview" class="img-thumbnail" style="max-width:240px; display:none;">
        @endif
    </div>
    <div class="mb-3">
        <label for="guide-button-label" class="form-label">Label Tombol Panduan Produk</label>
        <input type="text" name="guide_button_label" id="guide-button-label" class="form-control"
            value="{{ old('guide_button_label', $merchProduct->guide_button_label ?? '') }}" maxlength="50"
            placeholder="Contoh: Panduan Ukuran, Panduan Dimensi, dsb.">
        <small class="text-muted">Opsional. Jika dikosongkan, akan menggunakan 'Panduan Produk'.</small>
    </div>
    <div class="mb-3">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" id="categories-bubble" multiple>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @if(isset($merchProduct) && $merchProduct->categories->contains($cat->id))
                selected @endif>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
        <small class="text-muted">Pilih satu atau lebih kategori.</small>
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
            <b>Normal:</b> Produk tampil di cell biasa. <span class="text-info">Ukuran gambar yang disarankan:
                400x300px</span><br>
            <b>Featured:</b> Produk tampil di cell besar (span 2 kolom). <span class="text-info">Ukuran gambar yang
                disarankan: 800x300px</span><br>
            <span class="text-warning">Format gambar: JPEG, JPG, atau WEBP</span>
        </small>
    </div>

    <hr>
    <h5>Variants</h5>
    <div id="variants-container">
        @php
        $oldVariants = old('variants', isset($merchProduct) ? $merchProduct->variants->toArray() : []);
        @endphp
        @foreach($oldVariants as $vIdx => $variant)
        <div class="card mb-3 variant-item" data-index="{{ $vIdx }}">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center mb-2 variant-header">
                    <div class="d-flex align-items-center gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary variant-toggle"
                            aria-expanded="false">Show</button>
                        <strong>Variant #{{ $vIdx+1 }}</strong>
                    </div>
                    <div>
                        <input type="radio" name="default_variant" value="{{ $variant['id'] ?? 'new_' . $vIdx }}"
                            {{ (isset($variant['is_default']) && $variant['is_default']) || (!isset($variant['is_default']) && $vIdx == 0) ? 'checked' : '' }}>
                        <small class="text-primary">Default</small>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
                </div>

                <div class="variant-details" style="display:none">
                    <input type="hidden" name="variants[{{ $vIdx }}][id]" value="{{ $variant['id'] ?? '' }}">
                    <div class="mb-2">
                        <label>Variant Name</label>
                        <input type="text" name="variants[{{ $vIdx }}][name]" class="form-control"
                            value="{{ $variant['name'] ?? '' }}" required>
                    </div>
                    <div class="mb-2">
                        <label>Variant Code</label>
                        <input type="text" name="variants[{{ $vIdx }}][code]" class="form-control"
                            value="{{ $variant['code'] ?? '' }}">
                    </div>
                    <div class="mb-2">
                        <label>
                            Images
                            <small class="text-muted ms-2">Maximal 2MB/img | Format: JPEG, JPG, atau WEBP</small>
                        </label>
                        <small class="d-block text-info mb-2">
                            💡 Ukuran yang disarankan: <b>Normal (400x300px)</b> | <b>Featured (800x300px)</b>
                        </small>
                        <div class="variant-images-container d-flex flex-wrap gap-2">
                            @php
                            $images = $variant['images'] ?? [];
                            @endphp
                            @foreach($images as $iIdx => $img)
                            <div class="variant-image-item border rounded p-2 shadow-sm" style="width: 180px;">
                                <div class="image-preview mb-2"
                                    style="height: 110px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; cursor:pointer;">
                                    @if(isset($img['image_path']))
                                    <img src="{{ asset($img['image_path']) }}" alt="Current Image"
                                        style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                    @else
                                    <small class="text-muted">Klik untuk upload</small>
                                    @endif
                                </div>
                                <input type="hidden" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][id]"
                                    value="{{ $img['id'] ?? '' }}">
                                <input type="file" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][image_path]"
                                    class="d-none variant-image-input"
                                    {{ isset($img['image_path']) ? '' : 'required' }}>
                                <div class="d-grid gap-1">
                                    <button type="button" class="btn btn-outline-secondary btn-sm upload-trigger">Pilih
                                        Gambar</button>
                                    <small class="filename text-muted text-truncate"></small>
                                </div>
                                <input type="text" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][label]"
                                    class="form-control form-control-sm mt-1" placeholder="Label"
                                    value="{{ $img['label'] ?? '' }}">
                                <button type="button"
                                    class="btn btn-outline-danger btn-sm mt-2 remove-variant-image">Remove</button>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm add-variant-image">Add
                            Image</button>
                    </div>

                    {{-- ========== Tambahan: Stock, Price, Discount di level variant ========== --}}
                    <div class="mb-2 variant-stock-fields" @if(!empty($variant['sizes'])) style="display:none" @endif>
                        <label>Stock</label>
                        <input type="number" name="variants[{{ $vIdx }}][stock]" class="form-control"
                            placeholder="Stock" value="{{ $variant['stock'] ?? 0 }}">
                    </div>
                    <div class="mb-2 variant-price-fields" @if(!empty($variant['sizes'])) style="display:none" @endif>
                        <label>Price</label>
                        <input type="number" name="variants[{{ $vIdx }}][price]" class="form-control"
                            placeholder="Price" value="{{ $variant['price'] ?? '' }}">
                    </div>
                    <div class="mb-2 variant-discount-fields" @if(!empty($variant['sizes'])) style="display:none"
                        @endif>
                        <label>Discount (%)</label>
                        <input type="number" name="variants[{{ $vIdx }}][discount]" class="form-control"
                            placeholder="Discount" value="{{ $variant['discount'] ?? 0 }}" step="any" min="0" max="100">
                    </div>

                    {{-- Berat selalu tampil, tidak di-hide --}}
                    <div class="mb-2">
                        <label>Berat (gram)</label>
                        <input type="number" name="variants[{{ $vIdx }}][weight]" class="form-control"
                            placeholder="Berat (gram)" value="{{ $variant['weight'] ?? '' }}" required min="0"
                            step="0.01">
                    </div>

                    <div class="mb-2">
                        <small class="text-muted">
                            Jika variant tidak memiliki size, isi Stock/Price/Discount di atas.<br>
                            Jika variant memiliki size, isi Stock/Price/Discount di setiap size di bawah.
                        </small>
                    </div>
                    {{-- ========== END Tambahan ========== --}}

                    <div class="mb-2">
                        <label>Sizes</label>
                        <div class="variant-sizes-container">
                            @php
                            $sizes = $variant['sizes'] ?? [];
                            @endphp
                            @foreach($sizes as $sIdx => $sz)
                            <div class="row mb-1 variant-size-item">
                                <input type="hidden" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][id]"
                                    value="{{ $sz['id'] ?? '' }}">
                                <div class="col">
                                    <input type="text" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][size]"
                                        class="form-control" placeholder="(cnth:. Default, S, M, L, dll)"
                                        value="{{ $sz['size'] ?? '' }}" required>
                                </div>
                                <div class="col">
                                    <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][stock]"
                                        class="form-control" placeholder="Stock" value="{{ $sz['stock'] ?? 0 }}">
                                </div>
                                <div class="col">
                                    <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][price]"
                                        class="form-control" placeholder="Price" value="{{ $sz['price'] ?? '' }}">
                                </div>
                                <div class="col">
                                    <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][discount]"
                                        class="form-control" placeholder="Discount (%)"
                                        value="{{ $sz['discount'] ?? 0 }}" step="any" min="0" max="100">
                                </div>
                                <div class="col-auto">
                                    <button type="button"
                                        class="btn btn-outline-danger remove-variant-size">Remove</button>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        <button type="button" class="btn btn-outline-primary btn-sm add-variant-size">Add Size</button>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    <button type="button" class="btn btn-success mb-3" id="add-variant-btn">Add Variant</button>
    <hr>
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

{{-- ========================= TEMPLATE UNTUK JS (DINAMIS) ========================= --}}
<template id="variant-template">
    <div class="card mb-3 variant-item">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-center mb-2 variant-header">
                <div class="d-flex align-items-center gap-2">
                    <button type="button" class="btn btn-sm btn-outline-secondary variant-toggle"
                        aria-expanded="false">Show</button>
                    <strong>Variant #IDX#</strong>
                </div>
                <div>
                    <input type="radio" name="default_variant" value="#IDX#">
                    <small class="text-primary">Default - Jadikan product utama/default sebagai display</small>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
            </div>

            <div class="variant-details" style="display:none">
                <div class="mb-2">
                    <label>Variant Name</label>
                    <input type="text" name="variants[#IDX#][name]" class="form-control" placeholder="Variant Name"
                        required>
                </div>
                <div class="mb-2">
                    <label>Variant Code</label>
                    <input type="text" name="variants[#IDX#][code]" class="form-control">
                </div>
                <div class="mb-2">
                    <label>
                        Images
                        <small class="text-muted ms-2">Maximal 2MB/img | Format: JPEG, JPG, atau WEBP</small>
                    </label>
                    <small class="d-block text-info mb-2">
                        💡 Ukuran yang disarankan: <b>Normal (400x300px)</b> | <b>Featured (800x300px)</b>
                    </small>
                    <div class="variant-images-container"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm add-variant-image">Add Image</button>
                </div>
                {{-- ========== Tambahan: Stock, Price, Discount di level variant ========== --}}
                <div class="mb-2 variant-stock-fields">
                    <label>Stock</label>
                    <input type="number" name="variants[#IDX#][stock]" class="form-control" placeholder="Stock">
                </div>
                <div class="mb-2 variant-price-fields">
                    <label>Price</label>
                    <input type="number" name="variants[#IDX#][price]" class="form-control" placeholder="Price">
                </div>
                <div class="mb-2 variant-discount-fields">
                    <label>Discount (%)</label>
                    <input type="number" name="variants[#IDX#][discount]" class="form-control" placeholder="Discount"
                        step="any" min="0" max="100">
                </div>

                {{-- Berat selalu tampil, tidak di-hide --}}
                <div class="mb-2">
                    <label>Berat (gram)</label>
                    <input type="number" name="variants[#IDX#][weight]" class="form-control" placeholder="Berat (gram)"
                        value="0" required min="0" step="0.01">
                </div>

                <div class="mb-2">
                    <small class="text-muted">
                        Jika variant tidak memiliki size, isi Stock/Price/Discount di atas.<br>
                        Jika variant memiliki size, isi Stock/Price/Discount di setiap size di bawah.
                    </small>
                </div>
                {{-- ========== END Tambahan ========== --}}
                <div class="mb-2">
                    <label>Sizes</label>
                    <div class="variant-sizes-container"></div>
                    <button type="button" class="btn btn-outline-primary btn-sm add-variant-size">Add Size</button>
                </div>
            </div>
        </div>
    </div>
</template>
<template id="variant-image-template">
    <div class="variant-image-item border rounded p-2 shadow-sm" style="width: 180px;">
        <div class="image-preview mb-2"
            style="height: 110px; display:flex; align-items:center; justify-content:center; background:#f8f9fa; cursor:pointer;">
            <small class="text-muted">Klik untuk upload</small>
        </div>
        <input type="file" name="variants[#VIDX#][images][#IIDX#][image_path]" class="d-none variant-image-input"
            required>
        <input type="text" name="variants[#VIDX#][images][#IIDX#][label]" class="form-control form-control-sm mt-1"
            placeholder="Label">
        <button type="button" class="btn btn-outline-danger btn-sm mt-2 remove-variant-image">Remove</button>
    </div>
</template>
<template id="variant-size-template">
    <div class="row mb-1 variant-size-item">
        <div class="col">
            <input type="text" name="variants[#VIDX#][sizes][#SIDX#][size]" class="form-control"
                placeholder="Size (e.g., S, M, L)" required>
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][stock]" class="form-control" placeholder="Stock">
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][price]" class="form-control" placeholder="Price">
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][discount]" class="form-control"
                placeholder="Discount (%)" value="" step="any" min="0" max="100">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger remove-variant-size">Remove</button>
        </div>
    </div>
</template>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const input = document.getElementById('size-guide-image');
    const preview = document.getElementById('size-guide-preview');
    if(input && preview) {
        input.addEventListener('change', function(e) {
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(ev) {
                    preview.src = ev.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(input.files[0]);
            }
        });
    }
});
</script>