<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Color as ColorComponent;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeString;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Traits\Fields\WithDefaultValue;

class Color extends Field implements HasDefaultValue, DefaultCanBeString
{
    use WithDefaultValue;

    protected string $view = 'moonshine::fields.color';

    protected string $type = 'color';

    protected function resolvePreview(): View|string
    {
        if ($this->isRawMode()) {
            return $this->toValue();
        }

        return ColorComponent::make($this->value())
            ->render();
    }
}
