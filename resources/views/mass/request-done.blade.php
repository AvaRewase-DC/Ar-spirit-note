<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تم ارسال الطلب</title>
    <style>
        :root {--ken-primary:#ad1100; --ken-bg:#f4f4f4;}
        body {font-family: Tahoma, sans-serif; background: var(--ken-bg); margin: 0;}
        .container {max-width: 700px; margin: 50px auto; padding: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 20px; box-shadow: 0 2px 6px rgba(0,0,0,.06); text-align: center;}
        .btn {background: var(--ken-primary); color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 14px;}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h2>تم ارسال طلبك بنجاح</h2>
            <p>برجاء مراجعة حالة الطلب. سنقوم بمراجعة الطلب في اقرب فرصة .</p>
            <a class="btn" href="{{ route('mass.index') }}">العودة لطلبات الحجز</a>
        </div>
    </div>
</body>
</html>
