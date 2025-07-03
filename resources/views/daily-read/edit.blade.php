<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="UTF-8">
    <title>تعديل القراءة اليومية</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            direction: rtl;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }

        .container {
            max-width: 800px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
        }

        h2 {
            margin-bottom: 25px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 6px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="url"],
        input[type="date"],
        textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 6px;
            box-sizing: border-box;
        }

        button {
            padding: 10px 20px;
            background-color: #3490dc;
            border: none;
            color: white;
            font-size: 1em;
            border-radius: 6px;
            cursor: pointer;
        }

        button:hover {
            background-color: #2779bd;
        }

        .btn-secondary {
            background-color: #6c757d;
            margin-top: -10px;
            margin-bottom: 15px;
        }

        .btn-secondary:hover {
            background-color: #5a6268;
        }
    </style>
</head>

<body>
    <div class="container">
        <h2>تعديل القراءة اليومية</h2>

        @if ($errors->any())
            <div
                style="background-color: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                <strong>حدثت أخطاء:</strong>
                <ul style="margin: 10px 0 0 0; padding-right: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @if (session('success'))
            <div
                style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                {{ session('success') }}
            </div>
        @endif

        <form action="{{ route('daily-read.update', $read->id) }}" method="POST">
            @csrf
            @method('PUT')

            <label for="day">اليوم</label>
            <input type="date" name="day" id="day"
                value="{{ old('day', Carbon\Carbon::parse($read->day)->format('Y-m-d')) }}" required>

            <label for="description">الوصف</label>
            <textarea name="description" id="description" rows="3">{{ old('description', $read->description) }}</textarea>

            <label for="katamars">رابط القطمارس</label>
            <input type="url" name="katamars" id="katamars" value="{{ old('katamars', $read->katamars) }}">

            <label for="read_parts">القراءات</label>
            <textarea name="read_parts" id="read_parts" rows="3">{{ old('read_parts', $read->read_parts) }}</textarea>

            <label for="bible">الكتاب المقدس</label>
            <textarea name="bible" id="bible" rows="3">{{ old('bible', $read->bible) }}</textarea>

            <label for="quiz">رابط الاختبار</label>
            <input type="url" name="quiz" id="quiz" value="{{ old('quiz', $read->quiz) }}">

            <label>روابط الفيديو</label>
            <div id="video-links">
                @forelse(old('videos', $read->videos ?? []) as $video)
                    <input type="url" name="videos[]" value="{{ $video->video }}" placeholder="أدخل رابط الفيديو">
                @empty
                    <input type="url" name="videos[]" placeholder="أدخل رابط الفيديو">
                @endforelse
            </div>
            <button type="button" class="btn-secondary" onclick="addVideoInput()">+ أضف رابط فيديو آخر</button>

            <br><br>
            <button type="submit">تحديث</button>
        </form>
    </div>

    <script>
        function addVideoInput() {
            const container = document.getElementById('video-links');
            const input = document.createElement('input');
            input.type = 'url';
            input.name = 'videos[]';
            input.placeholder = 'أدخل رابط الفيديو';
            container.appendChild(input);
        }

        // Remove empty optional fields before submission
        document.querySelector('form').addEventListener('submit', function(e) {
            const inputs = this.querySelectorAll('input[type="url"], textarea');
            inputs.forEach(input => {
                if (!input.required && !input.value.trim()) {
                    input.remove();
                }
            });
        });
    </script>
</body>

</html>
