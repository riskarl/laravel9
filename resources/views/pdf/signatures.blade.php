<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pengesahan Kegiatan</title>
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
                <td>Ketua Badan Eksekutif Mahasiswa</td>
                <td>Ketua Pelaksana</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[0]['ttd'] }}" alt="Signature" class="signature-image">
                     <div>{{ $signatures[0]['nama'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $ketupel['ttd'] ?? null }}" alt="Signature" class="signature-image">
                    <div>{{ $ketupel['name'] ?? null }}</div>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td>Mengetahui,</td>
                <td></td>
            </tr>
            <tr>
                <td>Pembina BEM</td>
                <td>Ketua Badan Perwakilan Mahasiswa</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[2]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[2]['nama'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $signatures[1]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[1]['nama'] }}</div>
                </td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td colspan="2">Menyetujui,</td>
            </tr>
            <tr>
                <td>Wakil Direktur Bagian Kemahasiswaan dan Alumni</td>
                <td>Koordinator Subbagian Akademik dan Kemahasiswaan</td>
            </tr>
            <tr class="spacer"></tr>
            <tr>
                <td class="signature-block">
                    <img src="{{ $signatures[4]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[4]['nama'] }}</div>
                </td>
                <td class="signature-block">
                    <img src="{{ $signatures[3]['ttd'] }}" alt="Signature" class="signature-image">
                    <div>{{ $signatures[3]['nama'] }}</div>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>