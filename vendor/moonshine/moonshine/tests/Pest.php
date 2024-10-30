<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use MoonShine\Fields\Field;
use MoonShine\Fields\Fields;
use MoonShine\Fields\Text;
use MoonShine\Models\MoonshineUser;
use MoonShine\Tests\Fixtures\Factories\CommentFactory;
use MoonShine\Tests\Fixtures\Factories\ItemFactory;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestItemResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;
use MoonShine\Tests\TestCase;
use Pest\Expectation;

use function Pest\Laravel\actingAs;

use Symfony\Component\HttpFoundation\Request as SymfonyRequest;

uses(TestCase::class)
    ->in(__DIR__);

function fakeRequest(string $url = '/', string $method = 'GET', array $parameters = [], bool $dispatchRoute = false): void
{
    app()->instance(
        'request',
        request()->create($url, $method, $parameters)
    );

    if ($dispatchRoute) {
        Route::dispatchToRoute(app('request'));
    }
}

function asAdmin(): TestCase
{
    return actingAs(MoonshineUser::query()->find(1), 'moonshine');
}

function fillFromModel(Field $field, Model $model)
{
    $field->resolveFill($model->toArray(), $model);
}

function createItem(int $countItems = 1, int $countComments = 3)
{
    return ItemFactory::new()
        ->count($countItems)
        ->has(
            CommentFactory::new()->count($countComments)
        )
        ->create()
        ->first();
}

function addFieldsToTestResource(array|Field $fields): TestResource
{
    if (! is_array($fields)) {
        $fields = [$fields];
    }

    return TestResourceBuilder::new(Item::class)
        ->setTestFields([
            ...(new TestItemResource())->fields(),
            ...$fields,
        ]);
}

function exampleFields(): Fields
{
    return Fields::make([
        Text::make('Field 1'),
        Text::make('Field 2'),
    ]);
}

function createRequest($method, $uri): Request
{
    $symfonyRequest = SymfonyRequest::create(
        $uri,
        $method,
    );

    return Request::createFromBase($symfonyRequest);
}

expect()->extend('isForbidden', fn (): Expectation => expect($this->value->isForbidden())->toBeTrue());

expect()->extend('isSuccessful', fn (): Expectation => expect($this->value->status())->toBeIn([200]));

expect()->extend('isRedirect', fn (): Expectation => expect($this->value->status())->toBeIn([301, 302]));

expect()->extend('isSuccessfulOrRedirect', fn (): Expectation => expect($this->value->status())->toBeIn([200, 301, 302]));

expect()->extend('see', fn (string $value): Expectation => expect($this->value->content())->toContain($value));

expect()->extend('hasFields', fn (array $fields = null) => expect($this->value)
    ->toBeCollection()
    ->toHaveCount($fields ? count($fields) : 0));
