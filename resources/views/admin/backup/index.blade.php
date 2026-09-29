@extends('layouts.admin')

@section('title', 'Database Backup & Restore')

@section('content')
<div class="container-fluid py-4">
    <!-- Header Title -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <div>
            <h2 class="h3 mb-1 text-gray-800 fw-bold">Database Backup & Restore</h2>
            <p class="text-muted small mb-0">Create, download, restore, reset or erase database contents securely.</p>
        </div>
    </div>

    <!-- Backup & Restore Cards Row -->
    <div class="row g-4 mb-4">
        {{-- Card 1: Current Backup --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 d-flex flex-column justify-content-between" style="background: #ffffff; border: 1px solid #f1f5f9 !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #eef2ff; color: #4f46e5;">
                            <i class="fas fa-database fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 15px;">Current Backup</h5>
                            <small class="text-muted" style="font-size: 11px;">Single overwritten file</small>
                        </div>
                    </div>

                    @if ($backupExists)
                        <div class="p-3 mb-3" style="border-radius: 12px; border: 1px solid #e0e7ff; background-color: #f5f7ff;">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="fw-bold" style="color: #4f46e5; font-size: 13px;">backup.sql</span>
                                <span class="badge bg-success px-2 py-1" style="border-radius: 20px; font-weight: 600; font-size: 10px;">Available</span>
                            </div>
                            <div class="text-muted" style="font-size: 11px;">
                                <div class="mb-1"><i class="far fa-calendar-alt me-2 text-primary"></i>{{ $backupDate }}</div>
                                <div><i class="fas fa-hdd me-2 text-primary"></i>{{ $backupSize }}</div>
                            </div>
                        </div>
                    @else
                        <div class="py-4 px-3 mb-3 text-center border-dashed" style="border: 2px dashed #cbd5e1; border-radius: 12px; background-color: #f8fafc;">
                            <i class="fas fa-folder-open text-muted fa-2x mb-2 d-block"></i>
                            <span class="text-muted" style="font-size: 12px;">No backup file yet.</span>
                        </div>
                    @endif
                </div>

                @if ($backupExists)
                    <div>
                        <a href="{{ route('admin.backup.download') }}" class="btn btn-light w-100 fw-bold p-3 shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 12px; border: 1px solid #cbd5e1; font-size: 13px; color: #334155;">
                            <i class="fas fa-download me-2 text-primary"></i>Download SQL
                        </a>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 2: Create Backup --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 d-flex flex-column justify-content-between" style="background: #ffffff; border: 1px solid #f1f5f9 !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #fffbeb; color: #d97706;">
                            <i class="fas fa-cloud-download-alt fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 15px;">Create Backup</h5>
                            <small class="text-muted" style="font-size: 11px;">Replaces previous file</small>
                        </div>
                    </div>
                    <p style="font-size: 13px; color: #475569; line-height: 1.6;">
                        Creates a full database SQL dump on the server. The previous backup file will be permanently overwritten.
                    </p>
                </div>

                <div>
                    <form action="{{ route('admin.backup.run') }}" method="POST" onsubmit="return confirm('This will overwrite the existing backup. Continue?')">
                        @csrf
                        <button type="submit" class="btn btn-primary w-100 fw-bold p-3 shadow-sm d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 13px; background: #4f46e5; border-color: #4f46e5;">
                            <i class="fas fa-plus-circle me-2"></i>Create Backup Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card 3: Restore Database --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 d-flex flex-column justify-content-between" style="background: #ffffff; border: 1px solid #f1f5f9 !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #f0fdf4; color: #16a34a;">
                            <i class="fas fa-history fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 15px;">Restore Backup</h5>
                            <small class="text-muted" style="font-size: 11px;">Restore from saved file</small>
                        </div>
                    </div>

                    @if ($backupExists)
                        <div class="p-3 mb-3 border border-warning" style="border-radius: 12px; background-color: #fffbeb;">
                            <span class="text-warning fw-bold d-block mb-1" style="font-size: 12px;">
                                <i class="fas fa-exclamation-triangle me-1"></i>Warning:
                            </span>
                            <span style="font-size: 11px; color: #78350f; line-height: 1.5; display: block;">
                                Restoring will overwrite all current database data with the backup from <strong>{{ $backupDate }}</strong>. This action cannot be undone.
                            </span>
                        </div>
                    @else
                        <div class="py-4 px-3 mb-3 text-center" style="border-radius: 12px; background-color: #f8fafc; border: 1px solid #e2e8f0;">
                            <span class="text-muted" style="font-size: 12px; display: block; line-height: 1.5;">
                                No backup available. Create a backup first before restoring.
                            </span>
                        </div>
                    @endif
                </div>

                @if ($backupExists)
                    <div>
                        <form id="restore-form" action="{{ route('admin.backup.restore') }}" method="POST">
                            @csrf
                            <button type="button" id="restore-btn" class="btn btn-outline-danger w-100 fw-bold p-3 d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 13px; border: 1px solid #fecdd3; background-color: #fff1f2; color: #e11d48;">
                                <i class="fas fa-trash-restore me-2"></i>Restore Database Now
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- Card 4: Upload & Restore --}}
        <div class="col-xl-3 col-md-6">
            <div class="card border-0 shadow-sm rounded-3 h-100 p-4 d-flex flex-column justify-content-between" style="background: #ffffff; border: 1px solid #f1f5f9 !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #faf5ff; color: #9333ea;">
                            <i class="fas fa-cloud-upload-alt fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 15px;">Upload & Restore</h5>
                            <small class="text-muted" style="font-size: 11px;">Upload .sql file</small>
                        </div>
                    </div>
                    <p style="font-size: 13px; color: #475569; line-height: 1.6; margin-bottom: 1.2rem;">
                        Upload a <code style="background-color: #f1f5f9; padding: 2px 6px; border-radius: 4px; font-size: 12px;">.sql</code> file and restore it instantly.
                    </p>
                </div>

                <div>
                    <form id="upload-form" action="{{ route('admin.backup.upload') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        <label class="mb-3 d-flex flex-column align-items-center justify-content-center py-3 px-2 text-center" id="upload-label" style="border: 2px dashed #cbd5e1; border-radius: 12px; background-color: #f8fafc; cursor: pointer; transition: 0.2s;">
                            <i class="fas fa-file-upload text-muted fa-lg mb-2"></i>
                            <span id="upload-text" style="font-size: 11px; color: #64748b;">Click to choose .sql file</span>
                            <input type="file" name="sql_file" id="sql_file" accept=".sql,.txt" class="d-none" onchange="document.getElementById('upload-text').textContent = this.files[0] ? this.files[0].name : 'Click to choose .sql file'">
                        </label>
                        @error('sql_file')
                            <p class="text-danger mb-2" style="font-size: 11px;">{{ $message }}</p>
                        @enderror

                        <button type="button" id="upload-btn" class="btn btn-outline-danger w-100 fw-bold p-3 d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 13px; border: 1px solid #fecdd3; background-color: #fff1f2; color: #e11d48;">
                            <i class="fas fa-upload me-2"></i>Upload & Restore
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Reset Section Header -->
    <div class="d-flex align-items-center justify-content-between mb-4 mt-5">
        <div>
            <h2 class="h4 mb-1 text-danger fw-bold">Database Reset & Maintenance</h2>
            <p class="text-muted small mb-0">Warning: Actions in this section are highly destructive and delete table contents.</p>
        </div>
    </div>

    <!-- Reset Card Row -->
    <div class="row g-4 mb-4">
        {{-- Card 5A: Clear Products Only --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 d-flex flex-column justify-content-between h-100" style="background: #ffffff; border: 1px solid #fed7aa !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #fff7ed; color: #ea580c;">
                            <i class="fas fa-boxes fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 16px;">Purge Products Only</h5>
                            <small class="text-muted" style="font-size: 11px;">Deletes all products; preserves categories</small>
                        </div>
                    </div>
                    <p class="mb-4" style="font-size: 13px; color: #475569; line-height: 1.6;">
                        Deletes all products, product variants, and gallery images. <strong>Categories, brands, attributes, tags, and orders remain completely intact</strong> so you can upload new products immediately.
                    </p>
                </div>

                <div>
                    <form id="clear-products-form" action="{{ route('admin.backup.clear-products') }}" method="POST">
                        @csrf
                        <button type="button" id="clear-products-btn" class="btn btn-warning w-100 fw-bold p-3 d-flex align-items-center justify-content-center text-white" style="border-radius: 12px; font-size: 13px; background: #ea580c; border-color: #ea580c;">
                            <i class="fas fa-box-open me-2"></i>Purge All Products Now
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Card 5B: Reset DB --}}
        <div class="col-md-6">
            <div class="card border-0 shadow-sm rounded-3 p-4 d-flex flex-column justify-content-between h-100" style="background: #ffffff; border: 1px solid #fee2e2 !important;">
                <div>
                    <div class="d-flex align-items-center mb-4">
                        <div class="d-flex align-items-center justify-content-center me-3" style="width: 45px; height: 45px; border-radius: 12px; background-color: #fff1f2; color: #f43f5e;">
                            <i class="fas fa-trash-alt fa-lg"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 fw-bold" style="color: #1e293b; font-size: 16px;">Reset DB (Products & Orders)</h5>
                            <small class="text-muted" style="font-size: 11px;">Clears products, orders & customer profiles</small>
                        </div>
                    </div>
                    <p class="mb-4" style="font-size: 13px; color: #475569; line-height: 1.6;">
                        Clears all products, orders, courier logs, customer profiles, and addresses. <strong>Categories, brands, attributes, site pages, and settings are preserved.</strong>
                    </p>
                </div>

                <div>
                    <form id="reset-form" action="{{ route('admin.backup.reset') }}" method="POST">
                        @csrf
                        <button type="button" id="reset-db-btn" class="btn btn-danger w-100 fw-bold p-3 d-flex align-items-center justify-content-center" style="border-radius: 12px; font-size: 13px; background: #e11d48; border-color: #e11d48;">
                            <i class="fas fa-exclamation-triangle me-2"></i>Reset Store Data Now
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Bottom info box --}}
    <div class="row mt-4">
        <div class="col-md-12">
            <div class="card border-0 shadow-sm p-4" style="border-radius: 15px; border: 1px solid rgba(0,0,0,0.05) !important; background-color: #f8fafc;">
                <h6 class="mb-3 fw-bold" style="color: #334155;"><i class="fas fa-info-circle text-primary me-2"></i>How it works</h6>
                <ul class="ps-3 mb-0" style="font-size: 13px; color: #475569; line-height: 1.8;">
                    <li>Backups are stored on the server at: <code style="background-color: #fff; padding: 2px 6px; border-radius: 4px; border: 1px solid #e2e8f0; font-size: 12px;">storage/app/backups/backup.sql</code></li>
                    <li>Each new backup <strong>replaces</strong> the previous backup copy.</li>
                    <li>The backup and restore scripts run directly using pure PDO connections within default hosting limits.</li>
                    <li>Always download a backup before resetting database tables.</li>
                </ul>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Fetch fresh CSRF token to prevent 419 Page Expired
    async function refreshCsrfAndSubmit(formId, confirmMsg) {
        if (!confirm(confirmMsg)) return;

        const form = document.getElementById(formId);

        // Show loading spinner
        Swal.fire({
            title: 'Please wait...',
            text: 'Processing your request.',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        try {
            const res = await fetch('{{ route("admin.backup.csrf-token") }}', {
                headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            if (data.token) {
                form.querySelector('input[name="_token"]').value = data.token;
            }
        } catch (e) {
            // fallback
        }

        form.submit();
    }

    document.getElementById('restore-btn')?.addEventListener('click', function () {
        refreshCsrfAndSubmit(
            'restore-form',
            '⚠️ DANGER: This will overwrite your entire live database with the backup. Are you absolutely sure?'
        );
    });

    document.getElementById('upload-btn')?.addEventListener('click', function () {
        if (!document.getElementById('sql_file').files.length) {
            alert('Please select a .sql file first.');
            return;
        }
        refreshCsrfAndSubmit(
            'upload-form',
            '⚠️ DANGER: This will completely overwrite your live database with the uploaded file. Are you sure?'
        );
    });

    document.getElementById('clear-products-btn')?.addEventListener('click', function () {
        refreshCsrfAndSubmit(
            'clear-products-form',
            '⚠️ WARNING: All products and gallery images will be deleted. Categories, brands, attributes and tags will NOT be touched. Continue?'
        );
    });

    document.getElementById('reset-db-btn')?.addEventListener('click', function () {
        refreshCsrfAndSubmit(
            'reset-form',
            '⚠️ DANGER: All products, orders, and customer accounts will be permanently deleted! Are you absolutely sure you want to reset?'
        );
    });
</script>
@endsection
