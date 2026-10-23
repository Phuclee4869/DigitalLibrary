@extends('layouts.admin')

@section('content')
<div class="container">
    <h2>Thống kê thư viện</h2>
    
    <form action="{{ route('thong-ke.muon-thang') }}" method="GET">
        <div class="row">
            <div class="col-md-3">
                <select name="thang" class="form-control">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ request('thang') == $i ? 'selected' : '' }}>
                            Tháng {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <select name="nam" class="form-control">
                    @for($i = date('Y'); $i >= 2020; $i--)
                        <option value="{{ $i }}" {{ request('nam') == $i ? 'selected' : '' }}>
                            Năm {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary">Xem thống kê</button>
            </div>
        </div>
    </form>
</div>
@endsection