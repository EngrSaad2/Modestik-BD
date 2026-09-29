@extends('layouts.admin')

@section('title', 'Salaries')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-user-tie text-dark me-2"></i>Salaries & Remuneration</h4>
                <p class="text-muted mb-0 small">Monthly salary and honorarium expense records for employees and management</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="p-3 bg-dark bg-opacity-10 text-dark rounded-4 d-flex align-items-center gap-3">
                    <i class="fas fa-money-check-alt fa-2x"></i>
                    <div>
                        <span class="text-xs fw-bold text-uppercase d-block">Total Paid Salaries</span>
                        <h3 class="fw-extrabold mb-0">৳{{ number_format($totalPaidSalaries, 2) }}</h3>
                    </div>
                </div>
                <button type="button" class="btn btn-dark rounded-3 px-3 py-2 fw-bold" data-bs-toggle="modal" data-bs-target="#addSalaryModal">
                    <i class="fas fa-plus me-1"></i>New Salary Entry
                </button>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="text-muted text-xs text-uppercase">
                            <th class="ps-4">Name</th>
                            <th>Type</th>
                            <th>Salary Month</th>
                            <th>Total Salary</th>
                            <th>Paid Amount</th>
                            <th>Due Amount</th>
                            <th>Payment Date</th>
                            <th class="text-end pe-4">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($salaries as $sal)
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-dark">{{ $sal->person_name }}</td>
                                <td>
                                    <span class="badge {{ $sal->person_type === 'owner' ? 'bg-primary' : 'bg-secondary' }} bg-opacity-10 text-dark fw-bold">
                                        {{ $sal->person_type === 'owner' ? 'Owner' : 'Employee' }}
                                    </span>
                                </td>
                                <td>{{ $sal->salary_month }}</td>
                                <td class="fw-bold">৳{{ number_format($sal->salary_amount, 2) }}</td>
                                <td class="fw-bold text-success">৳{{ number_format($sal->paid_amount, 2) }}</td>
                                <td class="fw-bold text-danger">৳{{ number_format($sal->due_amount, 2) }}</td>
                                <td>{{ $sal->payment_date ? $sal->payment_date->format('d M, Y') : '-' }}</td>
                                <td class="text-end pe-4">
                                    @if($sal->status === 'paid')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Paid</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Partial/Due</span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No salary records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($salaries->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $salaries->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal: Add Salary -->
<div class="modal fade" id="addSalaryModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content rounded-4 border-0">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-header-title fw-bold"><i class="fas fa-user-tie me-2"></i>Record Salary Payment</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.accounting.salaries.store') }}" method="POST">
                @csrf
                <div class="modal-body p-4">
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Person Type *</label>
                        <select name="person_type" class="form-select rounded-3" required>
                            <option value="employee">Employee</option>
                            <option value="owner">Owner Honorarium</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-sm fw-bold">Name *</label>
                        <input type="text" name="person_name" class="form-control rounded-3" placeholder="Person name..." required>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Salary Month *</label>
                            <input type="month" name="salary_month" class="form-control rounded-3" value="{{ date('Y-m') }}" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Total Salary (৳) *</label>
                            <input type="number" step="0.01" name="salary_amount" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Paid Amount (৳) *</label>
                            <input type="number" step="0.01" name="paid_amount" class="form-control rounded-3" placeholder="0.00" required>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Payment Date *</label>
                            <input type="date" name="payment_date" class="form-control rounded-3" value="{{ date('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="row g-3 mb-3">
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Payment Method</label>
                            <select name="payment_method" class="form-select rounded-3">
                                <option value="cash">Cash</option>
                                <option value="bkash">bKash</option>
                                <option value="bank">Bank Transfer</option>
                            </select>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-sm fw-bold">Money Account</label>
                            <select name="account_id" class="form-select rounded-3">
                                <option value="">Default Account</option>
                                @foreach($accounts as $acc)
                                    <option value="{{ $acc->id }}">{{ $acc->name }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light rounded-3" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-dark rounded-3 px-4">Save Salary</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
