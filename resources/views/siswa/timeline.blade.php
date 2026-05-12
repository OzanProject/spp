@extends('siswa.layouts.master')
@section('title', 'Timeline Pembayaran')
@section('content')
<div class="page-heading">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="mb-0 text-primary fw-bold">Timeline Pembayaran</h3>
            <p class="text-muted small">Visualisasi status pembayaran SPP Anda per tahun.</p>
        </div>
        <form action="" method="GET" class="d-flex gap-2 align-items-center">
            <label class="small fw-bold text-muted mb-0">Tahun:</label>
            <select name="year" class="form-select form-select-sm" onchange="this.form.submit()">
                @foreach($years as $y)
                    <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                @endforeach
            </select>
        </form>
    </div>
</div>

<div class="page-content">
    <section class="section">
        <div class="card shadow-sm border-0">
            <div class="card-body py-5">
                <div class="row g-4 justify-content-center">
                    @foreach($months as $num => $name)
                    @php 
                        $invoice = $invoices->get($num);
                        $status = $invoice ? $invoice->status : 'none';
                        $color = 'secondary';
                        $icon = 'dash-circle';
                        
                        if($status == 'paid') { $color = 'success'; $icon = 'check-circle-fill'; }
                        elseif($status == 'pending') { $color = 'warning'; $icon = 'clock-fill'; }
                        elseif($status == 'overdue') { $color = 'danger'; $icon = 'exclamation-circle-fill'; }
                        elseif($status == 'unpaid') { $color = 'primary'; $icon = 'circle'; }
                    @endphp
                    <div class="col-6 col-md-4 col-lg-3">
                        <div class="p-3 rounded border-start border-4 border-{{ $color }} bg-light position-relative transition-hover overflow-hidden">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h6 class="fw-bold mb-0">{{ $name }}</h6>
                                <i class="bi bi-{{ $icon }} text-{{ $color }} fs-5"></i>
                            </div>
                            
                            @if($invoice)
                                <p class="mb-1 fw-bold text-dark">{{ currency($invoice->amount) }}</p>
                                <span class="badge bg-light-{{ $color }} text-{{ $color }} p-0 small" style="font-size: 10px;">
                                    {{ strtoupper($invoice->status_label) }}
                                </span>
                            @else
                                <p class="mb-1 text-muted small italic">Belum Diterbitkan</p>
                                <span class="badge bg-light-secondary text-secondary p-0 small" style="font-size: 10px;">-</span>
                            @endif
                            
                            {{-- Decorative Background Icon --}}
                            <i class="bi bi-calendar3 position-absolute" style="right: -10px; bottom: -10px; font-size: 60px; opacity: 0.05; transform: rotate(-15deg);"></i>
                        </div>
                    </div>
                    @endforeach
                </div>

                <div class="mt-5 pt-4 border-top">
                    <h6 class="fw-bold mb-3 text-dark"><i class="bi bi-info-circle me-2"></i>Keterangan Status:</h6>
                    <div class="d-flex flex-wrap gap-4 align-items-center">
                        <div class="d-flex align-items-center text-nowrap">
                            <i class="bi bi-check-circle-fill text-success fs-5 me-2" style="line-height: 1;"></i>
                            <span class="small fw-semibold">Lunas</span>
                        </div>
                        <div class="d-flex align-items-center text-nowrap">
                            <i class="bi bi-clock-fill text-warning fs-5 me-2" style="line-height: 1;"></i>
                            <span class="small fw-semibold">Verifikasi</span>
                        </div>
                        <div class="d-flex align-items-center text-nowrap">
                            <i class="bi bi-exclamation-circle-fill text-danger fs-5 me-2" style="line-height: 1;"></i>
                            <span class="small fw-semibold">Terlambat / Ditolak</span>
                        </div>
                        <div class="d-flex align-items-center text-nowrap">
                            <i class="bi bi-circle text-primary fs-5 me-2" style="line-height: 1;"></i>
                            <span class="small fw-semibold">Belum Bayar</span>
                        </div>
                        <div class="d-flex align-items-center text-nowrap">
                            <i class="bi bi-dash-circle text-secondary fs-5 me-2" style="line-height: 1;"></i>
                            <span class="small fw-semibold">Belum Tersedia</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>

<style>
    .bg-light-success { background-color: rgba(25, 135, 84, 0.1); }
    .bg-light-warning { background-color: rgba(255, 193, 7, 0.1); }
    .bg-light-danger { background-color: rgba(220, 53, 69, 0.1); }
    .bg-light-primary { background-color: rgba(67, 94, 190, 0.1); }
    .transition-hover:hover {
        transform: scale(1.02);
        box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }
</style>
@endsection
