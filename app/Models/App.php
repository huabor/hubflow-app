<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

class App extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'hubspot_token_id',

        'type',
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
     * Get the hubspotToken associated with the Apps
     */
    public function hubspotToken(): HasOne
    {
        return $this->hasOne(
            related: HubspotToken::class
        );
    }
}
