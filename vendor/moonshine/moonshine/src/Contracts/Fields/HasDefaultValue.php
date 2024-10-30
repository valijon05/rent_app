<?php

declare(strict_types=1);

namespace MoonShine\Contracts\Fields;

interface HasDefaultValue
{
    public function default(mixed $default): static;

    public function getDefault(): mixed;
}
