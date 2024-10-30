<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Field;
use MoonShine\Fields\FormElement;
use MoonShine\Fields\Textarea;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = Textarea::make('Field name');
});

it('field and form element is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Field::class)
        ->toBeInstanceOf(FormElement::class);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBeEmpty();
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.textarea');
});

it('apply', function (): void {
    $data = ['field_name' => 'test'];

    fakeRequest(parameters: $data);

    expect(
        $this->field->apply(
            TestResourceBuilder::new()->onSave($this->field),
            new class () extends Model {
                protected $fillable = [
                    'field_name',
                ];
            }
        )
    )
        ->toBeInstanceOf(Model::class)
        ->field_name
        ->toBe($data['field_name'])
    ;
});
