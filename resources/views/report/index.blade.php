@extends('layouts.header')

@section('content')
    <ul class="nav nav-tabs">
        <li class="nav-item">
            <a class="nav-link active" data-toggle="tab" href="#visitors">Visitors Report</a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-toggle="tab" href="#newsletters">Newsletter Report</a>
        </li>
    </ul>
    <div class="tab-content mt-4">
        <!-- Visitors Report -->
        <div id="visitors" class="tab-pane fade show active">
            <div class="accordion" id="filterAccordionVisitors">
                <div class="card">
                    <div class="card-header" id="filterHeadingVisitors">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterCollapseVisitors" aria-expanded="true" aria-controls="filterCollapseVisitors">
                                Filters
                            </button>
                        </h5>
                    </div>
                    <div id="filterCollapseVisitors" class="collapse show" aria-labelledby="filterHeadingVisitors" data-parent="#filterAccordionVisitors">
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


            <h3>Visitors Report</h3>
            <table class="table table-bordered table-striped table-bordered" id="visitorTable">
{{--            <table id="visitorTable" class="table table-striped table-bordered">--}}
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
            <div class="row">
                <div class="col-md-8">
                    <canvas id="genderTrendChart" style="height:400px;"></canvas>
                </div>
                <div class="col-md-4">
                    <canvas id="genderPieChart" style="width:100%; height:100%;"></canvas>
                </div>
            </div>
        </div>
        <!-- Newsletter Report -->
        <div id="newsletters" class="tab-pane fade">
            <div class="accordion" id="filterAccordionNewsletters">
                <div class="card">
                    <div class="card-header" id="filterHeadingNewsletters">
                        <h5 class="mb-0">
                            <button class="btn btn-link" type="button" data-toggle="collapse" data-target="#filterCollapseNewsletters" aria-expanded="true" aria-controls="filterCollapseNewsletters">
                                Filters
                            </button>
                        </h5>
                    </div>
                    <div id="filterCollapseNewsletters" class="collapse show" aria-labelledby="filterHeadingNewsletters" data-parent="#filterAccordionNewsletters">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="dateRangeNew">Filter by Date:</label>
                                        <input type="text" id="dateRangeNew" class="form-control" placeholder="Select date range">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <!-- Export Button -->
            <button id="exportBtnNews" class="btn btn-primary">Export to Excel</button>

            <!-- Table for Newsletter Data -->
            <table class="table table-bordered table-striped table-bordered" id="newsletterTable">
                <thead>
                <tr>
                    <th>Date</th>
                    <th>Officer</th>
                    <th>Guardian</th>
                    <th>Nipashe</th>
                    <th>H/Leo</th>
                    <th>Uhuru</th>
                    <th>E/African</th>
                    <th>Mwananchi</th>
                    <th>Citizen</th>
                    <th>Daily News</th>
                    <th>Total Count</th>
                </tr>
                </thead>
                <tbody>
                @foreach($news as $newsletter)
                    <tr>
                        <td>{{ $newsletter->date }}</td>
                        <td>{{ $newsletter->user_name }}</td>
                        <td>{{ $newsletter->guardian_count ?: 0 }}</td>
                        <td>{{ $newsletter->nipashe_count ?: 0 }}</td>
                        <td>{{ $newsletter->habari_leo_count ?: 0 }}</td>
                        <td>{{ $newsletter->uhuru_count ?: 0 }}</td>
                        <td>{{ $newsletter->east_african_count ?: 0 }}</td>
                        <td>{{ $newsletter->mwananchi_count ?: 0 }}</td>
                        <td>{{ $newsletter->citizen_count ?: 0 }}</td>
                        <td>{{ $newsletter->daily_news_count ?: 0 }}</td>
                        <td>{{ $newsletter->total_count }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>


        </div>
        </div>




        @endsection
@push('scripts')
    <!-- Include jQuery, DataTables JS/CSS, Moment.js, Date Range Picker, and Chart.js -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/laravel-excel@3.1.0/dist/js/laravel-excel.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment.js"></script>

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

            var newslettertable = $('#newsletterTable').DataTable({
                responsive: true,
                paging: true,
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

            // Newsletter Date Range Picker
            $('#dateRangeNew').daterangepicker({
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
            $('#dateRangeNew').on('apply.daterangepicker', function(ev, picker) {
                var startDate = picker.startDate.format('YYYY-MM-DD');
                var endDate = picker.endDate.format('YYYY-MM-DD');

                $.fn.dataTable.ext.search.push(function(settings, data, dataIndex) {
                    var rowDate = moment(data[0], 'YYYY-MM-DD').format('YYYY-MM-DD');
                    return rowDate >= startDate && rowDate <= endDate;
                });

                newslettertable.draw();
            });

            $('#dateRange').on('cancel.daterangepicker', function(ev, picker) {
                $.fn.dataTable.ext.search.pop();
                table.draw();
                updateCharts();
            });
            $('#dateRangeNew').on('cancel.daterangepicker', function(ev, picker) {
                $.fn.dataTable.ext.search.pop();
                newslettertable.draw();
            });
            $('#exportBtn').on('click', function() {

                var activeTab = $('.nav-tabs .active').attr('href');

                if (activeTab === '#visitors') {
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
                } else if (activeTab === '#newsletters') {
                    var data = newslettertable.rows({ filter: 'applied' }).data();
                    var exportData = [];
                    data.each(function(row) {
                        exportData.push({
                            Date: row[0],
                            'User Name': row[1],
                            Guardian: row[2],
                            Nipashe: row[3],
                            'Habari Leo': row[4],
                            Uhuru: row[5],
                            'East African': row[6],
                            Mwananchi: row[7],
                            Citizen: row[8],
                            'Daily News': row[9],
                            'Total Count': row[10]
                        });
                    });
                    var csvContent = "Date, User Name, Guardian, Nipashe, Habari Leo, Uhuru, East African, Mwananchi, Citizen, Daily News, Total Count\n";
                }

                exportData.forEach(function(rowArray) {
                    var row = Object.values(rowArray).join(",");
                    csvContent += row + "\n";
                });

                var hiddenElement = document.createElement('a');
                hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csvContent);
                hiddenElement.target = '_blank';
                hiddenElement.download = (activeTab === '#visitors' ? 'visitor_report.csv' : 'newsletter_report.csv');
                hiddenElement.click();
            });

            $('#exportBtnNews').on('click', function() {
                var activeTab = $('.nav-tabs .active').attr('href');

                if (activeTab === '#newsletters') {
                    var data = newslettertable.rows({filter: 'applied'}).data();  // Corrected variable name here
                    var exportData = [];
                    var grandTotalBefore = 0; // Initialize grand total before 15% discount
                    var grandTotalAfter = 0;  // Initialize grand total after 15% discount
                    var totalGuardianCount = 0;
                    var totalNipasheCount = 0;
                    var totalHabariLeoCount = 0;
                    var totalUhuruCount = 0;
                    var totalEastAfricanCount = 0;
                    var totalMwananchiCount = 0;
                    var totalCitizenCount = 0;
                    var totalDailyNewsCount = 0;

                    // Prices for each newsletter type
                    const prices = {
                        Guardian: 2000,
                        Nipashe: 2000,
                        'Habari Leo': 3000,
                        Uhuru: 2000,
                        'East African': 3000,
                        Mwananchi: 1500,
                        Citizen: 2000,
                        'Daily News': 3000
                    };

                    data.each(function (row) {
                        // Prices for each newsletter type
                        const totalGuardian = row[2] * prices.Guardian;
                        const totalNipashe = row[3] * prices.Nipashe;
                        const totalHabariLeo = row[4] * prices['Habari Leo'];
                        const totalUhuru = row[5] * prices.Uhuru;
                        const totalEastAfrican = row[6] * prices['East African'];
                        const totalMwananchi = row[7] * prices.Mwananchi;
                        const totalCitizen = row[8] * prices.Citizen;
                        const totalDailyNews = row[9] * prices['Daily News'];

                        // Calculate the total price before the 15% discount
                        const totalBefore = totalGuardian + totalNipashe + totalHabariLeo + totalUhuru + totalEastAfrican + totalMwananchi + totalCitizen + totalDailyNews;

                        // Add to grand total before
                        grandTotalBefore += totalBefore;

                        // Calculate the total after 15% discount
                        const totalAfter15 = totalBefore * 0.85; // 15% discount

                        // Add to grand total after
                        grandTotalAfter += totalAfter15;

                        // Calculate Total Count for this row
                        const totalCount = sum(row[2] + row[3] + row[4] + row[5] + row[6] + row[7] + row[8] + row[9]);

                        // Add the counts for each newsletter type
                        totalGuardianCount += row[2];  // Add Guardian count
                        totalNipasheCount += row[3];   // Add Nipashe count
                        totalHabariLeoCount += row[4]; // Add Habari Leo count
                        totalUhuruCount += row[5];     // Add Uhuru count
                        totalEastAfricanCount += row[6]; // Add East African count
                        totalMwananchiCount += row[7];  // Add Mwananchi count
                        totalCitizenCount += row[8];    // Add Citizen count
                        totalDailyNewsCount += row[9];  // Add Daily News count

                        // Push the data into exportData without the individual total columns
                        exportData.push({
                            Date: row[0],
                            'User Name': row[1],
                            Guardian: row[2],
                            'Guardian Price': totalGuardian,  // Added total price for Guardian
                            Nipashe: row[3],
                            'Nipashe Price': totalNipashe,    // Added total price for Nipashe
                            'Habari Leo': row[4],
                            'Habari Price': totalHabariLeo,   // Added total price for Habari Leo
                            Uhuru: row[5],
                            'Uhuru Price': totalUhuru,        // Added total price for Uhuru
                            'East African': row[6],
                            'East Price': totalEastAfrican,   // Added total price for East African
                            Mwananchi: row[7],
                            'Mwananchi Price': totalMwananchi, // Added total price for Mwananchi
                            Citizen: row[8],
                            'Citizen Price': totalCitizen,    // Added total price for Citizen
                            'Daily News': row[9],
                            'Daily Price': totalDailyNews,    // Added total price for Daily News
                            'Total Count': totalCount // Correct Total Count
                        });
                    });

// After processing all rows, add the grand totals and total counts as a final row
                    exportData.push({
                        Date: 'TOTAL',
                        'User Name': '',
                        Guardian: totalGuardianCount,
                        'Guardian Price': totalGuardianCount * prices.Guardian,
                        Nipashe: totalNipasheCount,
                        'Nipashe Price': totalNipasheCount * prices.Nipashe,
                        'Habari Leo': totalHabariLeoCount,
                        'Habari Price': totalHabariLeoCount * prices['Habari Leo'],
                        Uhuru: totalUhuruCount,
                        'Uhuru Price': totalUhuruCount * prices.Uhuru,
                        'East African': totalEastAfricanCount,
                        'East Price': totalEastAfricanCount * prices['East African'],
                        Mwananchi: totalMwananchiCount,
                        'Mwananchi Price': totalMwananchiCount * prices.Mwananchi,
                        Citizen: totalCitizenCount,
                        'Citizen Price': totalCitizenCount * prices.Citizen,
                        'Daily News': totalDailyNewsCount,
                        'Daily Price': totalDailyNewsCount * prices['Daily News'],
                        'Total Count': totalGuardianCount + totalNipasheCount + totalHabariLeoCount + totalUhuruCount + totalEastAfricanCount + totalMwananchiCount + totalCitizenCount + totalDailyNewsCount, // Correct Total Count sum
                        'Total Before 15%': grandTotalBefore,
                        'Total After 15%': grandTotalAfter,
                    });

// Now, generate the CSV content with the headers
                    var csvContent = "Date, User Name, Guardian, Guardian Price, Nipashe, Nipashe Price, Habari Leo, Habari Price, Uhuru, Uhuru Price, East African, East Price, Mwananchi, Mwananchi Price, Citizen, Citizen Price, Daily News, Daily Price, Total Count, Total Before 15%, Total After 15%\n";

// Now, append the rows to csvContent
                    exportData.forEach(function (rowData) {
                        var row = [];
                        for (var key in rowData) {
                            row.push(rowData[key]);
                        }
                        csvContent += row.join(",") + "\n";
                    });
                }
// Trigger the download
                    var hiddenElement = document.createElement('a');
                    hiddenElement.href = 'data:text/csv;charset=utf-8,' + encodeURI(csvContent);
                    hiddenElement.target = '_blank';
                    hiddenElement.download = 'newsletter_report.csv';
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
