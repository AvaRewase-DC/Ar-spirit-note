<?php

namespace App\Http\Controllers;

use App\Repositories\MassRepository;
use Carbon\Carbon;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class MassController extends Controller
{
    public function __construct(protected MassRepository $massRepository) {}

    public function index(Request $request)
    {
        $model = [
            'familyNumber' => $request->old('familyNumber', ''),
            'familyMemberCode' => $request->old('familyMemberCode', ''),
        ];

        return view('mass.index', [
            'massSetting' => $this->massSettings(),
            'model' => $model,
            'dateList' => [],
            'isSearch' => false,
        ]);
    }

    public function search(Request $request)
    {
        $validated = $request->validate([
            'familyNumber' => 'required|digits_between:1,5',
            'familyMemberCode' => 'required|digits_between:1,2',
        ]);

        $membershipNumber = sprintf('E1C1F%sNR%s', $validated['familyNumber'], $validated['familyMemberCode']);

        $dateList = $this->massRepository->getRequestsByMembership($membershipNumber);
        if (! is_array($dateList)) {
            $dateList = [];
        }
        $dateList = array_reverse($dateList);

        return view('mass.index', [
            'massSetting' => $this->massSettings(),
            'model' => $validated,
            'dateList' => $dateList,
            'isSearch' => true,
        ]);
    }

    public function details(Request $request)
    {
        $payload = $request->query('payload');
        abort_if(! $payload, 404);

        $decoded = json_decode(base64_decode($payload, true) ?: '', true);
        abort_if(! is_array($decoded) || empty($decoded), 404);

        $massDate = data_get($decoded, 'massAppointment.appointmentDate');
        $isMassDone = $massDate ? Carbon::now()->isAfter(Carbon::parse($massDate)) : false;
        $qrPayload = $this->formatQrPayload($decoded);
        $qrSvg = $qrPayload ? QrCode::encoding('UTF-8')->size(200)->margin(1)->generate($qrPayload) : '';

        return view('mass.details', [
            'item' => $decoded,
            'isMassDone' => $isMassDone,
            'payload' => $payload,
            'qrSvg' => $qrSvg,
        ]);
    }

    public function policy()
    {
        return view('mass.policy', [
            'massSetting' => $this->massSettings(),
        ]);
    }

    public function createRequest()
    {
        $appointments = $this->massRepository->getActiveAppointments();
        if (! is_array($appointments)) {
            $appointments = [];
        }

        return view('mass.request', [
            'appointments' => $appointments,
            'massSetting' => $this->massSettings(),
        ]);
    }

    public function storeRequest(Request $request)
    {
        $validated = $request->validate([
            'massAppointmentId' => 'required',
            'familyNumber' => 'required|digits_between:1,5',
            'familyMemberCode' => 'required|digits_between:1,2',
            'memberName' => ['required', 'string', 'max:255', function ($attribute, $value, $fail) {
                $parts = array_filter(explode(' ', trim($value)));
                if (count($parts) < 2) {
                    $fail('الاسم يجب أن يتكون من جزءين على الأقل (الاسم الأول واسم العائلة)');
                }
            }],
            'nationalId' => ['required', 'digits:14', 'regex:/(2|3)[0-9][0-9][0-1][0-9][0-3][0-9](01|02|03|04|11|12|13|14|15|16|17|18|19|21|22|23|24|25|26|27|28|29|31|32|33|34|35|88)\d\d\d\d\d/'],
            'mobile' => ['required', 'digits:11', 'regex:/^(010|011|012)[0-9]{8}$/'],
        ]);

        [$birthDate, $gender] = $this->extractBirthAndGender($validated['nationalId']);

        $payload = [
            'massAppointmentId' => (string) $validated['massAppointmentId'],
            'membershipNumber' => sprintf('E1C1F%sNR%s', $validated['familyNumber'], $validated['familyMemberCode']),
            'memberName' => $validated['memberName'],
            'birthDate' => $birthDate ?: '2001-01-01',
            'gender' => $gender ?: '0',
            'seatNumber' => '',
            'nationalId' => $validated['nationalId'],
            'mobile' => $validated['mobile'],
            'familyNumber' => $validated['familyNumber'],
            'familyMemberCode' => $validated['familyMemberCode'],
        ];

        $response = $this->massRepository->newMassRequest($payload);

        $statusCode = (int) $response;

        if ($statusCode === 1) {
            return redirect()->route('mass.request.done')->with('success', 'تم ارسال طلبك بنجاح. برجاء مراجعة حالة الطلب. سنقوم بمراجعة الطلب في اقرب فرصة .');
        }

        $message = match ($statusCode) {
            2 => 'رقم العضوية غير صحيح. برجاء التأكد منه او التواصل مع مكتب العضوية الكنسية لمزيد من المساعدة.',
            3 => 'تم حجز جميع الاماكن في هذا الخدمة برجاء اختيار ميعاد اخر ',
            4 => 'تم تخطي عدد الطلبات لهذا العضو. برجاء  مراجعة تعليمات تقديم الطلب',
            default => 'حدث مشكلة في الارسال برجاء اعادة المحاولة',
        };

        return back()->withInput()->withErrors(['mass' => $message]);
    }

    public function requestDone()
    {
        return view('mass.request-done');
    }

    public function cancelRequest(Request $request)
    {
        $validated = $request->validate([
            'requestId' => 'required',
            'nationalId' => ['required', 'regex:/(2|3)[0-9][0-9][0-1][0-9][0-3][0-9](01|02|03|04|11|12|13|14|15|16|17|18|19|21|22|23|24|25|26|27|28|29|31|32|33|34|35|88)\d\d\d\d\d/'],
            'payload' => 'nullable|string',
        ]);

        $response = $this->massRepository->cancelRequestByUser((string) $validated['requestId'], $validated['nationalId']);

        if ((int) $response === 1) {
            return redirect()->route('mass.index')->with('success', 'تم الغاء الطلب');
        }

        return back()->withInput()->withErrors(['cancel' => 'لم يتم الغاء الطلب ، برجاء اعادة المحاولة']);
    }

    protected function extractBirthAndGender(string $nationalId): array
    {
        $digits = preg_replace('/\D+/', '', $nationalId);
        if (strlen($digits) < 13) {
            return ['', ''];
        }

        $century = $digits[0] === '2' ? '19' : '20';
        $year = $century.substr($digits, 1, 2);
        $month = substr($digits, 3, 2);
        $day = substr($digits, 5, 2);

        $birthDate = sprintf('%s-%s-%s', $year, $month, $day);
        $gender = ((int) substr($digits, 12, 1) % 2 === 0) ? '1' : '0';

        return [$birthDate, $gender];
    }

    protected function massSettings(): array
    {
        $apiSettings = $this->massRepository->getMassSettings();

        return [
            'massEnabled' => (bool) ($apiSettings['massEnabled'] ?? config('mass.enabled', true)),
            'massMessageTitle' => $apiSettings['massMessageTitle'] ?? config('mass.message_title', ''),
            'massMessageBody' => $apiSettings['massMessageBody'] ?? config('mass.message_body', ''),
            'massPolicy' => $apiSettings['massPolicy'] ?? config('mass.policy', ''),
        ];
    }

    protected function formatQrPayload(array $item): string
    {
        $lines = [
            'الاسم: '.(data_get($item, 'memberName') ?? ''),
            'رقم العضوية: '.(data_get($item, 'membershipNumber') ?? ''),
            'حالة الطلب: '.(data_get($item, 'statusName') ?? ''),
            'التاريخ: '.(data_get($item, 'massAppointment.appointmentDate') ?? ''),
            'الخدمة: '.(data_get($item, 'massAppointment.title') ?? ''),
            'المكان: '.(data_get($item, 'massAppointment.place') ?? ''),
            'المقعد: '.(data_get($item, 'seatNumber') ?? ''),
            'رقم الطلب: '.(data_get($item, 'requestId') ?? ''),
        ];

        return trim(implode("\n", $lines));
    }
}
