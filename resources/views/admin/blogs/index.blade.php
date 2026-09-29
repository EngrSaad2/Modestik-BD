@extends('layouts.admin')

@section('title', 'Blogs')

@section('breadcrumbs')
    <li class="breadcrumb-item active">Blogs</li>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h4 class="fw-bold mb-0">Blogs</h4>
    <a href="{{ route('admin.blogs.create') }}" class="btn btn-admin-primary"><i class="fas fa-plus me-1"></i>Add Blog Post</a>
</div>

<div class="table-card">
    <div class="card-header">
        <h5>Manage Blog Articles</h5>
    </div>
    <div class="card-body p-0">
        <form action="{{ route('admin.blogs.bulk-delete') }}" method="POST" id="bulkBlogForm">
            @csrf
            <div class="d-flex align-items-center justify-content-between p-3 border-bottom bg-light">
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-semibold text-dark" style="font-size:12px;">Bulk Actions:</span>
                    <button type="submit" class="btn btn-sm btn-danger fw-bold px-3" style="font-size:11px;" onclick="return confirmBulkBlogDelete()">
                        <i class="fas fa-trash-alt me-1"></i>Delete Selected
                    </button>
                </div>
                <span class="text-muted" style="font-size: 12px;" id="blogSelectedCount">0 items selected</span>
            </div>

            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th style="width: 40px;" class="text-center">
                                <input type="checkbox" class="form-check-input" id="selectAllBlogs">
                            </th>
                            <th>Image</th>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Author</th>
                            <th>Views</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($blogs as $blog)
                            <tr>
                                <td class="text-center">
                                    <input type="checkbox" name="ids[]" value="{{ $blog->id }}" class="form-check-input blog-checkbox">
                                </td>
                                <td>
                                    @if($blog->image)
                                        <img src="{{ asset('storage/' . $blog->image) }}" alt="blog" class="image-preview" style="height: 40px; width: 60px; object-fit: cover; border-radius: 4px;">
                                    @else
                                        <span class="text-muted">No Image</span>
                                    @endif
                                </td>
                                <td><strong>{{ $blog->title }}</strong></td>
                                <td>{{ $blog->category->name ?? 'Uncategorized' }}</td>
                                <td>{{ $blog->author->name ?? 'Admin' }}</td>
                                <td>{{ $blog->views }}</td>
                                <td>
                                    <span class="badge {{ $blog->status ? 'bg-success' : 'bg-danger' }}">
                                        {{ $blog->status ? 'Published' : 'Draft' }}
                                    </span>
                                </td>
                                <td>{{ $blog->created_at->format('d M Y') }}</td>
                                <td>
                                    <div class="d-flex gap-2">
                                        <a href="{{ route('admin.blogs.edit', $blog) }}" class="btn btn-sm btn-outline-primary"><i class="fas fa-edit"></i></a>
                                        <button type="button" class="btn btn-sm btn-outline-danger" onclick="if(confirm('Delete blog post {{ addslashes($blog->title) }}?')) document.getElementById('delete-blog-{{ $blog->id }}').submit();">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="9" class="text-center text-muted py-4">No blogs found.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>

        @foreach($blogs as $blog)
            <form id="delete-blog-{{ $blog->id }}" action="{{ route('admin.blogs.destroy', $blog) }}" method="POST" class="d-none">
                @csrf
                @method('DELETE')
            </form>
        @endforeach

        <div class="p-3">
            {{ $blogs->links('pagination::bootstrap-5') }}
        </div>
    </div>
</div>

<script>
function confirmBulkBlogDelete() {
    const checkedCount = document.querySelectorAll('.blog-checkbox:checked').length;
    if (checkedCount === 0) {
        alert('Please select at least one blog post to delete.');
        return false;
    }
    return confirm('Are you sure you want to permanently delete ' + checkedCount + ' selected blog post(s)?');
}

document.addEventListener('DOMContentLoaded', function () {
    const selectAll = document.getElementById('selectAllBlogs');
    const checkboxes = document.querySelectorAll('.blog-checkbox');
    const countText = document.getElementById('blogSelectedCount');

    function updateCount() {
        const checkedCount = document.querySelectorAll('.blog-checkbox:checked').length;
        if (countText) countText.textContent = checkedCount + ' items selected';
    }

    if (selectAll) {
        selectAll.addEventListener('change', function () {
            checkboxes.forEach(cb => cb.checked = selectAll.checked);
            updateCount();
        });
    }

    checkboxes.forEach(cb => {
        cb.addEventListener('change', function() {
            if (!this.checked && selectAll) selectAll.checked = false;
            if (selectAll && document.querySelectorAll('.blog-checkbox:checked').length === checkboxes.length) {
                selectAll.checked = true;
            }
            updateCount();
        });
    });
});
</script>
@endsection
