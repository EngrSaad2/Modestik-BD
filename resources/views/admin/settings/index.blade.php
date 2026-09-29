@extends('layouts.admin')

@section('title', 'System Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Settings</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">System Settings</h4>
</div>

<div class="bg-white rounded-3 p-3 shadow-sm mb-4">
    <div class="d-flex gap-2 flex-wrap" style="font-size: 13px;">
        @foreach(['general', 'social'] as $grp)
            <a href="{{ route('admin.settings.index', $grp) }}" 
               class="btn btn-sm {{ $group === $grp ? 'btn-primary' : 'btn-light' }} text-capitalize">
               {{ $grp }} Settings
            </a>
        @endforeach
    </div>
</div>

<div class="form-card">
    <h5 class="fw-bold mb-4 text-capitalize">{{ $group }} Settings</h5>
    
    <form action="{{ route('admin.settings.update', $group) }}" method="POST">
        @csrf
        <div class="row g-3">
            @foreach($settings as $setting)
                @if($setting->key === 'about_description')
                    <div class="col-12">
                        <label class="form-label text-capitalize">{{ str_replace('_', ' ', $setting->key) }}</label>
                        <textarea name="{{ $setting->key }}" class="form-control" rows="4">{{ $setting->value }}</textarea>
                    </div>
                @else
                    <div class="col-md-6">
                        <label class="form-label text-capitalize">{{ str_replace('_', ' ', $setting->key) }}</label>
                        <input type="text" name="{{ $setting->key }}" class="form-control" value="{{ $setting->value }}">
                    </div>
                @endif
            @endforeach
            <div class="col-12 mt-4">
                <button type="submit" class="btn btn-admin-primary px-4">Save Settings</button>
            </div>
        </div>
    </form>
</div>
@endsection
