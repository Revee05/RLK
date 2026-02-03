{{-- filepath: resources/views/admin/master/merchProduct/create.blade.php --}}
@extends('admin.partials._layout')
@section('title','Create Merch Product')
@section('collapseMerch','show')
@section('addmerchproduct','active')
@section('merch','active')
@section('content')
<div class="container">
    <h1>Add Merchandise Product</h1>
    @include('admin.master.merchProduct.form', ['mode' => 'create', 'categories' => $categories])
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>

<script>
$(document).ready(function() {
    $('#deskripsi').summernote({
        placeholder: 'Tulis deskripsi produk disini...',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['font', ['strikethrough']],
            ['fontsize', ['fontsize']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['height', ['height']],
        ],
        height: 150
    });

    // Initialize Choices.js for categories bubble
    new Choices('#categories-bubble', {
        removeItemButton: true,
        placeholder: true,
        placeholderValue: 'Pilih kategori...',
        searchPlaceholderValue: 'Cari kategori...',
        noResultsText: 'Kategori tidak ditemukan',
        itemSelectText: '',
        shouldSort: false
    });

    // Initialize Summernote for product guide content
    $('#size-guide-content').summernote({
        placeholder: 'Tulis panduan ukuran/dimensi di sini... (opsional)',
        toolbar: [
            ['style', ['bold', 'italic', 'underline', 'clear']],
            ['para', ['ul', 'ol', 'paragraph']],
            ['table', ['table']],
        ],
        height: 180
    });
});

// ========================= SCRIPT DINAMIS VARIANT =========================
document.addEventListener('DOMContentLoaded', function() {
    let variantIdx = document.querySelectorAll('.variant-item').length || 0;

    document.getElementById('add-variant-btn').addEventListener('click', function() {
        addVariant();
    });

    // Fungsi untuk re-index semua variant agar array tetap urut
    function reindexVariants() {
        document.querySelectorAll('.variant-item').forEach((card, idx) => {
            card.setAttribute('data-index', idx);
            // Update variant header label
            let headerLabel = card.querySelector('.variant-header strong');
            if (headerLabel) headerLabel.textContent = 'Variant #' + (idx + 1);
            // Update all input name attributes inside this variant
            card.querySelectorAll('[name]').forEach(input => {
                input.name = input.name.replace(/variants\[\d+\]/, `variants[${idx}]`);
            });
            // Reindex images
            card.querySelectorAll('.variant-image-item').forEach((imgCard, iIdx) => {
                imgCard.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/variants\[\d+\]\[images\]\[\d+\]/, `variants[${idx}][images][${iIdx}]`);
                });
            });
            // Reindex sizes
            card.querySelectorAll('.variant-size-item').forEach((szCard, sIdx) => {
                szCard.querySelectorAll('[name]').forEach(input => {
                    input.name = input.name.replace(/variants\[\d+\]\[sizes\]\[\d+\]/, `variants[${idx}][sizes][${sIdx}]`);
                });
            });
        });
    }

    function addVariant() {
        let template = document.getElementById('variant-template').innerHTML.replace(/#IDX#/g, variantIdx);
        let div = document.createElement('div');
        div.innerHTML = template;
        let card = div.firstElementChild;
        // Accordion: default collapsed
        let details = card.querySelector('.variant-details');
        if (details) details.style.display = 'none';
        document.getElementById('variants-container').appendChild(card);
        reindexVariants();
        updateVariantEvents();
        variantIdx++;
    }

    function updateVariantEvents() {
        // Accordion toggle
        document.querySelectorAll('.variant-header').forEach(header => {
            header.onclick = function(e) {
                if (e.target.classList.contains('remove-variant')) return;
                let details = header.parentElement.querySelector('.variant-details');
                if (details) details.style.display = details.style.display === 'none' ? '' : 'none';
            }
        });

        // Remove variant with confirm
        document.querySelectorAll('.remove-variant').forEach(btn => {
            btn.onclick = function(e) {
                e.stopPropagation();
                if (confirm('Apakah Anda yakin ingin menghapus variant ini? Tindakan ini tidak dapat dibatalkan.')) {
                    btn.closest('.variant-item').remove();
                    reindexVariants();
                    updateVariantEvents();
                }
            }
        });

        // Tambah/hapus gambar dan size (jika ada)
        document.querySelectorAll('.add-variant-image').forEach((btn) => {
            btn.onclick = function() {
                let variantCard = btn.closest('.variant-item');
                let vIdx = variantCard.getAttribute('data-index') || 0;
                let imagesContainer = variantCard.querySelector('.variant-images-container');
                let iIdx = imagesContainer.querySelectorAll('.variant-image-item').length;
                let template = document.getElementById('variant-image-template').innerHTML
                    .replace(/#VIDX#/g, vIdx)
                    .replace(/#IIDX#/g, iIdx);
                let div = document.createElement('div');
                div.innerHTML = template;
                imagesContainer.appendChild(div.firstElementChild);
                reindexVariants();
                updateVariantEvents();
            }
        });

        document.querySelectorAll('.remove-variant-image').forEach(btn => {
            btn.onclick = function() {
                if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                    btn.closest('.variant-image-item').remove();
                    reindexVariants();
                    updateVariantEvents();
                }
            }
        });

        // Preview image on file select
        document.querySelectorAll('.variant-image-input').forEach(input => {
            input.onchange = function() {
                const file = input.files && input.files[0];
                const preview = input.closest('.variant-image-item')?.querySelector('.image-preview');
                const nameEl = input.closest('.variant-image-item')?.querySelector('.filename');
                if (!preview) return;
                preview.innerHTML = '';
                if (file) {
                    if (nameEl) nameEl.textContent = file.name;
                    const reader = new FileReader();
                    reader.onload = function(evt) {
                        const img = document.createElement('img');
                        img.src = evt.target.result;
                        img.style.maxWidth = '100%';
                        img.style.maxHeight = '100%';
                        img.style.objectFit = 'contain';
                        preview.appendChild(img);
                    };
                    reader.readAsDataURL(file);
                } else {
                    if (nameEl) nameEl.textContent = '';
                    const small = document.createElement('small');
                    small.className = 'text-muted';
                    small.textContent = 'No preview';
                    preview.appendChild(small);
                }
            };
        });

        // Click preview or button to trigger file input
        document.querySelectorAll('.variant-image-item .image-preview').forEach(preview => {
            preview.onclick = function() {
                const input = preview.closest('.variant-image-item')?.querySelector('.variant-image-input');
                if (input) input.click();
            };
        });
        document.querySelectorAll('.variant-image-item .upload-trigger').forEach(btn => {
            btn.onclick = function() {
                const input = btn.closest('.variant-image-item')?.querySelector('.variant-image-input');
                if (input) input.click();
            };
        });

        document.querySelectorAll('.add-variant-size').forEach((btn) => {
            btn.onclick = function() {
                let variantCard = btn.closest('.variant-item');
                let vIdx = variantCard.getAttribute('data-index') || 0;
                let sizesContainer = variantCard.querySelector('.variant-sizes-container');
                let sIdx = sizesContainer.querySelectorAll('.variant-size-item').length;
                let template = document.getElementById('variant-size-template').innerHTML
                    .replace(/#VIDX#/g, vIdx)
                    .replace(/#SIDX#/g, sIdx);
                let div = document.createElement('div');
                div.innerHTML = template;
                sizesContainer.appendChild(div.firstElementChild);
                reindexVariants();
                updateVariantEvents();
                toggleVariantStockFields(variantCard);
            }
        });

        document.querySelectorAll('.remove-variant-size').forEach(btn => {
            btn.onclick = function() {
                if (confirm('Apakah Anda yakin ingin menghapus size ini?')) {
                    let variantCard = btn.closest('.variant-item');
                    btn.closest('.variant-size-item').remove();
                    reindexVariants();
                    toggleVariantStockFields(variantCard);
                    updateVariantEvents();
                }
            }
        });
    }

    // Fungsi untuk toggle stock/price/discount di variant
    function toggleVariantStockFields(variantCard) {
        let sizes = variantCard.querySelectorAll('.variant-size-item');
        let stockField = variantCard.querySelector('.variant-stock-fields');
        let priceField = variantCard.querySelector('.variant-price-fields');
        let discountField = variantCard.querySelector('.variant-discount-fields');
        if (sizes.length > 0) {
            if (stockField) stockField.style.display = 'none';
            if (priceField) priceField.style.display = 'none';
            if (discountField) discountField.style.display = 'none';
        } else {
            if (stockField) stockField.style.display = '';
            if (priceField) priceField.style.display = '';
            if (discountField) discountField.style.display = '';
        }
    }

    // Helper: open/close a variant details
    function setVariantOpen(variantCard, open = true) {
        let details = variantCard.querySelector('.variant-details');
        if (details) details.style.display = open ? '' : 'none';
    }

    updateVariantEvents();
});
</script>
@endsection