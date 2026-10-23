@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card shadow-sm rounded-0 border">
                <div class="card-header bg-dark text-white rounded-0 fw-bold">{{ __('Đặt lại mật khẩu') }}</div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success rounded-0" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('password.email') }}">
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

                        <div class="mb-0 row">
                            <div class="col-md-6 offset-md-4">
                                <button type="submit" class="btn btn-dark rounded-0 px-4">
                                    {{ __('Gửi liên kết đặt lại mật khẩu') }}
                                </button>
                            </div>
                        </div>
                    </form>
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
    .form-control,
    .alert,
    .btn {
        border-radius: 0 !important;
    }

    /* Đổi màu viền khi focus ô nhập email sang tông màu tối */
    .form-control:focus {
        border-color: #212529 !important;
        box-shadow: 0 0 0 0.25rem rgba(33, 37, 41, 0.25) !important;
    }
</style>
@endsection