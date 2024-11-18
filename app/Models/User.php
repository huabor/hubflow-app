<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Order\Contracts\ProvidesInvoiceInformation;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements ProvidesInvoiceInformation
{
    use Billable;

    use HasApiTokens;

    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory;
    use Notifiable;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',

        'tax_percentage',
        'trial_ends_at',
        'extra_billing_information',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the hubspotTokens for the User
     */
    public function hubspotTokens(): HasMany
    {
        return $this->hasMany(
            related: HubspotToken::class,
        );
    }

    /**
     * Get all of the apps for the User
     */
    public function apps(): HasMany
    {
        return $this->hasMany(
            related: App::class,
        );
    }

    /**
     * Get the receiver information for the invoice.
     * Typically includes the name and some sort of (E-mail/physical) address.
     *
     * @return array An array of strings
     */
    public function getInvoiceInformation()
    {
        return [$this->name, $this->email];
    }

    /**
     * Get additional information to be displayed on the invoice. Typically a note provided by the customer.
     *
     * @return string|null
     */
    public function getExtraBillingInformation()
    {
        return $this->extra_billing_information;
    }
}
