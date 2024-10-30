<?php

declare(strict_types=1);

namespace MoonShine\Menu;

use Closure;
use MoonShine\Attributes\Icon;
use MoonShine\Contracts\Menu\MenuFiller;
use MoonShine\Support\Attributes;
use MoonShine\Traits\WithBadge;

/**
 * @method static static make(Closure|string $label, Closure|MenuFiller|string $filler, string $icon = null, Closure|bool $blank = false)
 */
class MenuItem extends MenuElement
{
    use WithBadge;

    final public function __construct(
        Closure|string $label,
        protected Closure|MenuFiller|string $filler,
        string $icon = null,
        Closure|bool $blank = false
    ) {
        $this->setLabel($label);

        if ($icon) {
            $this->icon($icon);
        }

        if ($filler instanceof MenuFiller) {
            $this->resolveMenuFiller($filler);
        } else {
            $this->setUrl($filler);
        }

        $this->blank($blank);
    }

    protected function resolveMenuFiller(MenuFiller $filler): void
    {
        $this->setUrl(fn (): string => $filler->url());

        $icon = Attributes::for($filler)
            ->attribute(Icon::class)
            ->attributeProperty('icon')
            ->get();

        if (method_exists($filler, 'getBadge')) {
            $this->badge(fn () => $filler->getBadge());
        }

        if (! is_null($icon) && $this->iconValue() === '') {
            $this->icon($icon);
        }
    }

    public function getFiller(): MenuFiller|Closure|string
    {
        return $this->filler;
    }
}
