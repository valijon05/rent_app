<?php

declare(strict_types=1);

namespace MoonShine\Traits;

use Closure;
use Illuminate\Support\Arr;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\AsyncCallback;

trait HasAsync
{
    protected Closure|string|null $asyncUrl = null;

    protected string|array|null $asyncEvents = null;

    protected ?string $asyncCallback = null;

    protected ?string $asyncBeforeCallback = null;

    public function isAsync(): bool
    {
        return ! is_null($this->asyncUrl);
    }

    protected function prepareAsyncUrl(Closure|string|null $asyncUrl = null): Closure|string|null
    {
        return $asyncUrl;
    }

    protected function prepareAsyncUrlFromPaginator(): string
    {
        $withoutQuery = strtok($this->asyncUrl(), '?');

        if (! $withoutQuery) {
            return $this->asyncUrl();
        }

        $query = parse_url($this->asyncUrl(), PHP_URL_QUERY);

        parse_str((string) $query, $asyncUri);

        $paginatorUri = $this->getPaginator()
            ?->resolveQueryString() ?? [];

        $asyncUri = array_filter(
            $asyncUri,
            static fn ($value, $key): bool => ! isset($paginatorUri[$key]),
            ARRAY_FILTER_USE_BOTH
        );

        if ($asyncUri !== []) {
            return $withoutQuery . "?" . Arr::query($asyncUri);
        }

        return $withoutQuery;
    }

    public function async(
        Closure|string|null $asyncUrl = null,
        string|array|null $asyncEvents = null,
        string|AsyncCallback|null $asyncCallback = null,
    ): static {
        $this->asyncUrl = $this->prepareAsyncUrl($asyncUrl);
        $this->asyncEvents = $asyncEvents;

        //TODO remove $callback string type in future version
        if ($asyncCallback instanceof AsyncCallback) {
            $this->asyncCallback = $asyncCallback->success();
            $this->asyncBeforeCallback = $asyncCallback->before();
        } else {
            $this->asyncCallback = $asyncCallback;
        }

        return $this;
    }

    public function asyncUrl(): ?string
    {
        return value($this->asyncUrl);
    }

    public function asyncEvents(): string|null
    {
        if (is_null($this->asyncEvents)) {
            return $this->asyncEvents;
        }

        return AlpineJs::prepareEvents($this->asyncEvents);
    }

    public function asyncCallback(): ?string
    {
        return $this->asyncCallback;
    }

    public function asyncBeforeCallback(): ?string
    {
        return $this->asyncBeforeCallback;
    }
}
