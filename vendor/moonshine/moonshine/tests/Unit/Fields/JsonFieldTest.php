<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\DefaultValueTypes\DefaultCanBeArray;
use MoonShine\Contracts\Fields\HasDefaultValue;
use MoonShine\Contracts\Fields\HasFields;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\Json;
use MoonShine\Fields\Text;

uses()->group('fields');
uses()->group('json-field');

beforeEach(function (): void {
    $this->field = Json::make('Json')
        ->fields(exampleFields()->toArray());

    $this->fieldKeyValue = Json::make('Key value')
        ->keyValue();

    $this->fieldOnlyValue = Json::make('Only value')
        ->onlyValue();

    $this->item = new class () extends Model {
        public array $key_value = [
            'key1' => 'value1',
            'key2' => 'value2',
        ];

        public array $only_value = [
            'value1',
            'value2',
        ];

        public array $json = [
            ['field1' => 'field1_value', 'field2' => 'field2_value'],
        ];
    };

    $this->field->fill($this->item->json);

    $this->fieldKeyValue->fill(
        $this->item->key_value
    );

    $this->fieldOnlyValue->fill(
        $this->item->only_value
    );
});

describe('basic methods', function () {
    it('change preview', function () {
        expect($this->field->changePreview(static fn () => 'changed'))
            ->preview()
            ->toBe('changed');
    });

    it('formatted value', function () {
        $field = Json::make('Json', formatted: static fn () => ['changed'])
            ->fields(exampleFields()->toArray())
            ->fill([]);

        expect($field->toFormattedValue())
            ->toBe(['changed']);
    });

    it('default value', function () {
        $field = Json::make('Json')
            ->fields(exampleFields()->toArray())
            ->default(['default']);

        expect($field->toValue())
            ->toBe(['default']);

        $field = Json::make('Json')
            ->fields(exampleFields()->toArray())
            ->default(['default'])
            ->fill(['value']);

        expect($field->toValue())
            ->toBe(['value']);
    });

    it('applies', function () {
        $field = Json::make('Json')
            ->fields(exampleFields()->toArray());

        expect()
            ->applies($field);
    });
});

describe('common field methods', function () {
    it('names', function (): void {
        expect($this->field)
            ->name()
            ->toBe('json[]')
            ->name('1')
            ->toBe('json[1]');
    });

    it('correct interfaces', function (): void {
        expect($this->field)
            ->toBeInstanceOf(HasFields::class)
            ->toBeInstanceOf(RemovableContract::class)
            ->toBeInstanceOf(HasDefaultValue::class)
            ->toBeInstanceOf(DefaultCanBeArray::class);
    });

    it('type', function (): void {
        expect($this->field->type())
            ->toBeEmpty();
    });

    it('view', function (): void {
        expect($this->field->getView())
            ->toBe('moonshine::fields.json');
    });

    it('is group', function (): void {
        expect($this->field->isGroup())
            ->toBeTrue();
    });
});

describe('unique field methods', function () {
    it('removable method', function (): void {
        expect($this->field)
            ->isRemovable()
            ->toBeFalse()
            ->and($this->field->removable())
            ->isRemovable()
            ->toBeTrue();
    });

    it('vertical method', function (): void {
        expect($this->field)
            ->isVertical()
            ->toBeFalse()
            ->and($this->field->vertical())
            ->isVertical()
            ->toBeTrue();
    });

    it('creatable method', function (): void {
        expect($this->field)
            ->isCreatable()
            ->toBeTrue()
            ->and($this->field->creatable(false))
            ->isCreatable()
            ->toBeFalse();
    });

    it('has fields', function (): void {
        expect($this->field->getFields())
            ->hasFields(exampleFields()->toArray())
            ->each(function ($field, $key): void {
                $key++;

                $field->toBeInstanceOf(Text::class)
                    ->name()
                    ->toBe('field' . $key)
                    ->id()
                    ->toBe('field' . $key);
            });
    });

    it('has fields key value', function (): void {
        expect($this->fieldKeyValue->getFields())
            ->hasFields([
                Text::make('Key2'),
                Text::make('Value2'),
            ])
            ->each(function ($field, $key): void {
                $name = $key === 0 ? 'key' : 'value';

                $field->toBeInstanceOf(Text::class)
                    ->name()
                    ->toBe($name)
                    ->id()
                    ->toBe($name);
            });
    });

    it('has fields only value', function (): void {
        expect($this->fieldOnlyValue->getFields())
            ->hasFields([
                Text::make('Value'),
            ])
            ->each(function ($field): void {
                $name = 'value';

                $field->toBeInstanceOf(Text::class)
                    ->name()
                    ->toBe($name)
                    ->id()
                    ->toBe($name);
            });
    });

    it('extract value', function () {
        expect($this->field->toValue())
            ->toBe([
                ['field1' => 'field1_value', 'field2' => 'field2_value'],
            ]);
    });

    it('extract value for key value', function () {
        expect($this->fieldKeyValue->toValue())
            ->toBe([
                ['key' => 'key1', 'value' => 'value1'],
                ['key' => 'key2', 'value' => 'value2'],
            ]);
    });

    it('extract value for only value', function () {
        expect($this->fieldOnlyValue->toValue())
            ->toBe([
                ['value' => 'value1'],
                ['value' => 'value2'],
            ]);
    });
});
