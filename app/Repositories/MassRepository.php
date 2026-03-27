<?php

namespace App\Repositories;

use Illuminate\Support\Facades\Http;

class MassRepository
{
    protected string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.mass.base_url', 'http://41.130.162.206:3000/api/'), '/').'/';
    }

    public function getMassSettings(): array
    {
        try {
            $firebaseUrl = rtrim(config('services.mass.firebase_url', 'https://kenesty.firebaseio.com'), '/');
            $response = Http::timeout(10)->get($firebaseUrl.'/mass-settings.json');
            $data = json_decode($response->body(), true);

            if (is_array($data) && isset($data['massEnabled'])) {
                return [
                    'massEnabled' => (bool) ($data['massEnabled'] ?? true),
                    'massMessageTitle' => $data['massMessageTitle'] ?? '',
                    'massMessageBody' => $data['massMessageBody'] ?? '',
                    'massPolicy' => $data['massPolicy'] ?? '',
                ];
            }
        } catch (\Throwable $e) {
            // fall through to config fallback
        }

        // Fallback to local config
        return [
            'massEnabled' => (bool) config('mass.enabled', true),
            'massMessageTitle' => config('mass.message_title', ''),
            'massMessageBody' => config('mass.message_body', ''),
            'massPolicy' => config('mass.policy', ''),
        ];
    }

    public function getActiveAppointments()
    {
        $response = Http::acceptJson()->timeout(20)->get($this->baseUrl.'mass-appointments/get-active-appointments');

        return $this->decodeResponse($response->body());
    }

    public function getRequestsByMembership(string $membershipID)
    {
        $response = Http::acceptJson()->timeout(20)->get($this->baseUrl.'requests/search-by-membership/'.urlencode($membershipID));

        return $this->decodeResponse($response->body());
    }

    public function newMassRequest(array $requestData)
    {
        // dd($requestData);
        $response = Http::acceptJson()->timeout(20)->post($this->baseUrl.'requests', $requestData);

        return $this->decodeResponse($response->body());
    }

    public function cancelRequestByUser(string $requestId, string $nationalId)
    {
        $response = Http::acceptJson()->timeout(20)->post(
            $this->baseUrl.'requests/cancel-request-by-user/'.urlencode($requestId).'/'.urlencode($nationalId),
            []
        );

        return $this->decodeResponse($response->body());
    }

    protected function decodeResponse(string $body)
    {
        $decoded = json_decode($body, true);

        return json_last_error() === JSON_ERROR_NONE ? $decoded : $body;
    }
}
