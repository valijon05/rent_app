<?php

declare(strict_types=1);

namespace MoonShine\Components;

use Closure;
use Illuminate\View\ComponentAttributeBag;
use JsonException;
use MoonShine\Contracts\Resources\ResourceContract;
use MoonShine\Enums\JsEvent;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Hidden;
use MoonShine\Pages\Page;
use MoonShine\Support\AlpineJs;
use MoonShine\Support\AsyncCallback;
use MoonShine\Traits\Fields\WithAdditionalFields;
use MoonShine\Traits\HasAsync;
use Throwable;

/**
 * @method static static make(string $action = '', string $method = 'POST', Fields|array $fields = [], mixed $values = [])
 */
final class FormBuilder extends RowComponent
{
    use HasAsync;

    use WithAdditionalFields;

    protected string $view = 'moonshine::components.form.builder';

    protected array $excludeFields = [
        '_force_redirect',
        '_redirect',
        '_method',
        '_component_name',
        '_async_field',
    ];

    protected bool $isPrecognitive = false;

    protected ?string $submitLabel = null;

    protected bool $hideSubmit = false;

    protected ComponentAttributeBag $submitAttributes;

    protected ?Closure $onBeforeFieldsRender = null;

    protected Closure|string|null $reactiveUrl = null;

    protected bool $errorsAbove = true;

    public function __construct(
        protected string $action = '',
        protected string $method = 'POST',
        Fields|array $fields = [],
        mixed $values = []
    ) {
        $this->fields($fields);
        $this->fill($values);

        $this->submitAttributes = $this->newAttributeBag([
            'type' => 'submit',
        ]);

        $this->withAttributes([
            'action' => $this->action,
            'method' => $this->method,
            'enctype' => 'multipart/form-data',
        ]);
    }

    public function action(string $action): self
    {
        $this->action = $action;
        $this->attributes['action'] = $this->action;

        return $this;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function precognitive(): self
    {
        $this->isPrecognitive = true;

        return $this;
    }

    public function isPrecognitive(): bool
    {
        return $this->isPrecognitive;
    }

    public function errorsAbove(bool $enable = true): self
    {
        $this->errorsAbove = $enable;

        return $this;
    }

    public function hasErrorsAbove(): bool
    {
        return $this->errorsAbove;
    }


    protected function prepareAsyncUrl(Closure|string|null $asyncUrl = null): Closure|string|null
    {
        return $asyncUrl ?? $this->getAction();
    }

    /**
     * @throws Throwable
     */
    public function asyncMethod(
        string $method,
        ?string $message = null,
        array $events = [],
        string|AsyncCallback|null $callback = null,
        ?Page $page = null,
        ?ResourceContract $resource = null,
    ): self {
        $asyncUrl = moonshineRouter()->asyncMethod(
            $method,
            $message,
            params: ['resourceItem' => $resource?->getItemID()],
            page: $page,
            resource: $resource
        );

        return $this->action($asyncUrl)->async(
            $asyncUrl,
            asyncEvents: $events,
            asyncCallback: $callback
        );
    }

    public function reactiveUrl(Closure|string $reactiveUrl): self
    {
        $this->reactiveUrl = $reactiveUrl;

        return $this;
    }

    private function getReactiveUrl(): string
    {
        if (! is_null($this->reactiveUrl)) {
            return value($this->reactiveUrl, $this);
        }

        return moonshineRouter()->reactive();
    }

    public function method(string $method): self
    {
        $this->method = $method;
        $this->attributes['method'] = $this->method;

        return $this;
    }

    public function redirect(?string $uri = null): self
    {
        if (! is_null($uri)) {
            $this->additionalFields[] = Hidden::make('_redirect')
                ->setValue($uri);
        }

        return $this;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function dispatchEvent(array|string $events): self
    {
        return $this->customAttributes([
            '@submit.prevent' => "dispatchEvents(
                `" . AlpineJs::prepareEvents($events) . "`,
                `_component_name,_token,_method`
            )",
        ]);
    }

    public function hideSubmit(): self
    {
        $this->hideSubmit = true;

        return $this;
    }

    public function isHideSubmit(): bool
    {
        return $this->hideSubmit;
    }

    public function submit(string $label, array $attributes = []): self
    {
        $this->submitLabel = $label;
        $this->submitAttributes->setAttributes(
            $attributes + $this->submitAttributes->getAttributes()
        );

        return $this;
    }

    public function submitAttributes(): ComponentAttributeBag
    {
        return $this->submitAttributes;
    }

    public function submitLabel(): string
    {
        return $this->submitLabel ?? __('moonshine::ui.save');
    }

    public function switchFormMode(bool $isAsync, string|array|null $asyncEvents = ''): self
    {
        return $isAsync ? $this->async(asyncEvents: $asyncEvents) : $this->precognitive();
    }

    public function getExcludedFields(): array
    {
        return $this->excludeFields;
    }

    public function excludeFields(array $excludeFields): self
    {
        $this->excludeFields = array_merge($this->excludeFields, $excludeFields);

        return $this;
    }

    public function onBeforeFieldsRender(Closure $closure): self
    {
        $this->onBeforeFieldsRender = $closure;

        return $this;
    }

    /**
     * @throws Throwable
     */
    public function apply(
        Closure $apply,
        ?Closure $default = null,
        ?Closure $before = null,
        ?Closure $after = null,
        bool $throw = false,
    ): bool {
        $values = $this->castData(
            $this->getValues()
        );

        if (is_null($default)) {
            $default = static fn (Field $field): Closure => static function (mixed $item) use ($field): mixed {
                if (! $field->hasRequestValue() && ! $field->defaultIfExists()) {
                    return $item;
                }

                $value = $field->requestValue() !== false ? $field->requestValue() : null;

                data_set($item, $field->column(), $value);

                return $item;
            };
        }

        try {
            $fields = $this
                ->preparedFields()
                ->onlyFields()
                ->exceptElements(
                    fn (Field $element): bool => in_array($element->column(), $this->getExcludedFields(), true)
                );

            $values = is_null($before) ? $values : $before($values);

            $fields->each(fn (Field $field): mixed => $field->beforeApply($values));

            $fields
                ->withoutOutside()
                ->each(fn (Field $field): mixed => $field->apply($default($field), $values));

            $apply($values, $fields);

            $fields->each(fn (Field $field): mixed => $field->afterApply($values));

            value($after, $values);
        } catch (Throwable $e) {
            report_if(! $throw, $e);
            throw_if($throw, $e);

            return false;
        }

        return true;
    }

    /**
     * @throws Throwable
     * @return array<string, mixed>
     * @throws JsonException
     */
    protected function viewData(): array
    {
        $fields = $this->preparedFields();

        if ($this->hasAdditionalFields()) {
            $this->getAdditionalFields()->each(fn ($field) => $fields->push($field));
        }

        $onlyFields = $fields->onlyFields();

        if (! is_null($this->getName())) {
            $onlyFields->each(
                fn (Field $field): Field => $field->formName($this->getName())
            );

            $fields->prepend(
                Hidden::make('_component_name')->setValue($this->getName())
            );
        }

        $reactiveFields = $onlyFields->reactiveFields()
            ->mapWithKeys(fn (Field $field): array => [$field->column() => $field->value()]);

        $whenFields = [];
        foreach ($onlyFields->whenFieldsConditions() as $whenConditions) {
            foreach ($whenConditions as $value) {
                $whenFields[] = $value;
            }
        }

        $initData = json_encode([
            'whenFields' => $whenFields,
            'reactiveUrl' => $reactiveFields->isNotEmpty()
                ? $this->getReactiveUrl()
                : '',
        ], JSON_THROW_ON_ERROR);

        $this->customAttributes([
            'x-data' => "formBuilder(`{$this->getName()}`, $initData, {$reactiveFields->toJson()})",
        ]);

        if ($this->isPrecognitive()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'precognition()',
            ]);
        }

        $this->customAttributes([
            AlpineJs::eventBlade(JsEvent::FORM_RESET, $this->getName()) => 'formReset',
            AlpineJs::eventBlade(JsEvent::FORM_SUBMIT, $this->getName()) => 'submit',
        ]);

        if ($this->isAsync()) {
            $this->customAttributes([
                'x-on:submit.prevent' => 'async(`' . $this->asyncEvents(
                ) . '`, `' . $this->asyncCallback() . '`, `' . $this->asyncBeforeCallback() . '`)',
            ]);
        }

        if (! is_null($this->onBeforeFieldsRender)) {
            $fields = value($this->onBeforeFieldsRender, $fields);
        }

        return [
            'fields' => $fields,
            'precognitive' => $this->isPrecognitive(),
            'async' => $this->isAsync(),
            'asyncUrl' => $this->asyncUrl(),
            'buttons' => $this->getButtons(),
            'hideSubmit' => $this->isHideSubmit(),
            'submitLabel' => $this->submitLabel(),
            'submitAttributes' => $this->submitAttributes(),
            'errorsAbove' => $this->hasErrorsAbove(),
        ];
    }
}
