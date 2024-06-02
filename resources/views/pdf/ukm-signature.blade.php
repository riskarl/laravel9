<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pengesahan Kegiatan UKM</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            box-sizing: border-box;
            text-align: center;
        }
        .header {
            margin-bottom: 20px;
        }
        .content {
            width: 100%;
            margin: 0 auto;
            text-align: center;
        }
        .signatures-table {
            width: 100%;
            margin-top: 20px;
            border-collapse: collapse;
            table-layout: fixed;
        }
        .signatures-table th, .signatures-table td {
            padding: 10px;
            vertical-align: middle;
            text-align: center;
            border: none;
        }
        .signature-block {
            height: 100px; /* Adjust based on your need */
        }
        .signature-image {
            height: 80px;
            width: auto;
            display: block;
            margin-left: auto;
            margin-right: auto;
        }
        .title {
            font-weight: bold;
            margin-bottom: 10px;
        }
        .spacer {
            height: 30px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>LEMBAR PENGESAHAN</h1>
        <h2>{{ $namaKegiatan }}</h2>
    </div>
    <div class="content">
        <table class="signatures-table">
            <tr>
                <td>Ketua UKM</td>
                <td>Ketua Pelaksana</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[0]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[0]['nama'] }}</div>
                    <div>{{ $signatures[0]['code_id'] }}. {{ $signatures[0]['number_id'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $ketupel['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $ketupel['name'] ?? null }}</div>
                    <div>NIM. {{ $ketupel['nim'] ?? null }}</div>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td>Ketua Badan Perwakilan Mahasiswa</td>
                <td>Ketua Badan Eksekutif Mahasiswa</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[1]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[1]['nama'] }}</div>
                    <div>{{ $signatures[1]['code_id'] }}. {{ $signatures[1]['number_id'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $signatures[2]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[2]['nama'] }}</div>
                    <div>{{ $signatures[2]['code_id'] }}. {{ $signatures[2]['number_id'] }}</div>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td colspan="2">Mengetahui,</td>
            </tr>
            <tr>
                <td>Pembina UKM</td>
                <td>Koordinator Subbagian Akademik dan Kemahasiswaan</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[3]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[3]['nama'] }}</div>
                    <div>{{ $signatures[3]['code_id'] }}. {{ $signatures[3]['number_id'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $signatures[4]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[4]['nama'] }}</div>
                    <div>{{ $signatures[4]['code_id'] }}. {{ $signatures[4]['number_id'] }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
