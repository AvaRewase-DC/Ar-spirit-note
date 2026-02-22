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
        body {font-family: Tahoma, sans-serif; background: #f5f6fa; margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 14px; margin-bottom: 12px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .title {text-align: center; margin: 0;}
        .subtitle {text-align: center; color: #666; margin-top: 6px;}
        .label {font-weight: bold; margin-bottom: 10px; display: block;}
        .inline {display: flex; gap: 8px; align-items: center;}
        input[type="text"], input[type="tel"], select {width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 6px; box-sizing: border-box;}
        .btn {background: #2c4ec7; color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; width: 100%;}
        .help {font-size: 13px; color: #666; margin-top: 8px;}
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
                <h2 class="title">طلب حضور خدمة</h2>
                <p class="subtitle">برجاء ادخال البيانات المطلوبه ادناه لتتمكن من تسجيل حضور الخدمة</p>
            </div>

            <div class="card">
                <span class="label">اختر الخدمة</span>
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
                <span class="label">ادخل بياناتك الشخصية</span>
                <div class="inline" dir="ltr">
                    <span>E1C1F</span>
                    <input type="tel" maxlength="5" name="familyNumber" value="{{ old('familyNumber') }}" required>
                    <span>NR</span>
                    <input type="tel" maxlength="2" name="familyMemberCode" value="{{ old('familyMemberCode') }}" required>
                    <span>رقم العضوية</span>
                </div>
            </div>

            <div class="card">
                <input type="text" name="memberName" placeholder="الاسم بالكامل" value="{{ old('memberName') }}" required>
            </div>

            <div class="card">
                <input type="tel" name="nationalId" maxlength="14" placeholder="الرقم القومي" value="{{ old('nationalId') }}" required oninput="parseNationalId()">
            </div>

            <p class="help">أدخل الرقم القومي الخاص بالأب أو الأم للأعضاء الأقل من 16 سنة الغير معروف الرقم القومي الخاص بهم</p>

            <div class="card">
                <input type="tel" name="mobile" maxlength="11" placeholder="رقم الموبايل" value="{{ old('mobile') }}" required>
            </div>

            <div class="card">
                <input type="text" id="birthDate" name="birthDateDisplay" placeholder="تاريخ الميلاد" value="" disabled>
            </div>

            <div class="card">
                <select id="gender" name="genderDisplay" disabled>
                    <option value="">النوع</option>
                    <option value="0">ذكر</option>
                    <option value="1">انثي</option>
                </select>
            </div>

            <button class="btn" type="submit">حجز الخدمة</button>
        </form>
    </div>

    <script>
        function parseNationalId() {
            const nationalId = (document.querySelector('input[name="nationalId"]').value || '').replace(/\D/g, '');
            if (nationalId.length < 13) return;

            const century = nationalId[0] === '2' ? '19' : '20';
            const year = century + nationalId.substring(1, 3);
            const month = nationalId.substring(3, 5);
            const day = nationalId.substring(5, 7);

            document.getElementById('birthDate').value = `${year}-${month}-${day}`;
            const genderDigit = parseInt(nationalId.substring(12, 13), 10);
            document.getElementById('gender').value = Number.isNaN(genderDigit) ? '' : (genderDigit % 2 === 0 ? '1' : '0');
        }
        parseNationalId();
    </script>
</body>
</html>
