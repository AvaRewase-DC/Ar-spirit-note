@php
    use Carbon\Carbon;

    $parsedDate = Carbon::parse($read?->day ?? today());
    Carbon::setLocale('ar');
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
            background-color: #f0f2f5;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 850px;
            margin: 30px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 2em;
            margin-bottom: 10px;
            color: #333;
        }

        .date-subtitle {
            font-size: 1em;
            color: #666;
            margin-bottom: 30px;
        }

        .part {
            margin-bottom: 25px;
            padding: 20px;
            border-right: 5px solid #ffc107;
            background-color: #fdfdfd;
            border-radius: 8px;
            transition: 0.3s ease;
        }

        .part:hover {
            background-color: #fffbea;
        }

        .part h2 {
            margin-top: 0;
            margin-bottom: 10px;
            font-size: 1.4em;
            color: #222;
        }

        .part p {
            margin: 0;
            font-size: 1em;
            line-height: 1.8;
            color: #444;
        }

        a {
            color: #007bff;
            text-decoration: none;
        }

        a:hover {
            text-decoration: underline;
        }

        .back-link {
            display: inline-block;
            margin-top: 30px;
            padding: 12px 25px;
            background-color: #3490dc;
            color: #fff;
            text-decoration: none;
            border-radius: 6px;
            font-size: 1em;
        }

        .back-link:hover {
            background-color: #2779bd;
        }

        .empty {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            border-radius: 6px;
            padding: 20px;
            font-size: 1.1em;
            color: #856404;
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px;
            }

            h1 {
                font-size: 1.5em;
            }

            .part h2 {
                font-size: 1.2em;
            }
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>يوم {{ $formattedDate }}</h1>
        @if ($read)
            <div class="date-subtitle">
                الموافق <strong>{{ $read?->getCopticDate() }}</strong>
            </div>
        @endif

        @if ($read)
            @if ($read->description)
                <div class="part">
                    <h2>الوصف</h2>
                    <p>{{ $read->description }}</p>
                </div>
            @endif

            @if ($read->read_parts)
                <div class="part">
                    <h2>القراءة</h2>
                    <p>{{ $read->read_parts }}</p>
                </div>
            @endif

            @if ($read->videos)
                <div class="part">
                    <h2>تفاسير</h2>
                    @foreach ($read->videos as $video)
                        <p>
                            <a href="{{ $video }}" target="_blank" rel="noopener noreferrer">
                                اضغط هنا للذهاب للقراءات اليومية
                            </a>
                        </p>
                    @endforeach
                </div>
            @endif

            @if ($read->bible)
                <div class="part">
                    <h2>الكتاب المقدس</h2>
                    <p>{{ $read->bible }}</p>
                </div>
            @else
                <div class="part">
                    <h2>الكتاب المقدس</h2>
                    <p>لا يوجد كتاب مقدس.</p>
                </div>
            @endif

            @if ($read->quiz)
                <div class="part">
                    <h2>الاختبار</h2>
                    <p>
                        <a href="{{ $read->quiz }}" target="_blank" rel="noopener noreferrer">
                            اضغط هنا للذهاب إلى الاختبار
                        </a>
                    </p>
                </div>
            @else
                <div class="part">
                    <h2>الاختبار</h2>
                    <p>لا توجد اختبارات.</p>
                </div>
            @endif

            @if ($read->katamars)
                <div class="part">
                    <h2>قطمارس اليوم</h2>
                    <p>
                        <a href="{{ $read->katamars }}" target="_blank" rel="noopener noreferrer">
                            اضغط هنا للذهاب للقراءات اليومية
                        </a>
                    </p>
                </div>
            @else
                <div class="part">
                    <h2>قطمارس اليوم</h2>
                    <p>لا يوجد قطمارس متاح الآن.</p>
                </div>
            @endif
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
