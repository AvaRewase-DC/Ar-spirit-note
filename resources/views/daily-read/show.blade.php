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
            @if (!empty($read->videos))
                <div class="part">
                    <h2>تفاسير</h2>
                    @foreach ($read->videos as $video)
                        @php $url = $video ? $video->video ?? '#' : $video; @endphp
                        <p>
                            <a href="#" class="video-popup" data-url="{{ $url }}">مشاهدة الفيديو</a>
                        </p>
                    @endforeach

                </div>
            @endif


            <div class="part">
                <h2>الكتاب المقدس</h2>
                <p>{{ $read->bible ?? 'لا يوجد كتاب مقدس.' }}</p>
            </div>

            <div class="part">
                <h2>الاختبار</h2>
                @if ($read->quiz)
                    <p><a href="{{ $read->quiz }}" target="_blank">اضغط هنا للذهاب إلى الاختبار</a></p>
                @else
                    <p>لا توجد اختبارات.</p>
                @endif
            </div>

            <div class="part">
                <h2>قطمارس اليوم</h2>
                @if ($read->katamars)
                    <p><a href="{{ $read->katamars }}" target="_blank">اضغط هنا للذهاب للقراءات اليومية</a></p>
                @else
                    <p>لا يوجد قطمارس متاح الآن.</p>
                @endif
            </div>
        @else
            <div class="empty">لا توجد قراءة مسجلة لهذا اليوم.</div>
        @endif

        <a href="{{ route('daily-read.index', ['year' => $parsedDate->year, 'month' => $parsedDate->month]) }}"
            class="back-link">
            العودة إلى التقويم
        </a>
    </div>
    <!-- Video Modal (supports iframe and <video>) -->
    <div id="videoModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
           background:rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:9999;">
        <div
            style="position:relative; width:90%; max-width:800px; background:#000; border-radius:8px; overflow:hidden;">
            <!-- For iframe embeds (YouTube, Vimeo, etc.) -->
            <iframe id="iframePlayer" style="display:none;" width="100%" height="450" frameborder="0"
                allowfullscreen allow="autoplay"></iframe>

            <!-- For direct video files -->
            <video id="videoPlayer" style="display:none; width:100%; height:auto;" controls></video>

            <button onclick="closeVideoModal()"
                style="position:absolute; top:-10px; right:-10px; background:red; color:white;
                   border:none; border-radius:50%; width:30px; height:30px; font-size:18px; cursor:pointer;">×</button>
        </div>
    </div>


</body>
<script>
    document.querySelectorAll('.video-popup').forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const url = this.getAttribute('data-url');
            openVideoModal(url);
        });
    });

    function openVideoModal(url) {
        const iframe = document.getElementById('iframePlayer');
        const video = document.getElementById('videoPlayer');
        const modal = document.getElementById('videoModal');

        iframe.style.display = 'none';
        video.style.display = 'none';
        iframe.src = '';
        video.src = '';

        if (isDirectVideo(url)) {
            video.src = url;
            video.style.display = 'block';
        } else if (isYouTube(url)) {
            const embedUrl = convertYouTubeEmbed(url);
            iframe.src = embedUrl;
            iframe.style.display = 'block';
        } else if (isVimeo(url)) {
            const embedUrl = convertVimeoEmbed(url);
            iframe.src = embedUrl;
            iframe.style.display = 'block';
        } else {
            // Fallback: open in new tab
            window.open(url, '_blank');
            return;
        }

        modal.style.display = 'flex';
    }

    function closeVideoModal() {
        document.getElementById('iframePlayer').src = '';
        document.getElementById('videoPlayer').pause();
        document.getElementById('videoPlayer').src = '';
        document.getElementById('videoModal').style.display = 'none';
    }

    function isDirectVideo(url) {
        return /\.(mp4|webm|ogg)$/i.test(url);
    }

    function isYouTube(url) {
        return /youtu\.?be/.test(url);
    }

    function isVimeo(url) {
        return /vimeo\.com/.test(url);
    }

    function convertYouTubeEmbed(url) {
        const match = url.match(/(?:v=|\/)([0-9A-Za-z_-]{11})/);
        const videoId = match ? match[1] : null;
        return videoId ? `https://www.youtube.com/embed/${videoId}?autoplay=1` : url;
    }

    function convertVimeoEmbed(url) {
        const match = url.match(/vimeo\.com\/(\d+)/);
        const videoId = match ? match[1] : null;
        return videoId ? `https://player.vimeo.com/video/${videoId}?autoplay=1` : url;
    }
</script>


</html>
