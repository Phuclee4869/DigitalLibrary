@extends('layouts.admin')

@section('title', 'Chỉnh sửa Người dùng')

@section('content')
<div class="container-fluid">
    <h1 class="mt-4">Chỉnh sửa Người dùng</h1>
    <ol class="breadcrumb mb-4">
        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Trang chủ</a></li>
        <li class="breadcrumb-item"><a href="{{ route('user.index') }}">Người dùng</a></li>
        <li class="breadcrumb-item active">Chỉnh sửa</li>
    </ol>
    
    <div class="card mb-4">
        <div class="card-header">
            <i class="fas fa-user-edit mr-1"></i>
            Chỉnh sửa thông tin người dùng
        </div>
        <div class="card-body">
            <form action="{{ route('user.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')
                
                <div class="form-group mb-3">
                    <label for="name">Tên <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('name') is-invalid @enderror" id="name" name="name" value="{{ old('name', $user->name) }}" required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="email">Email <span class="text-danger">*</span></label>
                    <input type="email" class="form-control @error('email') is-invalid @enderror" id="email" name="email" value="{{ old('email', $user->email) }}" required>
                    @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="password">Mật khẩu <small class="text-muted">(Để trống nếu không muốn thay đổi)</small></label>
                    <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password">
                    @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="password_confirmation">Xác nhận mật khẩu</label>
                    <input type="password" class="form-control" id="password_confirmation" name="password_confirmation">
                </div>
                
                <div class="form-group mb-3">
                    <label for="role_id">Vai trò <span class="text-danger">*</span></label>
                    <select class="form-control @error('role_id') is-invalid @enderror" id="role_id" name="role_id" required>
                        <option value="">-- Chọn vai trò --</option>
                        @foreach($roles as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->ten_vai_tro }}
                            </option>
                        @endforeach
                    </select>
                    @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="so_dien_thoai">Số điện thoại</label>
                    <input type="text" class="form-control @error('so_dien_thoai') is-invalid @enderror" id="so_dien_thoai" name="so_dien_thoai" value="{{ old('so_dien_thoai', $user->so_dien_thoai) }}">
                    @error('so_dien_thoai')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <div class="form-group mb-3">
                    <label for="dia_chi">Địa chỉ</label>
                    <input type="text" class="form-control @error('dia_chi') is-invalid @enderror" id="dia_chi" name="dia_chi" value="{{ old('dia_chi', $user->dia_chi) }}">
                    @error('dia_chi')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                
                <button type="submit" class="btn btn-primary">Cập nhật người dùng</button>
                <a href="{{ route('user.index') }}" class="btn btn-secondary">Hủy</a>
            </form>
        </div>
    </div>
</div>
@endsection