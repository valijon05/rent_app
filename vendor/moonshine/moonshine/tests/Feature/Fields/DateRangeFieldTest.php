<?php

declare(strict_types=1);

use MoonShine\Applies\Filters\DateRangeModelApply;
use MoonShine\Fields\DateRange;
use MoonShine\Handlers\ExportHandler;
use MoonShine\Handlers\ImportHandler;
use MoonShine\Pages\Crud\DetailPage;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;

use function Pest\Laravel\get;

uses()->group('fields');
uses()->group('date-field');

beforeEach(function () {
    $this->item = createItem(countComments: 0);
    $this->field = DateRange::make('Range')->fromTo('start_date', 'end_date');
});

it('show field on pages', function () {
    $resource = addFieldsToTestResource($this->field->format('d.m.Y'));

    $from = now();
    $to = now()->addMonth();

    $this->item->start_date = $from->format('Y-m-d');
    $this->item->end_date = $to->format('Y-m-d');
    $this->item->save();

    asAdmin()->get(
        to_page(page: IndexPage::class, resource: $resource)
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee($from->format('d.m.Y') . ' - ' . $to->format('d.m.Y'))
    ;

    asAdmin()->get(
        to_page(page: DetailPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('Range')
        ->assertSee($from->format('d.m.Y') . ' - ' . $to->format('d.m.Y'))
    ;

    asAdmin()->get(
        to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $this->item->getKey()])
    )
        ->assertOk()
        ->assertSee('range')
        ->assertSee('start_date')
        ->assertSee('end_date')
    ;
});

it('apply as base', function () {
    $resource = addFieldsToTestResource($this->field);

    $from = now();
    $to = now()->addMonth();
    $data = ['start_date' => $from, 'end_date' => $to];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBe($from->format('Y-m-d'))
        ->and($this->item->end_date)
        ->toBe($to->format('Y-m-d'))
    ;
});

it('before apply', function () {
    $resource = addFieldsToTestResource(
        $this->field->onBeforeApply(function ($item, $data) {
            $item->name = $data['start_date'] . ' - ' . $data['end_date'];

            return $item;
        })
    );

    $from = now();
    $to = now()->addMonth();
    $data = ['start_date' => $from, 'end_date' => $to];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->name)
        ->toBe($from . ' - ' . $to)
    ;
});

it('after apply', function () {
    $resource = addFieldsToTestResource(
        $this->field->onAfterApply(function ($item) {
            $item->start_date = '2020-01-01';
            $item->end_date = '2020-01-02';

            return $item;
        })
    );

    $data = ['start_date' => now(), 'end_date' => now()];

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        ['range' => $data]
    )
        ->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBe('2020-01-01')
        ->and($this->item->end_date)
        ->toBe('2020-01-02')
    ;
});

it('apply as base with default', function () {
    $from = now();
    $to = now()->addMonth();

    $data = ['start_date' => $from, 'end_date' => $to];

    $resource = addFieldsToTestResource(
        $this->field->default($data)
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey())
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBe($from->format('Y-m-d'))
        ->and($this->item->end_date)
        ->toBe($to->format('Y-m-d'))
    ;
});

it('apply as base with null', function () {
    $data = ['start_date' => '', 'end_date' => ''];

    $resource = addFieldsToTestResource(
        $this->field->nullable()
    );

    asAdmin()->put(
        $resource->route('crud.update', $this->item->getKey()),
        $data
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBeNull()
        ->and($this->item->end_date)
        ->toBeNull()
    ;
});

it('apply as filter', function (): void {
    $field = $this->field
        ->wrapName('filters');

    $query = Item::query();

    get('/?filters[range][from]=2020-01-01&filters[range][to]=2020-01-02');

    $field
        ->onApply((new DateRangeModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        )
    ;

    expect($query->toRawSql())
        ->toContain('range', '2020-01-01', '2020-01-02')
    ;
});

it('apply as filter with time', function (): void {
    $field = $this->field
        ->withTime()
        ->wrapName('filters');

    $query = Item::query();

    get('/?filters[range][from]=2020-01-01T00:00&filters[range][to]=2020-01-02T23:59');

    $field
        ->onApply((new DateRangeModelApply())->apply($field))
        ->apply(
            static fn (Builder $query) => $query,
            $query
        )
    ;

    expect($query->toRawSql())
        ->toContain('range', '2020-01-01 00:00:00', '2020-01-02 23:59:00')
    ;
});

function dateRangeExport(Item $item): ?string
{
    $data = ['start_date' => '2020-01-01', 'end_date' => '2020-01-02'];

    $item->start_date = $data['start_date'];
    $item->end_date = $data['end_date'];

    $item->save();

    $resource = addFieldsToTestResource(
        DateRange::make('Range')->fromTo('start_date', 'end_date')->showOnExport()
    );

    $export = ExportHandler::make('');

    asAdmin()->get(
        $resource->route('handler', query: ['handlerUri' => $export->uriKey()])
    )->assertDownload();

    $file = Storage::disk('public')->get('test-resource.csv');

    expect($file)
        ->toContain('Range', $data['start_date'], $data['end_date']);

    return $file;
}

it('export', function (): void {
    dateRangeExport($this->item);
});

it('import', function (): void {

    $file = dateRangeExport($this->item);

    $resource = addFieldsToTestResource(
        $this->field->useOnImport()
    );

    $import = ImportHandler::make('');

    asAdmin()->post(
        $resource->route('handler', query: ['handlerUri' => $import->uriKey()]),
        [$import->getInputName() => $file]
    )->assertRedirect();

    $this->item->refresh();

    expect($this->item->start_date)
        ->toBe('2020-01-01')
        ->and($this->item->end_date)
        ->toBe('2020-01-02')
    ;
});
