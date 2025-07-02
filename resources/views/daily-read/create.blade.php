<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>إنشاء قراءة يومية</title>
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
        <h2>إنشاء قراءة يومية جديدة</h2>

        <form action="{{ route('daily-read.store') }}" method="POST">
            @csrf

            <label for="day">اليوم</label>
            <input type="date" name="day" id="day" required>

            <label for="description">الوصف</label>
            <textarea name="description" id="description" rows="3"></textarea>

            <label for="katamars">رابط القطمارس</label>
            <input type="url" name="katamars" id="katamars">

            <label for="read_parts">القراءات</label>
            <textarea name="read_parts" id="read_parts" rows="3"></textarea>

            <label for="bible">الكتاب المقدس</label>
            <textarea name="bible" id="bible" rows="3"></textarea>

            <label for="quiz">رابط الاختبار</label>
            <input type="url" name="quiz" id="quiz">

            <label>روابط الفيديو</label>
            <div id="video-links">
                <input type="url" name="videos[]" placeholder="أدخل رابط الفيديو">
            </div>
            <button type="button" class="btn-secondary" onclick="addVideoInput()">+ أضف رابط فيديو آخر</button>

            <br><br>
            <button type="submit">حفظ</button>
        </form>
    </div>

    <script>
        function addVideoInput() {
            const container = document.getElementById('video-links');
            const input = document.createElement('input');
            input.type = 'url';
            input.name = 'videos[]';
            input.placeholder = 'أدخل رابط الفيديو';
            input.className = 'mt-2';
            container.appendChild(input);
        }
    </script>
</body>
</html>
