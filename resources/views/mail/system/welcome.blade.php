<x-mail::message>
# Hey {{ $user->firstname }}!

Welcome to HubFlow Apps! We're thrilled to have you on board.

## Get Started with HubFlow - Unlock Your Potential Today
Our intuitive apps are designed for a seamless experience. Dive in and explore
features that will enhance your productivity and simplify your workflow.

<x-mail::button :url="route('app.index')">
Get Started
</x-mail::button>

## We're Here to Support You
If you need any assistance or have questions, don't hesitate to reach out. Our team is ready to help whenever you need it.
<x-mail::button url="mailto:hello@hubflow.app">
Need help?
</x-mail::button>

<x-mail::panel>
**Tip of the Day:** Use Contact Cluster's map view to easily organize client visits and strengthen your relationships.
</x-mail::panel>

Cheers,<br />
The HubFlow Apps Team
</x-mail::message>
