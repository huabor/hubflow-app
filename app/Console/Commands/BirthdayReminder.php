<?php

namespace App\Console\Commands;

use App\DTO\BirthdayReminderConfiguration;
use App\Enums\AppType;
use App\Enums\BirthdayReminderReceiver;
use App\Http\Integrations\Hubspot\CrmConnector;
use App\Http\Integrations\Hubspot\Requests\GetCompany;
use App\Http\Integrations\Hubspot\Requests\GetOwner;
use App\Http\Integrations\Hubspot\Requests\Property\ReadAllProperties;
use App\Http\Integrations\Hubspot\Requests\Property\ReadProperty;
use App\Http\Integrations\Hubspot\Requests\SearchContacts;
use App\Mail\Apps\BirthdayReminder as BirthdayReminderMail;
use App\Models\App;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class BirthdayReminder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'hubflow-apps:birthday-reminder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send daily Birthday Reminders for the HubSpot Contacts.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting birthday reminder command.');

        $apps = App::query()
            ->where(
                column: 'type',
                operator: '=',
                value: AppType::BIRTHDAY_REMINDER
            )
            ->orderBy('id', 'asc')
            ->get();

        foreach ($apps as $app) {
            $configuration = BirthdayReminderConfiguration::from($app->configuration);

            if ($configuration->enabled === false) {
                $this->info("$app->name is not enabled!");

                continue;
            }

            $hub = $app->hub;
            $user = $hub->billingAdmin;
            $token = $user->hubspotTokens()
                ->where(
                    column: 'hub_id',
                    operator: '=',
                    value: $hub->id
                )
                ->first();

            // Initialize Hubspot CRM connector with the API token from configuration
            $hubspotCrmConnector = new CrmConnector(
                token: $token->token,
                hubspotToken: $token,
            );

            $propertyName = 'date_of_birth';
            $readProperty = new ReadProperty(
                hubId: $hub->id,
                objectType: 'contacts',
                propertyName: $propertyName,
            );
            $propertyResponse = $hubspotCrmConnector->send($readProperty);
            $birthdayProperty = $propertyResponse->json();

            $readAllProperties = new ReadAllProperties(
                hubId: $hub->id,
                objectType: 'contacts',
            );
            $propertyResponse = $hubspotCrmConnector->send($readAllProperties);
            $contactProperties = $propertyResponse->collect('results');

            if (isset($response['status']) && $response['status'] === 'error') {
                $this->info("$propertyName doesn't exists!");

                // @Todo: Error handling
                continue;
            }

            // Set date to retrieve birthdays
            $date = Carbon::today()->addDays($configuration->send_reminder_before);
            $this->info('Date set to: '.$date->toDateString());

            $day = $date->day;
            $month = $date->month;
            $years = collect(range(date('Y'), 1925));
            $filterValue = $years
                ->map(function ($year) use ($birthdayProperty, $month, $day) {
                    $date = Carbon::parse("$year-$month-$day");

                    if ($birthdayProperty['fieldType'] === 'date') {
                        return $date->getTimestampMs();
                    }

                    return $date->format('Y-m-d');
                })
                ->toArray();

            // Search for contacts whose birthdays match today's day and month
            $searchContacts = new SearchContacts(
                field: $propertyName,
                operator: 'IN',
                filterValue: $filterValue,
                properties: $configuration->properties,
            );

            $this->info('Searching for contacts with birthdays today.');
            $res = $hubspotCrmConnector->send($searchContacts);
            $this->info('Search contacts response received.');

            // Collect contact results
            $contacts = $res->collect('results');

            $birthdays = $contacts->map(function ($contact) use ($configuration, $hubspotCrmConnector, $hub, $contactProperties) {
                $this->info('Processing contact ID: '.$contact['id']);

                $contactId = $contact['id'];
                $properties = $contact['properties'];

                // Store contact details
                $result = [
                    'firstname' => $properties['firstname'],
                    'lastname' => $properties['lastname'],
                    'dateOfBirth' => $properties['date_of_birth'],
                    'hubspotContactUrl' => "https://app-eu1.hubspot.com/contacts/$hub->hub_id/record/0-1/$contactId",
                ];

                $result['properties'] = collect($configuration->properties)
                    ->map(fn ($property) => [
                        'key' => $property,
                        'label' => $contactProperties->first(fn ($contactProperty) => $contactProperty['name'] === $property)['label'] ?? $property,
                        'value' => $properties[$property] ?? null,
                    ])
                    ->all();

                $this->info('Contact name: '.$result['firstname'].' '.$result['lastname']);

                if ($properties['hubspot_owner_id'] !== null) {
                    $ownerId = $properties['hubspot_owner_id'];
                    $getOwner = new GetOwner(userId: $ownerId);
                    $getOwnerResponse = $hubspotCrmConnector->send($getOwner);
                    $owner = $getOwnerResponse->json();
                    $result['ownerFirstname'] = $owner['firstName'];
                    $result['ownerLastname'] = $owner['lastName'];
                    $result['ownerEmail'] = $owner['email'];
                }

                if ($properties['associatedcompanyid'] !== null) {
                    $companyId = $properties['associatedcompanyid'];
                    $getCompany = new GetCompany(companyId: $companyId);
                    $getCompanyResponse = $hubspotCrmConnector->send($getCompany);
                    $company = $getCompanyResponse->json();
                    $result['companyDomain'] = $company['properties']['domain'];
                    $result['companyName'] = $company['properties']['name'];
                }

                return $result;
            });

            if ($configuration->receiver === BirthdayReminderReceiver::EMAIL_RECEIVER) {
                $emails = explode(
                    string: $configuration->receiver_emails,
                    separator: ','
                );

                foreach ($emails as $email) {
                    Mail::to(
                        users: [$email],
                    )->send(
                        new BirthdayReminderMail(
                            birthdays: $birthdays->all(),
                            date: $date,
                        )
                    );
                }
            } elseif ($configuration->receiver === BirthdayReminderReceiver::CONTACT_OWNER) {
                $groupedBirthdays = $birthdays->groupBy('ownerEmail');

                // Send an email to each owner with their contacts' birthday details
                $groupedBirthdays->each(function ($groupedBirthday) use ($configuration, $date) {
                    if (! isset($groupedBirthday->first()['ownerEmail'])) {
                        $emails = explode(
                            string: $configuration->receiver_emails,
                            separator: ','
                        );

                        foreach ($emails as $email) {
                            // Check if receiver is a valid E-Mail
                            $validator = Validator::make(['email' => $email], [
                                'email' => 'required|email',
                            ]);

                            if ($validator->passes()) {
                                Mail::to(
                                    users: [$email],
                                )->send(
                                    new BirthdayReminderMail(
                                        birthdays: $groupedBirthday->all(),
                                        date: $date,
                                    )
                                );
                            }
                        }
                    } else {
                        $receiver = [
                            'firstname' => $groupedBirthday->first()['ownerFirstname'],
                            'lastname' => $groupedBirthday->first()['ownerLastname'],
                            'email' => $groupedBirthday->first()['ownerEmail'],
                        ];

                        // Check if receiver is a valid E-Mail
                        $validator = Validator::make(['email' => $receiver['email']], [
                            'email' => 'required|email',
                        ]);

                        if ($validator->passes()) {
                            Mail::to(
                                users: [
                                    $receiver['email'],
                                ],
                                name: "{$receiver['firstname']} {$receiver['lastname']}"
                            )->send(
                                new BirthdayReminderMail(
                                    receiver: $receiver,
                                    birthdays: $groupedBirthday->all(),
                                    date: $date,
                                )
                            );
                        }
                    }
                });
            }
        }
    }
}
