<?php

namespace App\Controller;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class QrCodeController extends AbstractController
{
    #[Route('/qr-code/{data}', name: 'app_qr_code')]
    public function generateQrCode(string $data): Response
    {
        // Create QR code instance
        $qrCode = new QrCode($data);
        $writer = new PngWriter();
        
        // Generate QR code image
        $qrCodeImage = $writer->write($qrCode)->getString();

        return new Response($qrCodeImage, 200, ['Content-Type' => 'image/png']);
    }
}
