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
        :root {--ken-primary:#ad1100; --ken-bg:#f4f4f4; --ken-text:#353535; --ken-muted:#7c7c7c; --ken-success:#20bf6b; --ken-gray:#848484; --ken-warning:#ffc409;}
        body {font-family: Tahoma, sans-serif; background: var(--ken-bg); margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .toolbar {background: var(--ken-primary); color: #fff; padding: 14px; border-radius: 8px; margin-bottom: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .muted {color: var(--ken-text); font-size: 13px;}
        .status {display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 4px 8px; border-radius: 20px; color: var(--ken-text);}
        .dot {width: 8px; height: 8px; border-radius: 50%;}
        .gray {background: rgba(132,132,132,.12);} .gray .dot {background: var(--ken-gray);}
        .green {background: rgba(32,191,107,.12);} .green .dot {background: var(--ken-success);}
        .red {background: rgba(173,17,0,.12);} .red .dot {background: var(--ken-primary);}
        .yellow {background: rgba(255,196,9,.2);} .yellow .dot {background: var(--ken-warning);}
        .btn {background: #fff; color: var(--ken-primary); border: 1px solid var(--ken-primary); padding: 10px 14px; border-radius: 6px; cursor: pointer;}
        .error {background: #ffe9e9; color: #a00; border: 1px solid #f2b9b9; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        a {text-decoration: none; color: var(--ken-primary);}
        .ticket {
            position: relative;
            background: #fff7f4;
            border: 1px dashed rgba(173, 17, 0, 0.35);
            border-radius: 14px;
            padding: 16px;
            text-align: center;
            box-shadow: 0 6px 16px rgba(0,0,0,.08);
        }
        .ticket:before,
        .ticket:after {
            content: "";
            position: absolute;
            top: 50%;
            width: 22px;
            height: 22px;
            background: var(--ken-bg);
            border-radius: 50%;
            transform: translateY(-50%);
        }
        .ticket:before {left: -11px; box-shadow: inset -1px 0 0 rgba(173, 17, 0, 0.25);}
        .ticket:after {right: -11px; box-shadow: inset 1px 0 0 rgba(173, 17, 0, 0.25);}
        .ticket-title {margin: 0 0 8px; color: var(--ken-primary); font-size: 16px;}
        .ticket-sub {color: var(--ken-muted); font-size: 13px; margin-bottom: 12px;}
        .ticket-qr {
            display: inline-block;
            padding: 10px;
            background: #fff;
            border-radius: 10px;
            box-shadow: inset 0 0 0 1px rgba(0,0,0,.06);
        }
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

        @if (!empty($qrSvg))
            <div class="card">
                <div class="ticket">
                    <h4 class="ticket-title">تذكرة الحجز</h4>
                    <div class="ticket-sub">اعرض الكود عند الدخول</div>
                    <div class="ticket-qr">
                        {!! $qrSvg !!}
                    </div>
                </div>
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
