<?php

declare(strict_types=1);

namespace MoonShine\Forms;

use MoonShine\ActionButtons\ActionButton;
use MoonShine\Components\FormBuilder;
use MoonShine\Contracts\Fields\RangeField;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Resources\ModelResource;
use MoonShine\Support\AlpineJs;
use Throwable;

final class FiltersForm
{
    /**
     * @throws Throwable
     */
    public function __invoke(ModelResource $resource): FormBuilder
    {
        $values = $resource->getFilterParams();
        $filters = $resource->getFilters();

        $action = $resource->isAsync() ? '#' : $resource->currentRoute();

        $filters->fill($values);

        foreach ($filters->onlyFields() as $filter) {
            if ($filter instanceof RangeField) {
                data_forget($values, $filter->column());
            }
        }

        return FormBuilder::make($action, 'GET')
            ->name('filters')
            ->fillCast($values, $resource->getModelCast())
            ->fields(
                $filters
                    ->when(
                        request()->input('sort'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'sort')->setValue(request()->input('sort')))
                    )
                    ->when(
                        request()->input('query-tag'),
                        static fn ($fields): Fields => $fields
                            ->prepend(Hidden::make(column: 'query-tag')->setValue(request()->input('query-tag')))
                    )
                    ->toArray()
            )
            ->when($resource->isAsync(), function (FormBuilder $form) use ($resource): void {
                $events = [
                    $resource->listEventName(),
                    'show-reset-filters',
                    AlpineJs::event(JsEvent::OFF_CANVAS_TOGGLED, 'filters-off-canvas'),
                ];

                $form->customAttributes([
                    '@submit.prevent' => "asyncFilters(
                        `" . AlpineJs::prepareEvents($events) . "`,
                        `_component_name,_token,_method`
                    )",
                ]);

                $form->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )
                        ->secondary()
                        ->showInLine()
                        ->customAttributes([
                            AlpineJs::eventBlade('show-reset', 'filters') => "showResetButton",
                            'style' => 'display: none',
                            'id' => 'async-reset-button',
                        ])
                    ,
                ]);
            })
            ->submit(__('moonshine::ui.search'), ['class' => 'btn-primary'])
            ->when(
                request()->input('filters'),
                static fn ($fields): FormBuilder => $fields->buttons([
                    ActionButton::make(
                        __('moonshine::ui.reset'),
                        $resource->currentRoute(query: ['reset' => true])
                    )->secondary()->showInLine(),
                ])
            )
        ;
    }
}
