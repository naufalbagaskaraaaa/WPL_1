<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Barryvdh\DomPDF\Facade\Pdf;

class PdfController extends Controller
{
    public function generatePDFLandscape()
    {
        $data = [
            'nama' => 'Naufal Bagaskara',
            'peran' => 'Ketua Umum',
            'tanggal' => date('d F Y')
        ];

        $pdf = Pdf::loadView('pdf.sertifikat', $data);

        $pdf->setPaper('a4', 'landscape');

        return $pdf->download('sertifikat-acara.pdf');
    }


    public function generatePDFPortrait()
    {
        $data2 = [
            'tanggal' => date('d F Y')
        ];

        $pdf2 = Pdf::loadView('pdf.undangan', $data2);

        $pdf2->setPaper('a4', 'portrait');

        return $pdf2->download('surat-undangan.pdf');
    }
}
