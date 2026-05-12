@extends('backend.layouts.master')
@section('title', 'Laporan Keuangan')
@section('styles')
<link rel="stylesheet" href="https://zuramai.github.io/mazer/demo/assets/compiled/css/iconly.css">
@endsection
@section('content')
<div class="page-heading">
    <div class="page-title">
        <div class="row">
            <div class="col-12 col-md-6 order-md-1 order-last">
                <h3>Laporan Keuangan</h3>
                <p class="text-subtitle text-muted">Rekap pemasukan SPP per tahun ajaran.</p>
            </div>
            <div class="col-12 col-md-6 order-md-2 order-first">
                <nav aria-label="breadcrumb" class="breadcrumb-header float-start float-lg-end">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                        <li class="breadcrumb-item active">Laporan Keuangan</li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>

    <section class="section">
        {{-- Filter Tahun & Export --}}
        <div class="d-flex justify-content-between align-items-center mb-3">
            <form method="GET" class="d-flex gap-2 align-items-center">
                <label class="fw-semibold mb-0">Tahun:</label>
                <select name="year" class="form-select" style="width:130px;" onchange="this.form.submit()">
                    @foreach($years as $y)
                        <option value="{{ $y }}" {{ $y == $year ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
            </form>
            <a href="{{ route('admin.pdf.report', ['year' => $year, 'month' => date('n')]) }}" target="_blank" class="btn btn-danger">
                <i class="bi bi-file-pdf me-1"></i> Cetak Laporan
            </a>
        </div>

        {{-- Stat cards --}}
        <div class="row">
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon blue mb-2">
                                    <i class="iconly-boldWallet"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold" style="font-size: 0.8rem">Total Pemasukan {{ $year }}</h6>
                                <h6 class="font-extrabold mb-0">{{ currency($stats['total_income']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon green mb-2">
                                    <i class="iconly-boldTick-Square"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold" style="font-size: 0.8rem">Tagihan Lunas</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($stats['total_paid']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon yellow mb-2">
                                    <i class="iconly-boldTime-Circle"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold" style="font-size: 0.8rem">Belum Bayar</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($stats['total_unpaid']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-6 col-lg-3 col-md-6">
                <div class="card">
                    <div class="card-body px-4 py-4-5">
                        <div class="row">
                            <div class="col-md-4 col-lg-12 col-xl-12 col-xxl-5 d-flex justify-content-start ">
                                <div class="stats-icon red mb-2">
                                    <i class="iconly-boldDanger"></i>
                                </div>
                            </div>
                            <div class="col-md-8 col-lg-12 col-xl-12 col-xxl-7">
                                <h6 class="text-muted font-semibold" style="font-size: 0.8rem">Jatuh Tempo</h6>
                                <h6 class="font-extrabold mb-0">{{ number_format($stats['total_overdue']) }}</h6>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Grafik --}}
        <div class="card">
            <div class="card-header"><h5 class="card-title mb-0">Grafik Pemasukan Bulanan {{ $year }}</h5></div>
            <div class="card-body">
                <div id="incomeChart"></div>
            </div>
        </div>

        {{-- Tabel bulanan & Data Siswa --}}
        <div class="row">
            <div class="col-12 col-lg-6">
                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <h5 class="card-title mb-0">Rincian Per Bulan</h5>
                        <span class="badge bg-primary">Tahun {{ $year }}</span>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr><th>Bulan</th><th class="text-end">Pemasukan</th></tr>
                                </thead>
                                <tbody>
                                    @php $grandTotal = 0; @endphp
                                    @foreach($months as $num => $name)
                                    @php $amount = $monthly[$num] ?? 0; $grandTotal += $amount; @endphp
                                    <tr>
                                        <td>{{ $name }}</td>
                                        <td class="text-end {{ $amount > 0 ? 'fw-semibold text-success' : 'text-muted' }}">
                                            Rp {{ number_format($amount, 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                                <tfoot class="table-light">
                                    <tr>
                                        <td class="fw-bold ps-3">Total Tahunan</td>
                                        <td class="text-end fw-bold text-primary pe-3">Rp {{ number_format($grandTotal, 0, ',', '.') }}</td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-12 col-lg-6">
                <div class="row">
                    <div class="col-12">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light-success py-3 d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0 text-success"><i class="bi bi-check-circle-fill me-2"></i>10 Siswa Lunas Terbaru</h5>
                            </div>
                            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                                @forelse($paidStudents as $invoice)
                                <div class="d-flex align-items-center px-4 py-3 border-bottom hover-bg-light">
                                    <div class="avatar avatar-md me-3">
                                        @if($invoice->student->user->photo)
                                            <img src="{{ Storage::url('photos/' . $invoice->student->user->photo) }}" class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="avatar-content bg-success text-white d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width: 40px; height: 40px; font-size: 14px;">
                                                {{ strtoupper(substr($invoice->student->user->name ?? 'S', 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold small text-dark">{{ $invoice->student->user->name ?? '-' }}</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px;">{{ $invoice->student->classRoom->name ?? '-' }} &bull; {{ $invoice->month_name }}</p>
                                    </div>
                                    <div class="text-end">
                                        <span class="badge bg-light-success text-success" style="font-size: 10px;">{{ currency($invoice->amount) }}</span>
                                        <p class="text-muted mb-0" style="font-size: 9px;">{{ $invoice->payment->paid_at->format('d/m/y') }}</p>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted small">Belum ada data pembayaran lunas.</div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                    <div class="col-12 mt-3">
                        <div class="card shadow-sm border-0">
                            <div class="card-header bg-light-danger py-3">
                                <h5 class="card-title mb-0 text-danger"><i class="bi bi-exclamation-triangle-fill me-2"></i>10 Siswa Menunggak</h5>
                            </div>
                            <div class="card-body p-0" style="max-height: 400px; overflow-y: auto;">
                                @forelse($debtors as $invoice)
                                <div class="d-flex align-items-center px-4 py-3 border-bottom">
                                    <div class="avatar avatar-md me-3">
                                        <div class="avatar-content bg-danger text-white d-flex align-items-center justify-content-center fw-bold rounded-circle" style="width: 40px; height: 40px; font-size: 14px;">
                                            {{ strtoupper(substr($invoice->student->user->name ?? 'X', 0, 1)) }}
                                        </div>
                                    </div>
                                    <div class="flex-grow-1">
                                        <h6 class="mb-0 fw-bold small text-dark">{{ $invoice->student->user->name ?? '-' }}</h6>
                                        <p class="text-muted mb-0" style="font-size: 11px;">{{ $invoice->student->classRoom->name ?? '-' }} &bull; {{ $invoice->month_name }}</p>
                                    </div>
                                    <div>
                                        <span class="badge bg-light-danger text-danger border-danger border" style="font-size: 10px;">{{ $invoice->status_label }}</span>
                                    </div>
                                </div>
                                @empty
                                <div class="text-center py-5 text-muted small">Tidak ada tunggakan 🎉</div>
                                @endforelse
                            </div>
                        </div>
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
    var options = {
        annotations: {
            position: 'back'
        },
        dataLabels: {
            enabled: false
        },
        chart: {
            type: 'bar',
            height: 300,
            toolbar: {
                show: false
            }
        },
        fill: {
            opacity: 1
        },
        plotOptions: {
            bar: {
                borderRadius: 4,
            }
        },
        series: [{
            name: 'Pemasukan (Rp)',
            data: {!! json_encode(array_values($monthly)) !!}
        }],
        colors: [getComputedStyle(document.documentElement).getPropertyValue('--color-primary-custom').trim() || '#435ebe'],
        xaxis: {
            categories: {!! json_encode(array_values(\App\Models\Invoice::MONTHS)) !!},
        },
        yaxis: {
            labels: {
                formatter: function (value) {
                    return "Rp " + new Intl.NumberFormat('id-ID').format(value);
                }
            }
        }
    };
    
    var chart = new ApexCharts(document.querySelector("#incomeChart"), options);
    chart.render();
</script>
@endsection
