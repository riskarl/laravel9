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
        .footer {
            text-align: center;
            font-size: 12px;
            color: #555;
        }
    </style>
</head>
<body>
    <h2>Laporan Program Kerja</h2>
    <table>
        <thead>
            <tr>
                <th>NO</th>
                <th>Nama Organisasi</th>
                <th>Nama Program Kerja</th>
                <th>Proposal</th>
                <th>LPJ</th>
                <th>Keterangan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($listproker as $index => $item)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $item->organisasi['nama_organisasi'] }}</td>
                    <td>{{ $item['nama_proker'] }}</td>
                    {{-- <td>{{ number_format($item['status_flow'], 0, ',', '.') }}</td>
                    <td>{{ $item['lpj'] }}</td>
                    <td>{{ $item['keterangan'] }}</td> --}}
                </tr>
            @endforeach
        </tbody>
    </table>
    <div class="footer">
        <p>Laporan ini dihasilkan oleh sistem pada {{ now()->format('d M Y') }}.</p>
        <p>&copy; {{ now()->year }} (SIPROKER). Semua hak dilindungi.</p>
    </div>
</body>
</html>
