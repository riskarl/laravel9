<!DOCTYPE html>
<html>
<head>
    <title>Pemberitahuan Persetujuan Berkas</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .email-container {
            max-width: 600px;
            margin: auto;
            background-color: #ffffff;
            padding: 20px;
            border: 1px solid #e4e4e4;
            border-radius: 8px;
        }
        .email-header {
            text-align: center;
            padding: 10px 0;
            border-bottom: 1px solid #e4e4e4;
        }
        .email-body {
            padding: 20px;
            color: #333333;
        }
        .email-footer {
            text-align: center;
            padding: 10px;
            color: #888888;
            font-size: 12px;
        }
        .email-button {
            display: inline-block;
            padding: 10px 20px;
            margin-top: 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 4px;
            font-size: 16px;
        }
        .email-button:hover {
            background-color: #0056b3;
            color: #ffffff;
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Pemberitahuan Persetujuan Berkas</h1>
        </div>
        <div class="email-body">
            <p>Yth. {{ $details['receiver_name'] }},</p>
            <p>Dengan hormat,</p>
            <p>Kami sampaikan bahwa berkas yang Anda ajukan telah berhasil disetujui. Silakan lihat detail berkas Anda di bawah ini:</p>
            <ul>
                <li><strong>Jenis Berkas:</strong> {{ $details['file_type'] }}</li>
                <li><strong>Judul:</strong> {{ $details['file_title'] }}</li>
                <li><strong>Tanggal Persetujuan:</strong> {{ $details['approval_date'] }}</li>
            </ul>
            <p>Anda dapat mengakses berkas yang telah disetujui dengan mengklik tombol di bawah ini:</p>
            <a href="{{ $details['base_url'] }}" class="email-button">Lihat Berkas</a>
            <p>Terima kasih atas kerja sama Anda.</p>
            <p>Salam hormat,</p>
            <p><strong>SIPROKER Politeknik Negeri Cilacap</strong></p>
        </div>
        <div class="email-footer">
            <p>&copy; 2024 SIPROKER Politeknik Negeri Cilacap. Hak cipta dilindungi undang-undang.</p>
        </div>
    </div>
</body>
</html>
