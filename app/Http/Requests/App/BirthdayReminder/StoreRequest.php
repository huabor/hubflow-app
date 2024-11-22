<?php

namespace App\Http\Requests\App\BirthdayReminder;

use App\Enums\BirthdayReminderReceiver;
use App\Enums\Hubspot\ObjectType;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Validation\ValidationException;
use Spatie\ValidationRules\Rules\Delimited;

class StoreRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {
        return [
            'enabled' => [
                'present',
            ],

            // 'property' => [
            //     'required',
            //     'string',
            // ],

            'receiver' => [
                'required',
                'string',
                Rule::in(BirthdayReminderReceiver::values()),
            ],

            'receiver_emails' => [
                'present',
                'required_if:receiver,' . BirthdayReminderReceiver::EMAIL_RECEIVER->value,
                new Delimited('email')
            ],

            'send_reminder_before' => [
                'required',
                'numeric',
                'min:0',
                'max:21',
            ],

            'properties' => [
                'present',
                'array',
            ],

            'properties.*' => [
                'string',
            ],
        ];
    }
}
