@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-0 border">
                <div class="card-header bg-dark text-white rounded-0">
                    <h4 class="mb-0">{{ __('Đăng nhập') }}</h4>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="mb-3 row">
                            <label for="email" class="col-md-4 col-form-label text-md-end">{{ __('Email') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control rounded-0 @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <label for="password" class="col-md-4 col-form-label text-md-end">{{ __('Mật khẩu') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control rounded-0 @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3 row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input rounded-0" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>

                                    <label class="form-check-label" for="remember">
                                        {{ __('Ghi nhớ đăng nhập') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mb-0 row">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-dark rounded-0">
                                    {{ __('Đăng nhập') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link text-decoration-none text-dark" href="{{ route('password.request') }}">
                                        {{ __('Quên mật khẩu?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
                
                <div class="card-footer bg-light rounded-0">
                    <div class="text-center">
                        <p class="mb-0">Chưa có tài khoản? <a href="{{ route('register') }}" class="text-dark fw-bold text-decoration-none">Đăng ký ngay</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
<style>
    /* Ép tất cả các phần tử trên trang bỏ góc bo tròn */
    .card,
    .card-header,
    .card-footer,
    .form-control,
    .btn,
    .form-check-input {
        border-radius: 0 !important;
    }

    /* Đổi màu viền khi click vào ô nhập dữ liệu */
    .form-control:focus {
        border-color: #212529 !important;
        box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25) !important;
    }

    /* Đổi màu ô checkbox khi được tích */
    .form-check-input:checked {
        background-color: #212529 !important;
        border-color: #212529 !important;
    }
</style>
@endsection