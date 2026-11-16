@extends('layouts.header')

@section('content')
<style>
    /* Consolidated and updated CSS */
   /* Consolidated and updated CSS */
.logo {
    text-align: center;
    color: lightblue;
}
.emblem, .client-logo img {
    max-height: 80px;
    width: auto;
}
.title {
    font-size: 2rem;
    color: #007bff;
    margin: 0;
}
.my-swal-popup {
    border-radius: 15px;
    padding: 20px;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
    background: linear-gradient(135deg, #f0f8ff, #e6e6e6);
}
.my-swal-title {
    font-size: 1.5rem;
    font-weight: bold;
    color: #007bff;
}
.my-swal-text {
    font-size: 1rem;
    color: #333;
    margin-top: 10px;
}
.my-swal-timer-progress {
    height: 5px;
    background: #007bff;
}
.swal2-backdrop {
    background-color: rgba(0, 0, 0, 0.5);
}
.container {
    padding: 10px;
}
.chat-box {
    border: 1px solid #ccc;
    padding: 20px;
    border-radius: 8px;
    height: 100%;
    display: flex;
    flex-direction: column;
    background-color: #fff;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
}
.chat-log {
    height: 300px;
    overflow-y: auto;
    border-bottom: 1px solid #ccc;
    margin-bottom: 10px;
    padding-bottom: 10px;
    background-color: #f9f9f9;
}
.chat-input-container {
    display: flex;
    padding: 10px;
    border-top: 1px solid #ddd;
    background-color: #fff;
}
.chat-input {
    flex-grow: 1;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 4px;
    margin-right: 10px;
}
.send-button {
    padding: 10px 15px;
    border: none;
    border-radius: 4px;
    background-color: #007bff;
    color: #fff;
    cursor: pointer;
}
.send-button:hover {
    background-color: #0056b3;
}
.message-icon {
    position: fixed;
    bottom: 20px;
    right: 20px;
    background-color: #007bff;
    color: white;
    border-radius: 50%;
    width: 50px;
    height: 50px;
    display: flex;
    justify-content: center;
    align-items: center;
    cursor: pointer;
    font-size: 30px;
}
.modal-dialog.custom-size {
    max-width: 400px;
}
.modal-backdrop.show {
    opacity: 0.5;
}
.chart-container {
    position: fixed;
    bottom: 10px;
    right: 10px;
    width: 300px;
    z-index: 1050;
}
.chart-container canvas {
    width: 100% !important;
}
body {
    font-family: Arial, sans-serif;
    margin: 0;
    padding: 0;
    background-color: #f4f4f4;
    position: relative;
}
.chat-container {
    position: fixed;
    bottom: 20px;
    right: 20px;
    width: 100%;
    max-width: 400px;
    background-color: #fff;
    border-radius: 8px;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    display: flex;
    flex-direction: column;
    height: 400px;
    z-index: 1050;
}
.message {
    margin-bottom: 10px;
}
.user-message {
    text-align: right;
}
.bot-message {
    text-align: left;
}
.message-content {
    display: inline-block;
    padding: 10px;
    border-radius: 8px;
    max-width: 70%;
    line-height: 1.4;
}
.user-message .message-content {
    background-color: #007bff;
    color: #fff;
}
.bot-message .message-content {
    background-color: #f1f1f1;
    color: #333;
}
    /* Custom styles for smaller decrement buttons */
    .decrement-btn {
        width: 20px;          /* Reduced width */
        height: 20px;         /* Reduced height */
        padding: 0;           /* No padding */
        font-size: 14px;      /* Smaller font size */
        line-height: 1;       /* Maintain single line height */
        display: inline-flex;  /* Center the content */
        align-items: center;   /* Center vertically */
        justify-content: center; /* Center horizontally */
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.2); /* Subtle shadow */
        transition: background-color 0.3s ease, box-shadow 0.3s ease; /* Smooth transition */
    }

    .decrement-btn:hover {
        background-color: #f8d7da; /* Light background on hover */
        box-shadow: 0 3px 6px rgba(0, 0, 0, 0.2); /* Shadow on hover */
    }

    .decrement-btn:active {
        box-shadow: inset 0 3px 5px rgba(0, 0, 0, 0.2); /* Inset shadow when active */
    }
    /* Floating button */
    .chat-toggle {
        position: fixed;
        bottom: 30px;
        right: 30px;
        background-color: #4caf50;
        color: white;
        border: none;
        border-radius: 50%;
        width: 60px;
        height: 60px;
        font-size: 30px;
        cursor: pointer;
        box-shadow: 0 4px 10px rgba(0,0,0,0.2);
    }

    /* Chat container */
    .chat-container {
        position: fixed;
        bottom: 100px;
        right: 30px;
        width: 350px;
        max-height: 500px;
        background: white;
        border: 2px solid #a2e3a2;
        border-radius: 15px;
        display: none;
        flex-direction: column;
        box-shadow: 0 0 20px rgba(0,0,0,0.2);
        animation: fadeIn 0.3s ease-in-out;
    }

    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    .chat-box {
        flex: 1;
        padding: 15px;
        overflow-y: auto;
    }

    .message {
        margin: 10px 0;
        max-width: 75%;
        padding: 10px 15px;
        border-radius: 20px;
        line-height: 1.4;
        font-size: 14px;
    }

    .user {
        background-color: #d0ebff;
        align-self: flex-end;
        border-bottom-right-radius: 0;
    }

    .bot {
        background-color: #e3fce4;
        align-self: flex-start;
        border-bottom-left-radius: 0;
    }

    .input-area {
        display: flex;
        border-top: 1px solid #ddd;
        padding: 10px;
    }

    .input-area input {
        flex: 1;
        border: 1px solid #ccc;
        padding: 10px;
        border-radius: 10px;
    }

    .input-area button {
        margin-left: 10px;
        background: #4caf50;
        color: white;
        border: none;
        padding: 10px 15px;
        border-radius: 10px;
        cursor: pointer;
    }

    .typing {
        font-size: 13px;
        font-style: italic;
        color: #777;
        margin-left: 10px;
    }

    .container .card.counter-3d-card {
        overflow: hidden;
        border: 1px solid #e1e6ed;
        border-radius: 0.75rem;
        background: #fff;
        box-shadow: 0 2px 5px rgba(25, 42, 66, 0.06), 0 9px 22px rgba(25, 42, 66, 0.1) !important;
        transition: transform 180ms ease, box-shadow 180ms ease;
    }

    .container .card.counter-3d-card:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 9px rgba(25, 42, 66, 0.08), 0 13px 26px rgba(25, 42, 66, 0.12) !important;
    }

    @media (min-width: 768px) {
        .visitor-dashboard-row > .visitor-summary-column,
        .visitor-dashboard-row > .visitor-chart-column {
            display: flex;
        }

        .visitor-summary-card,
        .visitor-chart-card {
            display: flex;
            width: 100%;
            height: 390px;
            flex-direction: column;
        }

        .visitor-summary-card > .card-body,
        .visitor-chart-card > .card-body {
            flex: 1 1 auto;
        }

        .visitor-summary-card > .card-body {
            overflow: auto;
        }
    }

    .visitor-summary-table {
        margin-bottom: 0;
        color: #344054;
        font-size: 0.9rem;
        font-variant-numeric: tabular-nums;
    }

    .visitor-summary-table thead th {
        padding: 0.65rem 0.55rem;
        border-top: 0;
        border-bottom: 1px solid #dfe5ec;
        background: #f3f6fa;
        color: #526174;
        font-size: 0.72rem;
        font-weight: 700;
        letter-spacing: 0.035em;
        text-transform: uppercase;
        white-space: nowrap;
    }

    .visitor-summary-table thead th:first-child,
    .visitor-summary-table tbody td:first-child {
        text-align: left;
    }

    .visitor-summary-table tbody td {
        padding: 0.58rem 0.55rem;
        border-color: #e8edf2;
        vertical-align: middle;
        white-space: nowrap;
    }

    .visitor-summary-table tbody td:first-child {
        color: #465468;
        font-weight: 500;
    }

    .visitor-summary-table .summary-today td {
        background: #f1f7fd;
        color: #183e64;
        font-weight: 700;
    }

    .visitor-summary-table .summary-today td:first-child {
        box-shadow: inset 3px 0 #3978b8;
    }

    .visitor-summary-table .summary-total td {
        border-top: 2px solid #cfd8e3;
        background: #f8fafc;
        color: #25364a;
        font-weight: 700;
    }

    .visitor-summary-table .decrement-btn {
        margin-left: 0.25rem !important;
        vertical-align: middle;
    }

    .counter-chart-stage {
        display: flex;
        min-height: 290px;
        padding: 0.65rem 1rem 1rem !important;
        flex-direction: column;
        align-items: center;
        justify-content: space-between;
    }

    .counter-pie-legend {
        display: flex;
        gap: 1.25rem;
        margin: 0.15rem 0 0;
        padding: 0;
        color: #526174;
        font-size: 0.82rem;
        list-style: none;
    }

    .counter-pie-legend li { display: flex; align-items: center; gap: 0.35rem; }
    .counter-pie-legend i { width: 0.65rem; height: 0.65rem; border-radius: 2px; }
    .counter-pie-svg {
        display: block;
        width: min(100%, 330px);
        height: 235px;
        overflow: visible;
    }
    .counter-pie-segment {
        cursor: pointer;
        transform-box: fill-box;
        transform-origin: center;
        transition: transform 180ms ease, filter 180ms ease;
        outline: none;
    }
    .counter-pie-segment:hover,
    .counter-pie-segment:focus-visible {
        filter: drop-shadow(0 7px 5px rgba(20, 35, 62, 0.3));
        transform: translateY(-7px) scale(1.025);
    }
    .counter-pie-segment:focus-visible path:first-of-type {
        stroke: #172d46;
        stroke-width: 2;
    }
    .counter-pie-empty { fill: #748195; font: 14px Arial, sans-serif; text-anchor: middle; }

    @media (prefers-reduced-motion: reduce) {
        .container .card.counter-3d-card,
        .counter-pie-segment {
            transition: none;
        }
    }

</style>
@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visitor Summary</li>
        </ol>
    </nav>
    <div class="row justify-content-center visitor-dashboard-row">
        <!-- Summary Table -->
        <div class="col-md-4 mb-2 visitor-summary-column">
            <div class="card shadow-sm counter-3d-card visitor-summary-card">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Visitor's Summary</h2>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                    <table class="table table-sm visitor-summary-table text-center">
                        <thead class="thead-light">
                        <tr>
                            <th>Period</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Total Visits</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (['today'=>'Today','yesterday' => 'Yesterday', 'weekly' => 'This Week', 'monthly' => 'This Month', 'lastMonth' => 'Last Month', 'threeMonths' => 'Last Three Months', 'total' => 'TOTAL VISITS'] as $period => $label)
                            <tr class="{{ $period === 'today' ? 'summary-today' : '' }} {{ $period === 'total' ? 'summary-total' : '' }}">
                                <td>{{ $label }}</td>
                                <td>{{ $summary[$period]['Male'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('male')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Female'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('female')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['totalVisits'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-1">
            <div class="card shadow-sm counter-3d-card">
                <div class="card-body text-center">
                    <form id="incrementForm" action="{{ route('counter.increment') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="showGenderSelect()">Count</button>

                        <div id="genderSelectContainer" style="display:none; margin-top:20px;">
                            <label for="gender">Select Gender:</label>
                            <select id="gender" name="gender" class="form-control" required>
                                <option value="">Select</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                            </select>
                            <button type="submit" class="btn btn-success mt-3">Submit</button>
                        </div>
                    </form>


                    <div id="thankYouMessage" style="display:none; margin-top:20px;" class="alert alert-success">
                        Thank you!
                    </div>
                </div>
                    <div class="card-body text-center" style="display:none;" id = "multiple">
                        <form id="multiple" action="{{ route('counter.increment.multiple') }}" method="POST" class="mb-3">
                                @csrf

                                <div id="genderCounts" style="margin-top:20px;">
                                <label for="maleCountInput">Male Count:</label>
                                <input type="number" id="maleCountInput" name ="maleCountInput" class="form-control" >

                                <label for="femaleCountInput" style="margin-top:10px;">Female Count:</label>
                                <input type="number" id="femaleCountInput" name ="femaleCountInput" class="form-control" >

                                </div>
                                <button type="submit" class="btn btn-success mt-3">Submit</button>
                            </form>

                            </div>
                    </div>
                </div>


        <!-- Visitor Pie Chart -->
        <div class="col-md-4 mb-1 visitor-chart-column">
            <div class="card shadow-sm counter-3d-card visitor-chart-card">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Visitor's Chart</h2>
                </div>
                <div class="card-body counter-chart-stage">
                    <ul class="counter-pie-legend" aria-label="Visitor categories">
                        <li><i style="background:#1476d4"></i>Male</li>
                        <li><i style="background:#e52668"></i>Female</li>
                    </ul>
                    <svg id="visitorPieChart" class="counter-pie-svg" viewBox="0 0 360 250"
                         role="img" aria-label="Today's visitor counts by gender">
                        <defs>
                            <linearGradient id="male-pie-top" x1="0" y1="0" x2="0.8" y2="1">
                                <stop offset="0%" stop-color="#2588e8"/>
                                <stop offset="100%" stop-color="#0862bd"/>
                            </linearGradient>
                            <linearGradient id="female-pie-top" x1="0" y1="0" x2="0.8" y2="1">
                                <stop offset="0%" stop-color="#f33c7a"/>
                                <stop offset="100%" stop-color="#cc1558"/>
                            </linearGradient>
                            <filter id="pie-base-shadow" x="-30%" y="-30%" width="160%" height="180%">
                                <feGaussianBlur stdDeviation="7"/>
                            </filter>
                        </defs>
                        <ellipse cx="180" cy="170" rx="125" ry="26" fill="#192a40" opacity=".16"
                                 filter="url(#pie-base-shadow)"/>
                        <g id="visitor-pie-slices"></g>
                        <text id="visitor-pie-empty" class="counter-pie-empty" x="180" y="126"
                              style="display:none">No visits recorded today</text>
                    </svg>
                </div>
            </div>
        </div>
    </div>
{{--    <script src="https://cdn.pulse.is/livechat/loader.js" data-live-chat-id="67a5c56c81212f461c081862" async></script>--}}
 <!-- Modal Trigger -->
<!-- Modal Trigger -->
{{--<div class="message-icon" data-toggle="modal" id="message-icon" data-target="#chatModal">💬</div>--}}
{{--<!-- Modal -->--}}
{{--<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">--}}
{{--    <div class="modal-dialog modal-sm custom-size" role="document"> <!-- Added modal-sm and custom-size classes -->--}}
{{--        <div class="modal-content">--}}
{{--            <div class="modal-header">--}}
{{--                <h5 class="modal-title" id="chatModalLabel">Chatbot</h5>--}}
{{--                <button type="button" class="close" data-dismiss="modal" aria-label="Close">--}}
{{--                    <span aria-hidden="true">&times;</span>--}}
{{--                </button>--}}
{{--            </div>--}}
{{--            <div class="modal-body">--}}

{{--                    <div class="chat-box">--}}
{{--                        <div class="chat-log" id="chat-log"></div>--}}
{{--                        <meta name="csrf-token" content="{{ csrf_token() }}">--}}
{{--                        <div class="chat-input-container">--}}
{{--                            <input type="text" id="chat-input" class="chat-input" placeholder="Type a message...">--}}
{{--                            <button class="send-button" onclick="sendMessage()">Send</button>--}}
{{--                        </div>--}}
{{--                    </div>--}}
{{--                </div>--}}
{{--            </div>--}}
{{--        </div>--}}
{{--    </div>--}}

{{--    <form onsubmit="sendMessage(); return false;">--}}
{{--        <input type="text" id="user-input" placeholder="Ask me something..." />--}}
{{--        <button type="submit">Send</button>--}}
{{--    </form>--}}
    <!-- Toggle button -->
    <button type="button" class="chat-toggle" onclick="toggleChat()" aria-label="Open visitor assistant">💬</button>

    <!-- Chat UI -->
    <div class="chat-container" id="chat-container">
        <div class="chat-box" id="chat-box">
            <div class="message bot">Hi! I'm your library assistant. How can I help you today?</div>
        </div>
        <div class="typing" id="typing" style="display:none;">Bot is typing...</div>
        <form class="input-area" onsubmit="event.preventDefault(); sendMessage();">
            <input type="text" id="user-input" placeholder="Type your question..." maxlength="500" autocomplete="off">
            <button type="submit">Send</button>
        </form>
    </div>

    @include('partials.motivation-carousel')

@endsection


@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function toggleChat() {
        const chat = document.getElementById("chat-container");
        const input = document.getElementById("user-input");
        chat.style.display = chat.style.display === "flex" ? "none" : "flex";
        setTimeout(() => input.focus(), 100); // Auto-focus after animation
    }

    function appendMessage(message, sender) {
        const box = document.getElementById('chat-box');
        const div = document.createElement('div');
        div.className = 'message ' + sender;
        div.textContent = message;
        box.appendChild(div);
        box.scrollTop = box.scrollHeight;
    }

    let isChatSending = false;

    async function sendMessage() {
        const input = document.getElementById('user-input');
        const message = input.value.trim();
        if (!message || isChatSending) return;

        appendMessage(message, 'user');
        input.value = '';
        isChatSending = true;
        showTyping(true);

        try {
            const response = await fetch('{{ route('chatbot.respond') }}', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ message })
            });

            const data = await response.json();
            if (!response.ok) {
                throw new Error(data.reply || `Chat request failed with status ${response.status}`);
            }

            if (typeof data.reply !== 'string') {
                throw new Error('Chat response did not contain a reply.');
            }

            appendMessage(data.reply, 'bot');
        } catch (error) {
            console.error('Chatbot request failed:', error);
            appendMessage(error instanceof Error ? error.message : 'Sorry, I could not get a response. Please try again.', 'bot');
        } finally {
            isChatSending = false;
            showTyping(false);
        }
    }

    function showTyping(show) {
        document.getElementById('typing').style.display = show ? 'block' : 'none';
    }


    document.addEventListener('DOMContentLoaded', function () {
        const svg = document.getElementById('visitorPieChart');
        const slices = document.getElementById('visitor-pie-slices');
        const emptyMessage = document.getElementById('visitor-pie-empty');
        const values = [
            { label: 'Male', value: Number(@json($summary['today']['Male'])), gradient: 'url(#male-pie-top)', side: '#064680' },
            { label: 'Female', value: Number(@json($summary['today']['Female'])), gradient: 'url(#female-pie-top)', side: '#94123f' },
        ];
        const total = values.reduce((sum, item) => sum + item.value, 0);
        const centerX = 180;
        const centerY = 112;
        const radiusX = 125;
        const radiusY = 66;
        const depth = 20;
        const tau = Math.PI * 2;
        const point = (angle, yOffset = 0, offsetX = 0, offsetY = 0) => [
            centerX + offsetX + radiusX * Math.cos(angle),
            centerY + offsetY + radiusY * Math.sin(angle) + yOffset,
        ];

        if (total <= 0) {
            emptyMessage.style.display = '';
            return;
        }

        const segments = [];
        let angle = -Math.PI / 2;
        values.forEach(function (item) {
            if (item.value <= 0) return;
            const end = angle + (item.value / total) * tau;
            segments.push({ ...item, start: angle, end: end });
            angle = end;
        });

        segments.forEach(function (segment) {
            const middle = (segment.start + segment.end) / 2;
            const explode = segment.value < total / 2 ? 7 : 0;
            const offsetX = Math.cos(middle) * explode;
            const offsetY = Math.sin(middle) * explode;
            const group = document.createElementNS('http://www.w3.org/2000/svg', 'g');
            const percentage = Math.round((segment.value / total) * 100);
            group.setAttribute('class', 'counter-pie-segment');
            group.setAttribute('tabindex', '0');
            group.setAttribute('role', 'img');
            group.setAttribute('aria-label', `${segment.label}: ${segment.value} visits, ${percentage}%`);
            const title = document.createElementNS('http://www.w3.org/2000/svg', 'title');
            title.textContent = `${segment.label}: ${segment.value} visits (${percentage}%)`;
            group.append(title);

            for (let turn = -2; turn <= 2; turn++) {
                const start = Math.max(segment.start, turn * tau);
                const end = Math.min(segment.end, turn * tau + Math.PI);
                if (start >= end) continue;

                const frontStart = start - turn * tau;
                const frontEnd = end - turn * tau;
                const [sx, sy] = point(frontStart, 0, offsetX, offsetY);
                const [ex, ey] = point(frontEnd, 0, offsetX, offsetY);
                const [bottomX, bottomY] = point(frontEnd, depth, offsetX, offsetY);
                const [bottomStartX, bottomStartY] = point(frontStart, depth, offsetX, offsetY);
                const wall = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                wall.setAttribute('d', `M ${sx} ${sy} A ${radiusX} ${radiusY} 0 0 1 ${ex} ${ey} L ${bottomX} ${bottomY} A ${radiusX} ${radiusY} 0 0 0 ${bottomStartX} ${bottomStartY} Z`);
                wall.setAttribute('fill', segment.side);
                group.append(wall);
            }
            const [sx, sy] = point(segment.start, 0, offsetX, offsetY);
            const [ex, ey] = point(segment.end, 0, offsetX, offsetY);
            const largeArc = segment.end - segment.start > Math.PI ? 1 : 0;
            const top = document.createElementNS('http://www.w3.org/2000/svg', 'path');
            if (segment.end - segment.start >= tau - 0.0001) {
                top.setAttribute('d', `M ${centerX + offsetX - radiusX} ${centerY + offsetY} A ${radiusX} ${radiusY} 0 1 1 ${centerX + offsetX + radiusX} ${centerY + offsetY} A ${radiusX} ${radiusY} 0 1 1 ${centerX + offsetX - radiusX} ${centerY + offsetY} Z`);
            } else {
                top.setAttribute('d', `M ${centerX + offsetX} ${centerY + offsetY} L ${sx} ${sy} A ${radiusX} ${radiusY} 0 ${largeArc} 1 ${ex} ${ey} Z`);
            }
            top.setAttribute('fill', segment.gradient);
            top.setAttribute('stroke', 'rgba(23, 45, 70, .16)');
            top.setAttribute('stroke-width', '1');
            group.append(top);

            const labelAngle = middle;
            const labelRadius = segment.end - segment.start >= tau - 0.0001 ? 0 : 0.57;
            const label = document.createElementNS('http://www.w3.org/2000/svg', 'text');
            label.setAttribute('x', centerX + offsetX + radiusX * Math.cos(labelAngle) * labelRadius);
            label.setAttribute('y', centerY + offsetY + radiusY * Math.sin(labelAngle) * labelRadius);
            label.setAttribute('fill', '#fff');
            label.setAttribute('font-size', '15');
            label.setAttribute('font-weight', '700');
            label.setAttribute('text-anchor', 'middle');
            label.setAttribute('dominant-baseline', 'middle');
            label.setAttribute('paint-order', 'stroke');
            label.setAttribute('stroke', 'rgba(25, 42, 66, .35)');
            label.setAttribute('stroke-width', '2');
            label.setAttribute('pointer-events', 'none');
            label.textContent = Math.round((segment.value / total) * 100) + '%';
            group.append(label);
            slices.append(group);
        });
    });

    let isSubmitting = false;

    document.addEventListener('DOMContentLoaded', function () {
        document.getElementById('incrementForm').addEventListener('submit', function (event) {
            event.preventDefault();
            submitForm();
        });

        document.addEventListener('keydown', function (event) {
            if (event.target.closest && event.target.closest('#chat-container')) return;

            if (event.key === 'Shift' && !event.repeat && document.getElementById('multiple').style.display === 'none') {
                event.preventDefault();
                showMultiple();
                return;
            }

            if (event.key !== 'Enter') return;
            if (event.target.form?.id === 'multiple') return;

            if (event.target.id === 'gender' && document.getElementById('genderSelectContainer').style.display !== 'none') {
                event.preventDefault();
                document.getElementById('incrementForm').requestSubmit();
            } else if (!event.target.closest('input, select, textarea, button, a')) {
                event.preventDefault();
                if (document.getElementById('genderSelectContainer').style.display === 'none') {
                    showGenderSelect();
                } else {
                    document.getElementById('incrementForm').requestSubmit();
                }
            }
        });

        document.addEventListener('keydown', function (event) {
            if (document.getElementById('genderSelectContainer').style.display !== 'none') {
                const genderSelect = document.getElementById('gender');
                if (event.key === 'ArrowDown') {
                    event.preventDefault();
                    genderSelect.selectedIndex = (genderSelect.selectedIndex + 1) % genderSelect.options.length;
                } else if (event.key === 'ArrowUp') {
                    event.preventDefault();
                    genderSelect.selectedIndex = (genderSelect.selectedIndex - 1 + genderSelect.options.length) % genderSelect.options.length;
                }
            }
        });
    });
    // $('#message-icon').on('click', function() {
    //         $('#chatModal').modal('show');
    //     });
    $(document).ready(function() {
        $('#message-icon').on('click', function() {
            $('#chatModal').modal('show');
        });
    });
//     function sendMessage() {
//     var messageInput = document.getElementById('chat-input');
//     var message = messageInput.value.trim();
//
//     if (message === '') {
//         alert('Please enter a message.');
//         return;
//     }
//
//     $.ajax({
//         url: '/chatbot',
//         method: 'POST',
//         data: {
//             message: message,
//             _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token
//         },
//         success: function(response) {
//             var chatLog = document.getElementById('chat-log');
//
//             // User Message
//             var userMessageElement = document.createElement('div');
//             userMessageElement.classList.add('user-message');
//             userMessageElement.innerHTML = `<div class="message-content">You: ${message}</div>`;
//             chatLog.appendChild(userMessageElement);
//
//             // Bot Response
//             var responseMessageElement = document.createElement('div');
//             responseMessageElement.classList.add('bot-message');
//             responseMessageElement.innerHTML = `<div class="message-content">Bot: ${response.totalVisits !== undefined ? 'Total Visits: ' + response.totalVisits : response.message}</div>`;
//             chatLog.appendChild(responseMessageElement);
//
//             messageInput.value = '';
//             chatLog.scrollTop = chatLog.scrollHeight; // Scroll to bottom
//
//             console.log('Message sent:', response);
//         },
//         error: function(xhr, status, error) {
//             console.error('Error sending message:', error);
//         }
//     });
// }


    function showGenderSelect() {
        document.getElementById('genderSelectContainer').style.display = 'block';
        document.getElementById('gender').focus();
    }

    function showMultiple() {
        document.getElementById('multiple').style.display = 'block';
        document.getElementById('maleCountInput').focus();
    }


    async function submitForm() {
        if (isSubmitting) return;
        isSubmitting = true;

        const form = document.getElementById('incrementForm');
        try {
            const response = await fetch(form.action, {
                method: form.method,
                body: new FormData(form),
                headers: {
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                    'X-Requested-With': 'XMLHttpRequest'
                }
            });
            const data = await response.json();

            if (!response.ok || !data.success) {
                const validationMessage = data.errors
                    ? Object.values(data.errors).flat().join(' ')
                    : data.message;
                await Swal.fire({
                    icon: 'error',
                    title: 'Unable to record count',
                    text: validationMessage || 'Please try again.'
                });
                return;
            }

            await Swal.fire({
                icon: 'success',
                title: 'Thank you!',
                text: 'Your action has been successfully recorded.',
                showConfirmButton: false,
                timer: 2000,
                timerProgressBar: true,
                position: 'center',
                backdrop: true,
                customClass: {
                    container: 'my-swal-container',
                    title: 'my-swal-title',
                    text: 'my-swal-text',
                    popup: 'my-swal-popup',
                    timerProgressBar: 'my-swal-timer-progress'
                }
            });
            window.location.reload();
        } catch (error) {
            console.error('Unable to submit visitor count:', error);
            await Swal.fire({
                icon: 'error',
                title: 'Unable to record count',
                text: 'Please check your connection and try again.'
            });
        } finally {
            isSubmitting = false;
        }
    }

    function decrementCount(gender) {
        $.ajax({
            url: '{{ route('counter.decrement') }}',
            type: 'POST',
            data: {
                gender: gender,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    Swal.fire({
                        title: 'Success',
                        text: response.message,
                        icon: 'success',
                        timer: 1500,
                        showConfirmButton: false
                    });
                    // Reload or update the table data here, if necessary.
                    location.reload();  // Refresh the page to show the updated count
                }
            },
            error: function() {
                Swal.fire({
                    title: 'Error',
                    text: 'Could not decrement the count.',
                    icon: 'error'
                });
            }
        });
    }


</script>
@endpush
