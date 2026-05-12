@extends('siswa.layouts.master')
@section('title', 'Bayar Tagihan')
@section('content')

<style>
    .step-indicator {
        display: flex;
        justify-content: space-between;
        margin-bottom: 2rem;
        position: relative;
    }
    .step-indicator::before {
        content: '';
        position: absolute;
        top: 20px;
        left: 0;
        right: 0;
        height: 2px;
        background: #e9ecef;
        z-index: 0;
    }
    .step {
        position: relative;
        z-index: 1;
        background: #fff;
        text-align: center;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        line-height: 40px;
        border: 2px solid #e9ecef;
        color: #adb5bd;
        font-weight: bold;
        transition: all 0.3s;
    }
    .step.active {
        border-color: var(--bs-primary);
        background: var(--bs-primary);
        color: #fff;
        box-shadow: 0 0 15px rgba(67, 94, 190, 0.3);
    }
    .step.completed {
        border-color: #28a745;
        background: #28a745;
        color: #fff;
    }
    .step-label {
        font-size: 11px;
        font-weight: bold;
        position: absolute;
        top: 45px;
        left: 50%;
        transform: translateX(-50%);
        white-space: nowrap;
        color: #6c757d;
    }
    .step.active + .step-label { color: var(--bs-primary); }

    .method-card {
        border: 2px solid #f1f3f5;
        border-radius: 1.25rem;
        padding: 1.5rem;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }
    .method-card:hover {
        border-color: var(--bs-primary);
        background: rgba(67, 94, 190, 0.02);
        transform: translateY(-5px);
    }
    .method-card.selected {
        border-color: var(--bs-primary);
        background: rgba(67, 94, 190, 0.05);
    }
    .method-card.selected::after {
        content: '\F272';
        font-family: 'bootstrap-icons';
        position: absolute;
        top: 10px;
        right: 15px;
        color: var(--bs-primary);
        font-size: 1.25rem;
    }

    .payment-summary {
        background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
        border-radius: 1.5rem;
        padding: 1.5rem;
    }
    
    .file-upload-zone {
        border: 2px dashed #dee2e6;
        border-radius: 1rem;
        padding: 2rem;
        text-align: center;
        transition: all 0.2s;
        cursor: pointer;
        background: #fbfbfb;
    }
    .file-upload-zone:hover {
        border-color: var(--bs-primary);
        background: #f8faff;
    }
</style>

<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <div>
            <h3 class="mb-1 text-primary fw-bold">Pusat Pembayaran</h3>
            <p class="text-muted small">Selesaikan pembayaran tagihan Anda dengan mudah dan aman.</p>
        </div>
    </div>
</div>

<div class="page-content">
    <section class="section">
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm">
                <i class="bi bi-exclamation-octagon-fill me-2"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm">
                <div class="d-flex align-items-center">
                    <div class="bg-success text-white rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px;">
                        <i class="bi bi-check-lg fs-5"></i>
                    </div>
                    <div>
                        <h6 class="alert-heading mb-0 fw-bold">Upload Berhasil!</h6>
                        <p class="mb-0 small">{{ session('success') }}</p>
                    </div>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($invoices->isEmpty())
            <div class="card shadow-sm border-0 rounded-4">
                <div class="card-body text-center py-5">
                    <div class="bg-light-success p-4 rounded-circle d-inline-flex mb-4">
                        <i class="bi bi-check-all text-success display-4"></i>
                    </div>
                    <h4 class="fw-bold">Alhamdulillah, Semua Lunas!</h4>
                    <p class="text-muted mb-4">Tidak ada tagihan yang tertunggak saat ini. Terima kasih atas kerja samanya.</p>
                    <a href="{{ route('siswa.dashboard') }}" class="btn btn-primary rounded-pill px-5">Kembali ke Dashboard</a>
                </div>
            </div>
        @else
            <div class="row g-4">
                <div class="col-12 col-lg-8">
                    {{-- Steps Indicator --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-body py-4 px-5">
                            <div class="step-indicator">
                                <div class="step completed" id="step-1-icon">1 <span class="step-label">Pilih Tagihan</span></div>
                                <div class="step {{ $selectedInvoice ? 'active' : '' }}" id="step-2-icon">2 <span class="step-label">Metode</span></div>
                                <div class="step" id="step-3-icon">3 <span class="step-label">Konfirmasi</span></div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 1: Pilih Tagihan --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0">Langkah 1: Pilih Tagihan Anda</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="form-group mb-0">
                                <select name="invoice_id" id="invoice_id" class="form-select form-select-lg border-2" onchange="updateInvoiceDetail(this)">
                                    <option value="">-- Silakan Pilih Periode Tagihan --</option>
                                    @foreach($invoices as $inv)
                                        <option value="{{ $inv->id }}" 
                                                data-amount="{{ $inv->amount }}" 
                                                data-month="{{ $inv->month_name }}"
                                                data-year="{{ $inv->year }}"
                                                {{ (old('invoice_id') == $inv->id || ($selectedInvoice && $selectedInvoice->id == $inv->id)) ? 'selected' : '' }}>
                                            SPP {{ $inv->month_name }} {{ $inv->year }} ({{ currency($inv->amount) }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 2: Pilih Metode --}}
                    <div class="card border-0 shadow-sm rounded-4 mb-4 animate__animated animate__fadeIn" id="method-section" style="{{ $selectedInvoice ? '' : 'display:none;' }}">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0">Langkah 2: Pilih Metode Pembayaran</h6>
                        </div>
                        <div class="card-body p-4">
                            <div class="row g-4">
                                @if($gateway['active'])
                                <div class="col-md-6">
                                    <div class="method-card h-100" id="method-gateway" onclick="selectMethod('gateway')">
                                        <div class="bg-light-primary p-3 rounded-4 d-inline-block mb-3">
                                            <i class="bi bi-lightning-charge text-primary fs-3"></i>
                                        </div>
                                        <h6 class="fw-bold">Otomatis & Instan</h6>
                                        <p class="text-muted smaller mb-0">Bayar via QRIS, Virtual Account, atau E-Wallet. Terverifikasi dalam hitungan detik.</p>
                                        <div class="mt-3">
                                            <span class="badge bg-success rounded-pill px-3">Rekomendasi</span>
                                        </div>
                                    </div>
                                </div>
                                @endif
                                <div class="col-md-6">
                                    <div class="method-card h-100" id="method-manual" onclick="selectMethod('manual')">
                                        <div class="bg-light-secondary p-3 rounded-4 d-inline-block mb-3">
                                            <i class="bi bi-bank text-secondary fs-3"></i>
                                        </div>
                                        <h6 class="fw-bold">Transfer Manual</h6>
                                        <p class="text-muted smaller mb-0">Transfer ke rekening sekolah dan unggah bukti pembayaran secara manual.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- STEP 3: FORM MANUAL --}}
                    <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeInUp" id="manual-form" style="display:none;">
                        <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                            <h6 class="fw-bold text-dark mb-0">Langkah 3: Unggah Bukti Transfer</h6>
                        </div>
                        <div class="card-body p-4">
                            <form id="manual-payment-form" action="{{ route('siswa.payments.store') }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" name="invoice_id" id="manual_invoice_id" value="{{ $selectedInvoice->id ?? '' }}">
                                <div class="row g-3">
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Dari Bank Mana Anda Transfer?</label>
                                        <input type="text" name="bank_name" class="form-control rounded-pill px-3" placeholder="Contoh: BCA, BNI, Mandiri" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Nama Atas Nama Pengirim</label>
                                        <input type="text" name="sender_name" class="form-control rounded-pill px-3" placeholder="Sesuai nama di struk/M-Banking" required>
                                    </div>
                                    <div class="col-md-6">
                                        <label class="form-label small fw-bold">Tanggal Transfer</label>
                                        <input type="date" name="paid_at" class="form-control rounded-pill px-3" value="{{ date('Y-m-d') }}" required>
                                    </div>
                                    <div class="col-12">
                                        <label class="form-label small fw-bold">Unggah Bukti (Foto/Screenshot)</label>
                                        <div class="file-upload-zone" onclick="document.getElementById('proof-input').click()">
                                            <i class="bi bi-cloud-arrow-up text-primary display-6 mb-2 d-block"></i>
                                            <span class="text-muted small d-block">Klik atau seret file gambar di sini</span>
                                            <span class="badge bg-light text-muted border mt-2" id="file-name">Belum ada file dipilih</span>
                                        </div>
                                        <input type="file" name="proof" id="proof-input" class="d-none" accept="image/*" required onchange="handleFileSelect(event)">
                                        
                                        <div id="previewContainer" class="mt-4 text-center" style="display:none;">
                                            <div class="position-relative d-inline-block">
                                                <img src="" id="proofPreview" style="max-height: 250px;" class="img-fluid rounded-4 shadow-sm border p-1">
                                                <button type="button" class="btn btn-danger btn-sm rounded-circle position-absolute top-0 end-0 m-2" onclick="clearFile()">
                                                    <i class="bi bi-x"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-12 pt-3">
                                        <button type="submit" id="manual-submit-btn" class="btn btn-primary w-100 py-3 rounded-pill fw-bold shadow-sm">
                                            <i class="bi bi-send-fill me-2"></i> Konfirmasi Pembayaran Manual
                                        </button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- STEP 3: FORM GATEWAY --}}
                    <div class="card border-0 shadow-sm rounded-4 animate__animated animate__fadeInUp" id="gateway-form" style="display:none;">
                        <div class="card-body py-5 text-center px-5">
                            <div class="mb-4">
                                <div class="bg-light-primary p-4 rounded-circle d-inline-flex mb-4">
                                    <i class="bi bi-shield-check text-primary display-4"></i>
                                </div>
                                <h4 class="fw-bold">Pembayaran Digital Aman</h4>
                                <p class="text-muted">Pembayaran Anda akan diproses secara otomatis melalui gerbang pembayaran aman {{ ucfirst($gateway['provider']) }}.</p>
                            </div>
                            <button type="button" id="pay-button" class="btn btn-primary btn-lg w-100 py-3 rounded-pill shadow-sm fw-bold">
                                <i class="bi bi-credit-card-2-back me-2"></i> MULAI PEMBAYARAN ONLINE
                            </button>
                            <p class="smaller text-muted mt-3"><i class="bi bi-lock me-1"></i> Data transaksi terenkripsi dengan aman</p>
                        </div>
                    </div>
                </div>

                {{-- SIDEBAR: SUMMARY --}}
                <div class="col-12 col-lg-4">
                    <div class="card border-0 shadow-sm rounded-4 sticky-top" style="top: 2rem; z-index: 5;">
                        <div class="card-body p-4">
                            <h6 class="fw-bold mb-4">Ringkasan Pembayaran</h6>
                            
                            <div class="payment-summary mb-4">
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Periode SPP</span>
                                    <span class="fw-bold small" id="detail-month">{{ $selectedInvoice ? "Bulan {$selectedInvoice->month_name}" : '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between mb-3">
                                    <span class="text-muted small">Tahun Ajaran</span>
                                    <span class="fw-bold small" id="detail-year">{{ $selectedInvoice ? $selectedInvoice->year : '-' }}</span>
                                </div>
                                <div class="d-flex justify-content-between border-top pt-3">
                                    <span class="fw-bold small text-dark">Total Tagihan</span>
                                    <span class="fw-bold text-primary fs-5" id="detail-amount">{{ $selectedInvoice ? currency($selectedInvoice->amount) : 'Rp 0' }}</span>
                                </div>
                            </div>
                            
                            <div id="bank-info" style="{{ $selectedInvoice ? '' : 'display:none;' }}">
                                <div class="alert alert-light-secondary border-0 small mb-0">
                                    <p class="fw-bold text-dark mb-2 small text-uppercase letter-spacing-1">Rekening Sekolah:</p>
                                    <div class="bg-white p-3 rounded-3 border">
                                        <h6 class="mb-1 fw-bold text-dark small">{{ setting('bank_name') }}</h6>
                                        <h5 class="mb-1 text-primary fw-bold">{{ setting('bank_account') }}</h5>
                                        <p class="text-muted mb-0" style="font-size: 11px;">A.n {{ setting('bank_holder') }}</p>
                                    </div>
                                    <p class="smaller text-muted mt-3 mb-0">*Mohon gunakan nominal yang tepat hingga digit terakhir agar mudah diverifikasi.</p>
                                </div>
                            </div>

                            <div class="mt-4 text-center" id="empty-hint" style="{{ $selectedInvoice ? 'display:none;' : '' }}">
                                <p class="text-muted smaller">Silakan pilih tagihan terlebih dahulu untuk melihat detail pembayaran.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    </section>
</div>
@endsection

@section('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
@if($gateway['active'])
<script src="{{ $gateway['snap_url'] }}" data-client-key="{{ $gateway['client_key'] }}"></script>
@endif

<script>
    function updateInvoiceDetail(select) {
        const option = select.options[select.selectedIndex];
        if (!option.value) {
            document.getElementById('method-section').style.display = 'none';
            document.getElementById('manual-form').style.display = 'none';
            document.getElementById('gateway-form').style.display = 'none';
            document.getElementById('empty-hint').style.display = 'block';
            document.getElementById('bank-info').style.display = 'none';
            document.getElementById('step-2-icon').classList.remove('active');
            return;
        }

        const amount = option.getAttribute('data-amount');
        const month = option.getAttribute('data-month');
        const year = option.getAttribute('data-year');
        
        document.getElementById('detail-month').innerText = "Bulan " + month;
        document.getElementById('detail-year').innerText = year;
        document.getElementById('detail-amount').innerText = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(amount);
        document.getElementById('manual_invoice_id').value = option.value;
        
        document.getElementById('empty-hint').style.display = 'none';
        document.getElementById('method-section').style.display = 'block';
        document.getElementById('bank-info').style.display = 'block';
        document.getElementById('step-2-icon').classList.add('active');
    }

    function selectMethod(method) {
        // Update Step Icons
        document.getElementById('step-2-icon').classList.remove('active');
        document.getElementById('step-2-icon').classList.add('completed');
        document.getElementById('step-3-icon').classList.add('active');

        // Update Method Cards
        document.querySelectorAll('.method-card').forEach(el => el.classList.remove('selected'));
        document.getElementById('method-' + method).classList.add('selected');

        // Toggle visibility
        if (method === 'manual') {
            document.getElementById('manual-form').style.display = 'block';
            document.getElementById('gateway-form').style.display = 'none';
        } else {
            document.getElementById('manual-form').style.display = 'none';
            document.getElementById('gateway-form').style.display = 'block';
        }

        // Scroll to form
        window.scrollTo({
            top: document.getElementById('method-section').offsetTop + 100,
            behavior: 'smooth'
        });
    }

    function handleFileSelect(event) {
        const file = event.target.files[0];
        if (file) {
            document.getElementById('file-name').innerText = file.name;
            const reader = new FileReader();
            reader.onload = function(e){
                const output = document.getElementById('proofPreview');
                const container = document.getElementById('previewContainer');
                output.src = e.target.result;
                container.style.display = 'block';
            };
            reader.readAsDataURL(file);
        }
    }

    function clearFile() {
        document.getElementById('proof-input').value = '';
        document.getElementById('file-name').innerText = 'Belum ada file dipilih';
        document.getElementById('previewContainer').style.display = 'none';
    }

    @if($gateway['active'] && $gateway['provider'] === 'midtrans')
    document.getElementById('pay-button')?.addEventListener('click', function() {
        const invoiceId = document.getElementById('invoice_id').value;
        if (!invoiceId) return Swal.fire('Oops!', 'Silakan pilih tagihan terlebih dahulu.', 'warning');

        this.innerHTML = '<i class="spinner-border spinner-border-sm me-2"></i> Menyiapkan Gerbang Pembayaran...';
        this.disabled = true;

        fetch('{{ route("siswa.payments.getSnapToken") }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ invoice_id: invoiceId })
        })
        .then(response => response.json())
        .then(data => {
            if (data.status) {
                window.snap.pay(data.token, {
                    onSuccess: function(result) {
                        console.log('Payment Success:', result);
                        // Beri waktu 3 detik agar Midtrans sempat memproses transaksi
                        setTimeout(function() {
                            fetch('{{ route("siswa.payments.verify") }}', {
                                method: 'POST',
                                headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                                body: JSON.stringify({
                                    order_id: result.order_id,
                                    transaction_status: result.transaction_status,
                                    payment_type: result.payment_type,
                                    transaction_id: result.transaction_id,
                                    fraud_status: result.fraud_status
                                })
                            }).finally(() => {
                                window.location.href = '{{ route("siswa.payments.index") }}?success=1';
                            });
                        }, 3000);
                    },
                    onPending: function(result) {
                        window.location.href = '{{ route("siswa.payments.index") }}?pending=1';
                    },
                    onError: function(result) { alert("Pembayaran gagal!"); location.reload(); },
                    onClose: function() { location.reload(); }
                });
            } else {
                alert(data.message || 'Gagal memulai pembayaran.');
                location.reload();
            }
        });
    });
    @endif

    @if(session('success'))
    Swal.fire({
        icon: 'success',
        title: 'Upload Berhasil! 🎉',
        html: '<p class="mb-2">Bukti pembayaran Anda telah berhasil dikirim.</p><p class="text-muted small">Silakan tunggu verifikasi dari admin. Anda akan menerima notifikasi setelah pembayaran dikonfirmasi.</p>',
        confirmButtonText: 'Lihat Riwayat Pembayaran',
        confirmButtonColor: '#435ebe',
        showCancelButton: true,
        cancelButtonText: 'Tetap di Halaman Ini',
        cancelButtonColor: '#6c757d',
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '{{ route("siswa.payments.index") }}';
        }
    });
    @endif

    // ===================================================
    // AJAX Submit Form Manual Upload
    // ===================================================
    const manualForm = document.getElementById('manual-payment-form');
    if (manualForm) {
        manualForm.addEventListener('submit', function(e) {
            e.preventDefault();

            const btn = document.getElementById('manual-submit-btn');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Mengupload...';
            btn.disabled = true;

            const formData = new FormData(this);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Upload Berhasil! 🎉',
                        html: '<p class="mb-2">Bukti pembayaran Anda telah berhasil dikirim.</p><p class="text-muted small mb-0">Silakan tunggu verifikasi dari admin. Anda akan menerima notifikasi setelah pembayaran dikonfirmasi.</p>',
                        confirmButtonText: 'Lihat Riwayat Pembayaran',
                        confirmButtonColor: '#435ebe',
                        showCancelButton: true,
                        cancelButtonText: 'Upload Lagi',
                        cancelButtonColor: '#6c757d',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = '{{ route("siswa.payments.index") }}';
                        } else {
                            window.location.reload();
                        }
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Gagal!',
                        text: data.message || 'Terjadi kesalahan. Silakan coba lagi.',
                        confirmButtonColor: '#435ebe',
                    });
                    btn.innerHTML = originalText;
                    btn.disabled = false;
                }
            })
            .catch(error => {
                Swal.fire({
                    icon: 'error',
                    title: 'Gagal Upload!',
                    text: 'Terjadi kesalahan koneksi. Silakan coba lagi.',
                    confirmButtonColor: '#435ebe',
                });
                btn.innerHTML = originalText;
                btn.disabled = false;
            });
        });
    }
</script>
@endsection
