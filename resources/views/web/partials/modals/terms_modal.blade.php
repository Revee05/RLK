{{-- Modal untuk menampilkan Terms & Privacy dalam iframe PDF --}}
<div class="modal fade" id="termsModal" tabindex="-1" aria-labelledby="termsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-centered" style="max-width:1200px;">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="termsModalLabel">Terms of Service & Privacy Policy</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-0" style="background:#fff;">
                <iframe src="{{ asset('docs/terms_privacy.pdf') }}" style="width:100%; height:80vh; border:0;" allowfullscreen></iframe>
            </div>
        </div>
    </div>
</div>
