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


</style>
@section('content')
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ url()->previous() }}">Back</a></li>
            <li class="breadcrumb-item active" aria-current="page">Newsletter Summary</li>
        </ol>
    </nav>
    <div class="row justify-content-center">
        <!-- Summary Table -->

        <div class="col-md-5 mb-1">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h2 class="text-center mb-0">Newsletter Summary</h2>
                </div>

                <div class="table-responsive"> <!-- Add this container for responsiveness -->
                    <table class="table table-bordered table-sm text-center">
                        <thead class="thead-light">
                        <tr>
                            <th>Period</th>
                            <th>Daily News</th>
                            <th>Guardian</th>
                            <th>Mwananchi</th>
                            <th>Nipashe</th>
                            <th>Uhuru</th>
                            <th>Habari Leo</th>
                            <th>Citizen</th>
                            <th>East African</th>
                            <th>Total</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach (['today'=>'Today','yesterday' => 'Yesterday', 'weekly' => 'This Week', 'monthly' => 'This Month', 'lastMonth' => 'Last Month', 'threeMonths' => 'Last Three Months', 'total' => 'TOTAL VISITS'] as $period => $label)
                            <tr>
                                <td>{{ $label }}</td>
                                <td>{{ $summary[$period]['Daily News'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Daily News')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Guardian'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Guardian')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Mwananchi'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Mwananchi')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Nipashe'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Nipashe')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Uhuru'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Uhuru')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Habari Leo'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Habari Leo')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['Citizen'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('Citizen')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['East African'] }}
                                    @if ($period === 'today')
                                        <button class="btn btn-sm btn-outline-danger rounded-circle ml-2 decrement-btn" onclick="decrementCount('East African')">-</button>
                                    @endif
                                </td>
                                <td>{{ $summary[$period]['totalVisits'] }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div> <!-- End table-responsive -->
            </div>
        </div>


        <div class="col-md-3 mb-1">
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <form id="incrementForm" action="{{ route('news_leter.store') }}" method="POST" class="mb-3">
                        @csrf
                        <button type="button" class="btn btn-primary btn-lg btn-block" onclick="showNewsLetterSelect()">Count</button>

                        <div id="newsletterSelectContainer" style="display:none; margin-top:20px;">
                            <label for="newsletter">Select Newsletter:</label>
                            <select id="newsletter" name="newsletter" class="form-control" onchange="checkNewsletterSelection()">
                                <option value="">Select</option>
                                <option value="1">Daily News</option>
                                <option value="2">Guardian</option>
                                <option value="3">Nipashe</option>
                                <option value="4">Uhuru</option>
                                <option value="5">Habari Leo</option>
                                <option value="6">Citizen</option>
                                <option value="7">East African</option>
                                <option value="8">Mwananchi</option>
                            </select>
                            <div class="card-body text-center" style="display:none;" id = "newsletterCount">
                            <input type="number" id="newsletterInput" name ="newsletterInput" class="form-control" >
                            </div>
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
            labels: ['Daily News', 'Guardian', 'Nipashe', 'Uhuru', 'Habari Leo', 'Citizen', 'East African', 'Mwananchi'],
            datasets: [{
                label: 'Newslatter',
                data: [
                    {{$summary['today']['Daily News'] ?? 0}},
                    {{$summary['today']['Guardian'] ?? 0}},
                    {{$summary['today']['Nipashe'] ?? 0}},
                    {{$summary['today']['Uhuru'] ?? 0}},
                    {{$summary['today']['Habari Leo'] ?? 0}},
                    {{$summary['today']['Citizen'] ?? 0}},
                    {{$summary['today']['East African'] ?? 0}},
                    {{$summary['today']['Mwananchi'] ?? 0}}
                ],
                backgroundColor: [
                    '#73721e', // Bright orange-red
                    '#33FF57', // Bright green
                    '#3357FF', // Bright blue
                    '#FF33A8', // Bright pink
                    '#33FFF2', // Cyan
                    '#FFB533', // Golden yellow
                    '#8D33FF', // Purple
                    '#FF3333'  // Bright red
                ],
                borderColor: [
                    '#ffffff'  // White border for all slices to create separation
                ],
                borderWidth: 1
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
            if (event.key === 'Enter' && document.getElementById('newsletterSelectContainer').style.display === 'none') {
                event.preventDefault();
                showNewsletterSelect();
            } else if (event.key === 'Enter' && document.getElementById('newsletterSelectContainer').style.display !== 'none') {
                event.preventDefault();
                submitForm();
            }

            if (event.shiftKey && document.getElementById('multiple').style.display === 'none') {
            event.preventDefault();
            showMultiple();
             }
                });

                document.addEventListener('keydown', function(event) {
            if (event.shiftKey && document.getElementById('multiple').style.display === 'none') {
                event.preventDefault();
                showMultiple();
                submitForm();
            }
        });



        document.addEventListener('keydown', function (event) {
            if (document.getElementById('newsletterSelectContainer').style.display !== 'none') {
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




function showNewsLetterSelect() {
    document.getElementById('newsletterSelectContainer').style.display = 'block';
    checkNewsletterSelection();
}

function checkNewsletterSelection() {
    const newsletterValue = document.getElementById('newsletter').value;
    const newsletterCountInput = document.getElementById('newsletterCount');

    newsletterInput.value = '';
    if (newsletterValue >= 1) {
        newsletterCountInput.style.display = 'block';
    } else {
        newsletterCountInput.style.display = 'none';
    }
}







    function showMultiple() {
        document.getElementById('multiple').style.display = 'block';
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
        document.getElementById('newsletterSelectContainer').style.display = 'none';
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
