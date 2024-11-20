<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
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
        'hub_id',

        'hubspot_id',

        'firstname',
        'lastname',
        'email',
        'password',
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
     * The additional relations that should allways be loaded.
     *
     * @var array<int, string>
     */
    protected $with = [
        'selectedHub',
    ];

    /**
     * The additional attributes that should allways be appended.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'avatar',
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
     * Get the selected hub for the User
     */
    public function selectedHub(): BelongsTo
    {
        return $this->belongsTo(
            related: Hub::class,
            foreignKey: 'hub_id',
        );
    }

    /**
     * Get all hubs for the User
     */
    public function hubs(): BelongsToMany
    {
        return $this->belongsToMany(
            related: Hub::class,
            table: HubspotToken::getTableName(),
        );
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

    protected function avatar(): Attribute
    {
        $hash = md5(strtolower(trim($this->email)));
        $firstname = $this->firstname[0] ?? '';
        $lastname = $this->lastname[0] ?? '';
        $fallback =urlencode("https://ui-avatars.com/api/$firstname$lastname/128/5E93D2/FFFFFF");

        return Attribute::make(
            get: fn() => "https://www.gravatar.com/avatar/$hash?d=$fallback",
        );
    }
}
