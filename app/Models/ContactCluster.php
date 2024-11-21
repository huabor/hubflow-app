<?php

namespace App\Models;

use App\Enums\Hubspot\ObjectType;
use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class ContactCluster extends Model
{
    use HasFactory;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'app_id',

        'type',
        'name',
        'color',
        'filter',

        'refreshed_at',
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
            'filter' => 'array',
            'refreshed_at' => 'datetime',
        ];
    }

    /**
     * Get the app that owns the ContactCluster
     */
    public function app(): BelongsTo
    {
        return $this->belongsTo(
            related: App::class,
        );
    }

    /**
     * The objects that belong to the ContactCluster
     */
    public function objects(): BelongsToMany
    {
        return $this->belongsToMany(
            related: HubspotObject::class,
            table: 'contact_cluster_objects',
        );
    }

    /**
     * The objects that belong to the ContactCluster
     */
    public function resolvedObjects(): BelongsToMany
    {
        return $this->belongsToMany(
            related: HubspotObject::class,
            table: 'contact_cluster_objects',
        )->whereNotNull('location');
    }
}
