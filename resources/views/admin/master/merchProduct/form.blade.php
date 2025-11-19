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
        <input type="text" name="name" class="form-control" value="{{ old('name', $merchProduct->name ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="deskripsi" class="form-label">Deskripsi Produk</label>
        <textarea name="description" id="deskripsi" class="form-control" required>{{ old('description', $merchProduct->description ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" class="form-control" multiple>
            @foreach($categories as $cat)
            <option value="{{ $cat->id }}" @if(isset($merchProduct) && $merchProduct->categories->contains($cat->id)) selected @endif>
                {{ $cat->name }}
            </option>
            @endforeach
        </select>
        <small class="text-muted">Hold CTRL/Command untuk memilih lebih dari satu kategori.</small>
    </div>

    <div class="form-group">
        <label for="type">Tipe Produk</label>
        <select name="type" id="type" class="form-control" required>
            <option value="normal" {{ old('type', $merchProduct->type ?? '') == 'normal' ? 'selected' : '' }}>Normal</option>
            <option value="featured" {{ old('type', $merchProduct->type ?? '') == 'featured' ? 'selected' : '' }}>Featured</option>
        </select>
        <small class="form-text text-muted">
            <b>Normal:</b> Produk tampil di cell biasa.<br>
            <b>Featured:</b> Produk tampil di cell besar (span 2 kolom).
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
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <strong>Variant #{{ $vIdx+1 }}</strong>
                    {{-- Radio untuk pilih default --}}
                    <div>
                        <input type="radio" name="default_variant" value="{{ $variant['id'] ?? 'new_' . $vIdx }}"
                            {{ (isset($variant['is_default']) && $variant['is_default']) || (!isset($variant['is_default']) && $vIdx == 0) ? 'checked' : '' }}>
                        <small class="text-primary">Default</small>
                    </div>
                    <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
                </div>
                <input type="hidden" name="variants[{{ $vIdx }}][id]" value="{{ $variant['id'] ?? '' }}">
                <div class="mb-2">
                    <label>Variant Name</label>
                    <input type="text" name="variants[{{ $vIdx }}][name]" class="form-control" value="{{ $variant['name'] ?? '' }}" required>
                </div>
                <div class="mb-2">
                    <label>Variant Code</label>
                    <input type="text" name="variants[{{ $vIdx }}][code]" class="form-control" value="{{ $variant['code'] ?? '' }}">
                </div>
                <div class="mb-2">
                    <label>
                        Images
                        <small class="text-muted ms-2">maximal 2MB/img format WEBP</small>
                    </label>
                    <div class="variant-images-container">
                        @php
                            $images = $variant['images'] ?? [];
                        @endphp
                        @foreach($images as $iIdx => $img)
                        <div class="input-group mb-1 variant-image-item">
                            <input type="hidden" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][id]" value="{{ $img['id'] ?? '' }}">
                            <input type="file" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][image_path]" class="form-control variant-image-input" {{ isset($img['image_path']) ? '' : 'required' }}>
                            <input type="text" name="variants[{{ $vIdx }}][images][{{ $iIdx }}][label]" class="form-control" placeholder="Label" value="{{ $img['label'] ?? '' }}">
                            <button type="button" class="btn btn-outline-danger remove-variant-image">Remove</button>
                            <div class="mt-1 image-preview">
                                @if(isset($img['image_path']))
                                    <img src="{{ asset($img['image_path']) }}" alt="Current Image" width="60">
                                    <small class="text-muted">Current image</small>
                                @endif
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm add-variant-image">Add Image</button>
                </div>
                <div class="mb-2">
                    <label>Sizes</label>
                    <div class="variant-sizes-container">
                        @php
                            $sizes = $variant['sizes'] ?? [];
                        @endphp
                        @foreach($sizes as $sIdx => $sz)
                        <div class="row mb-1 variant-size-item">
                            <input type="hidden" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][id]" value="{{ $sz['id'] ?? '' }}">
                            <div class="col">
                                <input type="text" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][size]" class="form-control" placeholder="(cnth:. Default, S, M, L, dll)" value="{{ $sz['size'] ?? '' }}" required>
                            </div>
                            <div class="col">
                                <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][stock]" class="form-control" placeholder="Stock" value="{{ $sz['stock'] ?? 0 }}">
                            </div>
                            <div class="col">
                                <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][price]" class="form-control" placeholder="Price" value="{{ $sz['price'] ?? '' }}">
                            </div>
                            <div class="col">
                                <input type="number" name="variants[{{ $vIdx }}][sizes][{{ $sIdx }}][discount]" class="form-control" placeholder="Discount" value="{{ $sz['discount'] ?? 0 }}">
                            </div>
                            <div class="col-auto">
                                <button type="button" class="btn btn-outline-danger remove-variant-size">Remove</button>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm add-variant-size">Add Size</button>
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
            <option value="active" {{ (old('status', $merchProduct->status ?? 'inactive') == 'active') ? 'selected' : '' }}>Publish</option>
            <option value="inactive" {{ (old('status', $merchProduct->status ?? 'inactive') == 'inactive') ? 'selected' : '' }}>Draft</option>
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
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Variant #IDX#</strong>
                <div>
                    <input type="radio" name="default_variant" value="#IDX#">
                    <small class="text-primary">Default - Jadikan product utama/default sebagai display</small>
                </div>
                <button type="button" class="btn btn-danger btn-sm remove-variant">Remove</button>
            </div>
            <div class="mb-2">
                <label>Variant Name</label>
                <input type="text" name="variants[#IDX#][name]" class="form-control" placeholder="Variant Name" required>
            </div>
            <div class="mb-2">
                <label>Variant Code</label>
                <input type="text" name="variants[#IDX#][code]" class="form-control">
            </div>
            <div class="mb-2">
                <label>
                    Images
                    <small class="text-muted ms-2">maximal 2MB/img format WEBP</small>
                </label>
                <div class="variant-images-container"></div>
                <button type="button" class="btn btn-outline-primary btn-sm add-variant-image">Add Image</button>
            </div>
            <div class="mb-2">
                <label>Sizes</label>
                <div class="variant-sizes-container"></div>
                <button type="button" class="btn btn-outline-primary btn-sm add-variant-size">Add Size</button>
            </div>
        </div>
    </div>
</template>
<template id="variant-image-template">
    <div class="input-group mb-1 variant-image-item">
        <input type="file" name="variants[#VIDX#][images][#IIDX#][image_path]" class="form-control variant-image-input" required>
        <input type="text" name="variants[#VIDX#][images][#IIDX#][label]" class="form-control" placeholder="Label">
        <button type="button" class="btn btn-outline-danger remove-variant-image">Remove</button>
        <div class="mt-1 image-preview"></div>
    </div>
</template>
<template id="variant-size-template">
    <div class="row mb-1 variant-size-item">
        <div class="col">
            <input type="text" name="variants[#VIDX#][sizes][#SIDX#][size]" class="form-control" placeholder="Size (e.g., S, M, L)" required>
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][stock]" class="form-control" placeholder="Stock">
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][price]" class="form-control" placeholder="Price">
        </div>
        <div class="col">
            <input type="number" name="variants[#VIDX#][sizes][#SIDX#][discount]" class="form-control" placeholder="Discount" value="">
        </div>
        <div class="col-auto">
            <button type="button" class="btn btn-outline-danger remove-variant-size">Remove</button>
        </div>
    </div>
</template>

{{-- ========================= SCRIPT DINAMIS (TAMBAH/HAPUS VARIANT, IMAGE, SIZE) ========================= --}}
<script>
/*
    Bagian ini untuk:
    - Menambah/menghapus variant, image, dan size secara dinamis
    - Menampilkan preview gambar setelah upload
*/
document.addEventListener('DOMContentLoaded', function() {
    let variantIdx = document.querySelectorAll('.variant-item').length || 0;

    document.getElementById('add-variant-btn').addEventListener('click', function() {
        addVariant();
    });

    function addVariant() {
        let template = document.getElementById('variant-template').innerHTML.replace(/#IDX#/g, variantIdx);
        let div = document.createElement('div');
        div.innerHTML = template;
        div.firstElementChild.setAttribute('data-index', variantIdx); // Tambahkan data-index
        document.getElementById('variants-container').appendChild(div.firstElementChild);
        variantIdx++;
        updateVariantEvents();
    }

    function updateVariantEvents() {
        document.querySelectorAll('.remove-variant').forEach(btn => {
            btn.onclick = function() {
                btn.closest('.variant-item').remove();
            }
        });

        document.querySelectorAll('.add-variant-image').forEach((btn, vIdx) => {
            btn.onclick = function() {
                let imagesContainer = btn.closest('.variant-item').querySelector('.variant-images-container');
                let iIdx = imagesContainer.querySelectorAll('.variant-image-item').length;
                let template = document.getElementById('variant-image-template').innerHTML
                    .replace(/#VIDX#/g, vIdx)
                    .replace(/#IIDX#/g, iIdx);
                let div = document.createElement('div');
                div.innerHTML = template;
                imagesContainer.appendChild(div.firstElementChild);
                updateVariantEvents();
            }
        });

        document.querySelectorAll('.remove-variant-image').forEach(btn => {
            btn.onclick = function() {
                btn.closest('.variant-image-item').remove();
            }
        });

        document.querySelectorAll('.add-variant-size').forEach((btn, vIdx) => {
            btn.onclick = function() {
                let sizesContainer = btn.closest('.variant-item').querySelector('.variant-sizes-container');
                let sIdx = sizesContainer.querySelectorAll('.variant-size-item').length;
                let template = document.getElementById('variant-size-template').innerHTML
                    .replace(/#VIDX#/g, vIdx)
                    .replace(/#SIDX#/g, sIdx);
                let div = document.createElement('div');
                div.innerHTML = template;
                sizesContainer.appendChild(div.firstElementChild);
                updateVariantEvents();
            }
        });

        document.querySelectorAll('.remove-variant-size').forEach(btn => {
            btn.onclick = function() {
                btn.closest('.variant-size-item').remove();
            }
        });

        // Preview image after upload
        document.querySelectorAll('.variant-image-input').forEach(input => {
            input.onchange = function(e) {
                const previewDiv = input.closest('.variant-image-item').querySelector('.image-preview');
                previewDiv.innerHTML = '';
                if (input.files && input.files[0]) {
                    const reader = new FileReader();
                    reader.onload = function(ev) {
                        previewDiv.innerHTML = '<img src="' + ev.target.result + '" alt="Preview" width="60">';
                    }
                    reader.readAsDataURL(input.files[0]);
                }
            }
        });
    }

    updateVariantEvents();

    document.querySelector('form').addEventListener('submit', function(e) {
        let defaultVariantSelected = document.querySelector('input[name="default_variant"]:checked');
        if (!defaultVariantSelected) {
            e.preventDefault();
            alert('Please select a default variant.');
        }
    });
});
</script>

@if ($errors->has('name'))
    <div class="text-danger">{{ $errors->first('name') }}</div>
@endif

@if ($errors->has('variants.*.name'))
    <div class="text-danger">{{ $errors->first('variants.*.name') }}</div>
@endif