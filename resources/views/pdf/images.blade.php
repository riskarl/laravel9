<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <style>
        .row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .row img {
            width: 48%;
        }
    </style>
</head>
<body>
    @foreach(array_chunk($images, 2) as $imagePair)
        <div class="row">
            @foreach($imagePair as $image)
                <img src="{{ $image }}" alt="Image">
            @endforeach
        </div>
    @endforeach
</body>
</html>
