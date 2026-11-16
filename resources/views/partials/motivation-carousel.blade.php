<style>
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
</style>

<section class="motivation-carousel" aria-label="Daily motivation">
    <p class="motivation-heading">Hello, {{ Auth::user()->name }}! Remember:</p>
    <p class="motivation-quote" id="motivationQuote" aria-live="polite"></p>
</section>

<script>
    (function () {
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

        showQuote(quoteIndex);
        setInterval(function () {
            showQuote(quoteIndex + 1);
        }, 30000);
    })();
</script>
