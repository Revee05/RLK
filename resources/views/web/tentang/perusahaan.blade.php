@extends('web.partials.layout')
@section('content')
    <div class="container" style="max-width:1200px; margin-top:40px; margin-bottom:80px;">
        <div class="row">
            @include('web.tentang.sidebar_tentang')

            <!-- Right content: form -->
            <div class="col-md-9">
                <div class="card content-border">
                    <div class="card-head border-bottom border-darkblue align-baseline ps-4">
                        <h3 class="mb-0 fw-bolder align-bottom">Perusahaan</h3>
                    </div>
                    <div class="card-body ps-4">

                        <div class="row pb-5">
                            <div class="col-md-8">
                                <div class="galeri-kami-figure">
                                    <img src="{{ asset('uploads/galeri-kami.png') }}">
                                </div>
                                <div class="py-3">
                                    <p><strong>Buahrista by Kedai Hokage and Hokgstudio</strong><br>Jalan Taman Siswa
                                        Nomor 69,
                                        Kelurahan Sekaran, Kecamatan Gunungpati, Kota Semarang, Jawa Tengah 50229</p>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <iframe
                                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3959.685232883548!2d110.39240561744384!3d-7.046226499999997!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2e708bf7dd6e3caf%3A0x7425f3878fe02af1!2sBUAHRISTA%20by%20Kedai%20Hokage!5e0!3m2!1sid!2sid!4v1683641397995!5m2!1sid!2sid"
                                    width="100%" height="70%" style="border:0;" allowfullscreen="" loading="lazy"
                                    referrerpolicy="no-referrer-when-downgrade"></iframe>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    </div>
@endsection
