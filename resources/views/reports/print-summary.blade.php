<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cetak Rekap Karyawan (Payroll) - {{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMM Y') }} s/d {{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMM Y') }}</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            font-size: 12pt;
            color: #333;
            margin: 0;
            padding: 20px;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
            border-bottom: 2px solid #333;
            padding-bottom: 15px;
        }
        .header h1 {
            margin: 0;
            font-size: 20pt;
            text-transform: uppercase;
        }
        .header p {
            margin: 5px 0 0 0;
            font-size: 12pt;
        }
        .summary-box {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            padding: 15px;
            background-color: #f8f9fa;
            border: 1px solid #ddd;
        }
        .summary-item {
            text-align: center;
            flex: 1;
        }
        .summary-item h4 {
            margin: 0 0 5px 0;
            font-size: 10pt;
            color: #555;
            text-transform: uppercase;
        }
        .summary-item p {
            margin: 0;
            font-size: 14pt;
            font-weight: bold;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #333;
            padding: 8px 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
            font-weight: bold;
            text-align: center;
            text-transform: uppercase;
            font-size: 10pt;
        }
        td.text-center { text-align: center; }
        td.text-right { text-align: right; }
        .text-muted { color: #666; font-size: 9pt; }
        .signature-area {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
        }
        .signature-box {
            width: 250px;
            text-align: center;
        }
        .signature-line {
            margin-top: 70px;
            border-bottom: 1px solid #333;
        }
        @media print {
            body { padding: 0; }
            .no-print { display: none !important; }
            .summary-box { border: 1px solid #333; background-color: transparent; }
            th { background-color: transparent !important; -webkit-print-color-adjust: exact; }
        }
    </style>
</head>
<body onload="window.print()">

    <div class="no-print" style="margin-bottom: 20px; text-align: right;">
        <button onclick="window.print()" style="padding: 10px 20px; background: #2563eb; color: white; border: none; border-radius: 5px; cursor: pointer; font-weight: bold;">Print Dokumen</button>
        <button onclick="window.close()" style="padding: 10px 20px; background: #6b7280; color: white; border: none; border-radius: 5px; cursor: pointer; margin-left: 10px;">Tutup</button>
    </div>

    <div class="header">
        <h1>Laporan Rekapitulasi Gaji & Bonus Kehadiran</h1>
        <p>Periode: <strong>{{ \Carbon\Carbon::parse($startDate)->isoFormat('D MMMM Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($endDate)->isoFormat('D MMMM Y') }}</strong></p>
    </div>

    <div class="summary-box">
        <div class="summary-item">
            <h4>Total Karyawan Aktif</h4>
            <p>{{ count($summaries) }} Orang</p>
        </div>
        <div class="summary-item">
            <h4>Total Keterlambatan</h4>
            <p>{{ number_format($totalTerlambat, 0, ',', '.') }} Menit</p>
        </div>
        <div class="summary-item">
            <h4>Total Pencairan Bonus</h4>
            <p>Rp {{ number_format($totalBonus, 0, ',', '.') }}</p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 5%;">No</th>
                <th style="width: 25%;">Nama Karyawan</th>
                <th style="width: 15%;">Total Hari Masuk</th>
                <th style="width: 15%;">Tepat Waktu</th>
                <th style="width: 20%;">Terlambat</th>
                <th style="width: 20%;">Total Bonus</th>
            </tr>
        </thead>
        <tbody>
            @forelse($summaries as $index => $summary)
                <tr>
                    <td class="text-center">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $summary['user']->name }}</strong><br>
                        <span class="text-muted">{{ $summary['user']->jabatan ?? 'Karyawan' }}</span>
                    </td>
                    <td class="text-center">{{ $summary['total_hari_kerja'] }} Hari</td>
                    <td class="text-center">{{ $summary['total_hadir_tepat'] }} Hari</td>
                    <td class="text-center">
                        @if($summary['total_terlambat'] > 0)
                            {{ $summary['total_terlambat'] }} Hari<br>
                            <span class="text-muted">({{ $summary['total_menit_terlambat'] }} mnt)</span>
                        @else
                            -
                        @endif
                    </td>
                    <td class="text-right">
                        @if($summary['total_bonus'] > 0)
                            <strong>Rp {{ number_format($summary['total_bonus'], 0, ',', '.') }}</strong>
                        @else
                            Rp 0
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="6" class="text-center">Tidak ada data absensi pada periode ini.</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="signature-area">
        <div class="signature-box">
            <p>Dibuat Oleh,</p>
            <div class="signature-line"></div>
            <p>( HRD / Admin )</p>
        </div>
        <div class="signature-box">
            <p>Menyetujui,</p>
            <div class="signature-line"></div>
            <p>( Pimpinan / Manajer )</p>
        </div>
    </div>

</body>
</html>
