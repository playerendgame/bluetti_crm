<?php

namespace App\Services;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Endroid\QrCode\Encoding\Encoding;
use Endroid\QrCode\Logo\Logo;

class QrGeneratorService
{
    public function generateQRCode($link)
    {
        $qrCode = QrCode::create($link)
            ->setEncoding(new Encoding('UTF-8'));
        
        $writer = new PngWriter();

        $logoPath = public_path('./bluetti_icon.jpg');
        
        $logo = Logo::create($logoPath)
        ->setResizeToWidth(50);


        $result = $writer->write($qrCode, $logo);

        return $result->getString();

    }
}