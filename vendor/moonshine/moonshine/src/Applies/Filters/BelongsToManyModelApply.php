<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;
use MoonShine\Fields\Relationships\ModelRelationField;

class BelongsToManyModelApply implements ApplyContract
{
    /* @param  \MoonShine\Fields\Relationships\BelongsToMany  $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! $field instanceof ModelRelationField) {
                return;
            }

            $value = $field->requestValue();

            $values = array_filter(
                is_array($value) ? $value : [$value]
            );

            if (is_null($field->getRelation()) || blank($values)) {
                return;
            }

            $query->whereHas(
                $field->getRelationName(),
                function (Builder $q) use ($field, $values): Builder {
                    $table = $field->getRelation()?->getTable();
                    $id = $field->getRelation()?->getRelatedPivotKeyName();

                    return $q->whereIn(
                        "$table.$id",
                        $field->isSelectMode()
                            ? $values
                            : array_keys($values)
                    );
                }
            );
        };
    }
}
