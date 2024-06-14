<!DOCTYPE html>
<html>
<head>
    <title>Pemberitahuan Proposal Baru</title>
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
        }
    </style>
</head>
<body>
    <div class="email-container">
        <div class="email-header">
            <h1>Pemberitahuan Proposal Baru</h1>
        </div>
        <div class="email-body">
            <p>Yth. {{ $details['receiver_name'] }},</p>
            <p>Kami ingin memberitahukan bahwa ada file proposal baru yang perlu Anda review. Mohon untuk melakukan pemeriksaan dan memberikan feedback sesegera mungkin.</p>
            <p>Detail proposal:</p>
            <ul>
                <li><strong>Judul:</strong> {{ $details['proposal_title'] }}</li>
                <li><strong>Pengirim:</strong> {{ $details['sender_name'] }}</li>
                <li><strong>Tanggal:</strong> {{ $details['date'] }}</li>
            </ul>
            <p>Anda bisa melihat proposal tersebut dengan mengklik tombol di bawah ini:</p>
            <a href="{{ $details['base_url'] }}" class="email-button">Lihat Proposal</a>
            <p>Terima kasih,</p>
            <p>SIPROKER Politeknik Negeri Cilacap</p>
        </div>
        <div class="email-footer">
            <p>&copy; 2024 SIPROKER Politeknik Negeri Cilacap. All rights reserved.</p>
        </div>
    </div>
</body>
</html>
