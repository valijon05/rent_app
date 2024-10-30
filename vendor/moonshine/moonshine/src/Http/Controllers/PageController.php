<?php

namespace MoonShine\Http\Controllers;

use Illuminate\Contracts\View\View;
use MoonShine\MoonShineRequest;

class PageController extends MoonShineController
{
    public function __invoke(MoonShineRequest $request): View|string
    {
        $request->getResource()?->boot();

        return $request
            ->getPage()
            ->checkUrl()
            ->render();
    }
}
