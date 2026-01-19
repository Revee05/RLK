@extends('admin.partials._layout')
@section('title','Form Blog')
@section('collapseBlog','show')
@section('addblog','active')

@section('css')
  <link href="{{ asset('css/blog-admin.css') }}" rel="stylesheet">
@endsection

@section('content')
<div class="container-fluid">
  <h1 class="h5 mb-4 text-gray-800">
    {{ isset($blog) ? 'Form Edit Blog' : 'Form Tambah Blog' }}
  </h1>

  <div class="card">
    <div class="card-body">

      {{-- === FORM === --}}
      @if(isset($blog))
        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
      @else
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
      @endif

        {{-- Judul --}}
        <div class="form-group">
          <label>Judul</label>
          <input type="text" name="title" class="form-control"
          value="{{ old('title',$blog->title ?? '') }}">
        </div>

        {{-- COVER --}}
        <div class="form-group">
          <label>Cover Blog</label>

          <input type="file" name="cover" id="coverInput" accept="image/*" hidden>

          <button type="button"
                  id="coverBtn"
                  class="btn btn-outline-primary">
            Pilih Cover
          </button>

          <div class="mt-3">
            <img id="coverPreview"
                class="d-none"
                style="max-width:300px;border-radius:8px">
          </div>
        </div>

        {{-- BLOCK EDITOR --}}
        <div class="form-group">
          <label>Konten Blog</label>

          <div id="editor-blocks"></div>
          
          <div class="editor-actions">
            <button type="button" id="add-text" class="btn btn-sm btn-primary">➕ Paragraf</button>
            <button type="button" id="add-image" class="btn btn-sm btn-secondary">🖼️ Gambar</button>
          </div>

          <input type="hidden" name="content_blocks" id="content_blocks">
          <div id="linkModal" class="link-modal d-none">
            <input type="text" id="linkUrl" placeholder="https://example.com">
            <button type="button" id="applyLink">Terapkan</button>
            <button type="button" id="removeLink">Hapus</button>
          </div>
        </div>

        {{-- 3 Kolom: Kategori, Status, Tags --}}
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="form-group">
              <label>Kategori</label>
              <select name="kategori_id" class="form-control">
                @foreach($cats as $id=>$name)
                  <option value="{{ $id }}" {{ old('kategori_id',$blog->kategori_id ?? '')==$id?'selected':'' }}>
                    {{ $name }}
                  </option>
                @endforeach
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label>Status</label>
              <select name="status" class="form-control">
                <option value="draft" {{ old('status', $blog->status ?? '') == 'DRAFT' ? 'selected' : '' }}>DRAFT</option>
                <option value="published" {{ old('status', $blog->status ?? '') == 'PUBLISHED' ? 'selected' : '' }}>PUBLISHED</option>
              </select>
            </div>
          </div>

          <div class="col-md-4">
            <div class="form-group">
              <label>Tags</label>
              <select id="selTag" name="tagger[]" multiple="multiple" class="form-control"></select>
            </div>
          </div>
        </div>

        <h6>Preview Blog</h6>
        <div id="blog-preview" class="blog-preview"></div>
        
        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-4">
          <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left"></i> Kembali
          </a>
          <button type="submit" class="btn btn-danger">
            <i class="fas fa-save"></i> {{ isset($blog) ? 'Update' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
/* =====================================================
   COVER
===================================================== */
const coverInput = document.getElementById('coverInput');
const coverImg   = document.getElementById('coverPreview');
const coverBtn   = document.getElementById('coverBtn');

coverBtn.addEventListener('click', () => {
  coverInput.click();
});

coverInput.addEventListener('change', e => {
  const file = e.target.files[0];
  if (!file) return;

  coverImg.src = URL.createObjectURL(file);
  coverImg.classList.remove('d-none');
  coverBtn.textContent = 'Ganti Cover';
});

/* =====================================================
   EDITOR CORE
===================================================== */
const editor = document.getElementById('editor-blocks');
let dragged = null;
let activeText = null;
let savedRange = null;

/* ================= DRAG ================= */
function enableDrag(block){
  block.draggable = true;
  block.addEventListener('dragstart', () => dragged = block);
  block.addEventListener('dragover', e => {
    e.preventDefault();
    const after = [...editor.children].find(el =>
      e.clientY <= el.getBoundingClientRect().top + el.offsetHeight / 2
    );
    after ? editor.insertBefore(dragged, after) : editor.appendChild(dragged);
  });
}

/* ================= SELECTION ================= */
function saveSelection(){
  const sel = window.getSelection();
  if (sel.rangeCount > 0) {
    savedRange = sel.getRangeAt(0);
  }
}

function restoreSelection(){
  if (savedRange) {
    const sel = window.getSelection();
    sel.removeAllRanges();
    sel.addRange(savedRange);
  }
}

/* =====================================================
   TEXT BLOCK
===================================================== */
function addTextBlock(){
  const block = document.createElement('div');
  block.className = 'editor-block';

  block.innerHTML = `
    <div class="toolbar">
      <button type="button" data-cmd="bold" class="tool-btn"><b>B</b></button>
      <button type="button" data-cmd="underline" class="tool-btn"><u>U</u></button>

      <select class="font-size">
        <option value="">Size</option>
        <option value="12">12px</option>
        <option value="14">14px</option>
        <option value="16">16px</option>
        <option value="18">18px</option>
        <option value="24">24px</option>
      </select>

      <input type="color" class="font-color">

      <button type="button" data-cmd="justifyLeft">⯇</button>
      <button type="button" data-cmd="justifyCenter">≡</button>
      <button type="button" data-cmd="justifyRight">⯈</button>
      <button type="button" data-cmd="justifyFull">☰</button>

      <button type="button" data-cmd="undo">↺</button>
      <button type="button" data-cmd="redo">↻</button>

      <select class="heading">
        <option value="">Normal</option>
        <option value="H1">H1</option>
        <option value="H2">H2</option>
        <option value="H3">H3</option>
      </select>

      <button type="button" data-cmd="insertUnorderedList">• List</button>
      <button type="button" data-cmd="insertOrderedList">1. List</button>

      <button type="button" class="link-btn">🔗</button>

      <button type="button" class="remove">Hapus</button>
    </div>

    <div class="text-content"
         contenteditable="true"
         data-placeholder="Tulis paragraf di sini..."></div>
  `;

  enableDrag(block);
  editor.appendChild(block);

  const text = block.querySelector('.text-content');
  text.focus();

  text.addEventListener('focus', () => activeText = text);
  text.addEventListener('click', () => activeText = text);
  text.addEventListener('keyup', saveSelection);
  text.addEventListener('mouseup', saveSelection);
  text.addEventListener('input', updatePreview);
}

/* =====================================================
   IMAGE BLOCK
===================================================== */
function addImageBlock(){
  const block = document.createElement('div');
  block.className = 'editor-block';

  block.innerHTML = `
    <div class="img-head">
      Gambar
      <button type="button" class="remove">Hapus</button>
    </div>

    <input type="file" accept="image/*" hidden>

    <button type="button"
            class="img-btn btn btn-sm btn-outline-secondary">
      Pilih Gambar
    </button>

    <div class="mt-2">
      <img class="preview d-none" style="max-width:100%">
    </div>

    <input type="hidden" class="image-id">
  `;

  const input  = block.querySelector('input[type=file]');
  const btn    = block.querySelector('.img-btn');
  const img    = block.querySelector('.preview');
  const hidden = block.querySelector('.image-id');

  btn.addEventListener('click', () => input.click());

  input.addEventListener('change', e => {
    const file = e.target.files[0];
    if (!file) return;

    img.src = URL.createObjectURL(file);
    img.classList.remove('d-none');
    btn.textContent = 'Ganti Gambar';

    const fd = new FormData();
    fd.append('image', file);
    fd.append('_token', '{{ csrf_token() }}');

    fetch('/admin/blogs/content/upload', {
      method: 'POST',
      body: fd
    })
    .then(r => r.json())
    .then(res => hidden.value = res.id);

    updatePreview();
  });

  enableDrag(block);
  editor.appendChild(block);
}

document.getElementById('applyLink').onclick = () => {
  restoreSelection();
  const url = linkUrl.value.trim();
  if (!url) return;

  document.execCommand('createLink', false, url);
  linkModal.classList.add('d-none');
  linkUrl.value = '';
  updatePreview();
};

document.getElementById('removeLink').onclick = () => {
  restoreSelection();
  document.execCommand('unlink');
  linkModal.classList.add('d-none');
  updatePreview();
};

/* =====================================================
   TOOLBAR ACTION
===================================================== */
document.addEventListener('click', e => {

  const btn = e.target.closest('[data-cmd]');
  if (btn && activeText) {
    e.preventDefault();
    restoreSelection();
    activeText.focus();
    document.execCommand(btn.dataset.cmd, false, null);
    updatePreview();
    return;
  }

  if (e.target.classList.contains('remove')) {
    const block = e.target.closest('.editor-block');
    const imgId = block.querySelector('.image-id')?.value;

    if (imgId) {
      fetch('/admin/blogs/content/image/' + imgId, {
        method: 'DELETE',
        headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
      });
    }

    block.remove();
    updatePreview();
  }
});

/* ================= HEADING ================= */
document.addEventListener('change', e => {
  if (!e.target.classList.contains('heading')) return;
  if (!activeText) return;

  restoreSelection();
  activeText.focus();

  const val = e.target.value;
  document.execCommand(
    'formatBlock',
    false,
    val === '' ? 'p' : val
  );

  updatePreview();
});

/* ================= FONT SIZE ================= */
document.addEventListener('change', e => {
  if (!e.target.classList.contains('font-size')) return;
  if (!activeText) return;

  restoreSelection();
  activeText.focus();

  const sizeMap = { 12:2, 14:3, 16:4, 18:5, 24:6 };
  const px = e.target.value;
  if (!px) return;

  document.execCommand('fontSize', false, sizeMap[px]);

  activeText.querySelectorAll('font[size]').forEach(el => {
    el.removeAttribute('size');
    el.style.fontSize = px + 'px';
  });

  e.target.value = '';
  updatePreview();
});


function detectHeading() {
  if (!activeText) return;

  const sel = window.getSelection();
  if (!sel.rangeCount) return;

  let node = sel.anchorNode;
  if (node.nodeType === 3) node = node.parentNode;

  while (node && !['H1','H2','H3','P','DIV'].includes(node.tagName)) {
    node = node.parentNode;
  }

  const select = document.querySelector('.heading');
  if (!select) return;

  if (node && ['H1','H2','H3'].includes(node.tagName)) {
    select.value = node.tagName;
  } else {
    select.value = '';
  }
}

document.addEventListener('selectionchange', detectHeading);

/* ================= FONT COLOR ================= */
document.addEventListener('input', e => {
  if (!e.target.classList.contains('font-color')) return;
  if (!activeText) return;

  activeText.focus();
  document.execCommand('foreColor', false, e.target.value);
  updatePreview();
});

function updateToolbarState() {
  if (!activeText) return;

  document.querySelectorAll('.tool-btn').forEach(btn => {
    const cmd = btn.dataset.cmd;
    if (!cmd) return;

    const state = document.queryCommandState(cmd);
    btn.classList.toggle('active', state);
  });
}

document.addEventListener('selectionchange', updateToolbarState);

function detectFontSize() {
  if (!activeText) return;

  const sel = window.getSelection();
  if (!sel.rangeCount) return;

  let node = sel.anchorNode;
  if (node.nodeType === 3) node = node.parentNode;

  const size = window.getComputedStyle(node).fontSize;
  const px = parseInt(size);

  const select = document.querySelector('.font-size');
  if (select) select.value = px;
}

document.addEventListener('selectionchange', detectFontSize);

function detectFontColor() {
  if (!activeText) return;

  const sel = window.getSelection();
  if (!sel.rangeCount) return;

  let node = sel.anchorNode;
  if (node.nodeType === 3) node = node.parentNode;

  const color = window.getComputedStyle(node).color;
  const input = document.querySelector('.font-color');

  if (input) input.value = rgbToHex(color);
}

function rgbToHex(rgb) {
  const m = rgb.match(/\d+/g);
  if (!m) return '#000000';
  return (
    '#' +
    m.slice(0, 3)
      .map(x => (+x).toString(16).padStart(2, '0'))
      .join('')
  );
}

document.addEventListener('selectionchange', detectFontColor);

let linkTargetText = null;

document.addEventListener('click', e => {
  if (!e.target.classList.contains('link-btn')) return;
  if (!activeText) return;

  saveSelection();
  linkTargetText = activeText;

  linkModal.classList.remove('d-none');
  linkUrl.focus();
});

document.getElementById('applyLink').onclick = () => {
  restoreSelection();
  const url = linkUrl.value.trim();
  if (!url) return;

  document.execCommand('createLink', false, url);
  linkModal.classList.add('d-none');
  linkUrl.value = '';
  updatePreview();
};

document.getElementById('removeLink').onclick = () => {
  restoreSelection();
  document.execCommand('unlink');
  linkModal.classList.add('d-none');
  updatePreview();
};

function cleanHTML(html) {
  const div = document.createElement('div');
  div.innerHTML = html;

  div.querySelectorAll('font').forEach(f => {
    const span = document.createElement('span');
    span.innerHTML = f.innerHTML;

    if (f.color) span.style.color = f.color;
    if (f.size) span.style.fontSize = f.size + 'px';

    f.replaceWith(span);
  });

  return div.innerHTML;
}

/* =====================================================
   PREVIEW BLOG
===================================================== */
function updatePreview(){
  const preview = document.getElementById('blog-preview');
  let html = `<h2>${document.querySelector('[name=title]')?.value || ''}</h2>`;

  editor.querySelectorAll('.editor-block').forEach(block => {
    const text = block.querySelector('.text-content');
    const img  = block.querySelector('.preview');

    if (text) html += `<div>${text.innerHTML}</div>`;
    if (img && !img.classList.contains('d-none')) {
      html += `<img src="${img.src}" style="max-width:100%">`;
    }
  });

  preview.innerHTML = html;
}

/* =====================================================
   BUTTON ADD
===================================================== */
document.getElementById('add-text').onclick  = addTextBlock;
document.getElementById('add-image').onclick = addImageBlock;

</script>
@endsection
