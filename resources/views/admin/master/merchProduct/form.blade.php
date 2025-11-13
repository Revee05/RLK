{{-- filepath: resources/views/admin/master/merchProduct/form.blade.php --}}
<form action="{{ isset($mode) && $mode == 'edit' ? route('master.merchProduct.update', $merchProduct->id) : route('master.merchProduct.store') }}"
      method="POST" enctype="multipart/form-data">
    @csrf
    @if(isset($mode) && $mode == 'edit')
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Product Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $merchProduct->name ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="price" class="form-label">Price</label>
        <input type="number" name="price" class="form-control" value="{{ old('price', $merchProduct->price ?? '') }}" required>
    </div>
    <div class="mb-3">
        <label for="discount" class="form-label">Discount</label>
        <input type="number" name="discount" class="form-control" value="{{ old('discount', $merchProduct->discount ?? 0) }}">
    </div>
    <div class="mb-3">
        <label for="stock" class="form-label">Stock</label>
        <input type="number" name="stock" class="form-control" value="{{ old('stock', $merchProduct->stock ?? 0) }}" required>
    </div>
    <div class="mb-3">
        <label for="description" class="form-label">Description</label>
        <textarea name="description" class="form-control" required>{{ old('description', $merchProduct->description ?? '') }}</textarea>
    </div>
    <div class="mb-3">
        <label for="categories" class="form-label">Categories</label>
        <select name="categories[]" class="form-control" multiple>
            @foreach($categories as $cat)
                <option value="{{ $cat->id }}"
                    @if(isset($merchProduct) && $merchProduct->categories->contains($cat->id)) selected @endif>
                    {{ $cat->name }}
                </option>
            @endforeach
        </select>
        <small class="text-muted">Hold CTRL/Command untuk memilih lebih dari satu kategori.</small>
    </div>
    <div class="mb-3">
        <label for="images" class="form-label">Product Images</label>
        <input type="file" name="images[]" class="form-control" multiple>
        @if(isset($merchProduct) && $merchProduct->images)
            <div class="row mt-2">
                @foreach($merchProduct->images as $img)
                    <div class="col-auto mb-2">
                        <img src="{{ asset('storage/'.$img->image_path) }}" alt="Image" width="80">
                    </div>
                @endforeach
            </div>
        @endif
    </div>
    <div class="mb-3">
        <label for="status" class="form-label">Status</label>
        <select name="status" class="form-control">
            <option value="active" {{ (old('status', $merchProduct->status ?? '') == 'active') ? 'selected' : '' }}>Active</option>
            <option value="inactive" {{ (old('status', $merchProduct->status ?? '') == 'inactive') ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary">
        {{ isset($mode) && $mode == 'edit' ? 'Update' : 'Create' }}
    </button>
</form>