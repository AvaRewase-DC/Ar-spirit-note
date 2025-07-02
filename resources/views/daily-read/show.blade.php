@php
    use Carbon\Carbon;

    // Parse the model’s day field
    $parsedDate = Carbon::parse($read?->day);
    // Format like "1 يوليو 2025"
    Carbon::setLocale('ar'); // set Arabic locale
    $formattedDate = $parsedDate->isoFormat('D MMMM YYYY');
@endphp

<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>نوتة يوم {{ $formattedDate }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            direction: rtl;
            text-align: right;
            background-color: #f9f9f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: auto;
            background: #fff;
            padding: 20px;
            border-radius: 6px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 1.8em;
            margin-bottom: 1em;
        }

        .part {
            margin-bottom: 1.5em;
            padding: 15px;
            background-color: #f1f1f1;
            border-radius: 4px;
        }

        .part h2 {
            margin-top: 0;
            font-size: 1.3em;
            color: #333;
        }

        .back-link {
            display: inline-block;
            margin-top: 2em;
            padding: 10px 20px;
            background-color: #3490dc;
            color: #fff;
            text-decoration: none;
            border-radius: 4px;
        }

        .back-link:hover {
            background-color: #2779bd;
        }

        .empty {
            padding: 20px;
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 4px;
            color: #856404;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>نوتة يوم {{ $formattedDate }} - {{ $read->getCopticDate() }}</h1>

        @if ($read)
            <div class="part">
                <h2>الوصف</h2>
                <p>{{ $read->description }}</p>
            </div>
            <div class="part">
                <h2>القراءة</h2>
                <p>{{ $read->read_parts }}</p>
            </div>
            <div class="part">
                <h2>الكتاب المقدس</h2>
                <p>{{ $read->bible }}</p>
            </div>
            <div class="part">
                <h2>الاختبار</h2>
                <p>
                    <a href="{{ $read->quiz }}" target="_blank" rel="noopener noreferrer">
                        اضغط هنا للذهاب إلى الاختبار
                    </a>
                </p>
            </div>
            <div class="part">
                <h2>قطمارس اليوم</h2>
                <p>
                    <a href="{{ $read->katamars }}" target="_blank" rel="noopener noreferrer">
                        اضغط هنا للذهاب للقرأت اليوميه
                    </a>
                </p>
            </div>
        @else
            <div class="empty">
                لا توجد قراءة مسجلة لهذا اليوم.
            </div>
        @endif


        <a href="{{ route('daily-read.index', ['year' => $parsedDate->year, 'month' => $parsedDate->month]) }}"
            class="back-link">
            العودة إلى التقويم
        </a>
    </div>
</body>

</html>
