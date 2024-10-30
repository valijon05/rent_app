<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Illuminate\Contracts\View\View;
use MoonShine\Components\Icon;

trait WithIcon
{
    protected ?string $icon = null;

    public function icon(string $icon): static
    {
        $this->icon = $icon;

        return $this;
    }

    public function getIcon(
        int $size = 8,
        string $color = '',
        string $class = ''
    ): View|string {
        if ($this->iconValue() === '') {
            return '';
        }

        return Icon::make(
            $this->iconValue(),
            $size,
            $color
        )->customAttributes(['class' => $class])->render();
    }

    public function iconValue(): string
    {
        return $this->icon ?? '';
    }
}
