@extends('layouts.admin')

@section('title', 'Courier Integration Settings')

@section('breadcrumbs')
    <li class="breadcrumb-item"><a href="{{ route('admin.settings.index') }}">Settings</a></li>
    <li class="breadcrumb-item active">Courier Integration</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Courier & Automated Shipping Settings</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Configure API credentials for SteadFast Courier & future logistics providers.</p>
    </div>
    <form action="{{ route('admin.couriers.test-connection') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-primary btn-sm fw-bold">
            <i class="fas fa-plug me-1"></i>Test API Connection
        </button>
    </form>
</div>

<div class="row g-4">
    <!-- SteadFast Settings Card -->
    <div class="col-lg-8">
        <div class="form-card">
            <div class="d-flex align-items-center justify-content-between mb-3 border-bottom pb-3">
                <div class="d-flex align-items-center gap-2">
                    <div class="bg-primary bg-opacity-10 p-2 rounded text-primary">
                        <i class="fas fa-shipping-fast fa-lg"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold mb-0">SteadFast Courier Integration</h5>
                        <small class="text-muted">Official API v1 Gateway</small>
                    </div>
                </div>
                <span class="badge bg-success">Active Driver</span>
            </div>

            <form action="{{ route('admin.couriers.settings.update') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Default Courier Provider</label>
                        <select name="default_courier" class="form-select">
                            <option value="steadfast" {{ $settings['default_courier'] == 'steadfast' ? 'selected' : '' }}>SteadFast Courier (Active)</option>
                            <option value="pathao" disabled>Pathao Courier (Coming Soon)</option>
                            <option value="redx" disabled>RedX Logistics (Coming Soon)</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Environment Mode</label>
                        <select name="steadfast_mode" class="form-select">
                            <option value="live" {{ $settings['steadfast_mode'] == 'live' ? 'selected' : '' }}>Live Mode (Production)</option>
                            <option value="test" {{ $settings['steadfast_mode'] == 'test' ? 'selected' : '' }}>Test / Sandbox Mode</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-bold">API Base URL</label>
                        <input type="url" name="steadfast_base_url" class="form-control" value="{{ $settings['steadfast_base_url'] }}" required>
                        <small class="text-muted">Default: <code>https://portal.steadfast.com.bd/api/v1</code></small>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">API Key <span class="text-danger">*</span></label>
                        <input type="text" name="steadfast_api_key" class="form-control" value="{{ $settings['steadfast_api_key'] }}" placeholder="e.g. st_key_xxxxxxx" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Secret Key <span class="text-danger">*</span></label>
                        <input type="password" name="steadfast_secret_key" class="form-control" value="{{ $settings['steadfast_secret_key'] }}" placeholder="e.g. st_secret_xxxxxxx" required>
                    </div>
                </div>

                <div class="mt-4 pt-3 border-top d-flex justify-content-end">
                    <button type="submit" class="btn btn-primary px-4 fw-bold">Save Settings</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Integration Helper Card -->
    <div class="col-lg-4">
        <div class="form-card bg-light border-0">
            <h5 class="fw-bold mb-3"><i class="fas fa-info-circle text-info me-2"></i>Automated Workflow</h5>
            <p style="font-size:13px;" class="text-muted">
                Once configured, orders pushed to SteadFast will automatically:
            </p>
            <ul class="text-muted ps-3" style="font-size:13px;">
                <li class="mb-2">Send customer name, 11-digit phone, and full address without manual entry.</li>
                <li class="mb-2">Generate Consignment ID & Tracking Code.</li>
                <li class="mb-2">Sync real-time parcel statuses every 30 minutes.</li>
                <li class="mb-2">Display tracking link on customer dashboard & invoice PDF.</li>
            </ul>
        </div>
    </div>
</div>
@endsection
