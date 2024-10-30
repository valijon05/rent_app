<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Closure;
use Illuminate\Contracts\View\View;

/**
 * @method static static make(Closure|string|null $label = null, ?string $column = null)
 */
class Position extends Preview
{
    public function __construct(?string $label = null, ?string $column = null)
    {
        parent::__construct($label ?? '#', $column, static fn ($item, $index): int|float => $index + 1);

        $this->customAttributes([
            'data-increment-position' => true,
        ]);
    }

    protected function resolveValue(): mixed
    {
        return $this->toFormattedValue();
    }

    protected function resolvePreview(): View|string
    {
        if ($this->isRawMode()) {
            return (string) $this->toFormattedValue();
        }

        return $this->render();
    }
}
