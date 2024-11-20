<?php

namespace App\Models;

use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Laravel\Cashier\Billable;
use Laravel\Cashier\Order\Contracts\ProvidesInvoiceInformation;

class Hub extends Model implements ProvidesInvoiceInformation
{
    use Billable;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',

        'hub_id',
        'domain',

        'tax_percentage',
        'trial_ends_at',
        'extra_billing_information',
    ];

    /**
     * Get the billingAdmin associated with the Hub
     */
    public function billingAdmin(): HasOne
    {
        return $this->hasOne(
            related: User::class
        );
    }

    /**
     * Get all of the apps for the Hub
     */
    public function apps(): HasMany
    {
        return $this->hasMany(
            related: App::class
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
        $billingAdmin = $this->billingAdmin;

        return [
            "$billingAdmin->firstname $billingAdmin->lastname",
            $billingAdmin->email,
        ];
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
