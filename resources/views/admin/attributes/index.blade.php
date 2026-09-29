@extends('layouts.admin')

@section('title', 'Attributes')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Attributes</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Attributes</h4>
    <a href="{{ route('admin.attributes.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Attribute</a>
</div>

<div class="row g-4">
    @forelse($attributes as $attribute)
        <div class="col-md-6">
            <div class="table-card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        <h5 class="mb-0">{{ $attribute->name }} <span class="badge bg-secondary text-capitalize" style="font-size:10px;">{{ $attribute->type }}</span></h5>
                    </div>
                    <div class="d-flex gap-2">
                        <a href="{{ route('admin.attributes.edit', $attribute) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                        <form action="{{ route('admin.attributes.destroy', $attribute) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger btn-delete"><i class="fas fa-trash"></i></button>
                        </form>
                    </div>
                </div>
                <div class="card-body">
                    <!-- Current Values -->
                    <div class="mb-3">
                        <label class="form-label fw-bold" style="font-size:11px;">Current Values:</label>
                        <div>
                            @forelse($attribute->values as $val)
                                <span class="badge bg-light text-dark border p-2 mb-2 d-inline-flex align-items-center">
                                    @if($attribute->type === 'color' && $val->color_code)
                                        <span style="display:inline-block; width:12px; height:12px; border-radius:50%; background:{{ $val->color_code }}; margin-right:6px; border:1px solid #ddd;"></span>
                                    @endif
                                    {{ $val->value }}
                                    <form action="{{ route('admin.attributes.values.destroy', $val->id) }}" method="POST" class="d-inline ms-2">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="border-0 bg-transparent p-0 text-danger" style="font-size:10px; line-height:1;"><i class="fas fa-times"></i></button>
                                    </form>
                                </span>
                            @empty
                                <span class="text-muted d-block" style="font-size:12px;">No values added yet.</span>
                            @endforelse
                        </div>
                    </div>

                    <!-- Add Value Form -->
                    <form action="{{ route('admin.attributes.values.store', $attribute) }}" method="POST" class="border-top pt-3">
                        @csrf
                        <div class="row g-2 align-items-end">
                            <div class="col">
                                <label class="form-label" style="font-size:10px;">New Value</label>
                                <input type="text" name="value" class="form-control form-control-sm" placeholder="e.g. Red or XL" required>
                            </div>
                            @if($attribute->type === 'color')
                                <div class="col">
                                    <label class="form-label" style="font-size:10px;">Color Hex</label>
                                    <input type="text" name="color_code" class="form-control form-control-sm" placeholder="#FF0000">
                                </div>
                            @endif
                            <div class="col-auto">
                                <button type="submit" class="btn btn-sm btn-primary py-1 px-3">Add</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @empty
        <div class="col-12 text-center text-muted py-5 bg-white border rounded">
            <i class="fas fa-palette fa-3x mb-3 text-secondary"></i>
            <p>No attributes configured yet.</p>
        </div>
    @endforelse
</div>
@endsection
