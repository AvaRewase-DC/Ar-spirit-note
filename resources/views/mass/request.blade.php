@php
    use Carbon\Carbon;
    Carbon::setLocale('ar');
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>طلب حضور خدمة</title>
    <style>
        :root {--ken-primary:#ad1100; --ken-bg:#f4f4f4; --ken-text:#353535; --ken-muted:#7c7c7c;}
        body {font-family: Tahoma, sans-serif; background: var(--ken-bg); margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .title {text-align: center; margin: 0;}
        .subtitle {text-align: center; color: var(--ken-muted); margin-top: 6px;}
        .label {font-weight: bold; margin-bottom: 10px; display: block;}
        .inline {display: flex; gap: 8px; align-items: center;}
        input[type="text"], input[type="tel"], select {width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;}
        .btn {background: var(--ken-primary); color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; width: 100%;}
        .help {font-size: 13px; color: var(--ken-muted); margin-top: 8px;}
        .keypad-wrap {margin-top: 12px;}
        .active-input {border-color: var(--ken-primary) !important; box-shadow: 0 0 0 2px rgba(173, 17, 0, 0.12);}
        .keypad-info {font-size: 13px; color: var(--ken-muted); margin: 8px 0;}
        .keypad-grid {display: grid; grid-template-columns: repeat(3, 1fr); gap: 8px;}
        .key-btn {background: #fff; color: var(--ken-primary); border: 1px solid rgba(173,17,0,.25); border-radius: 8px; padding: 12px; font-size: 18px; font-weight: bold; cursor: pointer;}
        .key-btn:hover {background: rgba(173,17,0,.08);}
        .key-btn:active {background: rgba(173,17,0,.22); box-shadow: 0 0 0 3px rgba(173,17,0,.2), 0 0 12px rgba(173,17,0,.45); transform: translateY(1px);}
        .key-btn.action {font-size: 14px;}
        .error {background: #ffe9e9; color: #a00; border: 1px solid #f2b9b9; padding: 10px; border-radius: 6px; margin-bottom: 10px;}
    </style>
</head>
<body>
    <div class="container">
        @if ($errors->any())
            <div class="error">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('mass.request.store') }}">
            @csrf

            <div class="card">
                <h2 class="title">طلب حجز خدمة</h2>
            </div>

            <div class="card">
                <span class="label"> الخدمة</span>
                <select name="massAppointmentId" required>
                    <option value="">اختر الخدمة</option>
                    @foreach ($appointments as $item)
                        @php
                            $appointmentDate = data_get($item, 'appointmentDate');
                            $dateText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('dddd D MMMM YYYY') : '';
                            $timeText = $appointmentDate ? Carbon::parse($appointmentDate)->addHours(2)->isoFormat('h:mm a') : '';
                        @endphp
                        <option value="{{ data_get($item, 'id') }}" @selected(old('massAppointmentId') == data_get($item, 'id'))>
                            {{ $dateText }} - {{ data_get($item, 'title') }} - {{ $timeText }} - {{ data_get($item, 'place') }} - الاماكن المتاحة: {{ data_get($item, 'availableSeats') }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="card">
                <span class="label" style="color: var(--ken-primary);"> بياناتك الشخصية</span>
                <div class="inline" dir="ltr">
                    <span>E1C1F</span>
                    <input id="familyNumber" type="tel" maxlength="5" name="familyNumber" value="{{ old('familyNumber') }}" required readonly inputmode="none" autocomplete="off">
                    <span>NR</span>
                    <input id="familyMemberCode" type="tel" maxlength="2" name="familyMemberCode" value="{{ old('familyMemberCode') }}" required readonly inputmode="none" autocomplete="off">
                    <span>رقم العضوية</span>
                </div>
            </div>

            <div class="card keypad-wrap" dir="ltr">
                <div id="keypadActiveLabel" class="keypad-info">الحقل الحالي: رقم العائلة</div>
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

            <button class="btn" type="submit">حجز الخدمة</button>
        </form>
    </div>

    <script>
        (function () {
            const familyNumber = document.getElementById('familyNumber');
            const familyMemberCode = document.getElementById('familyMemberCode');
            const switchFieldBtn = document.getElementById('switchFieldBtn');
            const backspaceBtn = document.getElementById('backspaceBtn');
            const clearBtn = document.getElementById('clearBtn');
            const digitButtons = document.querySelectorAll('[data-digit]');
            const activeLabel = document.getElementById('keypadActiveLabel');

            const fields = [
                { el: familyNumber, label: 'رقم العائلة' },
                { el: familyMemberCode, label: 'كود الفرد' },
            ];

            if (fields.some((field) => !field.el)) {
                return;
            }

            let activeIndex = 0;

            function markActiveField() {
                fields.forEach((field, index) => {
                    field.el.classList.toggle('active-input', index === activeIndex);
                });
                if (activeLabel) {
                    activeLabel.textContent = `الحقل الحالي: ${fields[activeIndex].label}`;
                }
            }

            function blockKeyboardInput(event) {
                event.preventDefault();
            }

            fields.forEach((field, index) => {
                field.el.setAttribute('readonly', 'readonly');
                field.el.addEventListener('focus', function () {
                    activeIndex = index;
                    markActiveField();
                    field.el.blur();
                });
                field.el.addEventListener('keydown', blockKeyboardInput);
                field.el.addEventListener('keypress', blockKeyboardInput);
                field.el.addEventListener('paste', blockKeyboardInput);
                field.el.addEventListener('drop', blockKeyboardInput);
            });

            digitButtons.forEach(function (button) {
                button.addEventListener('click', function () {
                    const digit = button.getAttribute('data-digit');
                    const activeField = fields[activeIndex].el;
                    const maxLength = Number(activeField.getAttribute('maxlength') || 999);
                    if (activeField.value.length >= maxLength) return;
                    activeField.value += digit;
                });
            });

            switchFieldBtn.addEventListener('click', function () {
                activeIndex = (activeIndex + 1) % fields.length;
                markActiveField();
            });

            backspaceBtn.addEventListener('click', function () {
                const activeField = fields[activeIndex].el;
                activeField.value = activeField.value.slice(0, -1);
            });

            clearBtn.addEventListener('click', function () {
                fields.forEach((field) => {
                    field.el.value = '';
                });
                activeIndex = 0;
                markActiveField();
            });

            markActiveField();
        })();
    </script>
</body>
</html>
