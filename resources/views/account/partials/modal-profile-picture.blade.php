<!-- Profile picture modals (Figma-derived) -->

<!-- 1) View / Change Profile Picture -->
<div class="modal fade" id="modalProfilePicture" tabindex="-1" aria-labelledby="modalProfilePictureLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header border-0">
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="pp-current"
                    src="{{ asset(Auth::user()->foto) ?? 'https://www.figma.com/api/mcp/asset/1bcfd75e-90c9-43bf-8586-79d92d395def' }}"
                    alt="avatar" class="mb-1">

                <div class="text-center">
                    <form id="pp-upload-form" action="{{ route('account.avatar.upload') }}" method="POST"
                        enctype="multipart/form-data" class="d-none">
                        @csrf
                        <input type="hidden" name="id" value="{{ Auth::user()->id }}">
                        <input type="file" name="avatar" id="pp-file-input" accept="image/*">
                    </form>
                </div>
            </div>
            <div class="modal-footer border-0 d-flex justify-content-center gap-2">
                <!-- 1) Camera icon button with dropup menu -->
                <div class="dropup">
                    <button class="btn btn-cyan dropdown-toggle" type="button" id="pp-camera-btn"
                        data-bs-toggle="dropdown" aria-expanded="false" title="Pilih Foto">
                        <i class="bi bi-camera" aria-hidden="true"></i>
                    </button>
                    <ul class="pp dropdown-menu text-center align-center" aria-labelledby="pp-camera-btn">
                        <li class="py-1"><button class="dropdown-item" id="pp-menu-gallery" type="button">Dari
                                Galeri</button></li>
                        <li class="py-1"><button class="dropdown-item" id="pp-menu-camera" type="button">Buka
                                Kamera</button></li>
                    </ul>
                </div>

                <!-- 2) Save button -->
                <button type="button" class="btn btn-cyan" id="pp-save-main">Simpan</button>

                <!-- 3) Back / Close button -->
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Kembali</button>
            </div>
        </div>
    </div>
</div>

<!-- Camera capture modal -->
<div class="modal fade" id="modalCameraCapture" tabindex="-1" aria-labelledby="modalCameraCaptureLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalCameraCaptureLabel">Ambil Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <video id="pp-camera-video" autoplay playsinline
                    style="width:100%; border-radius:8px; background:#000;"></video>
                <canvas id="pp-camera-canvas" style="display:none;"></canvas>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" id="pp-camera-cancel"
                    data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-cyan" id="pp-camera-capture">Ambil</button>
            </div>
        </div>
    </div>
</div>

<!-- 2) Upload / Preview / Crop Modal -->
<div class="modal fade" id="modalProfilePictureUpload" tabindex="-1" aria-labelledby="modalProfilePictureUploadLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="modalProfilePictureUploadLabel">Preview Foto</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-7 d-flex align-items-center justify-content-center">
                        <!-- Placeholder for cropper area; JS can mount cropper on this img -->
                        <div style="width:100%; max-width:480px;">
                            <div
                                class="ratio ratio-1x1 bg-light d-flex align-items-center justify-content-center pp-preview-wrap">
                                <img id="pp-preview" src="" alt="preview" class="pp-preview-img" />
                                <div id="pp-preview-placeholder" class="text-muted">Belum ada gambar yang dipilih
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-5">
                        <div class="mb-3">
                            <label class="form-label">Nama File</label>
                            <div id="pp-filename" class="text-truncate">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Ukuran</label>
                            <div id="pp-filesize">-</div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Preview Ukuran</label>
                            <div class="d-flex gap-2">
                                <div class="border p-2 text-center pp-thumb-wrap pp-thumb-64"><img id="pp-thumb-64"
                                        src="" alt="thumb"></div>
                                <div class="border p-2 text-center pp-thumb-wrap pp-thumb-120"><img id="pp-thumb-120"
                                        src="" alt="thumb"></div>
                            </div>
                        </div>
                        <div class="mt-4">
                            <button id="pp-cancel-upload" class="btn btn-secondary w-100 mb-2">Batal</button>
                            <button id="pp-save-upload" class="btn btn-cyan w-100">Simpan Perubahan</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 3) Confirm Remove Photo -->
<div class="modal fade" id="modalConfirmRemovePhoto" tabindex="-1" aria-labelledby="modalConfirmRemovePhotoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center">
                <p class="mb-3" style="font-weight:700">Hapus Foto Profil?</p>
                <p class="text-muted small">Foto ini akan dihapus dan diganti dengan avatar default.</p>
                <div class="d-flex justify-content-center gap-2 mt-3">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                    <form action="#" method="POST" style="display:inline;">
                        @csrf
                        <button type="submit" class="btn btn-danger">Hapus</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- 4) Upload Success -->
<div class="modal fade" id="modalUploadSuccess" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <div class="mb-3">✅</div>
                <h5>Sukses!</h5>
                <p class="small text-muted">Foto profil berhasil diperbarui.</p>
                <button type="button" class="btn btn-primary mt-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- 5) Upload Error -->
<div class="modal fade" id="modalUploadError" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content text-center">
            <div class="modal-body">
                <div class="mb-3 text-danger">⚠️</div>
                <h5>Terjadi Kesalahan</h5>
                <p class="small text-muted" id="pp-error-message">Gagal mengunggah gambar. Coba lagi.</p>
                <button type="button" class="btn btn-secondary mt-2" data-bs-dismiss="modal">Tutup</button>
            </div>
        </div>
    </div>
</div>

<!-- Minimal JS hooks: the project's JS should wire file input and cropper using these IDs -->
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const fileInput = document.getElementById('pp-file-input');
        const cameraBtn = document.getElementById('pp-camera-btn');
        const saveMainBtn = document.getElementById('pp-save-main');
        const previewImg = document.getElementById('pp-preview');
        const previewPlaceholder = document.getElementById('pp-preview-placeholder');
        const filenameEl = document.getElementById('pp-filename');
        const filesizeEl = document.getElementById('pp-filesize');
        const thumb64 = document.getElementById('pp-thumb-64');
        const thumb120 = document.getElementById('pp-thumb-120');

        // When a file is selected, show the upload modal and preview
        if (fileInput) {
            fileInput.addEventListener('change', function(e) {
                const file = e.target.files && e.target.files[0];
                if (!file) return;
                const url = URL.createObjectURL(file);
                if (previewImg) {
                    previewImg.src = url;
                    previewImg.style.display = 'block';
                }
                if (previewPlaceholder) previewPlaceholder.style.display = 'none';
                if (filenameEl) filenameEl.textContent = file.name;
                if (filesizeEl) filesizeEl.textContent = Math.round(file.size / 1024) + ' KB';
                if (thumb64) {
                    thumb64.src = url;
                    thumb64.style.display = 'block';
                }
                if (thumb120) {
                    thumb120.src = url;
                    thumb120.style.display = 'block';
                }

                // Directly update the current profile image without showing preview modal
                const ppCurrent = document.getElementById('pp-current');
                if (ppCurrent) {
                    ppCurrent.src = url;
                }
            });
        }

        // camera button triggers file chooser
        if (cameraBtn) {
            // cameraBtn is now a dropdown toggle; gallery and camera options are menu items
            const galleryItem = document.getElementById('pp-menu-gallery');
            const cameraItem = document.getElementById('pp-menu-camera');
            if (galleryItem) galleryItem.addEventListener('click', function() {
                if (fileInput) fileInput.click();
            });
            if (cameraItem) cameraItem.addEventListener('click', function() {
                openCameraModal();
            });
        }

        // save button: submit upload form to server (will update avatar)
        if (saveMainBtn) {
            saveMainBtn.addEventListener('click', function() {
                const form = document.getElementById('pp-upload-form');
                if (form) form.submit();
            });
        }

        // Camera modal logic
        let cameraStream = null;
        const cameraModalEl = document.getElementById('modalCameraCapture');
        const cameraVideo = document.getElementById('pp-camera-video');
        const cameraCaptureBtn = document.getElementById('pp-camera-capture');
        const cameraCancelBtn = document.getElementById('pp-camera-cancel');

        function openCameraModal() {
            if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
                // fallback: trigger file input
                if (fileInput) fileInput.click();
                return;
            }
            const modal = new bootstrap.Modal(cameraModalEl);
            modal.show();
            navigator.mediaDevices.getUserMedia({
                    video: true
                })
                .then(stream => {
                    cameraStream = stream;
                    if (cameraVideo) cameraVideo.srcObject = stream;
                })
                .catch(err => {
                    console.error('Camera error', err);
                    // fallback to gallery pick
                    if (fileInput) fileInput.click();
                });
        }

        function closeCameraStream() {
            if (cameraStream) {
                cameraStream.getTracks().forEach(t => t.stop());
                cameraStream = null;
            }
            if (cameraVideo) cameraVideo.srcObject = null;
        }

        if (cameraCaptureBtn) {
            cameraCaptureBtn.addEventListener('click', function() {
                // capture current frame to canvas, convert to blob and set to file input
                const canvas = document.getElementById('pp-camera-canvas');
                if (!canvas || !cameraVideo) return;
                canvas.width = cameraVideo.videoWidth;
                canvas.height = cameraVideo.videoHeight;
                const ctx = canvas.getContext('2d');
                ctx.drawImage(cameraVideo, 0, 0, canvas.width, canvas.height);
                canvas.toBlob(function(blob) {
                    if (!blob) return;
                    const file = new File([blob], 'camera.jpg', {
                        type: blob.type
                    });
                    // set file input with File via DataTransfer
                    const dt = new DataTransfer();
                    dt.items.add(file);
                    if (fileInput) {
                        fileInput.files = dt.files;
                        // trigger change to reuse preview flow
                        const ev = new Event('change', {
                            bubbles: true
                        });
                        fileInput.dispatchEvent(ev);
                    }
                    // close camera modal and stop stream
                    closeCameraStream();
                    const modalInstance = bootstrap.Modal.getInstance(cameraModalEl);
                    if (modalInstance) modalInstance.hide();
                }, 'image/jpeg', 0.95);
            });
        }

        if (cameraCancelBtn) {
            cameraCancelBtn.addEventListener('click', function() {
                closeCameraStream();
                // ensure main profile modal is visible after camera modal closes
                setTimeout(function() {
                    const main = document.getElementById('modalProfilePicture');
                    if (main) {
                        const mm = bootstrap.Modal.getOrCreateInstance(main);
                        mm.show();
                    }
                }, 150);
            });
        }

        // upload modal cancel -> return to main profile modal
        const uploadModalEl = document.getElementById('modalProfilePictureUpload');
        const ppCancelUpload = document.getElementById('pp-cancel-upload');
        if (ppCancelUpload) {
            ppCancelUpload.addEventListener('click', function(e) {
                e.preventDefault();
                // hide upload modal
                const um = bootstrap.Modal.getInstance(uploadModalEl) || new bootstrap.Modal(
                    uploadModalEl);
                if (um) um.hide();
                // show main profile modal
                setTimeout(function() {
                    const main = document.getElementById('modalProfilePicture');
                    if (main) {
                        const mm = bootstrap.Modal.getOrCreateInstance(main);
                        mm.show();
                    }
                }, 150);
            });
        }

        // ensure stream is stopped if camera modal is hidden by other means
        if (cameraModalEl) {
            cameraModalEl.addEventListener('hidden.bs.modal', function() {
                closeCameraStream();
            });
        }
        // cancel and save buttons can be wired by project-specific JS to submit or reset forms
    });
</script>
