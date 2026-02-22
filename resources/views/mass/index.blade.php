@php
    use Carbon\Carbon;
    Carbon::setLocale('ar');

    $statusClass = function ($status) {
        return match ((string) $status) {
            '1' => 'gray',
            '2' => 'green',
            '3', '5' => 'red',
            '4' => 'yellow',
            default => 'gray',
        };
    };
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>حجز الخدمات</title>
    <style>
        body {font-family: Tahoma, sans-serif; background: #f5f6fa; margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .toolbar {background: #2c4ec7; color: #fff; text-align: center; padding: 14px; border-radius: 8px; margin-bottom: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .label {font-weight: bold; color: #2c4ec7; margin-bottom: 10px; display: block;}
        .inline {display: flex; gap: 8px; align-items: center;}
        input[type="text"], input[type="tel"], select {width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px;}
        button, .btn {background: #2c4ec7; color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block;}
        .btn-outline {background: #fff; color: #2c4ec7; border: 1px solid #2c4ec7;}
        .hint {text-align: center; color: #666; margin: 18px 0;}
        .head {font-size: 18px; text-align: center; margin: 10px 0;}
        .item {display: flex; justify-content: space-between; gap: 12px; align-items: center; color: inherit; text-decoration: none;}
        .muted {color: #666; font-size: 13px;}
        .status {display: inline-flex; align-items: center; gap: 6px; font-size: 13px; padding: 4px 8px; border-radius: 20px; color: #fff;}
        .dot {width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,.85);}
        .gray {background: #6c757d;} .green {background: #23a55a;} .red {background: #dc3545;} .yellow {background: #f0ad4e;}
        .error {background: #ffe9e9; color: #a00; border: 1px solid #f2b9b9; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        .success {background: #e9fff1; color: #156f34; border: 1px solid #b9f2cd; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
        .fab-wrap {position: sticky; bottom: 16px; display: flex; justify-content: flex-end;}
        .fab {width: 46px; height: 46px; border-radius: 50%; font-size: 28px; line-height: 46px; text-align: center; padding: 0;}
        .keypad-wrap {margin-top: 12px;}
        .active-input {border-color: #2c4ec7 !important; box-shadow: 0 0 0 2px rgba(44, 78, 199, 0.12);}
        .keypad-info {font-size: 13px; color: #666; margin: 8px 0;}
        .keypad-grid {display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;}
        .key-btn {background: #eef2ff; color: #2c4ec7; border: 1px solid #cfd8ff; border-radius: 8px; padding: 12px; font-size: 18px; font-weight: bold; cursor: pointer;}
        .key-btn.action {font-size: 14px;}
    </style>
</head>
<body>
    <div class="container">
        <div class="toolbar">حجز الخدمات</div>

        @if (session('success'))
            <div class="success">{{ session('success') }}</div>
        @endif
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        @if ($massSetting['massEnabled'])
            <div class="card">
                <span class="label">بحث برقم العضوية</span>
                <form action="{{ route('mass.search') }}" method="POST">
                    @csrf
                    <div class="inline" dir="ltr">
                        <span>E1C1F</span>
                        <input id="familyNumber" type="tel" maxlength="5" name="familyNumber" value="{{ $model['familyNumber'] ?? '' }}" required readonly inputmode="none" autocomplete="off">
                        <span>NR</span>
                        <input id="familyMemberCode" type="tel" maxlength="2" name="familyMemberCode" value="{{ $model['familyMemberCode'] ?? '' }}" required readonly inputmode="none" autocomplete="off">
                        <span>رقم العضوية</span>
                    </div>

                    <div class="keypad-wrap" dir="ltr">
                        <div class="keypad-info">استخدم لوحة الأرقام التالية فقط</div>
                        <div class="keypad-grid">
                            <button type="button" class="key-btn" data-digit="1">1</button>
                            <button type="button" class="key-btn" data-digit="2">2</button>
                            <button type="button" class="key-btn" data-digit="3">3</button>
                            <button type="button" class="key-btn" data-digit="4">4</button>
                            <button type="button" class="key-btn" data-digit="5">5</button>
                            <button type="button" class="key-btn" data-digit="6">6</button>
                            <button type="button" class="key-btn" data-digit="7">7</button>
                            <button type="button" class="key-btn" data-digit="8">8</button>
                            <button type="button" class="key-btn" data-digit="9">9</button>
                            <button type="button" class="key-btn action" id="switchFieldBtn">تبديل</button>
                            <button type="button" class="key-btn" data-digit="0">0</button>
                            <button type="button" class="key-btn action" id="backspaceBtn">حذف</button>
                            <button type="button" class="key-btn action" id="clearBtn" style="grid-column: span 3;">مسح الكل</button>
                        </div>
                    </div>

                    <div style="margin-top: 12px;">
                        <button type="submit">بحث</button>
                    </div>
                </form>
            </div>

            @if (count($dateList) === 0)
                <div class="hint">
                    @if (!$isSearch)
                        برجاء ادخال رقم العضوية و الضغط علي بحث
                    @else
                        لا يوجد حجوزات لهذا العضو..<br>يمكنك اضافة طلب جديد عن طريق زر "+" بالاسفل
                    @endif
                </div>
            @else
                <h3 class="head">{{ data_get($dateList, '0.memberName') }}</h3>
                <div class="label">طلبات الحجز</div>
                @foreach ($dateList as $item)
                    @php
                        $encoded = base64_encode(json_encode($item, JSON_UNESCAPED_UNICODE));
                        $appointmentDate = data_get($item, 'massAppointment.appointmentDate');
                        $dateText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('dddd D MMMM YYYY') : '';
                        $timeText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('h:mm a') : '';
                    @endphp
                    <div class="card">
                        <a class="item" href="{{ route('mass.details', ['payload' => $encoded]) }}">
                            <div>
                                <div class="muted">{{ $dateText }}</div>
                                <div><strong>{{ data_get($item, 'massAppointment.title') }}</strong></div>
                                <div class="muted">{{ $timeText }} - {{ data_get($item, 'massAppointment.place') }}</div>
                            </div>
                            <span class="status {{ $statusClass(data_get($item, 'status')) }}">
                                <span class="dot"></span>
                                {{ data_get($item, 'statusName') }}
                            </span>
                        </a>
                    </div>
                @endforeach
            @endif

            <div class="fab-wrap">
                <a href="{{ route('mass.policy') }}" class="btn fab">+</a>
            </div>
        @else
            <div class="card">
                <h3 style="margin-top:0; color:#2c4ec7;">{{ $massSetting['massMessageTitle'] }}</h3>
                <div>{!! $massSetting['massMessageBody'] !!}</div>
            </div>
        @endif
    </div>

    <script>
        (function () {
            const familyNumber = document.getElementById('familyNumber');
            const familyMemberCode = document.getElementById('familyMemberCode');
            const switchFieldBtn = document.getElementById('switchFieldBtn');
            const backspaceBtn = document.getElementById('backspaceBtn');
            const clearBtn = document.getElementById('clearBtn');
            const digitButtons = document.querySelectorAll('[data-digit]');

            if (!familyNumber || !familyMemberCode) {
                return;
            }

            let activeField = familyNumber;

            function markActiveField() {
                familyNumber.classList.remove('active-input');
                familyMemberCode.classList.remove('active-input');
                activeField.classList.add('active-input');
            }

            function blockKeyboardInput(event) {
                event.preventDefault();
            }

            [familyNumber, familyMemberCode].forEach(function (input) {
                input.setAttribute('readonly', 'readonly');
                input.addEventListener('focus', function () {
                    activeField = input;
                    markActiveField();
                    input.blur();
                });
                input.addEventListener('keydown', blockKeyboardInput);
                input.addEventListener('keypress', blockKeyboardInput);
                input.addEventListener('paste', blockKeyboardInput);
                input.addEventListener('drop', blockKeyboardInput);
            });

            digitButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const digit = button.getAttribute('data-digit');
                    const maxLength = Number(activeField.getAttribute('maxlength') || 999);
                    if (activeField.value.length >= maxLength) return;
                    activeField.value += digit;
                });
            });

            switchFieldBtn.addEventListener('click', function () {
                activeField = activeField === familyNumber ? familyMemberCode : familyNumber;
                markActiveField();
            });

            backspaceBtn.addEventListener('click', function () {
                activeField.value = activeField.value.slice(0, -1);
            });

            clearBtn.addEventListener('click', function () {
                familyNumber.value = '';
                familyMemberCode.value = '';
                activeField = familyNumber;
                markActiveField();
            });

            markActiveField();
        })();
    </script>
</body>
</html>
