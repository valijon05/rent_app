<?php

declare(strict_types=1);

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use MoonShine\Applies\Filters\BelongsToManyModelApply;
use MoonShine\Fields\File;
use MoonShine\Fields\Relationships\BelongsToMany;
use MoonShine\Fields\Text;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Tests\Fixtures\Models\Category;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCategoryResource;
use MoonShine\Tests\Fixtures\Resources\TestResource;

use function Pest\Laravel\get;

uses()->group('model-relation-fields');
uses()->group('belongs-to-many-field');

beforeEach(function () {
    $this->item = createItem(countComments: 0);

    $this->pivotFields = [
        Text::make('Pivot 1', 'pivot_1'),
        Text::make('Pivot 2', 'pivot_2'),
        File::make('Pivot 3', 'pivot_3'),
    ];

    $this->field = BelongsToMany::make('Categories', resource: new TestCategoryResource())
        ->fields($this->pivotFields)
        ->resolveFill($this->item->toArray(), $this->item);

    expect($this->item->data)
        ->toBeEmpty();
});

function testBelongsToManyValue(TestResource $resource, Item $item, array $data, array $pivotData = [])
{
    asAdmin()->put(
        $resource->route('crud.update', $item->getKey()),
        [
            'categories' => $data,
            'categories_pivot' => $pivotData,
        ]
    )->assertRedirect();

    $item->refresh();

    expect($item->categories->pluck('id', 'id')->sort()->toArray())
        ->toBe(collect($data)->sort()->toArray());
}

it('apply as base', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data);
});

it('apply as base with pivot', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2']];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2');
    });

    // unsync
    $data = $categories->random(1)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);
});

it('apply as base with pivot and file', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) use ($file) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBe($file->hashName())
        ;
    });
});

it('apply as base with pivot and file after remove', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2']];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBeNull()
        ;
    });
});

it('apply as base with pivot and file stay by hidden', function () {
    $resource = addFieldsToTestResource(
        $this->field
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $file = UploadedFile::fake()->create('test.csv');

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'pivot_3' => $file]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    $pivotData = [];

    foreach ($data as $id) {
        $pivotData[$id] = ['pivot' => ['pivot_1' => 'test 1', 'pivot_2' => 'test 2', 'hidden_pivot_3' => $file->hashName()]];
    }

    testBelongsToManyValue($resource, $this->item, $data, pivotData: $pivotData);

    $this->item->categories->each(function ($category) use ($file) {
        expect($category->pivot->pivot_1)
            ->toBe('test 1')
            ->and($category->pivot->pivot_2)
            ->toBe('test 2')
            ->and($category->pivot->pivot_3)
            ->toBe($file->hashName())
        ;
    });
});

it('apply as tree', function () {
    $resource = addFieldsToTestResource(
        $this->field->tree('category_id')
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data);
});

it('apply as select', function () {
    $resource = addFieldsToTestResource(
        $this->field->selectMode()
    );

    $categories = Category::factory(5)->create();

    $data = $categories->random(3)->pluck('id', 'id')->toArray();

    testBelongsToManyValue($resource, $this->item, $data);
});

it('apply as filter', function (): void {
    $field = $this->field
        ->wrapName('filters');

    $query = Item::query();

    get('/?filters[categories][3]=3');

    $field
        ->onApply((new BelongsToManyModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        );

    expect($query->toRawSql())
        ->toContain('`category_item`.`category_id` in (3)');
});

function belongsToManyExport(Item $item, BelongsToMany $field): ?string
{
    $category = Category::factory()->create();

    $item->categories()->attach($category);
    $item->refresh();

    $resource = addFieldsToTestResource(
        $field->showOnExport()
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Categories');

    return $file;
}

it('export', function (): void {
    belongsToManyExport($this->item, $this->field);
});

it('import', function (): void {

    $file = belongsToManyExport($this->item, $this->field);

    $resource = addFieldsToTestResource(
        $this->field->useOnImport()
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->categories->count())
        ->toBe(1)
    ;
});
