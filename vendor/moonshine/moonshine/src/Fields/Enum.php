<?php

declare(strict_types=1);

namespace MoonShine\Fields;

use BackedEnum;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeEnum;

class Enum extends Select implements DefaultCanBeEnum
{
    /** @var class-string<BackedEnum>|null */
    protected ?string $attached = null;

    /**
     * @param  class-string<BackedEnum>  $class
     * @return $this
     */
    public function attach(string $class): static
    {
        $this->attached = $class;

        $values = collect($class::cases());

        $this->options(
            $values->mapWithKeys(fn ($value): array => [
                $value->value => method_exists($value, 'toString')
                    ? $value->toString()
                    : $value->value,
            ])->toArray()
        );

        return $this;
    }

    protected function resolvePreview(): string
    {
        $value = $this->toFormattedValue();

        if (is_null($value)) {
            return '';
        }

        if (! $value instanceof $this->attached) {
            $value = rescue(fn () => $this->attached::tryFrom($value)) ?? $value;
        }

        if (is_scalar($value)) {
            return data_get(
                $this->values(),
                $value,
                (string) $value
            );
        }

        if (method_exists($value, 'getColor')) {
            $this->badge($value->getColor());
        }

        if (method_exists($value, 'toString')) {
            return (string) $value->toString();
        }

        return (string) ($value?->value ?? '');
    }
}
