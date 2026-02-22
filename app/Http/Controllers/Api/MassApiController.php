<?php

namespace App\Http\Controllers\Api;

use App\Repositories\MassRepository;
use Illuminate\Http\Request;

class MassApiController extends BaseController
{
    public function __construct(protected MassRepository $massRepository) {}

    public function settings()
    {
        return $this->apiResponse([
            'massEnabled' => (bool) config('mass.enabled', true),
            'massMessageTitle' => config('mass.message_title', ''),
            'massMessageBody' => config('mass.message_body', ''),
            'massPolicy' => config('mass.policy', ''),
        ]);
    }

    public function activeAppointments()
    {
        return $this->apiResponse($this->massRepository->getActiveAppointments());
    }

    public function requestsByMembership(string $membershipID)
    {
        return $this->apiResponse($this->massRepository->getRequestsByMembership($membershipID));
    }

    public function storeRequest(Request $request)
    {
        return $this->apiResponse($this->massRepository->newMassRequest($request->all()));
    }

    public function cancelRequestByUser(string $requestId, string $nationalId)
    {
        return $this->apiResponse($this->massRepository->cancelRequestByUser($requestId, $nationalId));
    }
}
