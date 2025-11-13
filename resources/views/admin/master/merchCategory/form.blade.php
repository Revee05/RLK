{{-- filepath: resources/views/admin/master/merchCategory/form.blade.php --}}
<form action="{{ isset($mode) && $mode == 'edit' ? route('master.merchCategory.update', $category->id) : route('master.merchCategory.store') }}"
      method="POST">
    @csrf
    @if(isset($mode) && $mode == 'edit')
        @method('PUT')
    @endif

    <div class="mb-3">
        <label for="name" class="form-label">Category Name</label>
        <input type="text" name="name" class="form-control" value="{{ old('name', $category->name ?? '') }}" required>
    </div>
    <button type="submit" class="btn btn-primary">
        {{ isset($mode) && $mode == 'edit' ? 'Update' : 'Create' }}
    </button>
</form>