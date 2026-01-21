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

  <div class="blog-form-card">
    <div class="blog-form-body">

      {{-- === FORM === --}}
      @if(isset($blog))
        <form action="{{ route('admin.blogs.update', $blog->id) }}" method="POST" enctype="multipart/form-data">
          @csrf
          @method('PUT')
      @else
        <form action="{{ route('admin.blogs.store') }}" method="POST" enctype="multipart/form-data">
          @csrf
      @endif

        <input type="hidden" id="post_id" value="{{ $blog->id ?? '' }}">

        {{-- Judul --}}
        <div class="form-group">
          <label>Judul Blog</label>
          <input type="text" name="title" class="form-control" placeholder="Masukkan judul blog"
          value="{{ old('title',$blog->title ?? '') }}">
        </div>

        {{-- COVER --}}
        <div class="form-group">
          <label>Cover Blog</label>

          <input type="file" name="cover" id="coverInput" accept="image/*" hidden>

          <div>
            <button type="button"
                    id="coverBtn"
                    class="btn btn-outline-primary">
              Pilih Cover
            </button>
          </div>
          
          <div class="mt-3">
            <img id="coverPreview"
                class="d-none"
                style="max-width:400px;border-radius:12px">
          </div>
        </div>

        {{-- BLOCK EDITOR --}}
        <div class="form-group">
          <label class="section-header">Konten Blog</label>

          <div id="editor-blocks"></div>
          
          <div class="editor-actions">
            <button type="button" id="add-text" class="btn btn-primary">
              Tambah Paragraf
            </button>
            <button type="button" id="add-image" class="btn btn-outline-primary">
              Tambah Gambar
            </button>
          </div>

          <input type="hidden" name="body" id="body">

          {{-- Link Modal --}}
          <div id="linkModal" class="link-modal d-none">
            <h6 style="margin-top: 16px; font-weight: 700; color: #2c3e50;">🔗 Masukkan URL Link</h6>
            <input type="text" id="linkUrl" placeholder="https://example.com">
            <div style="display: flex; gap: 10px;">
              <button type="button" id="applyLink" class="btn btn-primary">Terapkan</button>
              <button type="button" id="removeLink" class="btn btn-outline-primary">Hapus Link</button>
            </div>
          </div>
        </div>

        {{-- 3 Kolom: Kategori, Status, Tags --}}
        <div class="row mb-4">
          <div class="col-md-4">
            <div class="form-group">
              <label> Kategori</label>
              <select name="kategori_id" class="form-control">
                @foreach($cats as $id=>$name)
                  <option value="{{ $id }}" {{ old('kategori_id', $blog->kategori_id ?? '')==$id?'selected':'' }}>
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
                <option value="DRAFT" {{ old('status', $blog->status ?? '') == 'DRAFT' ? 'selected' : '' }}>📝 DRAFT</option>
                <option value="PUBLISHED" {{ old('status', $blog->status ?? '') == 'PUBLISHED' ? 'selected' : '' }}>✅ PUBLISHED</option>
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

        {{-- Preview --}}
        <h6 class="section-header">Preview Blog</h6>
        <div id="blog-preview" class="blog-preview"></div>
        
        {{-- Tombol --}}
        <div class="d-flex justify-content-between mt-4">
          <a href="{{ route('admin.blogs.index') }}" class="btn btn-outline-secondary">
            Kembali
          </a>
          <button type="submit" class="btn btn-primary">
            {{ isset($blog) ? 'Update' : 'Simpan' }}
          </button>
        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')

{{-- DATA DARI SERVER --}}
<script>
  window.BLOG_RAW_BODY = @json($blog->body ?? '');
  window.IMAGE_MAP = @json(
    isset($images)
      ? collect($images)->mapWithKeys(fn($i)=>[$i->id => asset('uploads/blogs/'.$i->filename)])
      : []
  );
</script>

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

    updatePreview();
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

      after
        ? editor.insertBefore(dragged, after)
        : editor.appendChild(dragged);
    });

    block.addEventListener('dragend', () => {
      dragged = null;
      updatePreview();
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
  function addTextBlock() {
    const block = document.createElement('div');
    block.className = 'editor-block editor-text';

    block.innerHTML = `
      <div class="editor-toolbar">

        <div class="toolbar-left">
          <span class="toolbar-title">Paragraf</span>
        </div>

        <div class="toolbar-group">
          <button type="button" data-cmd="bold" class="tool-btn btn-outline-primary"><b>B</b></button>
          <button type="button" data-cmd="underline" class="tool-btn btn-outline-primary"><u>U</u></button>
          <button type="button" class="tool-btn link-btn btn-outline-primary">🔗</button>
        
          <select class="tool-select font-size btn-outline-primary">
            <option value="">16px</option>
            <option value="12">12px</option>
            <option value="14">14px</option>
            <option value="16">16px</option>
            <option value="18">18px</option>
            <option value="24">24px</option>
          </select>

          <input type="color" class="tool-color font-color btn-outline-primary">
        
          <button type="button" data-cmd="insertUnorderedList" class="tool-btn btn-outline-primary">•</button>
          <button type="button" data-cmd="insertOrderedList" class="tool-btn btn-outline-primary">1.</button>
        </div>

        <div class="toolbar-group">
          <button type="button" data-cmd="justifyLeft" class="tool-btn btn-outline-primary">⯇</button>
          <button type="button" data-cmd="justifyCenter" class="tool-btn btn-outline-primary">≡</button>
          <button type="button" data-cmd="justifyRight" class="tool-btn btn-outline-primary">⯈</button>
          <button type="button" data-cmd="justifyFull" class="tool-btn btn-outline-primary">☰</button>
        
          <button type="button" data-cmd="undo" class="tool-btn btn-outline-primary">↺</button>
          <button type="button" data-cmd="redo" class="tool-btn btn-outline-primary">↻</button>
        
          <select class="tool-select heading btn-outline-primary">
            <option value="">Normal</option>
            <option value="H1">H1</option>
            <option value="H2">H2</option>
            <option value="H3">H3</option>
          </select>
        
          <button type="button" class="tool-btn remove btn-outline-primary">Hapus</button>
        </div>

      </div>

      <div class="text-content"
        contenteditable="true"
        placeholder="Tulis paragraf di sini...">
      </div>
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
  function addImageBlock() {
    const block = document.createElement('div');
    block.className = 'editor-block editor-image';

    block.innerHTML = `
      <div class="image-toolbar">
        <span class="image-title">Gambar</span>

        <div class="image-actions">
          <button type="button" class="img-btn btn-outline-primary">Pilih Gambar</button>
          <button type="button" class="tool-btn remove btn-outline-primary">Hapus</button>
        </div>
      </div>

      <input type="file" accept="image/*" hidden>

      <div class="image-preview">
        <img class="preview d-none" style="max-width:400px;border-radius:12px">
      </div>

      <input type="hidden" class="image-id">
    `;

    const input  = block.querySelector('input[type=file]');
    const btn    = block.querySelector('.img-btn');
    const img    = block.querySelector('.preview');
    const hidden = block.querySelector('.image-id');

    btn.onclick = () => input.click();

    input.onchange = async e => {
      const file = e.target.files[0];
      if (!file) return;

      // preview sementara
      img.src = URL.createObjectURL(file);
      img.classList.remove('d-none');
      btn.textContent = 'Ganti Gambar';

      const postId = document.getElementById('post_id')?.value;

      const fd = new FormData();
      fd.append('image', file);

      if (postId) {
        fd.append('post_id', postId);
      }

      const res = await fetch("{{ route('admin.blogs.content.upload') }}", {
        method: 'POST',
        headers: {
          'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        credentials: 'same-origin',
        body: fd
      });
      
      const text = await res.text();

      let json;
      try {
        json = JSON.parse(text);
      } catch (e) {
        console.error('SERVER RETURN HTML:', text);
        alert('Upload gagal (bukan JSON)');
        return;
      }
    
      if (!json.success) {
        alert('Upload gagal');
        return;
      }

      // SIMPAN ID DB
      hidden.value = json.id;
      img.src = json.url;
      btn.textContent = 'Ganti Gambar';

      updatePreview();
    };

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
  function updatePreview() {
    const preview = document.getElementById('blog-preview');
    preview.innerHTML = '';

    /* COVER */
    if (coverImg && !coverImg.classList.contains('d-none')) {
      preview.innerHTML += `
        <div class="preview-cover">
          <img src="${coverImg.src}" style="max-width:100%;border-radius:8px;margin-bottom:20px">
        </div>
      `;
    }

    /* TITLE */
    const title = document.querySelector('[name=title]')?.value;
    if (title) {
      preview.innerHTML += `<h2>${title}</h2>`;
    }

    /* CONTENT */
    editor.querySelectorAll('.editor-block').forEach(block => {

      /* TEXT */
      const text = block.querySelector('.text-content');
      if (text && text.innerHTML.trim() !== '') {
        preview.innerHTML += `<div>${text.innerHTML}</div>`;
      }

      /* IMAGE */
      const img = block.querySelector('img.preview');
      const hidden = block.querySelector('.image-id');
      if (img && !img.classList.contains('d-none')) {
        preview.innerHTML += `
          <div style="margin:20px 0">
            <img src="${img.src}" style="max-width:100%;border-radius:12px">
          </div>
        `;
      }
    });
  }

  document.querySelector('form').addEventListener('submit', () => {
    const blocks = [];

    editor.querySelectorAll('.editor-block').forEach(block => {
      const text = block.querySelector('.text-content');
      const hidden = block.querySelector('.image-id');

      if (text && text.innerHTML.trim() !== '') {
        blocks.push({
          type: 'text',
          html: text.innerHTML
        });
      }

      if (hidden && hidden.value) {
        blocks.push({
          type: 'image',
          image_id: hidden.value
        });
      }
    });

    document.getElementById('body').value = JSON.stringify(blocks);
  });

  /* =====================================================
    BUTTON ADD
  ===================================================== */
  document.getElementById('add-text').onclick  = addTextBlock;
  document.getElementById('add-image').onclick = addImageBlock;

  /* =====================================================
   LOAD EDIT CONTENT (FINAL – STABIL)
   ===================================================== */
  document.addEventListener('DOMContentLoaded', () => {

    /* === LOAD BODY === */
    if (window.BLOG_RAW_BODY) {

      let blocks = [];
      const raw = window.BLOG_RAW_BODY;

      const isJson = (str) => {
        if (typeof str !== 'string') return false;
        str = str.trim();
        if (!str.startsWith('[')) return false;
        try { JSON.parse(str); return true; } catch { return false; }
      };

      if (isJson(raw)) {
        blocks = JSON.parse(raw);
      } else {
        // HTML lama
        blocks = [{ type: 'text', html: raw }];
      }

      blocks.forEach(b => {

        if (b.type === 'text') {
          addTextBlock();
          editor.lastElementChild
            .querySelector('.text-content').innerHTML = b.html;
        }

        if (b.type === 'image' && window.IMAGE_MAP?.[b.image_id]) {
          addImageBlock();

          const block = editor.lastElementChild;
          const img   = block.querySelector('.preview');

          block.querySelector('.image-id').value = b.image_id;

          img.src = IMAGE_MAP[b.image_id];
          img.classList.remove('d-none');

          block.querySelector('.img-btn').textContent = 'Ganti Gambar';
        }

      });
    }

    /* === LOAD COVER === */
    @if(!empty($blog->image))
      coverImg.src = "{{ asset('uploads/blogs/'.$blog->image) }}";
      coverImg.classList.remove('d-none');
      coverBtn.textContent = 'Ganti Cover';
    @endif

    updatePreview();
  });

  /* ================= HELPERS ================= 
  function addTextBlockFromHTML(html){ addTextBlock(); editor.lastElementChild.querySelector('.text-content').innerHTML = html; }
  function addImageBlockFromSrc(src){
    addImageBlock();
    const block = editor.lastElementChild;
    const img = block.querySelector('img.preview');
    const hidden = block.querySelector('.image-id');
    img.src = src; img.classList.remove('d-none'); hidden.value = src.split('/').pop();
    const btn = block.querySelector('.img-btn'); btn.textContent = 'Ganti Gambar';
  }

  function addImageBlockFromData(image){
    addImageBlock();
    const block  = editor.lastElementChild;
    const img    = block.querySelector('img.preview');
    const hidden = block.querySelector('.image-id');

    img.src = image.url;
    img.classList.remove('d-none');
    hidden.value = image.id;

    block.querySelector('.img-btn').textContent = 'Ganti Gambar';
  } */

</script>
@endsection
