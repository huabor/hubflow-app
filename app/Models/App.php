<?php

namespace App\Models;

use App\Enums\AppType;
use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class App extends Model
{
    use HasFactory;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'hub_id',
        'hubspot_token_id',

        'type',
        'name',
        'configuration',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'type' => AppType::class,
            'configuration' => 'object',
        ];
    }

    /**
     * Get the user that owns the Apps
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class
        );
    }

    /**
     * Get the Hubspot Token that owns the App
     */
    public function hubspotToken(): BelongsTo
    {
        return $this->belongsTo(
            related: HubspotToken::class
        );
    }
}
