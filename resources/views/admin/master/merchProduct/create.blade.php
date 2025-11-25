{{-- filepath: resources/views/admin/master/merchProduct/create.blade.php --}}
@extends('admin.partials._layout')
@section('title','Create Merch Product')
@section('collapseMerch','show')
@section('addmerchproduct','active')
@section('content')
<div class="container">
    <h1>Add Merchandise Product</h1>
    @include('admin.master.merchProduct.form', ['mode' => 'create', 'categories' => $categories])
</div>
@endsection

@section('js')
<link href="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/summernote/0.8.20/summernote-lite.min.js"></script>
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
});

// ========================= SCRIPT DINAMIS VARIANT =========================
document.addEventListener('DOMContentLoaded', function() {
    let variantIdx = document.querySelectorAll('.variant-item').length || 0;

    document.getElementById('add-variant-btn').addEventListener('click', function() {
        addVariant();
    });

    function addVariant() {
        let template = document.getElementById('variant-template').innerHTML.replace(/#IDX#/g, variantIdx);
        let div = document.createElement('div');
        div.innerHTML = template;
        let card = div.firstElementChild;
        // Accordion: default collapsed
        let details = card.querySelector('.variant-details');
        if (details) details.style.display = 'none';
        document.getElementById('variants-container').appendChild(card);
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
                }
            }
        });

        // Tambah/hapus gambar dan size (jika ada)
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
                if (confirm('Apakah Anda yakin ingin menghapus gambar ini?')) {
                    btn.closest('.variant-image-item').remove();
                }
            }
        });

        // Preview image on file select
        document.querySelectorAll('.variant-image-input').forEach(input => {
            input.onchange = function() {
                const file = input.files && input.files[0];
                const preview = input.closest('.variant-image-item')?.querySelector('.image-preview');
                if (!preview) return;
                preview.innerHTML = '';
                if (file) {
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
                    const small = document.createElement('small');
                    small.className = 'text-muted';
                    small.textContent = 'No preview';
                    preview.appendChild(small);
                }
            };
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
                toggleVariantStockFields(btn.closest('.variant-item'));
            }
        });

        document.querySelectorAll('.remove-variant-size').forEach(btn => {
            btn.onclick = function() {
                if (confirm('Apakah Anda yakin ingin menghapus size ini?')) {
                    let variantCard = btn.closest('.variant-item');
                    btn.closest('.variant-size-item').remove();
                    toggleVariantStockFields(variantCard);
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