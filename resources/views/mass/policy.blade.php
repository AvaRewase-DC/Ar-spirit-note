<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تعليمات حضور الخدمة</title>
    <style>
        body {font-family: Tahoma, sans-serif; background: #f5f6fa; margin: 0;}
        .container {max-width: 860px; margin: 24px auto; padding: 16px;}
        .card {background: #fff; border-radius: 8px; padding: 16px; box-shadow: 0 2px 6px rgba(0,0,0,.06);}
        .title {font-size: 20px; text-align: center; margin-top: 0;}
        .subtitle {text-align: center; color: #666;}
        .policy {color: #666; line-height: 1.9; margin-top: 16px;}
        .footer {display: flex; gap: 10px; align-items: center; margin-top: 14px;}
        .btn {background: #2c4ec7; color: #fff; border: 0; padding: 10px 14px; border-radius: 6px; cursor: pointer; text-decoration: none; display: inline-block; margin-top: 10px;}
        .btn:disabled {opacity: .5; cursor: not-allowed;}
    </style>
</head>
<body>
    <div class="container">
        <div class="card">
            <h1 class="title">تعليمات حضور الخدمة</h1>
            <p class="subtitle">ابن الطاعة تحل عليه البركة</p>

            <div class="policy">{!! $massSetting['massPolicy'] !!}</div>

            <div class="footer">
                <input id="agree" type="checkbox">
                <label for="agree">لقد قرأت تعليمات حضور الخدمة واوافق عليها</label>
            </div>

            <a id="submitBtn" class="btn" href="{{ route('mass.request') }}" onclick="return canProceed(event)">تأكيد الحجز</a>
        </div>
    </div>

    <script>
        function canProceed(e) {
            if (!document.getElementById('agree').checked) {
                e.preventDefault();
                return false;
            }
            return true;
        }
    </script>
</body>
</html>
