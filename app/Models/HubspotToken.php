<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubspotToken extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',

        'token',
        'refresh_token',

        'hubspot_user_id',
        'email',
        'hub_id',
        'hub_domain',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'token',
        'refresh_token',
    ];

    /**
     * Get the user that owns the HubspotToken
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(
            related: User::class,
        );
    }
}
