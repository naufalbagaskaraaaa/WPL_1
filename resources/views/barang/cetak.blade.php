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

        .halaman {
            width: 210mm;
            height: 297mm;
            padding: 5mm 3mm;
            page-break-after: always;
        }

        .halaman:last-child {
            page-break-after: avoid;
        }

        .grid-label {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        .grid-label td {
            width: 20%;
            height: 36mm;
            vertical-align: middle;
            text-align: center;
            padding: 3px;
            border: 0.3px dashed #ccc;
        }

        .label-isi {
            font-family: Arial, sans-serif;
        }

        .label-nama {
            font-size: 9pt;
            font-weight: bold;
            margin-bottom: 3px;
            word-wrap: break-word;
        }

        .label-id {
            font-size: 7pt;
            color: #555;
            margin-bottom: 3px;
        }

        .label-harga {
            font-size: 13pt;
            font-weight: bold;
        }

        .label-rp {
            font-size: 8pt;
            font-weight: normal;
        }
    </style>
</head>

<body>

    @foreach($halaman as $indexHalaman => $labels)
    <div class="halaman">
        <table class="grid-label">

            <?php $baris = array_chunk($labels, 5); ?>

            @foreach($baris as $row)
            <tr>
                @for($i = 0; $i < 5; $i++)
                    <?php $label = $row[$i] ?? null; ?>

                    @if(is_null($label))
                    <td>
                    </td>
                    @else
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