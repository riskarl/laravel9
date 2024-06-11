<!DOCTYPE html>
<html>
<head>
    <title>Laporan Program Kerja</title>
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
        .badge-success {
            color: white;
            background-color: green;
            padding: 5px;
            border-radius: 4px;
        }
        .badge-warning {
            color: white;
            background-color: orange;
            padding: 5px;
            border-radius: 4px;
        }
        .badge-danger {
            color: white;
            background-color: red;
            padding: 5px;
            border-radius: 4px;
        }
    </style>
</head>
<body>
    <h2>Laporan Program Kerja</h2>
    <table>
        <thead>
            <tr>
                <th>No</th>
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
                    <td>{{ $item->organisasi->nama_organisasi }}</td>
                    <td>{{ $item->nama_proker }}</td>
                    <td>
                        @if ($item->proposal)
                            @if ($item->proposal->status_flow == 9)
                                Disetujui
                            @elseif ($item->proposal->status_flow < 9)
                                Belum Selesai
                            @else
                                Tidak ada
                            @endif
                        @else
                            Tidak ada
                        @endif
                    </td>
                    <td>
                        @if ($item->lpj)
                            @if ($item->lpj->status_flow_lpj == 9)
                                Disetujui
                            @elseif ($item->lpj->status_flow_lpj < 9)
                                Belum Selesai
                            @else
                                Tidak ada
                            @endif
                        @else
                            Tidak ada
                        @endif
                    </td>
                    <td>
                        @if ($item->proposal)
                            @if ($item->proposal->status_flow == 9 && $item->lpj && $item->lpj->status_flow_lpj == 9)
                                <span class="badge badge-success">Terlaksana</span>
                            @elseif ($item->proposal->status_flow == 9 && (!$item->lpj || $item->lpj->status_flow_lpj < 9))
                                <span class="badge badge-warning">Belum Selesai</span>
                            @else
                                <span class="badge badge-danger">Belum Terlaksana</span>
                            @endif
                        @else
                            <span class="badge badge-danger">Belum Terlaksana</span>
                        @endif
                    </td>
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
