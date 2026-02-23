<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kode OTP Login</title>
</head>
<body style="font-family: Arial, sans-serif; padding: 20px; color: #333; line-height: 1.6;">
    <div style="max-width: 500px; margin: 0 auto; border: 1px solid #e0e0e0; border-radius: 8px; padding: 20px;">
        <h2 style="text-align: center; color: #333;">Verifikasi Login</h2>
        <p>Halo!</p>
        <p>Sistem kami mendeteksi upaya login menggunakan akun Google Anda. Untuk melanjutkan proses authentikasi[cite: 10], silakan masukkan 6 digit kode OTP berikut:</p>
        
        <div style="background-color: #f8f9fa; padding: 15px; text-align: center; border-radius: 5px; margin: 20px 0;">
            <h1 style="letter-spacing: 8px; color: #0d6efd; margin: 0; font-size: 32px;">{{ $otp }}</h1>
        </div>
        
        <p style="font-size: 14px; color: #666;"><strong>Penting:</strong> Jangan berikan kode ini kepada siapa pun. Kode ini dihasilkan secara otomatis oleh sistem.</p>
        <p>Terima kasih.</p>
    </div>
</body>
</html>