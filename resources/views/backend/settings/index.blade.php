@extends('backend.layouts.master')
@section('title', 'Pengaturan Sistem')
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Pengaturan Sistem</h3>
                <p class="text-subtitle text-muted">Pusat konfigurasi — ubah sekali, berlaku di seluruh sistem.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Pengaturan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                <i class="bi bi-check-circle-fill me-1"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <form id="settings-form" action="{{ route('admin.settings.update') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="row">
                {{-- Sidebar Tab --}}
                <div class="col-12 col-md-3">
                    <div class="card p-0">
                        <div class="list-group list-group-flush rounded-3" id="settings-tab" role="tablist">
                            @foreach($groups as $groupKey => $groupLabel)
                            @php
                                $icons = [
                                    'school'       => 'bi-building',
                                    'finance'      => 'bi-cash-coin',
                                    'bank'         => 'bi-bank',
                                    'notification' => 'bi-bell-fill',
                                    'appearance'   => 'bi-palette-fill',
                                ];
                            @endphp
                            <a href="#tab-{{ $groupKey }}"
                               class="list-group-item list-group-item-action d-flex align-items-center gap-2 py-3 {{ $loop->first ? 'active' : '' }}"
                               data-bs-toggle="list" role="tab">
                                <i class="bi {{ $icons[$groupKey] ?? 'bi-gear' }}"></i>
                                {{ $groupLabel }}
                            </a>
                            @endforeach
                        </div>
                    </div>

                    {{-- Info box --}}
                    <div class="card mt-3">
                        <div class="card-body p-3" style="font-size:13px;">
                            <div class="fw-bold mb-2"><i class="bi bi-info-circle text-primary me-1"></i> Ringkasan</div>
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span class="text-muted">Total Siswa</span>
                                <strong>{{ \App\Models\Student::count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span class="text-muted">Total Kelas</span>
                                <strong>{{ \App\Models\ClassRoom::count() }}</strong>
                            </div>
                            <div class="d-flex justify-content-between border-bottom py-1">
                                <span class="text-muted">Tahun Ajaran</span>
                                <strong>{{ setting('academic_year', '-') }}</strong>
                            </div>
                            <div class="d-flex justify-content-between py-1">
                                <span class="text-muted">Tunggakan Aktif</span>
                                <strong class="text-danger">{{ \App\Models\Invoice::whereIn('status', ['unpaid','overdue'])->count() }}</strong>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Tab Content --}}
                <div class="col-12 col-md-9">
                    <div class="tab-content">
                        @foreach($groups as $groupKey => $groupLabel)
                        <div class="tab-pane fade {{ $loop->first ? 'show active' : '' }}" id="tab-{{ $groupKey }}" role="tabpanel">
                            <div class="card">
                                <div class="card-header d-flex align-items-center gap-2">
                                    <h5 class="card-title mb-0">{{ $groupLabel }}</h5>
                                </div>
                                <div class="card-body">
                                    @php 
                                        $groupSettings = $settings[$groupKey] ?? collect();
                                    @endphp
                                    @forelse($groupSettings as $setting)
                                    @if($setting->key === 'bank_name')
                                        <div class="mb-3">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <div class="bg-light-primary p-2 rounded text-primary">
                                                    <i class="bi bi-bank fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Rekening Bank Manual</h6>
                                                    <small class="text-muted">Informasi rekening untuk pembayaran via transfer manual (Siswa upload bukti).</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    @if($setting->key === 'payment_gateway_provider')
                                        <div class="mt-4 mb-3 pt-4 border-top">
                                            <div class="d-flex align-items-center gap-2 mb-3">
                                                <div class="bg-light-primary p-2 rounded text-primary">
                                                    <i class="bi bi-credit-card-2-front fs-5"></i>
                                                </div>
                                                <div>
                                                    <h6 class="mb-0 fw-bold">Pilih Sistem Pembayaran Otomatis (Gateway)</h6>
                                                    <small class="text-muted">Pilih provider yang Anda gunakan untuk integrasi pembayaran.</small>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                    
                                    @php
                                        $isMidtrans = str_starts_with($setting->key, 'midtrans_');
                                        $isXendit = str_starts_with($setting->key, 'xendit_');
                                        $isTripay = str_starts_with($setting->key, 'tripay_');
                                        $isGatewayField = $isMidtrans || $isXendit || $isTripay;
                                    @endphp

                                    <div class="mb-3 gateway-field {{ $isGatewayField ? 'ps-md-4 gateway-' . explode('_', $setting->key)[0] : '' }}" 
                                         data-provider="{{ explode('_', $setting->key)[0] }}"
                                         style="{{ $isGatewayField && setting('payment_gateway_provider') !== explode('_', $setting->key)[0] ? 'display:none;' : '' }}">
                                        
                                        <label class="form-label fw-semibold">{{ $setting->label ?? $setting->key }}</label>

                                        @if($setting->key === 'payment_gateway_provider')
                                            <select name="{{ $setting->key }}" class="form-select" onchange="switchGateway(this.value)">
                                                <option value="none" {{ $setting->value == 'none' ? 'selected' : '' }}>-- Gunakan Rekening Manual Sahaja --</option>
                                                <option value="midtrans" {{ $setting->value == 'midtrans' ? 'selected' : '' }}>Midtrans (Rekomendasi)</option>
                                                <option value="xendit" {{ $setting->value == 'xendit' ? 'selected' : '' }}>Xendit</option>
                                                <option value="tripay" {{ $setting->value == 'tripay' ? 'selected' : '' }}>Tripay</option>
                                            </select>
                                        @elseif(in_array($setting->key, ['school_address']))
                                            <textarea name="{{ $setting->key }}" class="form-control" rows="2">{{ old($setting->key, $setting->value) }}</textarea>

                                        @elseif(in_array($setting->key, ['late_fee_active', 'wa_active', 'midtrans_active', 'midtrans_is_production', 'xendit_active', 'tripay_active']))
                                            <div class="form-check form-switch">
                                                <input type="hidden" name="{{ $setting->key }}" value="0">
                                                <input class="form-check-input" type="checkbox"
                                                    name="{{ $setting->key }}" value="1"
                                                    id="{{ $setting->key }}"
                                                    {{ old($setting->key, $setting->value) == '1' ? 'checked' : '' }}>
                                                <label class="form-check-label" for="{{ $setting->key }}">
                                                    {{ $setting->value == '1' ? 'Aktif' : 'Nonaktif' }}
                                                </label>
                                            </div>

                                        @elseif(in_array($setting->key, ['school_logo', 'favicon']))
                                            <div class="mb-2">
                                                <img id="preview-{{ $setting->key }}" 
                                                     src="{{ $setting->value ? Storage::url('logos/' . $setting->value) : '' }}" 
                                                     style="max-height: 50px; {{ $setting->value ? '' : 'display:none;' }}">
                                            </div>
                                            <input type="file" name="{{ $setting->key }}" class="form-control" accept="image/png, image/jpeg, image/svg+xml" onchange="previewImage(this, 'preview-{{ $setting->key }}')">
                                        @elseif(in_array($setting->key, ['spp_amount', 'late_fee_amount']))
                                            <div class="input-group">
                                                <span class="input-group-text">Rp</span>
                                                <input type="number" name="{{ $setting->key }}"
                                                    class="form-control"
                                                    value="{{ old($setting->key, $setting->value) }}">
                                            </div>

                                        @elseif($setting->key === 'primary_color')
                                            <div class="d-flex gap-2 align-items-center">
                                                <input type="color" name="{{ $setting->key }}"
                                                    class="form-control form-control-color"
                                                    value="{{ old($setting->key, $setting->value ?? '#6366f1') }}"
                                                    style="width:60px; height:40px;">
                                                <input type="text" id="color_text" class="form-control"
                                                    value="{{ old($setting->key, $setting->value ?? '#6366f1') }}"
                                                    readonly style="max-width:120px;">
                                            </div>

                                        @elseif(in_array($setting->key, ['wa_token', 'midtrans_server_key', 'midtrans_client_key']))
                                            <div class="input-group">
                                                <input type="password" name="{{ $setting->key }}"
                                                    class="form-control font-monospace password-toggle"
                                                    value="{{ old($setting->key, $setting->value) }}"
                                                    placeholder="Token/Key rahasia">
                                                <button class="btn btn-outline-secondary" type="button" onclick="togglePassword(this)">
                                                    <i class="bi bi-eye"></i>
                                                </button>
                                            </div>
                                            @if($setting->key === 'wa_token')
                                                <div class="mt-2">
                                                    <button type="button" onclick="testWA()" class="btn btn-sm btn-outline-success">
                                                        <i class="bi bi-whatsapp me-1"></i> Tes Koneksi Fonnte
                                                    </button>
                                                    <span id="wa-test-status" class="ms-2 small"></span>
                                                </div>
                                            @endif
                                            @if($setting->key === 'midtrans_server_key')
                                                <div class="mt-2">
                                                    <button type="button" onclick="testMidtrans()" class="btn btn-sm btn-outline-primary">
                                                        <i class="bi bi-shield-check me-1"></i> Verifikasi Kredensial Midtrans
                                                    </button>
                                                    <span id="midtrans-test-status" class="ms-2 small"></span>
                                                </div>
                                            @endif

                                        @elseif($setting->key === 'timezone')
                                            <select name="{{ $setting->key }}" class="form-select">
                                                <option value="Asia/Jakarta" {{ old($setting->key, $setting->value) == 'Asia/Jakarta' ? 'selected' : '' }}>WIB (Asia/Jakarta)</option>
                                                <option value="Asia/Makassar" {{ old($setting->key, $setting->value) == 'Asia/Makassar' ? 'selected' : '' }}>WITA (Asia/Makassar)</option>
                                                <option value="Asia/Jayapura" {{ old($setting->key, $setting->value) == 'Asia/Jayapura' ? 'selected' : '' }}>WIT (Asia/Jayapura)</option>
                                            </select>

                                        @else
                                            <input type="text" name="{{ $setting->key }}"
                                                class="form-control"
                                                value="{{ old($setting->key, $setting->value) }}">
                                        @endif

                                        @php
                                            $hints = [
                                                'due_date_day'   => 'Angka 1–28. Tagihan akan jatuh tempo pada tanggal ini setiap bulan.',
                                                'invoice_prefix' => 'Contoh: INV → menghasilkan INV-2025-0001',
                                                'wa_token'       => 'Dapatkan token dari fonnte.com setelah registrasi.',
                                                'wa_sender'      => 'Nomor HP yang sudah terhubung ke Fonnte (format 628xxx).',
                                                'primary_color'  => 'Warna ini akan diterapkan ke sidebar dan elemen utama.',
                                                'midtrans_active'=> 'Jika aktif, siswa bisa membayar lewat VA/QRIS/E-Wallet.',
                                                'midtrans_is_production' => 'Off = Sandbox (Testing), On = Production (Live).',
                                            ];
                                        @endphp
                                        @if(isset($hints[$setting->key]))
                                            <small class="text-muted">{{ $hints[$setting->key] }}</small>
                                        @endif
                                    </div>
                                    @empty
                                    <p class="text-muted">Tidak ada pengaturan di grup ini.</p>
                                    @endforelse

                                    {{-- Custom Test Button for Notification Group --}}
                                    @if($groupKey === 'notification')
                                        <div class="mt-4 pt-4 border-top">
                                            <h6 class="fw-bold mb-3"><i class="bi bi-bug me-2"></i>Alat Pengujian</h6>
                                            <div class="alert alert-light-info border-0 mb-3 small">
                                                Gunakan tombol di bawah untuk mencoba mengirim notifikasi "Pembayaran Berhasil" ke siswa pertama yang ada di sistem. Ini membantu mendeteksi jika ada masalah pada pengiriman notifikasi database.
                                            </div>
                                            <button type="button" onclick="testNotification()" class="btn btn-outline-primary">
                                                <i class="bi bi-bell-fill me-1"></i> Kirim Notifikasi Tes ke Siswa
                                            </button>
                                            <button type="button" onclick="testWAMessage()" class="btn btn-outline-success ms-2">
                                                <i class="bi bi-whatsapp me-1"></i> Kirim Pesan Tes WA
                                            </button>
                                            <div id="notif-test-status" class="mt-2 small"></div>
                                            <div id="wa-msg-test-status" class="mt-2 small"></div>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="d-flex justify-content-end mt-2">
                        <button type="submit" class="btn btn-primary px-5" id="btn-save-settings">
                            <i class="bi bi-save me-1"></i> <span class="btn-text">Simpan Semua Pengaturan</span>
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </section>
</div>
@endsection

@section('scripts')
<script>
// Toggle Password Visibility
function togglePassword(btn) {
    const input = btn.parentElement.querySelector('input');
    if (input.type === 'password') {
        input.type = 'text';
        btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
    } else {
        input.type = 'password';
        btn.innerHTML = '<i class="bi bi-eye"></i>';
    }
}

// Test Midtrans Connection
function testMidtrans() {
    const status = document.getElementById('midtrans-test-status');
    status.innerHTML = '<i class="spinner-border spinner-border-sm text-primary"></i> Memverifikasi...';
    status.className = 'ms-2 small text-primary';

    fetch('{{ route("admin.settings.testMidtrans") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                status.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ' + data.message;
                status.className = 'ms-2 small text-success';
            } else {
                status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> ' + data.message;
                status.className = 'ms-2 small text-danger';
            }
        })
        .catch(err => {
            status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Error koneksi.';
            status.className = 'ms-2 small text-danger';
        });
}

// Test WA Connection
function testWA() {
    const status = document.getElementById('wa-test-status');
    status.innerHTML = '<i class="spinner-border spinner-border-sm text-primary"></i> Menghubungkan...';
    status.className = 'ms-2 small text-primary';

    fetch('{{ route("admin.settings.testWa") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                status.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ' + data.message;
                status.className = 'ms-2 small text-success';
            } else {
                status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> ' + data.message;
                status.className = 'ms-2 small text-danger';
            }
        })
        .catch(err => {
            status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Error sistem.';
            status.className = 'ms-2 small text-danger';
        });
}

// Sync color picker → text input
document.querySelector('input[type="color"]')?.addEventListener('input', function() {
    document.getElementById('color_text').value = this.value;
});

// Image preview
function previewImage(input, previewId) {
    const preview = document.getElementById(previewId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.style.display = 'block';
        }
        reader.readAsDataURL(input.files[0]);
    } else {
        preview.src = '';
        preview.style.display = 'none';
    }
}

// Preserve active tab on page reload
const hash = window.location.hash;
if (hash) {
    const tab = document.querySelector(`a[href="${hash}"]`);
    if (tab) new bootstrap.Tab(tab).show();
}
document.querySelectorAll('#settings-tab a').forEach(el => {
    el.addEventListener('shown.bs.tab', e => {
        history.replaceState(null, null, e.target.getAttribute('href'));
    });
});

// Switch Gateway Fields
function switchGateway(provider) {
    document.querySelectorAll('.gateway-field').forEach(el => {
        const p = el.getAttribute('data-provider');
        if (p === 'midtrans' || p === 'xendit' || p === 'tripay') {
            if (p === provider) {
                el.style.display = 'block';
            } else {
                el.style.display = 'none';
            }
        }
    });
}

// Test Notification
function testNotification() {
    const status = document.getElementById('notif-test-status');
    status.innerHTML = '<i class="spinner-border spinner-border-sm text-primary"></i> Sedang mengirim...';
    status.className = 'mt-2 small text-primary';

    fetch('{{ route("admin.settings.testNotification") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                status.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ' + data.message;
                status.className = 'mt-2 small text-success';
            } else {
                status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> ' + data.message;
                status.className = 'mt-2 small text-danger';
            }
        })
        .catch(err => {
            status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Terjadi kesalahan sistem.';
            status.className = 'mt-2 small text-danger';
        });
}
// Test WA Message
function testWAMessage() {
    const status = document.getElementById('wa-msg-test-status');
    status.innerHTML = '<i class="spinner-border spinner-border-sm text-success"></i> Sedang mengirim pesan WA...';
    status.className = 'mt-2 small text-success';

    fetch('{{ route("admin.settings.testWaMessage") }}')
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                status.innerHTML = '<i class="bi bi-check-circle-fill text-success"></i> ' + data.message;
                status.className = 'mt-2 small text-success';
            } else {
                status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> ' + data.message;
                status.className = 'mt-2 small text-danger';
            }
        })
        .catch(err => {
            status.innerHTML = '<i class="bi bi-x-circle-fill text-danger"></i> Terjadi kesalahan sistem.';
            status.className = 'mt-2 small text-danger';
        });
}
// AJAX Settings Update
document.getElementById('settings-form').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const form = this;
    const btn = document.getElementById('btn-save-settings');
    const btnText = btn.querySelector('.btn-text');
    const formData = new FormData(form);
    
    // UI state loading
    btn.disabled = true;
    const originalText = btnText.innerText;
    btnText.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Menyimpan...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            Toast.fire({
                icon: 'success',
                title: data.message || 'Pengaturan berhasil diperbarui.'
            });
            // Update UI components if colors changed (live update)
            if (formData.has('primary_color')) {
                document.documentElement.style.setProperty('--color-primary-custom', formData.get('primary_color'));
                document.documentElement.style.setProperty('--bs-primary', formData.get('primary_color'));
            }
        } else {
            Toast.fire({
                icon: 'error',
                title: data.message || 'Gagal menyimpan pengaturan.'
            });
        }
    })
    .catch(error => {
        console.error('Error:', error);
        Toast.fire({
            icon: 'error',
            title: 'Terjadi kesalahan sistem.'
        });
    })
    .finally(() => {
        btn.disabled = false;
        btnText.innerText = originalText;
    });
});
</script>
@endsection
