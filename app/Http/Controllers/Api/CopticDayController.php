<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\CopticDateRequest;
use App\Repositories\GlobalApiRepository;

class CopticDayController extends BaseController
{
    public function __construct(protected GlobalApiRepository $globalApiRepository) {}

    public function getCopticDate(CopticDateRequest $request): string
    {
        $copticDay = $this->globalApiRepository->getCopticDate($request->date);

        return $copticDay;
    }
}
