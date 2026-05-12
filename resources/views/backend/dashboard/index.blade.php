@extends('backend.layouts.master')

@section('title', 'Admin Dashboard')

@section('styles')
<link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/iconly.css">
<style>
    .bg-primary-light { background-color: rgba(67, 94, 190, 0.1); }
    .bg-success-light { background-color: rgba(25, 135, 84, 0.1); }
    .bg-danger-light { background-color: rgba(220, 53, 69, 0.1); }
    .bg-info-light { background-color: rgba(13, 202, 240, 0.1); }
    
    .transition-up { transition: transform 0.3s ease; }
    .transition-up:hover { transform: translateY(-5px); }
    
    .stats-card {
        border: none;
        border-radius: 1.25rem;
        transition: all 0.3s ease;
    }
    
    .letter-spacing-1 { letter-spacing: 1px; }
    
    .chart-card {
        border-radius: 1.5rem;
        border: none;
        box-shadow: 0 10px 30px rgba(0,0,0,0.03);
    }
    
    .transaction-item {
        transition: all 0.2s ease;
        border-left: 4px solid transparent;
    }
    .transaction-item:hover {
        background-color: #f8f9fa;
        border-left-color: var(--bs-primary);
    }
    
    .btn-gradient {
        background: linear-gradient(to right, #435ebe, #6366f1);
        color: #fff;
        border: none;
        border-radius: 0.75rem;
        padding: 0.75rem 1.5rem;
        transition: all 0.3s;
    }
    .btn-gradient:hover {
        transform: scale(1.02);
        box-shadow: 0 8px 15px rgba(67, 94, 190, 0.2);
        color: #fff;
    }
</style>
@endsection

@section('content')
<div class="page-heading mb-4">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h3 class="fw-bold text-primary mb-1">Dashboard Admin SPP</h3>
            <p class="text-muted small mb-0">Pantau data siswa dan arus keuangan sekolah secara real-time.</p>
        </div>
        <div class="d-none d-md-block text-end">
            <div class="d-inline-flex align-items-center bg-white shadow-sm px-3 py-2 rounded-pill border">
                <div class="bg-primary-light rounded-circle p-2 me-2 d-flex align-items-center justify-content-center" style="width: 32px; height: 32px;">
                    <i class="bi bi-calendar-event text-primary small"></i>
                </div>
                <span class="fw-bold small">{{ \Carbon\Carbon::now()->translatedFormat('d F Y') }}</span>
            </div>
        </div>
    </div>
</div> 

<div class="page-content"> 
    <section class="row">
        <div class="col-12 col-lg-9">
            {{-- Stats Row --}}
            <div class="row g-4 mb-4">
                {{-- Stat 1: Total Siswa --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stats-card border-0 shadow-sm overflow-hidden h-100 transition-up">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-primary-light p-3 rounded-3 text-primary">
                                    <i class="bi bi-people fs-4"></i>
                                </div>
                                <span class="badge bg-light-primary text-primary rounded-pill px-2">Data Master</span>
                            </div>
                            <h6 class="text-muted small fw-bold mb-1 text-uppercase letter-spacing-1">Total Siswa</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($totalSiswa) }}</h4>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-primary" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                {{-- Stat 2: Pemasukan --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stats-card border-0 shadow-sm overflow-hidden h-100 transition-up">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-info-light p-3 rounded-3 text-info">
                                    <i class="bi bi-wallet2 fs-4"></i>
                                </div>
                                <span class="badge bg-light-info text-info rounded-pill px-2">Bulan Ini</span>
                            </div>
                            <h6 class="text-muted small fw-bold mb-1 text-uppercase letter-spacing-1">Pemasukan</h6>
                            <h4 class="fw-bold mb-0">{{ currency($pemasukanBulanIni) }}</h4>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-info" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                {{-- Stat 3: SPP Lunas --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stats-card border-0 shadow-sm overflow-hidden h-100 transition-up">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-success-light p-3 rounded-3 text-success">
                                    <i class="bi bi-check-circle fs-4"></i>
                                </div>
                                <span class="badge bg-light-success text-success rounded-pill px-2">Kolektif</span>
                            </div>
                            <h6 class="text-muted small fw-bold mb-1 text-uppercase letter-spacing-1">SPP Lunas</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($lunasBulanIni) }}</h4>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-success" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                {{-- Stat 4: Menunggak --}}
                <div class="col-12 col-sm-6 col-lg-3">
                    <div class="card stats-card border-0 shadow-sm overflow-hidden h-100 transition-up">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="bg-danger-light p-3 rounded-3 text-danger">
                                    <i class="bi bi-exclamation-triangle fs-4"></i>
                                </div>
                                <span class="badge bg-light-danger text-danger rounded-pill px-2">Penting</span>
                            </div>
                            <h6 class="text-muted small fw-bold mb-1 text-uppercase letter-spacing-1">Menunggak</h6>
                            <h4 class="fw-bold mb-0">{{ number_format($menunggak) }}</h4>
                        </div>
                        <div class="progress rounded-0" style="height: 4px;">
                            <div class="progress-bar bg-danger" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Chart --}}
            <div class="row">
                <div class="col-12">
                    <div class="card chart-card border-0 shadow-sm">
                        <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center pt-4 px-4 pb-0">
                            <div>
                                <h5 class="fw-bold mb-0">Tren Pemasukan SPP</h5>
                                <p class="text-muted small mb-0">Perbandingan pendapatan bulanan di tahun {{ $year }}</p>
                            </div>
                        </div>
                        <div class="card-body p-4">
                            <div id="chart-income"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 col-lg-3">
            {{-- Admin Info --}}
            <div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
                <div class="card-body p-4 text-center">
                    <div class="position-relative d-inline-block mb-3">
                        @if(Auth::user()->photo)
                            <img src="{{ Storage::url('photos/' . Auth::user()->photo) }}" 
                                 class="rounded-circle border border-4 border-light shadow-sm" 
                                 style="width: 100px; height: 100px; object-fit: cover;">
                        @else
                            <div class="rounded-circle border border-4 border-light shadow-sm bg-primary text-white d-flex align-items-center justify-content-center fw-bold" 
                                 style="width: 100px; height: 100px; font-size: 2.5rem;">
                                {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                            </div>
                        @endif
                        <span class="position-absolute bottom-0 end-0 bg-success border border-white rounded-circle" style="width: 20px; height: 20px;"></span>
                    </div>
                    <h5 class="fw-bold mb-1">{{ Auth::user()->name }}</h5>
                    <span class="badge bg-light-primary text-primary rounded-pill px-3 py-2 small">Administrator</span>
                    
                    <div class="row g-0 border-top mt-4 pt-4">
                        <div class="col-12">
                            <a href="{{ route('admin.profile.edit') }}" class="btn btn-light w-100 rounded-pill small fw-bold">
                                <i class="bi bi-gear me-1"></i> Pengaturan Akun
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Recent Activity --}}
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-4 px-4 border-0 d-flex justify-content-between align-items-center">
                    <h6 class="fw-bold mb-0">Transaksi Terbaru</h6>
                    <a href="{{ route('admin.payments.index') }}" class="btn btn-sm btn-light-primary rounded-pill px-2">
                        <i class="bi bi-arrow-right"></i>
                    </a>
                </div>
                <div class="card-body p-0">
                    <div class="transaction-list">
                        @forelse($recentPayments as $payment)
                        <div class="transaction-item d-flex align-items-center px-4 py-3 border-bottom">
                            <div class="bg-light-primary rounded-circle p-2 me-3 d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                <span class="fw-bold text-primary small">
                                    {{ strtoupper(substr($payment->invoice->student->user->name ?? 'A', 0, 1)) }}
                                </span>
                            </div>
                            <div class="flex-grow-1 min-width-0">
                                <h6 class="mb-0 text-truncate fw-bold small text-dark">{{ $payment->invoice->student->user->name ?? '-' }}</h6>
                                <div class="d-flex justify-content-between mt-1">
                                    <span class="smaller text-muted" style="font-size: 11px;">{{ $payment->invoice->month_name }} {{ $payment->invoice->year }}</span>
                                    <span class="fw-bold text-primary" style="font-size: 11px;">{{ currency($payment->invoice->amount) }}</span>
                                </div>
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-5">
                            <i class="bi bi-inbox text-muted display-4 opacity-25"></i>
                            <p class="text-muted small mt-2">Belum ada transaksi.</p>
                        </div>
                        @endforelse
                    </div>
                    
                    <div class="p-4">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-gradient w-100 fw-bold small">
                            Lihat Semua Transaksi
                        </a>
                    </div>
                </div>
            </div> 
        </div>
    </section>
</div>
@endsection

@section('scripts')
<script src="https://zuramai.github.io/mazer/demo/assets/extensions/apexcharts/apexcharts.min.js"></script>
<script>
    var optionsIncome = {
        series: [{
            name: 'Pemasukan',
            data: {!! json_encode($chartData) !!}
        }],
        chart: {
            height: 350,
            type: 'area',
            toolbar: { show: false },
            zoom: { enabled: false }
        },
        dataLabels: { enabled: false },
        stroke: { curve: 'smooth', width: 3 },
        colors: ['#435ebe'],
        fill: {
            type: 'gradient',
            gradient: {
                shadeIntensity: 1,
                opacityFrom: 0.45,
                opacityTo: 0.05,
                stops: [20, 100, 100, 100]
            }
        },
        xaxis: {
            categories: ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agt", "Sep", "Okt", "Nov", "Des"],
            axisBorder: { show: false },
            axisTicks: { show: false }
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        },
        grid: {
            borderColor: '#f1f1f1',
            strokeDashArray: 4
        },
        tooltip: {
            y: {
                formatter: function (val) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(val);
                }
            }
        }
    }
    
    var chartIncome = new ApexCharts(document.querySelector("#chart-income"), optionsIncome);
    chartIncome.render();
</script>
@endsection
