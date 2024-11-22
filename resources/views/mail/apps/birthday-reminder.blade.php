<x-mail::message>
@if(isset($receiver)) 
    # Hey {{ $receiver['firstname'] }} {{ $receiver['lastname'] }}!
@else
    # Hey!
@endif

These HubSpot contacts are celebrating their birthday on {{ $date->format('Y-m-d') }}{{ $date->isToday() ? ' (Today)' : '' }}{{ $date->isTomorrow() ? ' (Tomorrow)' : '' }}:

@foreach ($birthdays as $birthday)
<x-mail::panel>
*Name*: {{ $birthday['firstname'] }} {{ $birthday['lastname'] }}<br>
*Birthday*: {{ $birthday['dateOfBirth'] }}<br>
*Company*: {{ $birthday['companyName'] ?? '-' }} ({{ $birthday['companyDomain'] ?? '-' }})<br>
@foreach($birthday['properties'] as $property)*{{ $property['label'] }}*: {{ $property['value'] ?? '-' }}<br>@endforeach
<a href="{{ $birthday['hubspotContactUrl'] }}" target="_blank">Show in HubSpot</a>
</x-mail::panel>
@endforeach

Cheers,<br />
The HubFlow Apps Team
</x-mail::message>
