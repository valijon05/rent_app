<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;

/**
 * @method static static make(Closure $condition, Closure $components, ?Closure $default = null)
 */
class When extends MoonShineComponent
{
    protected string $view = 'moonshine::components.components';

    public function __construct(
        protected Closure $condition,
        protected Closure $components,
        protected ?Closure $default = null
    ) {
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        if (($this->condition)()) {
            $components = ($this->components)();
        } else {
            $components = is_null($this->default) ? [] : ($this->default)();
        }

        return [
            'components' => $components,
        ];
    }
}
