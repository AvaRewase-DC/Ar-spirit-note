@php
    use Carbon\Carbon;

    $parsedDate = Carbon::parse($read?->day);
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

            @if ($read->relationLoaded('saintFests') && !$read->saintFests->isEmpty())
                <div class="date-subtitle">
                    أعياد: <br>
                    @foreach ($read->saintFests as $fest)
                        - <strong>{{ $fest->title }}</strong>
                    @endforeach
                </div>
            @endif

            @if ($read->description)
                <div class="part">
                    <h2>الوصف</h2>
                    <p>{{ $read->description }}</p>
                </div>
            @endif

            @if ($read->read_parts)
                <div class="part">
                    <h2>القراءة</h2>
                    @if (!empty($read->bible))
                        <p><a href="#" id="bibleLink"> {{ $read->read_parts }}</a></p>
                    @else
                        <p>{{ $read->read_parts }}</p>
                    @endif
                </div>
            @endif
            @if ($read->relationLoaded('videos') && $read->videos->isNotEmpty())
                <div class="part">
                    <h2>تفاسير</h2>
                    @foreach ($read->videos as $video)
                        @php
                            $url = is_object($video) ? $video->video ?? '#' : $video ?? '#';
                        @endphp
                        @if ($url && $url !== '#')
                            <p><a href="#" class="video-popup" data-url="{{ $url }}">مشاهدة
                                    الفيديو</a>
                            </p>
                        @endif
                    @endforeach
                </div>
            @endif

            {{-- <div class="part">
                <h2>الكتاب المقدس</h2>
                @if (!empty($read->bible))
                    <p><a href="#" id="bibleLink"> {{ $read->read_parts }}</a></p>
                @else
                    <p>لا يوجد كتاب مقدس.</p>
                @endif
            </div> --}}


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
        @if ($read)
            <a href="{{ route('daily-read.edit', $read->id) }}"
                style="display: inline-block; margin-top: 20px; margin-left: 10px;
              padding: 12px 25px; background-color: darkred; color: #fff;
              text-decoration: none; border-radius: 6px; font-size: 1em;">
                تعديل
            </a>
        @endif

    </div>

    <!-- Video Modal -->
    <div id="videoModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
           background:rgba(0,0,0,0.7); justify-content:center; align-items:center; z-index:9999;">
        <div
            style="position:relative; width:90%; max-width:800px; background:#000; border-radius:8px; overflow:hidden;">
            <iframe id="iframePlayer" style="display:none;" width="100%" height="450" frameborder="0"
                allowfullscreen allow="autoplay"></iframe>
            <video id="videoPlayer" style="display:none; width:100%; height:auto;" controls></video>
            <button onclick="closeVideoModal()"
                style="position:absolute; top:-10px; right:-10px; background:red; color:white;
                   border:none; border-radius:50%; width:30px; height:30px; font-size:18px; cursor:pointer;">×</button>
        </div>
    </div>

    <!-- Bible Modal -->
    <div id="bibleModal"
        style="display:none; position:fixed; top:0; left:0; width:100%; height:100%;
        background:rgba(0,0,0,0.6); justify-content:center; align-items:center; z-index:9999;">
        <div
            style="background:#fff; padding:30px; max-width:800px; width:90%; border-radius:15px; position:relative; max-height:80vh; overflow-y:auto;">
            <button onclick="closeBibleModal()"
                style="position:absolute; top:-10px; right:-10px; background:red; color:white;
                border:none; border-radius:50%; width:30px; height:30px; font-size:18px; cursor:pointer;">×</button>
            <h2 style="margin-top:0;">{{ $read?->read_parts }}</h2>
            <div id="bibleContent" style="white-space: pre-wrap; line-height: 1.8; color: #333;">
                {{ $read?->bible }}
            </div>
        </div>
    </div>


    <script>
        // Video logic
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
                iframe.src = convertYouTubeEmbed(url);
                iframe.style.display = 'block';
            } else if (isVimeo(url)) {
                iframe.src = convertVimeoEmbed(url);
                iframe.style.display = 'block';
            } else {
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

        // 👇 Triple-click in bottom-right to open edit
        let clickCount = 0;
        let clickTimer;
        document.addEventListener('click', function(e) {
            const x = e.clientX;
            const y = e.clientY;
            const w = window.innerWidth;
            const h = window.innerHeight;

            const inBottomRight = x > (w - 100) && y > (h - 100);

            if (inBottomRight) {
                clickCount++;
                if (clickCount >= 3 && $read) {
                    window.location.href = "{{ $read ? route('daily-read.edit', $read->id) : '#' }}";
                }
                clearTimeout(clickTimer);
                clickTimer = setTimeout(() => clickCount = 0, 2000);
            } else {
                clickCount = 0;
            }
        });

        const videoModal = document.getElementById('videoModal');
        const videoBox = videoModal.querySelector('div'); // this is the black box containing the video

        videoModal.addEventListener('click', function(e) {
            if (!videoBox.contains(e.target)) {
                closeVideoModal();
            }
        });
    </script>

    <script>
        const bibleModal = document.getElementById('bibleModal');
        const bibleContent = bibleModal.querySelector('div');

        document.getElementById('bibleLink')?.addEventListener('click', function(e) {
            e.preventDefault();
            bibleModal.style.display = 'flex';
        });

        function closeBibleModal() {
            bibleModal.style.display = 'none';
        }

        // Close when clicking outside the modal content
        bibleModal.addEventListener('click', function(e) {
            if (!bibleContent.contains(e.target)) {
                closeBibleModal();
            }
        });
    </script>

</body>

</html>
