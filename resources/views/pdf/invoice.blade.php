<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Kwitansi Pembayaran - {{ $invoiceNo }}</title>
    <style>
        body { font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif; font-size: 12px; color: #333; margin: 0; padding: 10px; line-height: 1.4; }
        .header { display: table; width: 100%; border-bottom: 2px solid {{ $primaryColor }}; padding-bottom: 10px; margin-bottom: 15px; }
        .header-left { display: table-cell; vertical-align: middle; width: 50px; }
        .header-right { display: table-cell; vertical-align: middle; text-align: right; }
        .logo { width: 45px; height: 45px; object-fit: contain; }
        .school-name { font-size: 16px; font-weight: bold; color: {{ $primaryColor }}; margin: 0; text-transform: uppercase; }
        .school-info { font-size: 10px; color: #555; margin: 3px 0 0; }
        .title { text-align: center; font-size: 14px; font-weight: bold; margin-bottom: 15px; text-transform: uppercase; letter-spacing: 2px; text-decoration: underline; }
        
        .content-wrapper { border: 1px solid #ddd; padding: 15px; border-radius: 4px; }
        .info-table { width: 100%; border-collapse: collapse; margin-bottom: 15px; }
        .info-table td { padding: 4px 0; vertical-align: top; }
        .info-table .label { width: 130px; color: #555; font-weight: normal; }
        .info-table .separator { width: 10px; text-align: center; }
        
        .amount-row { display: table; width: 100%; margin-top: 15px; padding-top: 15px; border-top: 1px dashed #ccc; }
        .amount-box { display: table-cell; background-color: #f8f9fa; border: 1px solid #e9ecef; padding: 10px 15px; text-align: left; border-radius: 4px; width: 60%; }
        .amount-title { font-size: 11px; color: #666; text-transform: uppercase; letter-spacing: 1px; margin-bottom: 3px; }
        .amount-value { font-size: 20px; font-weight: bold; color: {{ $primaryColor }}; margin: 0; }
        
        .status-badge { display: inline-block; padding: 3px 8px; border-radius: 3px; font-size: 10px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; border: 1px solid; }
        .status-paid { background-color: #e6f4ea; color: #1e8e3e; border-color: #1e8e3e; }
        .status-unpaid { background-color: #fce8e6; color: #d93025; border-color: #d93025; }
        
        .footer { margin-top: 30px; display: table; width: 100%; }
        .footer-left { display: table-cell; width: 60%; font-size: 10px; color: #666; vertical-align: bottom; }
        .footer-right { display: table-cell; width: 40%; text-align: center; vertical-align: bottom; }
        .signature-space { height: 60px; }
        .date { margin-bottom: 5px; font-size: 11px; }
        .name { font-weight: bold; text-decoration: underline; font-size: 11px; }
        .role { font-size: 11px; color: #555; }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-left">
            @if(setting('school_logo'))
                <img src="{{ public_path('storage/logos/' . setting('school_logo')) }}" class="logo" alt="Logo">
            @else
                <div style="width: 45px; height: 45px; background: {{ $primaryColor }}; border-radius: 50%; display: inline-block; text-align: center; line-height: 45px; color: white; font-weight: bold; font-size: 20px;">{{ substr($schoolName, 0, 1) }}</div>
            @endif
        </div>
        <div class="header-right">
            <h1 class="school-name">{{ $schoolName }}</h1>
            <p class="school-info">
                {{ $schoolAddress }}<br>
                Telp: {{ $schoolPhone }} &bull; Email: {{ $schoolEmail }}
            </p>
        </div>
    </div>

    <div class="title">Kwitansi Pembayaran SPP</div>

    <div class="content-wrapper">
        <table class="info-table">
            <tr>
                <td class="label">No. Kwitansi</td>
                <td class="separator">:</td>
                <td style="width: 40%;"><strong>{{ $invoiceNo }}</strong></td>
                <td class="label" style="width: 90px;">Tanggal Cetak</td>
                <td class="separator">:</td>
                <td>{{ now()->translatedFormat('d M Y') }}</td>
            </tr>
            <tr>
                <td class="label">Telah terima dari</td>
                <td class="separator">:</td>
                <td colspan="4"><strong>{{ $invoice->student->user->name ?? '-' }}</strong> <span style="color:#666; font-size: 10px;">(NIS: {{ $invoice->student->nis }})</span></td>
            </tr>
            <tr>
                <td class="label">Kelas</td>
                <td class="separator">:</td>
                <td colspan="4">{{ $invoice->student->classRoom->name ?? '-' }}</td>
            </tr>
            <tr>
                <td class="label">Untuk Pembayaran</td>
                <td class="separator">:</td>
                <td colspan="4">SPP Bulan <strong>{{ $invoice->month_name }} {{ $invoice->year }}</strong></td>
            </tr>
            <tr>
                <td class="label">Status Tagihan</td>
                <td class="separator">:</td>
                <td colspan="4">
                    <span class="status-badge {{ $invoice->status == 'paid' ? 'status-paid' : 'status-unpaid' }}">
                        {{ $invoice->status == 'paid' ? 'LUNAS' : 'BELUM LUNAS' }}
                    </span>
                    @if($invoice->status == 'paid' && $invoice->payment)
                        <span style="margin-left: 8px; color: #666; font-size: 10px;">(Tgl Bayar: {{ $invoice->payment->paid_at->format('d/m/Y H:i') }} {{ $timezoneAbbr }})</span>
                    @endif
                </td>
            </tr>
        </table>

        <div class="amount-row">
            <div class="amount-box">
                <div class="amount-title">Uang Sejumlah</div>
                <div class="amount-value">Rp {{ number_format($invoice->amount, 0, ',', '.') }}</div>
            </div>
        </div>
    </div>

    <div class="footer">
        <div class="footer-left">
            @if($bankName && $invoice->status != 'paid')
                <div style="border: 1px dashed #ccc; padding: 8px; display: inline-block; background: #fff;">
                    <strong>Info Transfer:</strong><br>
                    Bank {{ $bankName }}<br>
                    Rek: <strong>{{ $bankAccount }}</strong><br>
                    A.N: {{ $bankHolder }}
                </div>
            @endif
            <div style="margin-top: 10px;">
                <i>* Kwitansi ini sah jika telah ditandatangani dan dicap oleh petugas.</i><br>
                <i>* Simpan sebagai bukti pembayaran yang sah.</i>
            </div>
        </div>
        <div class="footer-right">
            <div class="date">{{ setting('school_address', 'Jakarta') }}, {{ now()->translatedFormat('d F Y') }}</div>
            <div class="role">Bendahara Sekolah</div>
            <div class="signature-space"></div>
            <div class="name">(__________________________)</div>
        </div>
    </div>
</body>
</html>
