<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sertifikat</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            text-align: center;
            margin-top: 50px;
        }
        .title {
            font-size: 40px;
            font-weight: bold;
            color: #4a148c;
            letter-spacing: 5px;
        }
        .nama {
            font-size: 35px;
            font-weight: bold;
            margin: 30px 0;
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="title">SERTIFIKAT_2</div>
    <p>Diberikan kepada :</p>
    
    <div class="nama">{{ $nama }}</div> 
    
    <p>Atas Partisipasinya Sebagai:</p>
    <h2>{{ $peran }}</h2>
    <p>Dicetak pada: {{ $tanggal }}</p>
</body>
</html>