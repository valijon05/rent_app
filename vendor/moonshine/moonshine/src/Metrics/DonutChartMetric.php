<?php

declare(strict_types=1);

namespace MoonShine\Metrics;

use Closure;

class DonutChartMetric extends Metric
{
    protected string $view = 'moonshine::metrics.donut-chart';

    protected array $values = [];

    protected array $colors = [];

    protected int $decimals = 3;

    protected array $assets = [
        'vendor/moonshine/libs/apexcharts/apexcharts.min.js',
        'vendor/moonshine/libs/apexcharts/apexcharts-config.js',
    ];

    /**
     * @param array<string, int|float>|Closure $values
     */
    public function values(array|Closure $values): self
    {
        $this->values = value($values);

        return $this;
    }

    /**
     * @return array<string, int|float>
     */
    public function getValues(): array
    {
        return array_values($this->values);
    }

    /**
     * @return string[]
     */
    public function getColors(): array
    {
        return $this->colors;
    }

    /**
     * @param string[]|Closure $colors
     */
    public function colors(array|Closure $colors): self
    {
        $this->colors = value($colors);

        return $this;
    }

    public function getDecimals(): int
    {
        return $this->decimals;
    }

    public function decimals(int $decimals): self
    {
        if (in_array($decimals, range(0, 100), true)) {
            $this->decimals = $decimals;
        }

        return $this;
    }

    public function labels(): array
    {
        return array_keys($this->values);
    }
}
