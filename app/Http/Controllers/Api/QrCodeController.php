<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\QrCodeRequest;
use App\Repositories\GlobalApiRepository;

class QrCodeController extends BaseController
{
    public function __construct(protected GlobalApiRepository $globalApiRepository) {}

    public function generateQrCode(QrCodeRequest $request): string
    {
        $qr = $this->globalApiRepository->generateQrCode($request->data);

        return $qr;
    }
}
