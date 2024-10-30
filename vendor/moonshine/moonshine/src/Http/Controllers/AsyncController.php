<?php

namespace MoonShine\Http\Controllers;

use Closure;
use Illuminate\Contracts\View\View;
use MoonShine\Components\FieldsGroup;
use MoonShine\Components\TableBuilder;
use MoonShine\Fields\Field;
use MoonShine\Fields\Relationships\BelongsTo;
use MoonShine\Fields\Select;
use MoonShine\MoonShineRequest;
use MoonShine\Support\TableRowRenderer;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\StreamedResponse;
use Throwable;

class AsyncController extends MoonShineController
{
    /**
     * @throws Throwable
     * @see component
     * @deprecated will be removed in 3.0
     */
    public function table(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $table = $page->getComponents()->findTable(
            $request->getComponentName()
        );

        if (is_null($table)) {
            return '';
        }

        return $this->responseWithTable($table);
    }

    /**
     * @throws Throwable
     */
    protected function responseWithTable(TableBuilder $table): View|Closure|string
    {
        if (! request()->filled('_key')) {
            return $table->render();
        }

        return TableRowRenderer::make(
            $table,
            request()->input('_key'),
            request()->integer('_index'),
        )->render();
    }

    /**
     * @throws Throwable
     */
    public function component(MoonShineRequest $request): View|Closure|string
    {
        $page = $request->getPage();

        $component = $page->getComponents()->findByName(
            $request->getComponentName()
        );

        if (is_null($component)) {
            return '';
        }

        if ($component instanceof TableBuilder) {
            return $this->responseWithTable($component);
        }

        return $component->render();
    }

    /**
     * @throws Throwable
     */
    public function method(MoonShineRequest $request): Response
    {
        $toast = [
            'type' => 'info',
            'message' => $request->input('message', ''),
        ];

        try {
            $pageOrResource = $request->hasResource()
                ? $request->getResource()
                : $request->getPage();

            $result = $pageOrResource
                ?->{$request->input('method')}(
                    $request
                );

            $toast = $request->session()->get('toast', $toast);
        } catch (Throwable $e) {
            report($e);

            $result = $e;
        }

        $request->session()->forget('toast');

        if ($result instanceof JsonResponse) {
            return $result;
        }

        if ($result instanceof BinaryFileResponse || $result instanceof StreamedResponse) {
            return $result;
        }

        return $this->json(
            message: $result instanceof Throwable ? $result->getMessage() : $toast['message'],
            redirect: $result instanceof RedirectResponse ? $result->getTargetUrl() : null,
            messageType: $result instanceof Throwable ? 'error' : $toast['type']
        );
    }

    /**
     * @throws Throwable
     */
    public function reactive(MoonShineRequest $request): JsonResponse
    {
        $page = $request->getPage();

        $form = $page->getComponents()->findForm(
            $request->getComponentName()
        );

        if (is_null($form)) {
            return $this->json();
        }

        $fields = $form
            ->preparedFields()
            ->onlyFields()
            ->reactiveFields();

        $casted = null;

        $values = $request->collect('values')->map(function ($value, $column) use ($fields, &$casted) {
            $field = $fields->findByColumn($column);

            if ($field instanceof Select) {
                $value = data_get($value, 'value', $value);
            }

            if ($field instanceof BelongsTo) {
                $value = data_get($value, 'value', $value);

                $casted = $field->getRelatedModel();
                $related = $field->getRelation()?->getRelated();

                $target = $related?->forceFill([
                    $related->getKeyName() => $value,
                ]);

                $casted?->setRelation($field->getRelationName(), $target);
            }

            return $value;
        });


        $fields->fill($values->toArray(), $casted);

        foreach ($fields as $field) {
            $fields = $field->reactiveCallback(
                $fields,
                data_get($values, $field->column()),
                $values->toArray(),
            );
        }

        $values = $fields
            ->mapWithKeys(fn (Field $field): array => [$field->column() => $field->value()]);

        $fields = $fields->mapWithKeys(
            fn (Field $field): array => [$field->column() => (string) FieldsGroup::make([$field])->render()]
        );

        return $this->json(data: [
            'form' => $form,
            'fields' => $fields,
            'values' => $values,
        ]);
    }
}
