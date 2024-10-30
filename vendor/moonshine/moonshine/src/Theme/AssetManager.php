<?php

declare(strict_types=1);

namespace MoonShine\Theme;

use Closure;
use Composer\InstalledVersions;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Traits\Conditionable;
use MoonShine\MoonShine;

class AssetManager
{
    use Conditionable;

    private array $assets = [];

    private string $mainJs = '/vendor/moonshine/assets/app.js';

    private string $mainCss = '/vendor/moonshine/assets/main.css';

    private ?Closure $lazy = null;

    private bool $extracted = false;

    public function lazyAssign(Closure $closure): self
    {
        $this->lazy = $closure;

        return $this;
    }

    public function mainCss(string $path): self
    {
        $this->mainCss = $path;

        return $this;
    }

    protected function getMainCss(): string
    {
        return $this->mainCss;
    }

    protected function getMainJs(): string
    {
        return $this->mainJs;
    }

    public function add(string|array|Closure $assets): void
    {
        if (is_closure($assets)) {
            $this->lazyAssign($assets);
        } else {
            $this->assets = array_unique(
                array_merge(
                    $this->assets,
                    is_array($assets) ? $assets : [$assets]
                )
            );
        }
    }

    public function getAssets(): array
    {
        return $this->assets;
    }

    private function isJs(string $value): bool
    {
        $value = strtolower(
            strtok($value, '?')
        );

        return pathinfo($value, PATHINFO_EXTENSION) === 'js';
    }

    private function isCss(string $value): bool
    {
        $value = strtolower(
            strtok($value, '?')
        );

        return pathinfo($value, PATHINFO_EXTENSION) === 'css';
    }

    public function js(): string
    {
        $attributes = $this->parseTagAttributes(
            config('moonshine.assets.js.script_attributes', ['defer'])
        );

        return collect($this->assets)
            ->when(! $this->isRunningHot(), fn (Collection $assets) => $assets->push($this->getMainJs()))
            ->filter(fn ($asset): bool => $this->isJs((string) $asset))
            ->map(
                fn ($asset): string => "<script $attributes src='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}'></script>"
            )->implode(PHP_EOL);
    }

    public function css(): string
    {
        $attributes = $this->parseTagAttributes(
            config('moonshine.assets.css.link_attributes', ['rel' => 'stylesheet'])
        );

        return collect($this->assets)
            ->when(! $this->isRunningHot(), fn (Collection $assets) => $assets->prepend($this->getMainCss()))
            ->filter(fn ($asset): bool => $this->isCss((string) $asset))
            ->map(
                fn ($asset): string => "<link $attributes href='" . asset(
                    $asset
                ) . (str_contains((string) $asset, '?') ? '&' : '?') . "v={$this->getVersion()}'>"
            )->implode(PHP_EOL);
    }

    private function parseTagAttributes(array|string $attributes): string
    {
        if (is_array($attributes)) {
            $attributes = collect($attributes)
                ->implode(fn ($v, $k) => is_int($k) ? $v : "$k='$v'", ' ');
        }

        return $attributes;
    }

    private function lazyExtract(): void
    {
        if (! $this->extracted) {
            $this->when(
                value($this->lazy, moonshineRequest()),
                fn (self $class, array $data) => $class
                    ->when(
                        Arr::isList($data) && filled($data),
                        static fn (AssetManager $assets) => $assets->add($data)
                    )
                    ->when(
                        isset($data['css']) && $data['css'] !== '',
                        static fn (AssetManager $assets): AssetManager => $assets->mainCss($data['css'])
                    )->when(
                        isset($data['assets']) && $data['assets'] !== [],
                        static fn (AssetManager $assets) => $assets->add($data['assets'])
                    )
            );
        }

        $this->extracted = true;
    }

    public function toHtml(): string
    {
        $this->lazyExtract();

        if ($this->isRunningHot()) {
            $vendorAssets = Vite::useBuildDirectory('vendor/moonshine')
                ->useHotFile($this->hotFile())
                ->withEntryPoints(['resources/css/main.css', 'resources/js/app.js'])
                ->toHtml();
        }

        return implode(PHP_EOL, [$this->js(), $vendorAssets ?? '', $this->css()]);
    }

    private function isRunningHot(): bool
    {
        return app()->isLocal() && is_file($this->hotFile());
    }

    private function hotFile(): string
    {
        return MoonShine::path('/public') . '/hot';
    }

    public function getVersion(): string
    {
        return InstalledVersions::getVersion('moonshine/moonshine');
    }
}
