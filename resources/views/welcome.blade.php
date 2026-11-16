<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/png" href="{{ asset('images/2.png') }}?v=aru">
    <title>ARU</title>
    <style>
        body {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            background: linear-gradient(135deg, #e0eafc, #cfdef3);
            font-family: 'Arial', sans-serif;
            margin: 0;
        }
        .container {
            display: flex;
            max-width: 800px;
            width: 100%;
            background: #ffffff;
            border-radius: 12px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .form-section, .advertisement {
            flex: 1;
            padding: 2rem;
            box-sizing: border-box;
        }
        .form-section {
            border-right: 1px solid #ddd;
            text-align: center;
        }
        .form-section h2 {
            margin-bottom: 1.25rem;
            font-size: 24px;
            color: #333;
        }
        .form-group {
            margin-bottom: 1rem;
        }
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            color: #555;
            font-weight: 600;
        }
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 14px;
            transition: border-color 0.3s ease, box-shadow 0.3s ease;
        }
        .form-group input:focus {
            border-color: #007bff;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
            outline: none;
        }
        .btn {
            width: 100%;
            padding: 0.75rem;
            border: none;
            border-radius: 8px;
            background: #007bff;
            color: #fff;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s ease, transform 0.3s ease;
        }
        .btn:hover {
            background: #0056b3;
            transform: scale(1.02);
        }
        .btn:focus {
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.5);
        }
        .forgot-password {
            text-align: right;
            margin-top: 0.5rem;
        }
        .forgot-password a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .forgot-password a:hover {
            color: #0056b3;
        }
        .advertisement {
            background: linear-gradient(160deg, #f8fafc, #edf2f7);
            border-left: 1px solid #e6ebf0;
            color: #333;
            font-size: 14px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            width: 50%;
        }
        .advertisement-visual {
            display: grid;
            width: min(100%, 360px);
            aspect-ratio: 1 / 1;
            place-items: center;
            padding: 12px;
            overflow: hidden;
            border: 1px solid #e3e8ef;
            border-radius: 14px;
            background: #fff;
            box-shadow: 0 8px 24px rgba(34, 52, 74, 0.12);
        }
        .advertisement-visual img {
            display: block;
            width: 100%;
            height: 100%;
            border-radius: 8px;
            object-fit: cover;
            object-position: center;
            transition: opacity 180ms ease;
        }
        .advertisement p {
            max-width: 360px;
            min-height: 2.8em;
            margin: 0;
            color: #39485a;
            font-size: 1rem;
            font-weight: 600;
            line-height: 1.4;
        }
        .footer {
            text-align: center;
            margin-top: 1.25rem;
            font-size: 12px;
            color: #888;
        }
        .footer a {
            color: #007bff;
            text-decoration: none;
            transition: color 0.3s ease;
        }
        .footer a:hover {
            color: #0056b3;
        }
        .motivation-carousel {
            margin: 1.25rem 0 0;
            padding: 1rem 1.1rem;
            border: 1px solid #e3eaf2;
            border-left: 3px solid #d55e1c;
            border-radius: 10px;
            background: linear-gradient(135deg, #f8fafc, #f1f5f9);
            text-align: left;
        }
        .motivation-heading {
            margin: 0 0 0.45rem;
            color: #637287;
            font-size: 0.7rem;
            font-weight: 700;
            letter-spacing: 0.08em;
            text-transform: uppercase;
        }
        .motivation-quote {
            min-height: 3.2em;
            margin: 0;
            color: #26384d;
            font-size: 0.95rem;
            font-weight: 600;
            line-height: 1.55;
        }
        @media (max-width: 720px) {
            body {
                height: auto;
                min-height: 100vh;
                padding: 1rem 0;
            }
            .container {
                width: calc(100% - 2rem);
                max-width: 480px;
                flex-direction: column;
            }
            .form-section,
            .advertisement {
                width: 100%;
                padding: 1.5rem;
            }
            .form-section {
                border-right: 0;
                border-bottom: 1px solid #e6ebf0;
            }
            .advertisement {
                border-left: 0;
            }
            .advertisement-visual {
                width: min(100%, 320px);
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="form-section">
            <h2>WELCOME BACK !!</h2>
            <form method="POST" action="{{ route('login') }}">
                @csrf

                <!-- Email Address -->
                <div class="form-group">
                    <label for="email">{{ __('Email') }}</label>
                    <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username">
                    @error('email')
                        <div class="mt-2 text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Password -->
                <div class="form-group mt-4">
                    <label for="password">{{ __('Password') }}</label>
                    <input id="password" type="password" name="password" required autocomplete="current-password">
                    @error('password')
                        <div class="mt-2 text-red-600">{{ $message }}</div>
                    @enderror
                </div>

                <div class="form-group">
                    @if (Route::has('password.request'))
                        <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('password.request') }}">
                            {{ __('Forgot your password?') }}
                        </a>
                    @endif
                </div>

                <div class="form-group">
                    <button type="submit" class="btn">
                        {{ __('Log in') }}
                    </button>
                </div>
            </form>
            <section class="motivation-carousel" aria-label="Daily motivation">
                <p class="motivation-heading">A thought for your day</p>
                <p class="motivation-quote" id="motivationQuote" aria-live="polite">
                    Small steps taken every day lead to meaningful progress.
                </p>
            </section>

            <div class="footer">
                <p>&copy; <span id="currentYear"></span> ARU Library. All rights reserved.</p>
            </div>
        </div>
        <div class="advertisement">
            <div class="advertisement-visual">
                <img id="adImage" src="" alt="Advertisement">
            </div>
            <p id="adMessage">Loading advertisement...</p>
        </div>
    </div>

    <script>
       const advertisements = [
    {
        message: "Every book you organize creates an opportunity for someone to discover knowledge. Your work matters!",
        image: "images/2.png"
    },
    {
        message: "Your dedication keeps knowledge alive. Thank you for making Ardhi University Library a place of learning and discovery.",
        image: "images/3.jpg"
    },
    {
        message: "Behind every successful reader is a dedicated librarian. Keep inspiring minds through your valuable service!",
        image: "images/4.jpg"
    },
    {
        message: "Your passion for knowledge makes a lasting difference. Every day is another opportunity to inspire, support, and make an impact!",
        image: "images/5.jpg"
    },
];
        let currentIndex = 0;

        function showAdvertisement(index) {
            const ad = advertisements[index];
            document.getElementById('adImage').src = ad.image;
            document.getElementById('adMessage').textContent = ad.message;
        }

        function showAdvertisementAtOffset(offset) {
            currentIndex = (currentIndex + offset + advertisements.length) % advertisements.length;
            showAdvertisement(currentIndex);
        }

        showAdvertisement(currentIndex);

        // Set the current year in the footer
        document.getElementById('currentYear').textContent = new Date().getFullYear();

        const motivationalQuotes = [
            "Small steps taken every day lead to meaningful progress.",
            "Every reader, question, and discovery helps knowledge grow.",
            "A helpful service today can inspire a lifetime of learning.",
            "Curiosity opens doors that routine may never find.",
            "Together, we make learning easier to reach for everyone.",
            "Your effort today is an investment in tomorrow.",
            "Every challenge is a chance to learn something new.",
            "Knowledge grows when it is shared.",
            "A kind word can make someone's difficult day easier.",
            "Consistency turns good intentions into real progress.",
            "The best time to begin learning is now.",
            "Your work helps someone take their next step.",
            "Be patient with progress; meaningful things take time.",
            "Ask questions. Curiosity is a strength.",
            "Every page turned brings a new possibility.",
            "Small acts of service create lasting impact.",
            "You do not need to know everything to keep learning.",
            "A fresh perspective can change the whole day.",
            "Keep going; steady effort adds up.",
            "Helping others find knowledge is valuable work.",
            "Make today a little better than yesterday.",
            "Your ideas matter. Give them room to grow.",
            "Learning begins with the courage to ask.",
            "A thoughtful choice can open a new path.",
            "Progress is built one useful action at a time.",
            "Every person you help carries that kindness forward.",
            "Stay curious, stay open, and keep moving.",
            "Good work is often made of small details done well.",
            "Your dedication makes a difference, even when it goes unseen.",
            "There is always something new to discover.",
            "Treat each new day as another opportunity to improve.",
            "A little patience can make room for a better answer.",
            "Great learning can start with one simple question.",
            "Your positive attitude can encourage the people around you.",
            "Use what you know to help someone else grow.",
            "A setback is a moment to adjust, not a reason to stop.",
            "Every effort toward understanding is worthwhile.",
            "Be proud of the progress you have already made.",
            "A welcoming space helps ideas flourish.",
            "The knowledge you share can travel far beyond today.",
            "Focus on the next step, then take it with care.",
            "Your service helps turn information into opportunity.",
            "Listen well; every person has something to teach.",
            "Choose progress over perfection.",
            "Encouragement can help someone believe in what is possible.",
            "Keep building habits that support the future you want.",
            "A curious mind can find opportunity in ordinary moments.",
            "Make room for learning, and growth will follow.",
            "Your commitment helps the whole community move forward.",
            "Finish today knowing your effort mattered.",
            "Believe in the value of the work you do every day.",
            "Every new skill begins with a willingness to try.",
            "Success grows from patience, practice, and determination.",
            "Your actions today help shape your future.",
            "Never underestimate the power of a positive mindset.",
            "A determined mind can overcome many obstacles.",
            "Every opportunity to learn is an opportunity to grow.",
            "Be the reason someone feels encouraged today.",
            "Great achievements begin with simple decisions.",
            "Keep your goals clear and your efforts consistent.",
            "Every step forward brings you closer to your dreams.",
            "Learning is a journey with endless possibilities.",
            "Your persistence can turn challenges into achievements.",
            "Make every opportunity count.",
            "A strong foundation creates room for greater success.",
            "Do something today that your future self will appreciate.",
            "Your willingness to improve is already a great achievement.",
            "Positive change begins with a single action.",
            "The more you learn, the more possibilities you discover.",
            "Let your actions reflect your ambitions.",
            "Every mistake offers a lesson worth remembering.",
            "Hard work becomes meaningful when guided by purpose.",
            "Great things happen when people work together.",
            "Stay focused on what you can improve today.",
            "Your kindness can create a ripple of positive change.",
            "A goal becomes achievable when you take the first step.",
            "Keep your mind open to new ideas and experiences.",
            "Success is built through everyday commitment.",
            "Your contribution makes the community stronger.",
            "Be willing to learn, grow, and try again.",
            "Every day brings a fresh chance to do meaningful work.",
            "A positive attitude can transform an ordinary task.",
            "The journey of growth begins with curiosity.",
            "Let determination guide you through difficult moments.",
            "Share your knowledge and multiply its value.",
            "Your dedication today creates opportunities for tomorrow.",
            "Small improvements can lead to remarkable results.",
            "Choose to make a positive difference wherever you are.",
            "A meaningful journey starts with a clear purpose.",
            "Keep discovering what you are capable of achieving.",
            "The best lessons often come from experience.",
            "Your patience and effort are building something worthwhile.",
            "Make every interaction an opportunity to help.",
            "Growth happens when you remain willing to learn.",
            "A helpful heart can make a lasting difference.",
            "Turn your ideas into actions and your actions into progress.",
            "Every achievement begins with the decision to try.",
            "Be consistent, remain hopeful, and trust the process.",
            "Knowledge is a gift that becomes greater when shared.",
            "Tomorrow's possibilities begin with today's effort."
        ];
        let quoteIndex = 0;
        const quoteElement = document.getElementById('motivationQuote');

        function showQuote(index) {
            quoteIndex = (index + motivationalQuotes.length) % motivationalQuotes.length;
            quoteElement.textContent = motivationalQuotes[quoteIndex];
        }

        function changeDailyContent(offset) {
            showQuote(quoteIndex + offset);
            showAdvertisementAtOffset(offset);
        }

        showQuote(quoteIndex);
        setInterval(function () {
            changeDailyContent(1);
        }, 30000);
    </script>
</body>
</html>
