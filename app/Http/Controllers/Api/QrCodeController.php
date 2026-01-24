<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\QrCodeRequest;
use App\Repositories\GlobalApiRepository;

class QrCodeController extends BaseController
{
    public function __construct(protected GlobalApiRepository $globalApiRepository) {}

    /**
     * Generate QR code - Simple API like Google Chart API
     * GET /api/qr-code?data=YOUR_DATA&size=400
     */
    public function generateQrCode(QrCodeRequest $request)
    {
        try {
            $size = $request->input('size', 400);
            $data = $request->input('data');

            $qr = $this->globalApiRepository->generateQrCode($data, $size);

            // Return image directly like Google Chart API
            return response($qr)
                ->header('Content-Type', 'image/png')
                ->header('Cache-Control', 'public, max-age=86400');

        } catch (\Exception $e) {
            return $this->apiErrorResponse('Error generating QR code: '.$e->getMessage(), 500);
        }
    }
}
