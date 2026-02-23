<!DOCTYPE html>
<html>
<head>
    <title>Surat Undangan</title>
    <style>
        body {
            font-family: "Times New Roman", Times, serif; 
            margin: 1cm;
        }
        .kop-surat {
            text-align: center;
            border-bottom: 3px solid black;
            margin-bottom: 20px;
        }
        .kop-surat h2 {
            margin: 0;
            font-size: 18pt;
            letter-spacing: 2px;
        }
        .kop-surat h3 {
            margin: 0;
            font-size: 14pt;
        }
        .kop-surat p {
            margin: 0;
            font-size: 10pt;
        }
        
        .content {
            font-size: 12pt;
            line-height: 1.5;
        }
        .ttd {
            float: right;
            width: 300px;
            text-align: left;
            margin-top: 50px;
        }
    </style>
</head>
<body>
    <div class="kop-surat">
        <h2>UNIVERSITAS AIRLANGGA</h2>
        <h3>FAKULTAS VOKASI</h3>
        <p>Kampus B Jl. Dharmawangsa Dalam Surabaya 60286 Telp. (031) 5033869 Fax (031) 5053156</p>
        <p>Laman : https://vokasi.unair.ac.id, e-mail : info@vokasi.unair.ac.id</p>
    </div>

    <div class="content">
        <table width="100%">
            <tr>
                <td width="15%">Nomor</td>
                <td width="5%">:</td>
                <td width="50%">556/B/DST/UN3.FV/TU.01.00/2026</td>
                <td width="30%" align="right">{{ $tanggal }}</td>
            </tr>
            <tr>
                <td>Lampiran</td>
                <td>:</td>
                <td colspan="2">Satu Lembar</td>
            </tr>
            <tr>
                <td>Perihal</td>
                <td>:</td>
                <td colspan="2">Undangan</td>
            </tr>
        </table>

        <br>
        <p>Yth.<br>
        1. Para Wakil Dekan<br>
        2. Para Ketua Departemen<br>
        3. Para Sekretaris Departemen<br>
        Fakultas Vokasi Universitas Airlangga</p>

        <p>Dalam rangka mempererat tali silaturahmi serta mengawali kegiatan tahun 2026, Fakultas Vokasi Universitas Airlangga akan menyelenggarakan Silaturahmi Awal Tahun. Sehubungan dengan hal tersebut, kami mengundang Bapak/Ibu untuk hadir pada kegiatan yang akan dilaksanakan pada:</p>

        <table width="100%" style="margin-left: 20px;">
            <tr>
                <td width="25%">Hari, Tanggal</td>
                <td width="5%">:</td>
                <td>Selasa, 20 Januari 2026</td>
            </tr>
            <tr>
                <td>Waktu</td>
                <td>:</td>
                <td>10.00 – 13.00 WIB</td>
            </tr>
            <tr>
                <td>Tempat</td>
                <td>:</td>
                <td>Aula Gedung A Lt.3 Fakultas Vokasi Universitas Airlangga</td>
            </tr>
        </table>

        <p>Demikian undangan ini kami sampaikan. Atas perhatian dan kehadiran Bapak/Ibu, kami ucapkan terima kasih.</p>
    </div>

    <div class="ttd">
        <p>Dekan,</p>
        <br><br><br> <p><b>Prof. Dian Yulie Reindrawati, S.Sos., M.M., Ph.D</b><br>
        NIP. 197607071999032001</p>
    </div>
</body>
</html>