<script>
/* =====================
   SUBMIT CONTENT
===================== */
document.getElementById('blogForm')
    .addEventListener('submit', function () {
        document.getElementById('body').value =
            document.getElementById('editor').innerHTML;
});

/* =====================
   TOOLBAR
===================== */
document.querySelectorAll('.editor-toolbar button')
    .forEach(btn => {
        btn.addEventListener('click', () => {
            document.execCommand(btn.dataset.cmd);
        });
    });

/* =====================
   TAG SYSTEM
===================== */
let tags = @json($existingTags ?? []);

const tagInput = document.getElementById('tagInput');
const tagBox   = document.getElementById('tagBox');
const tagsField = document.getElementById('tags');

tags.forEach(renderTag);
updateTags();

tagInput.addEventListener('keydown', e => {
    if (e.key === 'Enter' && tagInput.value.trim()) {
        e.preventDefault();
        addTag(tagInput.value.trim());
        tagInput.value = '';
    }
});

function addTag(text) {
    if (tags.includes(text)) return;
    tags.push(text);
    renderTag(text);
    updateTags();
}

function renderTag(text) {
    const span = document.createElement('span');
    span.className = 'tag';
    span.innerText = text + ' ×';
    span.onclick = () => {
        tags = tags.filter(t => t !== text);
        span.remove();
        updateTags();
    };
    tagBox.insertBefore(span, tagInput);
}

function updateTags() {
    tagsField.value = tags.join(',');
}

/* =====================
   IMAGE PREVIEW
===================== */
document.getElementById('images')
    .addEventListener('change', e => {
        const preview = document.getElementById('preview');
        preview.innerHTML = '';

        [...e.target.files].forEach(file => {
            if (file.size > 2_000_000) {
                alert('Maksimal ukuran gambar 2MB');
                return;
            }
            const r = new FileReader();
            r.onload = ev => {
                const div = document.createElement('div');
                div.className = 'preview-item';
                div.innerHTML = `<img src="${ev.target.result}">`;
                preview.appendChild(div);
            };
            r.readAsDataURL(file);
        });
});
</script>
