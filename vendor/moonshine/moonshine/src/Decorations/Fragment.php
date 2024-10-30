<?php

declare(strict_types=1);

namespace MoonShine\Decorations;

use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Exceptions\DecorationException;
use MoonShine\Exceptions\PageException;
use MoonShine\Pages\Page;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;

/**
 * @method static static make(array $fields = [])
 */
class Fragment extends Decoration
{
    protected bool $isUpdateAsync = false;

    protected string $updateAsyncUrl = '';

    protected string $view = 'moonshine::decorations.fragment';

    /**
     * @throws DecorationException
     * @throws PageException
     */
    public function name(string $name): static
    {
        return parent::name($name)->updateAsync();
    }

    /**
     * @throws DecorationException
     * @throws PageException
     */
    public function updateAsync(
        array $params = [],
        string|ResourceContract|null $resource = null,
        string|Page|null $page = null,
    ): static {

        if (is_null($this->getName())) {
            throw new DecorationException("To use updateAsync you must first give the fragment a name");
        }

        /** @var ModelResource $resource */
        $resource ??= moonshineRequest()->getResource();

        $page ??= moonshineRequest()->getPage();

        if (is_null($resource) && is_null($page)) {
            throw new PageException("Resource or FormPage not found when generating updateAsyncUrl");
        }

        $this->updateAsyncUrl = to_page(
            page: $page,
            resource: $resource,
            params: $params,
            fragment: $this->getName()
        );

        $this->isUpdateAsync = true;

        return $this;
    }

    protected function isUpdateAsync(): bool
    {
        return $this->isUpdateAsync;
    }

    protected function updateAsyncUrl(): string
    {
        return $this->updateAsyncUrl;
    }

    /**
     * @param  array<string, string> $selectors
     * TODO remove duplicate
     */
    public function withParams(array $selectors): self
    {
        $this->customAttributes([
            'data-with-params' => collect($selectors)->map(fn ($value, $key): string => is_numeric($key) ? $value : "$value/$key")->implode(','),
        ]);

        return $this;
    }

    /**
     * @return array<string, mixed>
     */
    protected function viewData(): array
    {
        if ($this->isUpdateAsync()) {
            $this->customAttributes([
                'x-data' => 'fragment(`' . $this->updateAsyncUrl() . '`)',
                AlpineJs::eventBlade(JsEvent::FRAGMENT_UPDATED, $this->getName()) => 'fragmentUpdate',
            ]);
        }

        return [
            'element' => $this,
        ];
    }
}
