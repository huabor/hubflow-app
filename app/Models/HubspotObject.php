<?php

namespace App\Models;

use App\Enums\Hubspot\ObjectType;
use App\Traits\Models\StaticTableName;
use Clickbar\Magellan\Database\Eloquent\HasPostgisColumns;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HubspotObject extends Model
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

        'type',
        'hubspot_id',

        'properties',

        'location',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => ObjectType::class,
            'properties' => 'object',
        ];
    }

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
            get: fn () => "https://app-eu1.hubspot.com/contacts/{$this->hub_id}/record/0-2/{$this->hubspot_id}",
        );
    }
}
