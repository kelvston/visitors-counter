<p>Hello,</p>

<p>
    Reminder: the submission deadline for <strong>{{ $proposal->title }}</strong>
    is in {{ $daysRemaining }} days, on {{ $proposal->timeline_to->format('d M Y') }}.
</p>

<p><strong>Where to submit:</strong> {{ $proposal->destination }}</p>

<p>Please make sure your submission is sent before the deadline.</p>
