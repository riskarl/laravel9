<!DOCTYPE html>
<html>
<head>
    <title>Laporan Anggaran</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            line-height: 1.6;
        }
        h2 {
            text-align: center;
            font-size: 24px;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
            padding: 12px;
            text-align: center;
        }
        td {
            padding: 10px;
            text-align: left;
        }
        .summary-table {
            width: 100%;
            margin-top: 20px;
            border: none;
        }
        .summary-table th, .summary-table td {
            padding: 10px;
            text-align: left;
            border: none;
        }
        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
            margin-top: 40px;
        }
    </style>
</head>
<body>
    <h2>Rekapitulasi Anggaran</h2>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Dana Diajukan (Rp)</th>
                <th>Dana Disetujui (Rp)</th>
                <th>Sisa Anggaran Organisasi (Rp)</th>
                <th>Total Sisa Anggaran (Rp)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($anggaran as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item['nama_organisasi'] }}</td>
                    <td>{{ $item['nama_proker'] }}</td>
                    <td>{{ number_format($item['dana_diajukan'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['dana_disetujui'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['sisa_anggaran'], 0, ',', '.') }}</td>
                    <td>{{ number_format($item['total_sisa_anggaran'], 0, ',', '.') }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <h3>Keterangan Anggaran</h3>
    <table class="summary-table">
        <tr>
            <th>Total Anggaran Periode:</th>
            <td>Rp {{ number_format($ketAnggaran['total_anggaran_periode'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Total Anggaran Disetujui:</th>
            <td>Rp {{ number_format($ketAnggaran['total_anggaran_disetujui'], 0, ',', '.') }}</td>
        </tr>
        <tr>
            <th>Sisa Anggaran Periode:</th>
            <td>Rp {{ number_format($ketAnggaran['sisa_anggaran_periode'], 0, ',', '.') }}</td>
        </tr>
    </table>

    <div class="footer">
        <p>Laporan ini dihasilkan oleh sistem pada {{ now()->format('d M Y') }}.</p>
        <p>&copy; {{ now()->year }} (SIPROKER). Semua hak dilindungi.</p>
    </div>
</body>
</html>
