<?php

namespace MoonShine\Attributes;

use Attribute;
use Illuminate\Support\Arr;

#[Attribute(Attribute::TARGET_METHOD)]
class SearchUsingFullText
{
    public array $columns = [];

    public array $options = [];

    public function __construct(array|string $columns, array $options = [])
    {
        $this->columns = Arr::wrap($columns);
        $this->options = Arr::wrap($options);
    }
}
