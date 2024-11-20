<x-mail::message>
# Hallo {{ $receiver['firstname'] }} {{ $receiver['lastname'] }},

diese Hubspot Kontakte haben heute Geburtstag:

@foreach ($birthdays as $birthday)
*Name*: {{ $birthday['firstname'] }} {{ $birthday['lastname'] }}<br>
*Geburtstag*: {{ $birthday['dateOfBirth'] }}<br>
*Unternehmen*: {{ $birthday['companyName'] }} ({{ $birthday['companyDomain'] }})<br>
*Anmerkung*: {{ $birthday['birthdayText'] }}<br>
<a href="{{ $birthday['hubspotContactUrl'] }}" target="_blank">In Hubspot ansehen</a>

@endforeach

Danke!

Team HubFlow Apps
</x-mail::message>
