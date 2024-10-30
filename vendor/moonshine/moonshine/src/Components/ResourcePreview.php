<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Collection;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Traits\HasResource;
use Throwable;

/**
 * @deprecated will be removed in 3.0
 * @see \MoonShine\Components\TableBuilder
 * @method static static make(ResourceContract $resource, ?Closure $query = null)
 */
final class ResourcePreview extends MoonShineComponent
{
    use HasResource;

    protected ?Closure $query = null;

    public function __construct(
        ResourceContract $resource,
        ?Closure $query = null,
    ) {
        $this->setResource($resource);

        if (! is_null($query)) {
            $this->getResource()
                ->customBuilder(
                    $query(
                        $this->getResource()->query()
                    )
                );
        }
    }

    /**
     * @throws Throwable
     */
    public function items(): Collection
    {
        return $this->getResource()
            ->resolveQuery()
            ->get();
    }

    public function render(): View|Closure|string
    {
        return TableBuilder::make(
            $this->getResource()->getIndexFields(),
            $this->items()
        )
            ->name((string) str($this->getResource()->uriKey())->snake())
            ->cast($this->getResource()->getModelCast())
            ->preview()
            ->render();
    }
}
