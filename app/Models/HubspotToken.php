<?php

namespace App\Models;

use App\Traits\Models\HasDefaultOrder;
use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class HubspotToken extends Model
{
    use HasDefaultOrder;
    use HasFactory;
    use StaticTableName;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'hub_id',

        'token',
        'refresh_token',

        'hubspot_user_id',
        'email',
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

    /**
     * Get the hub that owns the HubspotToken
     */
    public function hub(): BelongsTo
    {
        return $this->belongsTo(
            related: Hub::class,
        );
    }
}
