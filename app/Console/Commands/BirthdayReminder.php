<?php

namespace App\Console\Commands;

use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\GetCompany;
use App\Http\Integrations\Hubspot\Requests\GetOwner;
use App\Http\Integrations\Hubspot\Requests\SearchContacts;
use App\Mail\Apps\BirthdayReminder as BirthdayReminderMail;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use stdClass;

class BirthdayReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubspot-apps:birthday-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily birthday reminders to the Contact owner of hubspot';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $notificationText = new stdClass;
        $notificationText->name = 'Birthday Reminder Textfield';
        $notificationText->field = 'star_sign';
        $notificationText->operator = 'CONTAINS_TOKEN';
        $notificationText->field_type = 'text';
        $notificationText->filter = new stdClass;
        $notificationText->filter->daysInFuture = -1;
        $notificationText->filter->dateFormat = 'd|m';

        $notificationDate = new stdClass;
        $notificationDate->name = 'Birthday Reminder';
        $notificationDate->field = 'date_of_birth';
        $notificationDate->operator = 'IN';
        $notificationDate->field_type = 'date';
        $notificationDate->filter = new stdClass;
        $notificationDate->filter->daysInFuture = -1;
        $notificationDate->filter->ignoreYear = true;

        $notifications = [
            $notificationDate,
            $notificationText,
        ];

        foreach ($notifications as $notification) {

            $this->info('Starting birthday reminder command.');

            // Initialize Hubspot CRM connector with the API token from configuration
            $hubspotCrmConnector = new CrmConnector(
                token: config('services.hubspot.token'),
            );

            $this->info('Hubspot CRM Connector initialized.');

            // Set today's date
            $date = Carbon::today()->addDays($notification->filter->daysInFuture);
            $this->info('Date set to: '.$date->toDateString());

            $filterValue = '';
            if ($notification->field_type === 'date') {
                if ($notification->filter->ignoreYear) {
                    $years = collect(range(date('Y'), 1925));
                } else {
                    $years = collect([$date->year]);
                }

                $filterValue = $years->map(fn ($year) => Carbon::parse("$year-$date->month-$date->day")->getTimestampMs())->toArray();
            } elseif ($notification->field_type === 'text') {
                $filterValue = $date->format($notification->filter->dateFormat);
                $this->info($filterValue);
            }

            // Search for contacts whose birthdays match today's day and month
            $searchContacts = new SearchContacts(
                field: $notification->field,
                operator: $notification->operator,
                filterValue: $filterValue,
            );

            $this->info('Searching for contacts with birthdays today.');
            $res = $hubspotCrmConnector->send($searchContacts);
            $this->info('Search contacts response received.');

            // Collect contact results
            $contacts = $res->collect('results');

            $this->info($notification->name);
            $this->info($contacts->map(fn ($c) => $c['properties']['firstname'].' '.$c['properties']['lastname'].' - '.$c['properties']['date_of_birth'])->join(' | '));

            $owners = collect(); // Cache for owners to avoid multiple API calls
            $companies = collect(); // Cache for companies to avoid multiple API calls

            continue;
            $birthdays = $contacts->map(function ($contact) use ($hubspotCrmConnector, $owners, $companies) {
                $this->info('Processing contact ID: '.$contact['id']);

                $contactId = $contact['id'];
                $properties = $contact['properties'];

                // Store contact details
                $result = [
                    'firstname' => $properties['firstname'],
                    'lastname' => $properties['lastname'],
                    'dateOfBirth' => $properties['date_of_birth'],
                    'birthdayText' => $properties['birthdaytext'],
                    'hubspotContactUrl' => "https://app-eu1.hubspot.com/contacts/143411655/record/0-1/{$contactId}",
                ];

                $this->info('Contact name: '.$result['firstname'].' '.$result['lastname']);

                // Fetch owner details, using cache if available
                $ownerId = $properties['hubspot_owner_id'];
                $owner = $owners->get($ownerId);

                if (! $owner) {
                    $this->info('Fetching owner ID: '.$ownerId);
                    $getOwner = new GetOwner(userId: $ownerId);
                    $getOwnerResponse = $hubspotCrmConnector->send($getOwner);
                    $owner = $getOwnerResponse->json();
                    $owners->put($ownerId, $owner);
                    $this->info('Owner data cached for ID: '.$ownerId);
                } else {
                    $this->info('Owner ID '.$ownerId.' found in cache.');
                }

                // Store owner details
                $result['ownerFirstname'] = $owner['firstName'];
                $result['ownerLastname'] = $owner['lastName'];
                $result['ownerEmail'] = $owner['email'];

                // Fetch company details, using cache if available
                $companyId = $properties['associatedcompanyid'];
                $company = $companies->get($companyId);

                if (! $company) {
                    $this->info('Fetching company ID: '.$companyId);
                    $getCompany = new GetCompany(companyId: $companyId);
                    $getCompanyResponse = $hubspotCrmConnector->send($getCompany);
                    $company = $getCompanyResponse->json();
                    $companies->put($companyId, $company);
                    $this->info('Company data cached for ID: '.$companyId);
                } else {
                    $this->info('Company ID '.$companyId.' found in cache.');
                }

                // Store company details
                $result['companyDomain'] = $company['properties']['domain'];
                $result['companyName'] = $company['properties']['name'];

                $this->info('Contact processed: '.$result['firstname'].' '.$result['lastname']);

                return $result;
            });

            // Group birthdays by the owner's email address
            $groupedBirthdays = $birthdays->groupBy('ownerEmail');
            $this->info('Birthdays grouped by owner email.');

            // Send an email to each owner with their contacts' birthday details
            $groupedBirthdays->each(function ($groupedBirthday) {
                $receiver = [
                    'firstname' => $groupedBirthday->first()['ownerFirstname'],
                    'lastname' => $groupedBirthday->first()['ownerLastname'],
                    'email' => $groupedBirthday->first()['ownerEmail'],
                ];

                $this->info('Sending email to: '.$receiver['email']);

                // Send the birthday reminder email
                Mail::to(
                    // users: $receiver['email'],
                    users: [
                        'peter.huber@brandnamic.com',
                        'stephan.kohl@brandnamic.com',
                    ],
                    name: "{$receiver['firstname']} {$receiver['lastname']}"
                )->send(
                    new BirthdayReminderMail(
                        receiver: $receiver,
                        birthdays: $groupedBirthday->toArray()
                    )
                );

                $this->info('Email sent to: '.$receiver['email']);
            });

            $this->info('Birthday reminder command completed.');
        }
    }
}
