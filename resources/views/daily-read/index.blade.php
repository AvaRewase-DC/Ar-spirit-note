@php
    use Carbon\Carbon;

    $day = Carbon::parse($reads->first()?->day ?? now());
    $year = $day->year;
    $month = $day->month;

    $monthsArabic = [
        1 => 'يناير', 2 => 'فبراير', 3 => 'مارس', 4 => 'أبريل',
        5 => 'مايو', 6 => 'يونيو', 7 => 'يوليو', 8 => 'أغسطس',
        9 => 'سبتمبر', 10 => 'أكتوبر', 11 => 'نوفمبر', 12 => 'ديسمبر',
    ];
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>شهر {{ $monthsArabic[$month] }} {{ $year }}</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background-color: #f9f9f9;
            margin: 0;
            padding: 0;
            direction: rtl;
            text-align: center;
        }

        h1 {
            font-size: 1.5em;
            padding: 10px;
        }

        table {
            width: 100%;
            max-width: 700px;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            table-layout: fixed;
        }

        th, td {
            border: 1px solid #ccc;
            padding: 10px;
            min-height: 60px;
            font-size: 0.8em;
        }

        th {
            background-color: yellow;
        }

        .date-number {
            font-weight: bold;
            font-size: 0.75em;
        }

        hr {
            margin: 8px 0;
            border: none;
            border-top: 1px solid #ccc;
        }

        #month-selector {
            margin: 20px auto;
            max-width: 700px;
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 10px;
        }

        #month-selector div {
            cursor: pointer;
            padding: 10px 15px;
            border-radius: 6px;
            font-size: 0.9em;
            font-weight: bold;
            min-width: 60px;
            text-align: center;
        }

        .active {
            background-color: #ffc107;
            color: #000;
        }

        .inactive {
            background-color: #e0e0e0;
            color: #333;
        }

        @media (max-width: 600px) {
            th, td {
                padding: 8px;
                font-size: 0.7em;
            }

            .date-number {
                font-size: 0.7em;
            }
        }
    </style>
</head>

<body>
    <h1 id="calendar-title">شهر {{ $monthsArabic[$month] }} {{ $year }}</h1>

    <!-- Calendar Table Container -->
    <div id="calendar-table">
        @include('daily-read.partial-table', ['reads' => $reads, 'year' => $year, 'month' => $month])
    </div>

    <!-- Month Selector -->
    <div id="month-selector"></div>

    <script>
        const monthsArabic = @json($monthsArabic);
        let currentYear = {{ $year }};
        let currentMonth = {{ $month }};

        const monthSelector = document.getElementById('month-selector');
        const calendarTable = document.getElementById('calendar-table');
        const title = document.getElementById('calendar-title');

        for (const [num, name] of Object.entries(monthsArabic)) {
            const box = document.createElement('div');
            box.textContent = name;
            box.className = parseInt(num) === currentMonth ? 'active' : 'inactive';

            box.addEventListener('click', async () => {
                currentMonth = parseInt(num);

                try {
                    const response = await fetch(`/daily-reads?month=${currentMonth}&year=${currentYear}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) throw new Error('Failed to load calendar');

                    const html = await response.text();
                    calendarTable.innerHTML = html;
                    title.textContent = `شهر ${monthsArabic[currentMonth]} ${currentYear}`;

                    document.querySelectorAll('#month-selector div').forEach(div => div.className = 'inactive');
                    box.className = 'active';

                } catch (err) {
                    alert('حدث خطأ أثناء تحميل التقويم');
                    console.error(err);
                }
            });

            monthSelector.appendChild(box);
        }
    </script>
</body>
</html>
