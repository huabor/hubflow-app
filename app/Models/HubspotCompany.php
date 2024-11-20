<?php

namespace App\Models;

use App\Traits\Models\StaticTableName;
use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubspotCompany extends Model
{
    use HasFactory;
    use HasPostgisColumns;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hub_id',
        'hubspot_id',

        'name',

        'address',
        'city',
        'zip',
        'country',

        'location',
    ];

    /**
     * The additional attributes that should allways be appended.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'coordinates',
        'deep_link',
    ];

    /**
     * The attributes that are for postgis.
     *
     * @var array<int, string>
     */
    protected array $postgisColumns = [
        'location' => [
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

    protected function coordinates(): Attribute
    {
        return Attribute::make(
            get: fn () => [
                'x' => $this->location->getX(),
                'y' => $this->location->getY(),
            ]
        );
    }

    protected function deepLink(): Attribute
    {
        return Attribute::make(
            get: fn () => "https://app-eu1.hubspot.com/contacts/{$this->hubspotToken->hub_id}/record/0-2/{$this->hubspot_id}",
        );
    }
}
