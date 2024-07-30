@extends('layouts.header')
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

</style>
@section('content')
{{-- <div class="col-12 px-0 pb-1">

    <div class="container mt-0 top-middle">
        <div class="row align-items-center">
            <!-- Emblem Section -->
            <div class="col-md-2 col-sm-2 col-xs-2 text-center">
                <a href="https://www.aru.ac.tz">
                    <img src="https://www.aru.ac.tz/site/images/emblem.png" alt="emblem" class="emblem img-fluid">
                </a>
            </div>

            <!-- University Title Section -->
            <div class="col-md-8 col-sm-8 col-xs-8 text-center">
                <h1 class="mb-0 title">ARDHI UNIVERSITY</h1>
            </div>

            <!-- Client Logo Section -->
            <div class="col-md-2 col-sm-2 col-xs-2 text-center">
                <a href="https://www.aru.ac.tz">
                    <img src="https://www.aru.ac.tz/site/images/logo.jpg" alt="Logo" class="client-logo img-fluid">
                </a>
            </div>
        </div>
    </div>
 --}}

{{-- <div class="container mt-5 bordered-container"> --}}

    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
            <li class="breadcrumb-item active" aria-current="page">Visitor Summary</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <!-- Summary Table -->
        <div class="col-md-5 mb-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Visitor Summary</h2>
                </div>
                <div class="card-body">
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                        <tr>
                            <th>Period</th>
                            <th>Male</th>
                            <th>Female</th>
                            <th>Other</th>
                            <th>Total Visits</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (['today'=>'Today','yesterday' => 'Yesterday', 'weekly' => 'Last Week', 'monthly' => 'This Month', 'lastMonth' => 'Last Month', 'threeMonths' => 'Last Three Months', 'total' => 'TOTAL VISITS'] as $period => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $summary[$period]['Male'] }}</td>
                                <td>{{ $summary[$period]['Female'] }}</td>
                                <td>{{ $summary[$period]['Other'] }}</td>
                                <td>{{ $summary[$period]['totalVisits'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-1">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <form id="incrementForm" action="{{ route('counter.increment') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="showGenderSelect()">Count</button>

                        <div id="genderSelectContainer" style="display:none; margin-top:20px;">
                            <label for="gender">Select Gender:</label>
                            <select id="gender" name="gender" class="form-control">
                                <option value="">Select</option>
                                <option value="1">Male</option>
                                <option value="2">Female</option>
                                <option value="3">Other</option>
                            </select>
                            <button type="submit" class="btn btn-success mt-3">Submit</button>
                        </div>
                    </form>

                    <div id="thankYouMessage" style="display:none; margin-top:20px;" class="alert alert-success">
                        Thank you!
                    </div>
                </div>
            </div>
        </div>

        <!-- Visitor Pie Chart -->
        <div class="col-md-4 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Visitor Summary</h2>
                </div>
                <div class="card-body">
                    <canvas id="visitorPieChart" width="300" height="300"></canvas>
                </div>
            </div>
        </div>
    </div>
 <!-- Modal Trigger -->
<!-- Modal Trigger -->
<div class="message-icon" data-toggle="modal" id="message-icon" data-target="#chatModal">💬</div>

<!-- Modal -->
<div class="modal fade" id="chatModal" tabindex="-1" role="dialog" aria-labelledby="chatModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-sm custom-size" role="document"> <!-- Added modal-sm and custom-size classes -->
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="chatModalLabel">Chatbot</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">

                    <div class="chat-box">
                        <div class="chat-log" id="chat-log"></div>
                        <meta name="csrf-token" content="{{ csrf_token() }}">
                        <div class="chat-input-container">
                            <input type="text" id="chat-input" class="chat-input" placeholder="Type a message...">
                            <button class="send-button" onclick="sendMessage()">Send</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const pieChartData = {
            labels: ['Male', 'Female', 'Other'],
            datasets: [{
                label: 'Visitors by Gender',
                data: [{{$summary['today']['Male']}}, {{$summary['today']['Female']}}, {{$summary['today']['Other']}}],
                backgroundColor: ['#007bff', '#ff0077', '#00cc99'],
            }]
        };

        const ctxPie = document.getElementById('visitorPieChart');
        new Chart(ctxPie, {
            type: 'pie',
            data: pieChartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
            }
        });
    });

    let isSubmitting = false;

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('keypress', function (event) {
            if (event.key === 'Enter' && document.getElementById('genderSelectContainer').style.display === 'none') {
                event.preventDefault();
                showGenderSelect();
            } else if (event.key === 'Enter' && document.getElementById('genderSelectContainer').style.display !== 'none') {
                event.preventDefault();
                submitForm();
            }
        });

        document.addEventListener('keydown', function (event) {
            if (document.getElementById('genderSelectContainer').style.display !== 'none') {
                let genderSelect = document.getElementById('gender');
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
    function sendMessage() {
    var messageInput = document.getElementById('chat-input');
    var message = messageInput.value.trim();

    if (message === '') {
        alert('Please enter a message.');
        return;
    }

    $.ajax({
        url: '/chatbot',
        method: 'POST',
        data: {
            message: message,
            _token: document.querySelector('meta[name="csrf-token"]').getAttribute('content') // CSRF token
        },
        success: function(response) {
            var chatLog = document.getElementById('chat-log');

            // User Message
            var userMessageElement = document.createElement('div');
            userMessageElement.classList.add('user-message');
            userMessageElement.innerHTML = `<div class="message-content">You: ${message}</div>`;
            chatLog.appendChild(userMessageElement);

            // Bot Response
            var responseMessageElement = document.createElement('div');
            responseMessageElement.classList.add('bot-message');
            responseMessageElement.innerHTML = `<div class="message-content">Bot: ${response.totalVisits !== undefined ? 'Total Visits: ' + response.totalVisits : response.message}</div>`;
            chatLog.appendChild(responseMessageElement);

            messageInput.value = '';
            chatLog.scrollTop = chatLog.scrollHeight; // Scroll to bottom

            console.log('Message sent:', response);
        },
        error: function(xhr, status, error) {
            console.error('Error sending message:', error);
        }
    });
}


    function showGenderSelect() {
        document.getElementById('genderSelectContainer').style.display = 'block';
        document.getElementById('gender').focus();
    }

    function submitForm() {
        if (isSubmitting) return;
        isSubmitting = true;

        const form = document.getElementById('incrementForm');
        const formData = new FormData(form);

        fetch(form.action, {
            method: form.method,
            body: formData,
            headers: {
                'X-CSRF-TOKEN': document.querySelector('input[name="_token"]').value,
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
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
    },
    didOpen: () => {
        // Additional setup if needed
    }
}).then(() => {
            window.location.reload(); // Refresh the page after the alert
        });
        document.getElementById('genderSelectContainer').style.display = 'none';
        form.reset();
    }
})

        .catch(error => {
            console.error('Error:', error);
        })
        .finally(() => {
            isSubmitting = false;
        });
    }
</script>
@endpush