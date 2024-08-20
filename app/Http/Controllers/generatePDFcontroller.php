<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Support\Facades\Storage;

class generatePDFcontroller extends Controller
{
    private $options;
    private $dompdf;
    private $outputFolder;

    private $Request;

    public function __construct(Request $req)
    {
        $this->Request = $req->all();
        $this->options = new Options();
        $this->options->set('isPhpEnabled', true);
        $this->options->set('isHtml5ParserEnabled', true);
        $this->options->set('defaultFont', 'Arial');
        $this->options->set('isRemoteEnabled', true);

        $this->dompdf = new Dompdf($this->options);

        $this->outputFolder = "public/pdf";

        if (!Storage::exists($this->outputFolder)) {
            Storage::makeDirectory($this->outputFolder);
        }
    }

    public function generatePDF(): string
    {
        // $this->dompdf->setPaper('A4', 'portrait');
        $this->dompdf->setPaper('A4', 'landscape');
        $this->dompdf->loadHtml($this->Request["htmlContent"]);
        $this->dompdf->render();

        // $watermark = 'temp.png';
        $watermark = public_path('assets/dist/img/hd-logo.png');
        $canvas = $this->dompdf->get_canvas();
        $pageCount = $canvas->get_page_count();
        // Apply watermark on each page
        $canvas->page_script(function ($pageNumber, $pageCount, $canvas) use ($watermark) {
            $canvas->set_opacity(0.8, "Multiply");
            $imgWidth = 350;
            $imgHeight = 300;
            $x = ($canvas->get_width() - $imgWidth) / 2;
            $y = ($canvas->get_height() - $imgHeight) / 2;
            $canvas->image($watermark, $x, $y, $imgWidth, $imgHeight);
            $canvas->set_opacity(1, "Normal"); // Reset opacity for other content
        });

        $date = microtime(true);
        $filePath = "pdf/export-{$date}.pdf";
        Storage::put("public/{$filePath}", $this->dompdf->output());

        return Storage::url($filePath);
    }

    public function deletePDF(): bool
    {
        $url = preg_replace('~.*?/storage/pdf/~', '', $this->Request["deleteFileUrl"]);
        if (Storage::exists("public/pdf/{$url}")) {
            Storage::delete("public/pdf/{$url}");
            return true;
        }
        return false;
    }
}
