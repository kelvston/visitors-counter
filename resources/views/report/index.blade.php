@extends('layouts.header')

@section('content')
    <div class="accordion" id="filterAccordion">
        <div class="card">
            <div class="card-header" id="filterHeading">
                <h5 class="mb-0">
                    <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterCollapse" aria-expanded="true" aria-controls="filterCollapse">
                        Filters
                    </button>
                </h5>
            </div>

            <div id="filterCollapse" class="collapse show" aria-labelledby="filterHeading" data-parent="#filterAccordion">
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="dateRange">Filter by Date:</label>
                                <input type="text" id="dateRange" class="form-control" placeholder="Select date range">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <button id="exportBtn" class="btn btn-primary">Export to Excel</button>

    <h2>General Report</h2>

    <div class="row">
        <div class="col-md-12">
            <table id="visitorTable" class="table table-striped table-bordered">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Officer</th>
                    <th>Male</th>
                    <th>Female</th>
                    <th>Other</th>
                    <th>Total Count</th>
                </tr>
                </thead>
                <tbody>
                @foreach($data as $record)
                    <tr>
                        <td>{{ $record->date }}</td>
                        <td>{{ $record->name }}</td>
                        <td>{{ $record->male_count ?: 0 }}</td>
                        <td>{{ $record->female_count ?: 0 }}</td>
                        <td>{{ $record->other_count ?: 0 }}</td>
                        <td>{{ $record->total_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <div class="row">
        <div class="col-md-8">
            <canvas id="genderTrendChart" style="height:400px;"></canvas>
        </div>
       <div class="col-md-4">
            <canvas id="genderPieChart" style="width:100%; height:100%;"></canvas>
        </div>
    </div>
@endsection
@push('scripts')
    <!-- Include jQuery, DataTables JS/CSS, Moment.js, Date Range Picker, and Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <link href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.min.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/moment.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <link href="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css" rel="stylesheet"/>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-excel@3.1.0/dist/js/laravel-excel.min.js"></script>

    <script>
        $(document).ready(function() {
            // Initialize DataTable
            var table = $('#visitorTable').DataTable({
                responsive: true,
                pageLength: 10,
                lengthMenu: [10, 25, 50, 75, 100],
                order: [[0, 'desc']],
                paging: true,
                scrollY: '50vh',
                scrollCollapse: true,
                deferRender: true
            });

            $('#dateRange').daterangepicker({
                autoUpdateInput: false,
                locale: { cancelLabel: 'Clear' },
                ranges: {
                    'Today': [moment(), moment()],
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last 30 Days': [moment().subtract(29, 'days'), moment()],
                }
            });

            $('#dateRange').on('apply.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');

                $.fn.dataTable.ext.search.push(
                    function(settings, data, dataIndex) {
                        var rowDate = moment(data[0], 'YYYY-MM-DD').format('YYYY-MM-DD');
                        return rowDate >= startDate && rowDate <= endDate;
                    }
                );
                table.draw();
                updateCharts();
            });

            $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
                $.fn.dataTable.ext.search.pop();
                table.draw();
                updateCharts();
            });

            // Export Button
            $('#exportBtn').on('click', function() {
                var data = table.rows({ filter: 'applied' }).data();
                var exportData = [];
                data.each(function(row) {
                    exportData.push({
                        Date: row[0],
                        'Officer': row[1],
                        Male: row[2],
                        Female: row[3],
                        Other: row[4],
                        'Total Count': row[5]
                    });
                });
                var csvContent = "Date, Officer, Male, Female, Other, Total Count\n";
                exportData.forEach(function(rowArray) {
                    var row = Object.values(rowArray).join(",");
                    csvContent += row + "\n";
                });
                var hiddenElement = document.createElement('a');
                hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csvContent);
                hiddenElement.target = '_blank';
                hiddenElement.download = 'gender_report.csv';
                hiddenElement.click();
            });

            // Donut Chart (Gender Distribution)
            var ctxPie = document.getElementById('genderPieChart').getContext('2d');
            var genderPieChart = new Chart(ctxPie, {
                type: 'doughnut',
                data: {
                    labels: ['Male', 'Female', 'Other'],
                    datasets: [{
                        label: 'Gender Distribution',
                        data: [0, 0, 0],
                        backgroundColor: ['#007bff', '#ff4081', '#4caf50'],
                        borderColor: ['#007bff', '#ff4081', '#4caf50'],
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { position: 'top' },
                        tooltip: { callbacks: { label: function(tooltipItem) { return tooltipItem.label + ': ' + tooltipItem.raw + ' users'; }}}
                    }
                }
            });

            // Line Chart (Gender Distribution Over Time)
            var ctxLine = document.getElementById('genderTrendChart').getContext('2d');
            var genderTrendChart = new Chart(ctxLine, {
                type: 'line',
                data: {
                    labels: [],
                    datasets: [{
                        label: 'Male',
                        data: [],
                        borderColor: '#007bff',
                        backgroundColor: 'rgba(0, 123, 255, 0.2)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Female',
                        data: [],
                        borderColor: '#ff4081',
                        backgroundColor: 'rgba(255, 64, 129, 0.2)',
                        fill: true,
                        tension: 0.4
                    }, {
                        label: 'Other',
                        data: [],
                        borderColor: '#4caf50',
                        backgroundColor: 'rgba(76, 175, 80, 0.2)',
                        fill: true,
                        tension: 0.4
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: {
                        x: {
                            type: 'category',
                            title: { display: true, text: 'Gender distribution analysis per date' }
                        },
                        y: {
                            beginAtZero: true,
                            title: { display: true, text: 'Gender Count' }
                        }
                    }
                }
            });

            // Update both charts based on table data
            function updateCharts() {
                var maleCount = 0, femaleCount = 0, otherCount = 0;
                var dates = [], maleData = [], femaleData = [], otherData = [];
                var filteredData = table.rows({ filter: 'applied' }).data();

                // If no filters, use all data
                if (filteredData.length === 0) {
                    filteredData = table.rows().data();
                }

                filteredData.each(function(row) {
                    maleCount += parseInt(row[2]);
                    femaleCount += parseInt(row[3]);
                    otherCount += parseInt(row[4]);

                    // Trend data for line chart
                    var date = moment(row[0]).format('YYYY-MM-DD');
                    if (!dates.includes(date)) {
                        dates.push(date);
                        maleData.push(parseInt(row[2]));
                        femaleData.push(parseInt(row[3]));
                        otherData.push(parseInt(row[4]));
                    } else {
                        var index = dates.indexOf(date);
                        maleData[index] += parseInt(row[2]);
                        femaleData[index] += parseInt(row[3]);
                        otherData[index] += parseInt(row[4]);
                    }
                });

                // Sort dates and data in ascending order based on dates
                var sortedData = dates.map((date, index) => ({
                    date: date,
                    male: maleData[index],
                    female: femaleData[index],
                    other: otherData[index]
                }));

                sortedData.sort(function(a, b) {
                    return moment(a.date).isBefore(moment(b.date)) ? -1 : 1;
                });

                // Extract sorted values
                dates = sortedData.map(item => item.date);
                maleData = sortedData.map(item => item.male);
                femaleData = sortedData.map(item => item.female);
                otherData = sortedData.map(item => item.other);

                // Update Donut Chart
                genderPieChart.data.datasets[0].data = [maleCount, femaleCount, otherCount];
                genderPieChart.update();

                // Update Line Chart (Trend Analysis)
                genderTrendChart.data.labels = dates;
                genderTrendChart.data.datasets[0].data = maleData;
                genderTrendChart.data.datasets[1].data = femaleData;
                genderTrendChart.data.datasets[2].data = otherData;
                genderTrendChart.update();
            }

            // Initialize charts with general data on page load
            updateCharts();
        });
    </script>
@endpush
