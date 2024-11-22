<?php

declare(strict_types=1);

namespace App\Traits\Models;

trait HasDefaultOrder
{
    /*
     * Trait to add a default ordering to a eloquent model
     * The default order column is the models key and can be overwritten by setting $defaultOrderColumn on the model
     * The default order direction is ASC and can be overwritten by setting $defaultOrderDirection on the model. Must be ASC or DESC
     *
     * @return  void
     */
    public static function bootHasDefaultOrder(): void
    {
        $model = app(static::class);

        $column = $model->defaultOrderColumn;
        if ($column === null) {
            $column = $model->getKeyName();
        }

        $direction = $model->defaultOrderDirection;
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            $direction = 'ASC';
        }

        static::addGlobalScope(fn ($query) => $query->orderBy($column, $direction));
    }
}
