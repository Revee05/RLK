@extends('layouts.app_new')
@section('content')
<!-- Nested Row within Card Body -->
    <div class="p-5">
        <div class="text-center">
            <h1 class="h4 text-gray-900 mb-4">{{ __('Reset Password') }}</h1>
        </div>
        @if (session('status'))
            <div class="alert alert-success" role="alert">
                {{ session('status') }}
            </div>
        @endif
        <form class="user" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="form-group">
                <input type="email" id="email" class="form-control form-control-user @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" placeholder="Email">
                @error('email')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
                @enderror
            </div>
            <button type="submit" class="btn btn-danger btn-user btn-block">
            {{ __('Kirim Link Reset Password') }}
            </button>
        </form>
        <div class="text-center pt-3">
            <a class="small d-block text-decoration-none" href="{{route('home')}}">Kembali ke Beranda</a>
        </div>
    </div>
@endsection
