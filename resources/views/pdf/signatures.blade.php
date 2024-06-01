<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pengesahan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            padding: 40px;
            box-sizing: border-box;
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }
        th, td {
            padding: 10px;
            border-bottom: 1px solid #000;
            vertical-align: bottom;
            text-align: left;
        }
        .title-row th {
            border-bottom: none;
            text-align: center;
            font-weight: bold;
        }
        .signature-space img {
            height: 80px;
            width: auto;
            display: block;
        }
        .text-center {
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>FORMAT HIMA PRODI</h1>
        <h2>LEMBAR PENGESAHAN</h2>
        <h3>NAMA KEGIATAN</h3>
    </div>
    <table>
        <?php foreach ($signatures as $signature): ?>
        <tr>
            <td class="text-center"><?php echo htmlspecialchars($signature['jabatan']); ?></td>
        </tr>
        <tr>
            <td class="signature-space text-center">
                <img src="<?php echo htmlspecialchars($signature['ttd']); ?>" alt="Signature">
            </td>
        </tr>
        <tr>
            <td class="text-center">Nama: <?php echo htmlspecialchars($signature['nama']); ?></td>
        </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
