<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 0;
        }

        /* ✅ Tiap halaman = 1 lembar kertas A4 */
        .halaman {
            width: 210mm;
            height: 297mm;
            padding: 9mm 4mm;
            /* Page break antar halaman */
            page-break-after: always;
        }

        /* Halaman terakhir tidak perlu page break */
        .halaman:last-child {
            page-break-after: avoid;
        }

        /* Gunakan <table> HTML biasa — lebih reliable di DomPDF */
        .grid-label {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .grid-label td {
            width: 20%;
            /* 100% / 5 kolom */
            height: 34mm;
            vertical-align: middle;
            text-align: center;
            padding: 3px;
            border: 0.3px dashed #ccc;
            /* Hapus saat sudah pas */
        }

        /* Label berisi data */
        .label-isi {
            font-family: Arial, sans-serif;
        }

        .label-nama {
            font-size: 8pt;
            font-weight: bold;
            margin-bottom: 3px;
            word-wrap: break-word;
        }

        .label-id {
            font-size: 6pt;
            color: #555;
            margin-bottom: 4px;
        }

        .label-harga {
            font-size: 12pt;
            font-weight: bold;
        }

        .label-rp {
            font-size: 7pt;
            font-weight: normal;
        }
    </style>
</head>

<body>

    {{-- ✅ Loop tiap halaman --}}
    @foreach($halaman as $indexHalaman => $labels)

    <div class="halaman">
        <table class="grid-label">

            <?php
            // Bagi 40 label menjadi 8 baris x 5 kolom
            $baris = array_chunk($labels, 5);
            ?>

            @foreach($baris as $row)
            <tr>
                @for($i = 0; $i < 5; $i++)
                    <?php $label = $row[$i] ?? null; ?>

                    @if(is_null($label))
                    {{-- Slot kosong --}}
                    <td>
                    </td>
                    @else
                    {{-- Slot berisi data barang --}}
                    <td class="label-isi">
                        <div class="label-nama">{{ $label['nama'] }}</div>
                        <div class="label-id">{{ $label['id_barang'] }}</div>
                        <div class="label-harga">
                            <span class="label-rp">Rp </span>
                            {{ number_format($label['harga'], 0, ',', '.') }}
                        </div>
                    </td>
                    @endif

                    @endfor
            </tr>
            @endforeach

        </table>
    </div>

    @endforeach

</body>

</html>