<?php

namespace App\Models;

use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubspotCompany extends Model
{
    use HasFactory;
    use HasPostgisColumns;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hubspot_token_id',

        'hubspot_id',

        'name',
        'industry_sector',

        'address',
        'city',
        'zip',
        'country',

        'coordinates',
    ];

    /**
     * The additional attributes that should allways be appended.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'deep_link',
    ];

    /**
     * The attributes that are for postgis.
     *
     * @var array<int, string>
     */
    protected array $postgisColumns = [
        'coordinates' => [
            'type' => 'geometry',
            'srid' => 4326,
        ],
    ];

    /**
     * Get the hubspotToken that owns the HubspotCompany
     */
    public function hubspotToken(): BelongsTo
    {
        return $this->belongsTo(
            related: HubspotToken::class
        );
    }

    /**
     * Get the user's first name.
     */
    protected function deepLink(): Attribute
    {
        return Attribute::make(
            get: fn () => "https://app-eu1.hubspot.com/contacts/{$this->hubspotToken->hub_id}/record/0-2/{$this->hubspot_id}",
        );
    }
}
