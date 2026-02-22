@php
    use Carbon\Carbon;
    Carbon::setLocale('ar');

    $appointmentDate = data_get($item, 'massAppointment.appointmentDate');
    $dateText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('dddd D MMMM YYYY') : '';
    $timeText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('h:mm a') : '';

    $statusClass = match ((string) data_get($item, 'status')) {
        '1' => 'gray',
        '2' => 'green',
        '3', '5' => 'red',
        '4' => 'yellow',
        default => 'gray',
    };
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تفاصيل الخدمة</title>
    <style>
        body {font-family: Tahoma, sans-serif; background: #f5f6fa; margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .toolbar {background: #2c4ec7; color: #fff; padding: 14px; border-radius: 8px; margin-bottom: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .muted {color: #666; font-size: 13px;}
        .status {display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 4px 8px; border-radius: 20px; color: #fff;}
        .dot {width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,.85);}
        .gray {background: #6c757d;} .green {background: #23a55a;} .red {background: #dc3545;} .yellow {background: #f0ad4e;}
        .btn {background: #fff; color: #2c4ec7; border: 1px solid #2c4ec7; padding: 10px 14px; border-radius: 6px; cursor: pointer;}
        .error {background: #ffe9e9; color: #a00; border: 1px solid #f2b9b9; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        a {text-decoration: none; color: #2c4ec7;}
    </style>
</head>
<body>
    <div class="container">
        <div class="toolbar"><a href="{{ route('mass.index') }}" style="color:#fff;">&#8592;</a> تفاصيل الخدمة</div>

        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <div class="card">
            <div class="muted">{{ $dateText }}</div>
            <h3 style="margin: 8px 0;">{{ data_get($item, 'massAppointment.title') }}</h3>
            <span class="status {{ $statusClass }}"><span class="dot"></span>{{ data_get($item, 'statusName') }}</span>
            <p class="muted" style="margin-top:10px;">{{ $timeText }} - {{ data_get($item, 'massAppointment.place') }}</p>
            @if (data_get($item, 'seatNumber'))
                <p>رقم المقعد: <strong>{{ data_get($item, 'seatNumber') }}</strong></p>
            @endif
        </div>

        <div class="card">
            <h4 style="margin: 0 0 8px;">{{ data_get($item, 'memberName') }}</h4>
            <div class="muted">رقم العضوية</div>
            <div><strong>{{ data_get($item, 'membershipNumber') }}</strong></div>
        </div>

        @if (data_get($item, 'comments'))
            <div class="card">
                <h4 style="margin: 0 0 8px;">ملاحظات</h4>
                <p style="margin: 0;">{{ data_get($item, 'comments') }}</p>
            </div>
        @endif

        @if ((string) data_get($item, 'status') !== '5' && !$isMassDone)
            <form id="cancel-form" action="{{ route('mass.cancel') }}" method="POST">
                @csrf
                <input type="hidden" name="requestId" value="{{ data_get($item, 'requestId') }}">
                <input type="hidden" name="payload" value="{{ $payload }}">
                <input type="hidden" name="nationalId" id="nationalIdField">
                <button type="button" class="btn" onclick="cancelRequestPrompt()">الغاء طلب الحضور</button>
            </form>
        @endif
    </div>

    <script>
        function cancelRequestPrompt() {
            const nationalId = window.prompt('برجاء ادخال الرقم القومي لتاكيد الغاء طلب الحضور');
            if (!nationalId) return;
            const regex = /(2|3)[0-9][0-9][0-1][0-9][0-3][0-9](01|02|03|04|11|12|13|14|15|16|17|18|19|21|22|23|24|25|26|27|28|29|31|32|33|34|35|88)\d\d\d\d\d/;
            if (!regex.test(nationalId)) {
                alert('الرقم القومي غير صحيح');
                return;
            }
            document.getElementById('nationalIdField').value = nationalId;
            document.getElementById('cancel-form').submit();
        }
    </script>
</body>
</html>
