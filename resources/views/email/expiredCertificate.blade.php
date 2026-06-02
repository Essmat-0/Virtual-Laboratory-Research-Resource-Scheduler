<h2>Warning - Certificate Expiry</h2>
<p>Hello {{ $certification->user->name }},</p>
<p><strong>{{ $certification->id }}</strong> Is expiring in Less than 30 Days </p>
<ul>
    <li>Cert ID: {{ $certification->id }}</li>
    <li>End Date: {{ $certification->expiry_date }}</li>
</ul>
<p>Please Update Your Certification.</p>
