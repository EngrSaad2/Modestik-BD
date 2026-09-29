@extends('layouts.admin')

@section('title', 'Media Library')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Media</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h4 class="fw-bold mb-1">Media Library</h4>
        <p class="text-muted mb-0" style="font-size:13px;">Manage product assets, uploaded images, and clean orphaned files.</p>
    </div>
    <form action="{{ route('admin.media.clean-unused') }}" method="POST">
        @csrf
        <button type="submit" class="btn btn-outline-danger btn-sm fw-bold" onclick="return confirm('Clean up all unused product images from storage?')">
            <i class="fas fa-broom me-1"></i>Clean Unused Images
        </button>
    </form>
</div>

<div class="row g-4">
    <!-- Upload Card -->
    <div class="col-md-4">
        <div class="form-card mb-4">
            <h5 class="fw-bold mb-3"><i class="fas fa-cloud-upload-alt me-2 text-primary"></i>Upload New Asset</h5>
            <form action="{{ route('admin.media.upload') }}" method="POST" enctype="multipart/form-data" id="mediaUploadForm">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Choose File</label>
                    <input type="file" name="file" class="form-control" required>
                    <small class="text-muted d-block mt-1">PNG, JPG, WEBP, PDF up to 10MB.</small>
                </div>
                <button type="submit" class="btn btn-admin-primary w-100 fw-bold"><i class="fas fa-upload me-1"></i>Upload Asset</button>
            </form>
        </div>
    </div>

    <!-- Media Library Grid -->
    <div class="col-md-8">
        <div class="table-card">
            <div class="card-header bg-white py-3">
                <form method="GET" action="{{ route('admin.media.index') }}" class="row g-2 align-items-center">
                    <div class="col-md-8">
                        <div class="input-group input-group-sm">
                            <span class="input-group-text bg-light border-end-0"><i class="fas fa-search text-muted"></i></span>
                            <input type="text" name="search" class="form-control form-control-sm border-start-0" placeholder="Search media by filename..." value="{{ request('search') }}">
                        </div>
                    </div>
                    <div class="col-md-4 d-flex gap-2 justify-content-end">
                        <button type="submit" class="btn btn-sm btn-primary px-3"><i class="fas fa-search me-1"></i>Search</button>
                        @if(request()->filled('search'))
                            <a href="{{ route('admin.media.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
                        @endif
                    </div>
                </form>
            </div>
            <div class="card-body p-3">
                <div class="row g-3">
                    @forelse($media as $item)
                        <div class="col-6 col-sm-4 col-md-3">
                            <div class="card h-100 border rounded shadow-sm text-center p-2 position-relative">
                                @if(str_starts_with($item->mime_type, 'image/'))
                                    <img src="{{ asset('storage/' . $item->path) }}" alt="{{ $item->filename }}" style="height: 100px; object-fit: cover; border-radius: 4px;" class="w-100">
                                @else
                                    <div class="d-flex align-items-center justify-content-center bg-light text-muted" style="height: 100px; border-radius: 4px;">
                                        <i class="far fa-file-alt fa-3x"></i>
                                    </div>
                                @endif
                                <div class="card-body p-1 mt-2">
                                    <p class="text-truncate mb-1 fw-bold" style="font-size: 11px;" title="{{ $item->filename }}">{{ $item->filename }}</p>
                                    <small class="text-muted d-block" style="font-size: 10px;">{{ round($item->size / 1024, 1) }} KB</small>
                                </div>
                                <div class="card-footer bg-transparent border-0 p-1 d-flex gap-1">
                                    <button type="button" class="btn btn-sm btn-outline-secondary w-50 py-1" style="font-size: 10px;" onclick="navigator.clipboard.writeText('{{ asset('storage/' . $item->path) }}'); alert('URL copied to clipboard!');">
                                        Copy
                                    </button>
                                    <form action="{{ route('admin.media.destroy', $item) }}" method="POST" class="w-50">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger w-100 py-1" style="font-size: 10px;" onclick="return confirm('Delete file?')">Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="col-12 py-5 text-center text-muted">
                            <i class="far fa-images fa-3x mb-3"></i>
                            <p>No media files uploaded yet.</p>
                        </div>
                    @endforelse
                </div>
                <div class="mt-4">
                    {{ $media->links('pagination::bootstrap-5') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
