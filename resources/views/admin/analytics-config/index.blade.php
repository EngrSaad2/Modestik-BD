@extends('layouts.admin')

@section('title', 'Pixel & Analytics Configuration - Modestik')

@section('styles')
<style>
    .config-header {
        border-bottom: 1px solid #f1f5f9;
        padding-bottom: 20px;
        margin-bottom: 30px;
    }
    .config-card {
        background: #ffffff;
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 24px;
        margin-bottom: 24px;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        transition: all 0.3s ease;
    }
    .config-card:hover {
        box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05), 0 2px 4px -1px rgba(0,0,0,0.03);
    }
    .config-card.enabled {
        border-color: #a7f3d0;
        background: #fbfdfb;
    }
    .icon-wrapper {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 22px;
        margin-right: 16px;
    }
    .icon-fb { background: #e0f2fe; color: #0284c7; }
    .icon-ga { background: #fef3c7; color: #d97706; }
    .icon-tiktok { background: #f3f4f6; color: #111827; }
    .icon-pinterest { background: #fee2e2; color: #dc2626; }
    .icon-snapchat { background: #fef08a; color: #ca8a04; }
    .icon-clarity { background: #ccfbf1; color: #0d9488; }
    .icon-gsc { background: #e0e7ff; color: #4f46e5; }
    
    .status-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .badge-active {
        background: #d1fae5;
        color: #065f46;
    }
    .badge-inactive {
        background: #f3f4f6;
        color: #374151;
    }
    
    /* Toggle Green Styling */
    .form-switch .form-check-input {
        width: 46px;
        height: 24px;
        cursor: pointer;
    }
    .form-switch .form-check-input:checked {
        background-color: #10b981;
        border-color: #10b981;
    }
    
    .learn-link {
        font-size: 12px;
        color: #2563eb;
        text-decoration: none;
        font-weight: 500;
    }
    .learn-link:hover {
        text-decoration: underline;
    }
    
    .btn-save {
        background: #0f172a;
        color: #ffffff;
        font-weight: 600;
        font-size: 13px;
        padding: 8px 20px;
        border-radius: 8px;
        border: none;
        transition: all 0.2s ease;
    }
    .btn-save:hover {
        background: #1e293b;
        color: #ffffff;
    }
    .btn-save i {
        margin-right: 6px;
    }
</style>
@endsection

@section('content')
<div class="container-fluid py-4">
    <div class="config-header">
        <h3 class="fw-bold text-dark mb-1"><i class="fas fa-chart-line me-2 text-primary"></i>Pixel & Analytics Configuration</h3>
        <p class="text-muted mb-0">Configure high-performance Browser and Server-Side tracking to track user activities and events.</p>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="fas fa-check-circle me-2"></i>{{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <form action="{{ route('admin.analytics-config.update') }}" method="POST" id="analyticsForm">
        @csrf

        <!-- 1. Facebook Pixel & CAPI -->
        @php $fbEnabled = \App\Models\Setting::get('fb_pixel_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $fbEnabled ? 'enabled' : '' }}" id="card_fb">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-fb">
                        <i class="fab fa-facebook-f"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Facebook Pixel & CAPI</h5>
                        <span class="status-badge {{ $fbEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_fb">
                            {{ $fbEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="fb_pixel_enabled" name="fb_pixel_enabled" value="1"
                           data-target="fb" {{ $fbEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Measurement / Pixel ID</label>
                        <a href="https://www.facebook.com/business/help/952192354843755" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="fb_pixel_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('fb_pixel_id') }}" placeholder="e.g. 123456789012345">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Access Token</label>
                        <a href="https://developers.facebook.com/docs/sharing/webmasters/" target="_blank" class="learn-link">Generate API</a>
                    </div>
                    <input type="text" name="fb_pixel_token" class="form-control" 
                           value="{{ \App\Models\Setting::get('fb_pixel_token') }}" placeholder="EAAGb...">
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold text-dark" style="font-size:13px;">Test Event Code (Optional)</label>
                    <input type="text" name="fb_pixel_test_code" class="form-control" 
                           value="{{ \App\Models\Setting::get('fb_pixel_test_code') }}" placeholder="TEST12345">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 2. Google Analytics 4 (GA4) -->
        @php $gaEnabled = \App\Models\Setting::get('google_analytics_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $gaEnabled ? 'enabled' : '' }}" id="card_ga">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-ga">
                        <i class="fab fa-google"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Google Analytics 4 (GA4)</h5>
                        <span class="status-badge {{ $gaEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_ga">
                            {{ $gaEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="google_analytics_enabled" name="google_analytics_enabled" value="1"
                           data-target="ga" {{ $gaEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Measurement / Pixel ID</label>
                        <a href="https://support.google.com/analytics/answer/9539506" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="google_analytics_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('google_analytics_id') }}" placeholder="e.g. G-XXXXXXXXXX">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">API Secret Key</label>
                        <a href="https://developers.google.com/analytics/devguides/collection/protocol/ga4" target="_blank" class="learn-link">Generate API</a>
                    </div>
                    <input type="text" name="google_analytics_secret" class="form-control" 
                           value="{{ \App\Models\Setting::get('google_analytics_secret') }}" placeholder="e.g. secret_key_here">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 3. TikTok Pixel & Events API -->
        @php $tiktokEnabled = \App\Models\Setting::get('tiktok_pixel_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $tiktokEnabled ? 'enabled' : '' }}" id="card_tiktok">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-tiktok">
                        <i class="fab fa-tiktok"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">TikTok Pixel & Events API</h5>
                        <span class="status-badge {{ $tiktokEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_tiktok">
                            {{ $tiktokEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="tiktok_pixel_enabled" name="tiktok_pixel_enabled" value="1"
                           data-target="tiktok" {{ $tiktokEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Measurement / Pixel ID</label>
                        <a href="https://business-support.tiktok.com/portal/kb/article/tiktok-pixel" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="tiktok_pixel_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('tiktok_pixel_id') }}" placeholder="e.g. CXXXXXXXXXXXXXXXXXXX">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Access Token</label>
                        <a href="https://ads.tiktok.com/help/article/events-api" target="_blank" class="learn-link">Generate API</a>
                    </div>
                    <input type="text" name="tiktok_pixel_token" class="form-control" 
                           value="{{ \App\Models\Setting::get('tiktok_pixel_token') }}" placeholder="Generate from TikTok Events Manager">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 4. Pinterest Tag & API -->
        @php $pinterestEnabled = \App\Models\Setting::get('pinterest_pixel_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $pinterestEnabled ? 'enabled' : '' }}" id="card_pinterest">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-pinterest">
                        <i class="fab fa-pinterest-p"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Pinterest Tag & API</h5>
                        <span class="status-badge {{ $pinterestEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_pinterest">
                            {{ $pinterestEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="pinterest_pixel_enabled" name="pinterest_pixel_enabled" value="1"
                           data-target="pinterest" {{ $pinterestEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Measurement / Pixel ID</label>
                        <a href="https://help.pinterest.com/en/business/article/pinterest-tag" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="pinterest_pixel_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('pinterest_pixel_id') }}" placeholder="e.g. 26XXXXXXXXXX">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Access Token</label>
                        <a href="https://developers.pinterest.com/docs/api/v5/" target="_blank" class="learn-link">Generate API</a>
                    </div>
                    <input type="text" name="pinterest_pixel_token" class="form-control" 
                           value="{{ \App\Models\Setting::get('pinterest_pixel_token') }}" placeholder="Generate from Pinterest Ads Manager">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 5. Snapchat Pixel & CAPI -->
        @php $snapchatEnabled = \App\Models\Setting::get('snapchat_pixel_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $snapchatEnabled ? 'enabled' : '' }}" id="card_snapchat">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-snapchat">
                        <i class="fab fa-snapchat-ghost"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Snapchat Pixel & CAPI</h5>
                        <span class="status-badge {{ $snapchatEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_snapchat">
                            {{ $snapchatEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="snapchat_pixel_enabled" name="snapchat_pixel_enabled" value="1"
                           data-target="snapchat" {{ $snapchatEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Measurement / Pixel ID</label>
                        <a href="https://businesshelp.snapchat.com/s/article/snap-pixel" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="snapchat_pixel_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('snapchat_pixel_id') }}" placeholder="e.g. XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX">
                </div>
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Access Token</label>
                        <a href="https://businesshelp.snapchat.com/s/article/conversions-api" target="_blank" class="learn-link">Generate API</a>
                    </div>
                    <input type="text" name="snapchat_pixel_token" class="form-control" 
                           value="{{ \App\Models\Setting::get('snapchat_pixel_token') }}" placeholder="Generate from Snapchat Business Manager">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 6. Microsoft Clarity (Heatmaps) -->
        @php $clarityEnabled = \App\Models\Setting::get('clarity_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $clarityEnabled ? 'enabled' : '' }}" id="card_clarity">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-clarity">
                        <i class="fas fa-eye"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Microsoft Clarity (Heatmaps)</h5>
                        <span class="status-badge {{ $clarityEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_clarity">
                            {{ $clarityEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="clarity_enabled" name="clarity_enabled" value="1"
                           data-target="clarity" {{ $clarityEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">Clarity Project ID</label>
                        <a href="https://learn.microsoft.com/en-us/clarity/" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="clarity_project_id" class="form-control" 
                           value="{{ \App\Models\Setting::get('clarity_project_id') }}" placeholder="e.g. h8xxxxxxxx">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>

        <!-- 7. Google Search Console -->
        @php $gscEnabled = \App\Models\Setting::get('search_console_enabled', '0') === '1'; @endphp
        <div class="config-card {{ $gscEnabled ? 'enabled' : '' }}" id="card_gsc">
            <div class="d-flex align-items-center justify-content-between mb-4">
                <div class="d-flex align-items-center">
                    <div class="icon-wrapper icon-gsc">
                        <i class="fas fa-search"></i>
                    </div>
                    <div>
                        <h5 class="fw-bold text-dark mb-1">Google Search Console</h5>
                        <span class="status-badge {{ $gscEnabled ? 'badge-active' : 'badge-inactive' }}" id="status_gsc">
                            {{ $gscEnabled ? 'Active' : 'Inactive' }}
                        </span>
                    </div>
                </div>
                <div class="form-check form-switch">
                    <input class="form-check-input toggle-switch" type="checkbox" role="switch" 
                           id="search_console_enabled" name="search_console_enabled" value="1"
                           data-target="gsc" {{ $gscEnabled ? 'checked' : '' }}>
                </div>
            </div>
            
            <div class="row g-3">
                <div class="col-md-6">
                    <div class="d-flex justify-content-between mb-2">
                        <label class="form-label fw-bold mb-0 text-dark" style="font-size:13px;">HTML Verification Key</label>
                        <a href="https://support.google.com/webmasters/answer/9008080" target="_blank" class="learn-link">Learn More</a>
                    </div>
                    <input type="text" name="search_console_key" class="form-control" 
                           value="{{ \App\Models\Setting::get('search_console_key') }}" placeholder="e.g. google-site-verification=...">
                </div>
                <div class="col-12 mt-4 text-end">
                    <button type="submit" class="btn btn-save"><i class="fas fa-save"></i>Save Changes</button>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
    $(document).ready(function() {
        // Handle Switch Toggles
        $('.toggle-switch').change(function() {
            let target = $(this).data('target');
            let isChecked = $(this).is(':checked');
            let card = $('#card_' + target);
            let statusBadge = $('#status_' + target);
            
            if (isChecked) {
                card.addClass('enabled');
                statusBadge.removeClass('badge-inactive').addClass('badge-active').text('Active');
            } else {
                card.removeClass('enabled');
                statusBadge.removeClass('badge-active').addClass('badge-inactive').text('Inactive');
            }
        });
    });
</script>
@endsection
