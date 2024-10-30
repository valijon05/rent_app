<?php

declare(strict_types=1);

namespace MoonShine\Applies\Filters;

use Closure;
use Illuminate\Contracts\Database\Eloquent\Builder;
use MoonShine\Contracts\ApplyContract;
use MoonShine\Fields\Field;

class CheckboxModelApply implements ApplyContract
{
    /* @param \MoonShine\Fields\Checkbox $field */
    public function apply(Field $field): Closure
    {
        return static function (Builder $query) use ($field): void {
            if (! empty($field->requestValue())) {
                $query->where($field->column(), $field->requestValue());
            }
        };
    }
}
