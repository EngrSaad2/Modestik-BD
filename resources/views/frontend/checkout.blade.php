@extends('layouts.frontend')

@section('title', 'Checkout - Modestik')

@section('styles')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
    #shippingZoneSelect {
        font-size: 13px !important;
        border-radius: var(--radius-sm) !important;
        border: 1.5px solid var(--gray-400) !important;
        padding: 8px 12px !important;
        height: auto !important;
        font-weight: 500 !important;
        color: var(--dark) !important;
        box-shadow: var(--shadow-sm) !important;
        transition: var(--transition) !important;
    }
    #shippingZoneSelect:focus {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(217, 125, 140, 0.25) !important;
    }
    /* Style Select2 to match other form fields */
    .select2-container .select2-selection--single {
        height: 38px !important;
        border: 1.5px solid var(--gray-400) !important;
        border-radius: var(--radius-sm) !important;
        font-size: 14px !important;
        box-shadow: var(--shadow-sm) !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__rendered {
        line-height: 24px !important;
        padding-left: 12px !important;
        color: var(--dark) !important;
        font-weight: 500 !important;
    }
    .select2-container--default .select2-selection--single .select2-selection__arrow {
        height: 36px !important;
    }
    .select2-container--default .select2-selection--single:focus,
    .select2-container--default.select2-container--open .select2-selection--single {
        border-color: var(--primary) !important;
        box-shadow: 0 0 0 3px rgba(217, 125, 140, 0.25) !important;
        outline: none !important;
    }
</style>
@endsection

@section('content')
<div class="page-breadcrumb">
    <div class="container">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">হোম</a></li>
                <li class="breadcrumb-item"><a href="{{ route('cart.index') }}">কার্ট</a></li>
                <li class="breadcrumb-item active" aria-current="page">চেকআউট</li>
            </ol>
        </nav>
    </div>
</div>

<section class="section">
    <div class="container">
        <h4 class="fw-bold mb-4">অর্ডার চেকআউট</h4>

        <form action="{{ route('checkout.place-order') }}" method="POST" id="checkoutForm">
            @csrf
            <div class="row g-4">
                <!-- Shipping Billing Fields -->
                <div class="col-lg-7">
                    <div class="bg-white rounded-3 shadow-sm p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-truck me-2"></i>ডেলিভারি ঠিকানা ও বিবরণ</h5>

                        @if($addresses->count() > 0)
                            <div class="mb-4">
                                <label class="form-label fw-bold" style="font-size:13px;">সংরক্ষিত ঠিকানা থেকে বেছে নিন</label>
                                <div class="row g-2">
                                    @foreach($addresses as $addr)
                                        <div class="col-md-6">
                                            <div class="border rounded p-3 address-box cursor-pointer {{ $addr->is_default ? 'border-primary bg-light' : '' }}" 
                                                 onclick="selectSavedAddress({{ json_encode($addr) }}, this)">
                                                <div class="d-flex justify-content-between mb-1">
                                                    <strong>{{ $addr->label }}</strong>
                                                    @if($addr->is_default)
                                                        <span class="badge bg-primary">ডিফল্ট</span>
                                                    @endif
                                                </div>
                                                <small class="text-muted d-block">{{ $addr->name }} | {{ $addr->phone }}</small>
                                                <small class="text-muted">{{ $addr->address }}, {{ $addr->area }}, {{ $addr->district }}</small>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">নাম <span class="text-danger">*</span></label>
                                <input type="text" name="name" id="shippingName" class="form-control" required value="{{ auth()->user()?->name }}" placeholder="আপনার সম্পূর্ণ নাম লিখুন">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">মোবাইল নম্বর <span class="text-danger">*</span></label>
                                <input type="text" name="phone" id="shippingPhone" class="form-control" required value="{{ auth()->user()?->phone }}" maxlength="11" minlength="11" pattern="01[3-9][0-9]{8}" title="অনুগ্রহ করে একটি সঠিক ১১ ডিজিটের বাংলাদেশী মোবাইল নম্বর লিখুন (যেমন: 017XXXXXXXX)" placeholder="যেমন: 01XXXXXXXXX">
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size:13px;">ইমেইল এড্রেস (ঐচ্ছিক)</label>
                                <input type="email" name="email" id="shippingEmail" class="form-control" value="{{ auth()->user()?->email }}" placeholder="আপনার ইমেইল এড্রেস লিখুন">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">জেলা <span class="text-danger">*</span></label>
                                <select name="district" id="shippingDistrict" class="form-select" required>
                                    <option value="" disabled selected>জেলা নির্বাচন করুন</option>
                                    @foreach($districts as $dist)
                                        <option value="{{ $dist['bn_name'] }}" data-en="{{ $dist['name'] }}">{{ $dist['bn_name'] }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold" style="font-size:13px;">থানা <span class="text-danger">*</span></label>
                                <select name="division" id="shippingDivision" class="form-select" required disabled>
                                    <option value="" disabled selected>প্রথমে জেলা নির্বাচন করুন</option>
                                </select>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size:13px;">সম্পূর্ণ ঠিকানা <span class="text-danger">*</span></label>
                                <textarea name="address" id="shippingAddress" rows="3" class="form-control" required placeholder="বাসা নম্বর, রোড নম্বর, ফ্ল্যাট ইত্যাদি বিস্তারিত লিখুন..."></textarea>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold" style="font-size:13px;">অর্ডারের জন্য বিশেষ কোনো অনুরোধ (ঐচ্ছিক)</label>
                                <textarea name="notes" rows="2" class="form-control" placeholder="কুরিয়ার বা ডেলিভারি সম্পর্কিত কোনো বিশেষ নির্দেশনা থাকলে লিখতে পারেন..."></textarea>
                            </div>

                            @guest
                            <div class="col-12 border-top pt-3 mt-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="create_account" value="1" id="createAccountCheck">
                                    <label class="form-check-label fw-bold text-primary" for="createAccountCheck" style="cursor:pointer;">
                                        <i class="fas fa-user-plus me-1"></i> একটি কাস্টমার অ্যাকাউন্ট তৈরি করুন (Create an Account)
                                    </label>
                                </div>
                                <small class="text-muted d-block mt-1 ms-4" style="font-size:12px;">অর্ডার শেষে এই অ্যাকাউন্ট দিয়ে পরবর্তীতে লগইন করে আপনার সমস্ত অর্ডার ট্র্যাক করতে পারবেন।</small>

                                <div id="passwordFields" class="row g-3 mt-2 d-none ms-2 ps-2 border-start border-primary border-3">
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold" style="font-size:13px;">পাসওয়ার্ড <span class="text-danger">*</span></label>
                                        <input type="password" name="password" id="accountPassword" class="form-control" placeholder="কমপক্ষে ৬ অক্ষরের পাসওয়ার্ড">
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label fw-bold" style="font-size:13px;">কনফার্ম পাসওয়ার্ড <span class="text-danger">*</span></label>
                                        <input type="password" name="password_confirmation" id="accountPasswordConfirm" class="form-control" placeholder="পাসওয়ার্ড পুনরায় লিখুন">
                                    </div>
                                </div>
                            </div>
                            @endguest
                        </div>
                    </div>
                </div>

                <!-- Order Review & Payment -->
                <div class="col-lg-5">
                    <div class="bg-white rounded-3 shadow-sm p-4 mb-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-shopping-bag me-2"></i>অর্ডার সারাংশ</h5>
                        <div class="checkout-items mb-3" style="max-height: 250px; overflow-y: auto;">
                            @foreach($cart->items as $item)
                                <div class="d-flex align-items-center mb-3">
                                    <img src="{{ $item->product->primary_image_url }}" alt="img" class="rounded" style="width:40px;height:40px;object-fit:cover;margin-right:12px;">
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold" style="font-size:12px;">{{ $item->product->name }}</h6>
                                        @if($item->variant)
                                            <span class="text-muted d-block" style="font-size:10px;">অপশন: {{ $item->variant->display_name }}</span>
                                        @endif
                                        <small class="text-muted">{{ $item->quantity }} x ৳{{ number_format($item->price) }}</small>
                                    </div>
                                    <strong class="text-dark" style="font-size:13px;">৳{{ number_format($item->total) }}</strong>
                                </div>
                            @endforeach
                        </div>

                        <hr>

                        <!-- Shipping Zone Selector -->
                        <div class="mb-4">
                            <label class="form-label fw-bold" style="font-size:13px;">ডেলিভারি এলাকা নির্বাচন করুন <span class="text-danger">*</span></label>
                            <select name="shipping_zone_id" id="shippingZoneSelect" class="form-select" required>
                                <option value="" disabled selected>ডেলিভারি এলাকা বেছে নিন...</option>
                                @foreach($shippingZones as $zone)
                                    <option value="{{ $zone->id }}" data-charge="{{ $zone->charge }}" data-free="{{ $zone->free_shipping_min }}">
                                        {{ $zone->name }} (৳{{ number_format($zone->charge) }})
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted mt-1 d-block" style="font-size:11px;">
                                <i class="fas fa-info-circle me-1"></i> ঢাকা সিটির ভিতরে: ২ থেকে ৩ দিন | ঢাকা সিটির বাইরে: ২ থেকে ৫ দিন।
                            </small>
                        </div>

                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">সাবটোটাল</span>
                            <strong id="subtotalVal" data-val="{{ $cart->subtotal }}">৳{{ number_format($cart->subtotal, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ডেলিভারি চার্জ</span>
                            <strong id="shippingChargeVal">৳0.00</strong>
                        </div>
                        @if($cart->discount > 0)
                            <div class="d-flex justify-content-between mb-2 text-danger">
                                <span>ডিসকাউন্ট</span>
                                <strong>-৳{{ number_format($cart->discount, 2) }}</strong>
                            </div>
                        @endif
                        <hr>
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">সর্বমোট মূল্য</span>
                            <strong id="grandTotalVal" class="text-primary fs-4" data-discount="{{ $cart->discount }}">
                                ৳{{ number_format($cart->subtotal - $cart->discount, 2) }}
                            </strong>
                        </div>
                    </div>

                    <!-- Payment Gateway Selector -->
                    <div class="bg-white rounded-3 shadow-sm p-4">
                        <h5 class="fw-bold mb-3"><i class="fas fa-credit-card me-2"></i>পেমেন্ট পদ্ধতি নির্বাচন করুন</h5>
                        <div class="payment-options">
                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay-cod" value="cod" checked>
                                <label class="form-check-label fw-bold" for="pay-cod">
                                    <i class="fas fa-money-bill-wave text-success me-2"></i> ক্যাশ অন ডেলিভারি (Cash On Delivery)
                                </label>
                            </div>
                            <div class="form-check p-3 border rounded mb-2">
                                <input class="form-check-input ms-0 me-3" type="radio" name="payment_method" id="pay-bkash" value="bkash">
                                <label class="form-check-label fw-bold" for="pay-bkash">
                                    <i class="fas fa-mobile-alt text-danger me-2"></i> বিকাশ (বিকাশ ম্যানুয়াল পেমেন্ট)
                                </label>
                                <div id="bkash-payment-details" class="mt-3 p-3 bg-light border rounded d-none">
                                    <p class="mb-2 text-dark" style="font-size: 13px;">
                                        <strong>বিকাশ পেমেন্ট নিয়মাবলী:</strong><br>
                                        ১. মোট অর্ডার মূল্য আমাদের বিকাশ পার্সোনাল নম্বরে <strong>Send Money</strong> করুন: <strong>01621106653</strong>.<br>
                                        ২. পেমেন্ট সম্পন্ন হওয়ার পর নিচে আপনার বিকাশ নম্বর এবং ট্রানজেকশন আইডি (Transaction ID) লিখুন:
                                    </p>
                                    <div class="row g-2">
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold mb-1" style="font-size:11px; min-height: 33px; display: flex; align-items: flex-end;">বিকাশ নম্বর (যেখান থেকে টাকা পাঠিয়েছেন)</label>
                                            <input type="text" name="bkash_number" id="bkash_number" class="form-control form-control-sm" placeholder="যেমন: 01XXXXXXXXX" maxlength="11" pattern="01[3-9][0-9]{8}" title="অনুগ্রহ করে একটি সঠিক ১১ ডিজিটের বিকাশ মোবাইল নম্বর লিখুন">
                                        </div>
                                        <div class="col-md-6">
                                            <label class="form-label fw-bold mb-1" style="font-size:11px; min-height: 33px; display: flex; align-items: flex-end;">ট্রানজেকশন আইডি (TrxID)</label>
                                            <input type="text" name="bkash_trx_id" id="bkash_trx_id" class="form-control form-control-sm" placeholder="যেমন: 8N7X2K9L" minlength="8" maxlength="20">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <button type="button" class="btn btn-primary w-100 py-3 mt-4 fw-bold fs-5" id="openConfirmModalBtn">অর্ডার নিশ্চিত করুন</button>
                    </div>
                </div>
            </div>

            <!-- Order Confirmation Modal -->
            <div class="modal fade" id="orderConfirmModal" tabindex="-1" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content border-0 shadow">
                        <div class="modal-header bg-primary text-white">
                            <h5 class="modal-title fw-bold"><i class="fas fa-clipboard-check me-2"></i>অর্ডার তথ্য নিশ্চিতকরণ</h5>
                            <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="alert alert-light border mb-3" style="font-size:13px;">
                                <strong class="d-block mb-1 text-dark"><i class="fas fa-user me-1 text-primary"></i> গ্রাহকের নাম: <span id="modalCustomerName">-</span></strong>
                                <span class="d-block text-muted"><i class="fas fa-phone me-1 text-success"></i> মোবাইল: <span id="modalCustomerPhone">-</span></span>
                                <span class="d-block text-muted"><i class="fas fa-map-marker-alt me-1 text-danger"></i> ঠিকানা: <span id="modalCustomerAddress">-</span></span>
                                <span class="d-block text-muted"><i class="fas fa-credit-card me-1 text-info"></i> পেমেন্ট মেথড: <span id="modalPaymentMethod">-</span></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center p-3 bg-light rounded border">
                                <span class="fw-bold text-dark">সর্বমোট প্রদেয় মূল্য:</span>
                                <strong class="fs-4 text-primary" id="modalGrandTotal">৳0.00</strong>
                            </div>
                        </div>
                        <div class="modal-footer bg-light border-0">
                            <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">সম্পাদনা করুন</button>
                            <button type="submit" class="btn btn-primary px-4 fw-bold" id="finalSubmitBtn">হ্যাঁ, অর্ডার কনফার্ম করুন</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</section>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
    const bdLocations = @json($districts);

    // Custom Select2 Matcher for searching in English as well as Bangla
    function customMatcher(params, data) {
        if ($.trim(params.term) === '') {
            return data;
        }
        if (typeof data.text === 'undefined') {
            return null;
        }

        let search = params.term.toLowerCase();
        let text = data.text.toLowerCase();
        let enName = '';

        if (data.element) {
            enName = $(data.element).data('en') || '';
            enName = enName.toLowerCase();
        }

        if (text.indexOf(search) > -1 || enName.indexOf(search) > -1) {
            return data;
        }

        return null;
    }

    $(document).ready(function() {
        // Toggle Password Fields
        $('#createAccountCheck').on('change', function() {
            if ($(this).is(':checked')) {
                $('#passwordFields').removeClass('d-none');
                $('#accountPassword, #accountPasswordConfirm').attr('required', true);
            } else {
                $('#passwordFields').addClass('d-none');
                $('#accountPassword, #accountPasswordConfirm').removeAttr('required').val('');
            }
        });

        // Initialize Select2 with custom matcher
        $('#shippingDistrict').select2({
            width: '100%',
            matcher: customMatcher
        });
        $('#shippingDivision').select2({
            width: '100%',
            matcher: customMatcher
        });

        // Handle District Change
        $('#shippingDistrict').on('change', function() {
            let selectedBn = $(this).val();
            let divisionSelect = $('#shippingDivision');
            
            let distObj = bdLocations.find(d => d.bn_name === selectedBn);
            
            divisionSelect.empty();
            if (distObj && distObj.thanas && distObj.thanas.length > 0) {
                divisionSelect.append('<option value="" disabled selected>থানা নির্বাচন করুন</option>');
                distObj.thanas.forEach(function(thana) {
                    divisionSelect.append(`<option value="${thana.bn_name}" data-en="${thana.name}">${thana.bn_name}</option>`);
                });
                divisionSelect.prop('disabled', false);
            } else {
                divisionSelect.append('<option value="" disabled selected>কোনো থানা পাওয়া যায়নি</option>');
                divisionSelect.prop('disabled', true);
            }
            
            divisionSelect.val('').trigger('change.select2');
            updateShippingSelection();
        });

        // Handle Thana Change
        $('#shippingDivision').on('change', function() {
            updateShippingSelection();
        });

        // Open Confirmation Modal
        $('#openConfirmModalBtn').on('click', function(e) {
            const form = document.getElementById('checkoutForm');
            if (!form.checkValidity()) {
                form.reportValidity();
                return;
            }

            $('#modalCustomerName').text($('#shippingName').val());
            $('#modalCustomerPhone').text($('#shippingPhone').val());
            
            let addr = $('#shippingAddress').val() + ', ' + ($('#shippingDivision').val() || '') + ', ' + ($('#shippingDistrict').val() || '');
            $('#modalCustomerAddress').text(addr);

            let payMethod = $('input[name="payment_method"]:checked').val() === 'bkash' ? 'বিকাশ (bKash)' : 'ক্যাশ অন ডেলিভারি (COD)';
            $('#modalPaymentMethod').text(payMethod);
            $('#modalGrandTotal').text($('#grandTotalVal').text());

            const modal = new bootstrap.Modal(document.getElementById('orderConfirmModal'));
            modal.show();
        });

        // Initialize calculations
        updateShippingSelection();
    });

    // Handle Address Box Selection Click
    function selectSavedAddress(addr, element) {
        $('.address-box').removeClass('border-primary bg-light');
        $(element).addClass('border-primary bg-light');

        $('#shippingName').val(addr.name);
        $('#shippingPhone').val(addr.phone);
        $('#shippingArea').val(addr.area);
        $('#shippingZip').val(addr.zip);
        $('#shippingAddress').val(addr.address);

        let districtVal = addr.district.trim().toLowerCase();
        let matchedDistrict = null;

        bdLocations.forEach(function(d) {
            if (d.bn_name.trim().toLowerCase() === districtVal || d.name.trim().toLowerCase() === districtVal) {
                matchedDistrict = d.bn_name;
            }
        });

        if (matchedDistrict) {
            $('#shippingDistrict').val(matchedDistrict).trigger('change');
            
            setTimeout(function() {
                let divisionVal = addr.division.trim().toLowerCase();
                let matchedThana = null;

                let distObj = bdLocations.find(d => d.bn_name === matchedDistrict);
                if (distObj && distObj.thanas) {
                    distObj.thanas.forEach(function(t) {
                        if (t.bn_name.trim().toLowerCase() === divisionVal || t.name.trim().toLowerCase() === divisionVal) {
                            matchedThana = t.bn_name;
                        }
                    });
                }

                if (matchedThana) {
                    $('#shippingDivision').val(matchedThana).trigger('change');
                } else {
                    $('#shippingDivision').append(new Option(addr.division, addr.division)).val(addr.division).trigger('change');
                }
            }, 100);
        } else {
            $('#shippingDistrict').append(new Option(addr.district, addr.district)).val(addr.district).trigger('change');
            setTimeout(function() {
                $('#shippingDivision').append(new Option(addr.division, addr.division)).val(addr.division).trigger('change');
            }, 100);
        }
    }

    // Shipping calculations
    $('#shippingZoneSelect').change(function() {
        let selectedOption = $(this).find('option:selected');
        let charge = parseFloat(selectedOption.data('charge')) || 0;
        let freeMin = selectedOption.data('free') ? parseFloat(selectedOption.data('free')) : null;

        let subtotal = parseFloat($('#subtotalVal').data('val'));
        let discount = parseFloat($('#grandTotalVal').data('discount')) || 0;

        if (subtotal >= 1000 || (freeMin && subtotal >= freeMin)) {
            charge = 0;
        }

        $('#shippingChargeVal').text('৳' + charge.toFixed(2));
        let newTotal = subtotal + charge - discount;
        $('#grandTotalVal').text('৳' + newTotal.toFixed(2));
    });

    // Auto-select shipping zone based on district and thana
    function updateShippingSelection() {
        let districtVal = $('#shippingDistrict').val();
        let thanaVal = $('#shippingDivision').val();
        
        if (!districtVal || !thanaVal) return;

        districtVal = districtVal.trim().toLowerCase();
        thanaVal = thanaVal.trim().toLowerCase();

        let isDistrictDhaka = (districtVal === 'dhaka' || districtVal === 'ঢাকা');
        let suburbanThanas = [
            'savar', 'safor', 'সাভার',
            'keraniganj', 'keranigonj', 'কেরাণীগঞ্জ', 'কেরানীগঞ্জ',
            'dhamrai', 'ধামরাই',
            'dohar', 'দোহার',
            'nawabganj', 'nawabgonj', 'নবাবগঞ্জ'
        ];
        let isThanaSuburban = suburbanThanas.includes(thanaVal);

        let zoneType = 'outside';
        if (isDistrictDhaka) {
            zoneType = isThanaSuburban ? 'sub' : 'inside';
        }

        let selectedVal = null;
        $('#shippingZoneSelect option').each(function() {
            let optionText = $(this).text().toLowerCase();
            let optionId = $(this).val();
            
            if (zoneType === 'sub' && (optionText.includes('sub') || optionText.includes('suburban') || optionId === '3')) {
                selectedVal = optionId;
                return false;
            } else if (zoneType === 'inside' && (optionText.includes('inside') || optionId === '1')) {
                selectedVal = optionId;
                return false;
            } else if (zoneType === 'outside' && (optionText.includes('outside') || optionId === '2')) {
                selectedVal = optionId;
                return false;
            }
        });

        if (selectedVal) {
            $('#shippingZoneSelect').val(selectedVal).trigger('change');
        }
    }

    // Toggle Payment Details based on selection
    $('input[name="payment_method"]').change(function() {
        if ($(this).val() === 'bkash') {
            $('#bkash-payment-details').removeClass('d-none');
            $('#bkash_number, #bkash_trx_id').attr('required', true);
        } else {
            $('#bkash-payment-details').addClass('d-none');
            $('#bkash_number, #bkash_trx_id').removeAttr('required').val('');
        }
    });

    // Real-time Lead Capture for Incomplete Orders / Abandoned Cart recovery
    let leadDebounceTimer;
    function syncCheckoutLead() {
        clearTimeout(leadDebounceTimer);
        leadDebounceTimer = setTimeout(function() {
            let nameVal = $('#shippingName').val() || $('input[name="name"]').val() || '';
            let phoneVal = $('#shippingPhone').val() || $('input[name="phone"]').val() || '';
            let emailVal = $('#shippingEmail').val() || $('input[name="email"]').val() || '';
            let addressVal = $('#shippingAddress').val() || $('textarea[name="address"]').val() || '';
            let districtVal = $('#shippingDistrict').val() || $('select[name="district"]').val() || '';
            let divisionVal = $('#shippingDivision').val() || $('select[name="division"]').val() || '';

            if (nameVal || phoneVal || emailVal || addressVal || districtVal) {
                $.post("{{ route('checkout.update-lead') }}", {
                    _token: "{{ csrf_token() }}",
                    name: nameVal,
                    phone: phoneVal,
                    email: emailVal,
                    address: addressVal,
                    division: divisionVal,
                    district: districtVal
                });
            }
        }, 400);
    }
    $(document).on('input change keyup blur', '#checkoutForm input, #checkoutForm textarea, #checkoutForm select', syncCheckoutLead);
    $(document).ready(function() {
        syncCheckoutLead();
    });
</script>
@endsection
