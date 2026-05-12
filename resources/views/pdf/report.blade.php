<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan SPP {{ $monthName }} {{ $year }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 20px; }
        .header { display: table; width: 100%; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 10px; margin-bottom: 20px; }
        .header-left { display: table-cell; vertical-align: middle; width: 60px; }
        .header-right { display: table-cell; vertical-align: middle; text-align: left; }
        .header-date { display: table-cell; vertical-align: top; text-align: right; font-size: 12px; color: #666; }
        .logo { width: 50px; height: 50px; object-fit: contain; }
        .school-name { font-size: 18px; font-weight: bold; color: {{ $primaryColor }}; margin: 0; }
        .school-info { font-size: 12px; color: #666; margin: 5px 0 0; }
        .title { text-align: center; font-size: 16px; font-weight: bold; margin-bottom: 20px; text-transform: uppercase; }
        
        .summary-box { width: 100%; display: table; margin-bottom: 20px; }
        .summary-item { display: table-cell; padding: 10px; background: #f8f9fa; border: 1px solid #e9ecef; text-align: center; width: 25%; }
        .summary-label { font-size: 11px; color: #666; margin-bottom: 5px; text-transform: uppercase; }
        .summary-value { font-size: 16px; font-weight: bold; color: {{ $primaryColor }}; }
        
        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th, .data-table td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        .data-table th { background-color: {{ $primaryColor }}; color: white; font-weight: bold; }
        .data-table tbody tr:nth-child(even) { background-color: #f9f9f9; }
        
        .text-right { text-align: right !important; }
        .text-center { text-align: center !important; }
        
        .badge { padding: 3px 6px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .bg-success { background-color: #d1e7dd; color: #0f5132; }
        .bg-danger { background-color: #f8d7da; color: #842029; }
        .bg-warning { background-color: #fff3cd; color: #664d03; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(setting('school_logo'))
                <img src="{{ public_path('storage/logos/' . setting('school_logo')) }}" class="logo" alt="Logo">
            @else
                <div style="width: 50px; height: 50px; background: {{ $primaryColor }}; border-radius: 50%; display: inline-block; text-align: center; line-height: 50px; color: white; font-weight: bold; font-size: 24px;">{{ substr($schoolName, 0, 1) }}</div>
            @endif
        </div>
        <div class="header-right">
            <h1 class="school-name">{{ $schoolName }}</h1>
            <p class="school-info">
                {{ $schoolAddress }}<br>
                Telp: {{ $schoolPhone }}
            </p>
        </div>
        <div class="header-date">
            Dicetak: {{ now()->translatedFormat('d M Y H:i') }} {{ $timezoneAbbr }}
        </div>
    </div>

    <div class="title">Laporan Penerimaan SPP<br>Bulan {{ $monthName }} {{ $year }}</div>

    <div class="summary-box">
        <div class="summary-item">
            <div class="summary-label">Total Pemasukan</div>
            <div class="summary-value">Rp {{ number_format($totalPaid, 0, ',', '.') }}</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Lunas</div>
            <div class="summary-value" style="color: #198754;">{{ $invoices->where('status', 'paid')->count() }} Siswa</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Belum Lunas</div>
            <div class="summary-value" style="color: #ffc107;">{{ $totalUnpaid }} Siswa</div>
        </div>
        <div class="summary-item">
            <div class="summary-label">Menunggak (Overdue)</div>
            <div class="summary-value" style="color: #dc3545;">{{ $totalOverdue }} Siswa</div>
        </div>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th width="5%" class="text-center">No</th>
                <th width="25%">Nama Siswa</th>
                <th width="15%">NIS</th>
                <th width="15%">Kelas</th>
                <th width="15%" class="text-right">Nominal</th>
                <th width="15%" class="text-center">Status</th>
                <th width="10%" class="text-center">Jatuh Tempo</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $index => $invoice)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>{{ $invoice->student->user->name ?? '-' }}</td>
                    <td>{{ $invoice->student->nis }}</td>
                    <td>{{ $invoice->student->classRoom->name ?? '-' }}</td>
                    <td class="text-right">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</td>
                    <td class="text-center">
                        @if($invoice->status == 'paid')
                            <span class="badge bg-success">LUNAS</span>
                        @elseif($invoice->status == 'unpaid')
                            <span class="badge bg-warning">BELUM LUNAS</span>
                        @else
                            <span class="badge bg-danger">MENUNGGAK</span>
                        @endif
                    </td>
                    <td class="text-center">{{ $invoice->due_date->format('d/m/Y') }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" class="text-center py-3">Tidak ada data tagihan untuk periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

</body>
</html>
