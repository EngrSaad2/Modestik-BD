@extends('layouts.admin')

@section('title', 'Courier Settlement')

@section('content')
<div class="container-fluid px-0">
    <div class="card border-0 shadow-sm rounded-4 mb-4">
        <div class="card-body p-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
            <div>
                <h4 class="fw-bold mb-1" style="color: #2d2325;"><i class="fas fa-truck-loading text-primary me-2"></i>Courier Settlement Summary</h4>
                <p class="text-muted mb-0 small">Tracking Cash on Delivery (COD) collected, courier fees, and net payouts</p>
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
                            <th class="ps-4">Order #</th>
                            <th>Courier Company</th>
                            <th>Tracking / CID</th>
                            <th>Collected COD</th>
                            <th>Courier Charge</th>
                            <th>Net Received</th>
                            <th>Settlement Date</th>
                            <th class="pe-4 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($orders as $o)
                            @php
                                $cod = $o->total;
                                $charge = $o->shipping_charge > 0 ? $o->shipping_charge : 100;
                                $net = max(0, $cod - $charge);
                            @endphp
                            <tr>
                                <td class="ps-4 font-monospace fw-bold text-primary">#{{ $o->order_number }}</td>
                                <td><span class="badge bg-secondary bg-opacity-10 text-dark">{{ strtoupper($o->courier_name ?? 'SteadFast') }}</span></td>
                                <td><span class="font-monospace text-xs">{{ $o->consignment_id ?? $o->tracking_code ?? '-' }}</span></td>
                                <td class="fw-bold">৳{{ number_format($cod, 2) }}</td>
                                <td class="fw-bold text-danger">-৳{{ number_format($charge, 2) }}</td>
                                <td class="fw-extrabold text-success fs-6">৳{{ number_format($net, 2) }}</td>
                                <td>{{ $o->courier_settled_at ? \Carbon\Carbon::parse($o->courier_settled_at)->format('d M, Y') : '-' }}</td>
                                <td class="pe-4 text-center">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-1">Settled</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5 text-muted">No courier settlement records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($orders->hasPages())
            <div class="card-footer bg-white border-0 p-3">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
