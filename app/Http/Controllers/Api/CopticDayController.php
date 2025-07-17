<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CopticDateRequest;
use App\Repositories\GlobalApiRepository;

class CopticDayController extends BaseController
{
    public function __construct(protected GlobalApiRepository $globalApiRepository) {}

    public function getCopticDate(CopticDateRequest $request): string
    {
        $copticDate = $this->globalApiRepository->getCopticDate($request->date);

        return json_encode($copticDate, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
    }
}
