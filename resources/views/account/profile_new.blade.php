<!--
 Figma-derived account profile content (no navbar/footer)
 - Left: avatar + vertical menu
 - Right: account form
 Uses Bootstrap classes present in the project.
 -->
@extends('account.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            <!-- Left sidebar -->
            <div class="col-md-3">
                <div class="text-center mb-4">
                    <img src="https://www.figma.com/api/mcp/asset/1bcfd75e-90c9-43bf-8586-79d92d395def" alt="avatar"
                        class="rounded-circle img-fluid" style="width:200px; height:200px; object-fit:cover;">
                </div>

                <div class="list-group mb-4">
                    <a href="#" class="list-group-item list-group-item-action active" aria-current="true"
                        style="background:#051a36; border-color:#051a36; color:#fff;">
                        Akun
                    </a>
                    <a href="#" class="list-group-item list-group-item-action">Ubah Password</a>
                    <a href="#" class="list-group-item list-group-item-action">Alamat</a>
                    <a href="#" class="list-group-item list-group-item-action">Favorit</a>
                    <a href="#" class="list-group-item list-group-item-action">Riwayat Lelang</a>
                    <a href="#" class="list-group-item list-group-item-action">Riwayat Pembelian</a>
                    <a href="#" class="list-group-item list-group-item-action">Pengaturan Notifikasi</a>
                    <a href="#" class="list-group-item list-group-item-action">Logout</a>
                </div>

                <div class="d-grid">
                    <button class="btn" style="background:#58bcc2; color:#fff;">Save</button>
                </div>
            </div>

            <!-- Right content: form -->
            <div class="col-md-9">
                <div class="card" style="border-radius:8px;">
                    <div class="card-header" style="background:#fff; border-bottom:0;">
                        <h3 class="mb-0" style="font-weight:700;">Akun</h3>
                    </div>
                    <div class="card-body">
                        <form method="POST" action="#">
                            @csrf
                            <div class="mb-3">
                                <label class="form-label">First Name</label>
                                <input type="text" name="first_name" class="form-control"
                                    value="{{ old('first_name', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Last Name</label>
                                <input type="text" name="last_name" class="form-control"
                                    value="{{ old('last_name', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label">E-mail Address</label>
                                <input type="email" name="email" class="form-control" value="{{ old('email', '') }}">
                            </div>

                            <div class="mb-3">
                                <label class="form-label d-block">Gender</label>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_male"
                                        value="male">
                                    <label class="form-check-label" for="gender_male">Male</label>
                                </div>
                                <div class="form-check form-check-inline">
                                    <input class="form-check-input" type="radio" name="gender" id="gender_female"
                                        value="female">
                                    <label class="form-check-label" for="gender_female">Female</label>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Additional Info</label>
                                <textarea name="notes" class="form-control" rows="4">{{ old('notes', '') }}</textarea>
                            </div>

                            <div class="d-flex justify-content-start">
                                <button class="btn" style="background:#58bcc2; color:#fff;">Save</button>
                                <a href="#" class="btn btn-secondary ms-2">Cancel</a>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
