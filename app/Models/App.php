<?php

namespace App\Models;

use App\DTO\BirthdayReminderConfiguration;
use App\Enums\AppType;
use App\Traits\Models\HasDefaultOrder;
use App\Traits\Models\StaticTableName;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class App extends Model
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
     * Get the hub that owns the Apps
     */
    public function hub(): BelongsTo
    {
        return $this->belongsTo(
            related: Hub::class
        );
    }

    /**
     * Get all of the contactCluster for the App
     */
    public function contactCluster(): HasMany
    {
        return $this->hasMany(
            related: ContactCluster::class,
        );
    }

    /**
     * Get the user's first name.
     */
    protected function configuration(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                if ($this->type === AppType::BIRTHDAY_REMINDER) {
                    $configuration = json_decode($value);

                    return BirthdayReminderConfiguration::from($configuration);
                }

                return $value;
            },
        );
    }
}
