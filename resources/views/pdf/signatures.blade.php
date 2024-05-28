<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pengesahan Proposal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        .content {
            text-align: justify;
            margin-bottom: 50px;
        }
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .row img {
            width: 48%;
        }
        .signature-container {
            page-break-inside: avoid;
        }
    </style>
</head>
<body>
    <h1>Lembar Pengesahan Proposal</h1>
    <div class="content">
        <p>
            Ini adalah lembar pengesahan untuk proposal yang diajukan. Lembar ini memuat tanda tangan dari pihak-pihak yang berwenang.
        </p>
    </div>
    <div class="signatures">
        @foreach(array_chunk($signatures, 2) as $signaturePair)
            <div class="row signature-container">
                @foreach($signaturePair as $signature)
                    <div class="signature">
                        <img src="{{ $signature }}" alt="Signature">
                    </div>
                @endforeach
            </div>
        @endforeach
    </div>
</body>
</html>
