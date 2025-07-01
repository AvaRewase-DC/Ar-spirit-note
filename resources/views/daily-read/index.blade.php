@php
    use Carbon\Carbon;
    $day = Carbon::parse($reads->first()?->day);
    $year = $day->year ?? now()->year;
    $month = $day->month ?? now()->month;

    $startOfMonth = Carbon::create($year, $month, 1);
    $daysInMonth = $startOfMonth->daysInMonth;
    $startDayOfWeek = $startOfMonth->dayOfWeek;

    $weeks = ceil(($daysInMonth + $startDayOfWeek) / 7);
    $dayCounter = 1;

    $monthsArabic = [
        1 => 'يناير',
        2 => 'فبراير',
        3 => 'مارس',
        4 => 'أبريل',
        5 => 'مايو',
        6 => 'يونيو',
        7 => 'يوليو',
        8 => 'أغسطس',
        9 => 'سبتمبر',
        10 => 'أكتوبر',
        11 => 'نوفمبر',
        12 => 'ديسمبر',
    ];

    $daysArabic = ['الأحد', 'الاثنين', 'الثلاثاء', 'الأربعاء', 'الخميس', 'الجمعة', 'السبت'];
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title> شهر {{ $monthsArabic[$month] }} {{ $year }}</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            direction: rtl;
            text-align: center;
            background-color: #f9f9f9;
        }

        table {
            margin: 30px auto;
            border-collapse: collapse;
            width: 90%;
            max-width: 700px;
            background: #fff;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 15px;
            min-height: 80px; /* ارتفاع مرن */
            vertical-align: top;
            word-wrap: break-word;     /* التفاف النص */
            white-space: normal;        /* السماح بالتفاف الأسطر */
            overflow-wrap: break-word; /* دعم أفضل للتفاف النص */
        }

        th {
            background-color: #f0f0f0;
        }

        td {
            position: relative;
        }

        .date-number {
            font-weight: bold;
        }

        hr {
            margin: 8px 0;
            border: none;
            border-top: 1px solid #ccc;
        }
    </style>
</head>

<body>

    <h1>تقويم شهر {{ $monthsArabic[$month] }} {{ $year }}</h1>

    <table>
        <thead>
            <tr>
                @foreach ($daysArabic as $dayName)
                    <th>{{ $dayName }}</th>
                @endforeach
            </tr>
        </thead>

        <tbody>
            @for ($week = 0; $week < $weeks; $week++)
                <tr>
                    @for ($day = 0; $day < 7; $day++)
                        @php
                            $cellNumber = $week * 7 + $day;
                        @endphp

                        @if ($cellNumber < $startDayOfWeek || $dayCounter > $daysInMonth)
                            <td></td>
                        @else
                            <td>
                                <div class="date-number">{{ $dayCounter }}</div>
                                <hr>
                                @if (isset($reads[$dayCounter - 1]))
                                    {{ $reads[$dayCounter - 1]?->read_parts }}
                                @endif
                            </td>
                            @php $dayCounter++; @endphp
                        @endif
                    @endfor
                </tr>
            @endfor
        </tbody>
    </table>

</body>

</html>
