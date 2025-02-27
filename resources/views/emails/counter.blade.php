<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Visitors Counter Report</title>
</head>
<body>
    <h1>Visitors Counter Report</h1>
    <p>Dear User,</p>
    <p>Please find the attached Visitor Counter Report for your reference.</p>
    <p>Summary:</p>
    <ul>
        <li>Total Visitors: {{ $summary['total_count'] }}</li>
        <li>Male: {{ $summary['male_count'] }}</li>
        <li>Female: {{ $summary['female_count'] }}</li>
        <li>Other: {{ $summary['other_count'] }}</li>
    </ul>
</body>
</html>
