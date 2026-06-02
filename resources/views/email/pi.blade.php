<h2>Approval Required</h2>
<p>Hello PI,</p>
<p><strong>{{ $researcher->name }}</strong> has requested the following equipment:</p>
<ul>
    <li>Equipment ID: {{ $reservation->equipment_id }}</li>
    <li>Start: {{ $reservation->start_time }}</li>
    <li>End: {{ $reservation->end_time }}</li>
</ul>
<p>Please log in to the portal to approve or reject this request.</p>
