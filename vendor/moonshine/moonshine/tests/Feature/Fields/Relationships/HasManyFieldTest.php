<?php

declare(strict_types=1);

uses()->group('model-relation-fields');
uses()->group('has-many-field');

use MoonShine\Fields\Field;
use MoonShine\Fields\ID;
use MoonShine\Fields\Relationships\HasMany;
use MoonShine\Fields\Text;
use MoonShine\Pages\Crud\FormPage;
use MoonShine\Pages\Crud\IndexPage;
use MoonShine\Tests\Fixtures\Models\Item;
use MoonShine\Tests\Fixtures\Resources\TestCommentResource;
use MoonShine\Tests\Fixtures\Resources\TestResourceBuilder;

it('onlyLink preview', function () {
    createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('<span class="badge">6</span>', false)
    ;
});

it('onlyLink preview empty', function () {
    createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page(page: IndexPage::class, resource: $resource))
        ->assertOk()
    ;
});

it('onlyLink value', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertSee('<span class="badge">16</span>',  false)
        ->assertOk()
    ;
});

it('onlyLink value empty', function () {
    $item = createItem(countComments: 0);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Комментарии', 'comments', resource: new TestCommentResource())
            ->onlyLink(),
    ]);

    asAdmin()
        ->get(to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
    ;
});

it('onlyLink preview condition', function () {
    $item = createItem(countComments: 6);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->onlyLink(condition: function (int $count): bool {
                return $count > 10;
            })
        ,
    ]);

    asAdmin()
        ->get(to_page(page: IndexPage::class, resource: $resource))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments->first()->content)
        ->assertDontSee('<span class="badge">6</span>', false)
    ;
});

it('onlyLink value condition', function () {
    $item = createItem(countComments: 16);

    $resource = TestResourceBuilder::new(Item::class)->setTestFields([
        ID::make(),
        Text::make('Имя', 'name'),
        HasMany::make('Comments title', 'comments', resource: new TestCommentResource())
            ->onlyLink(condition: function (int $count, Field $field): bool {
                return $field->toValue()->total() > 20;
            })
        ,
    ]);

    asAdmin()
        ->get(to_page(page: FormPage::class, resource: $resource, params: ['resourceItem' => $item->id]))
        ->assertOk()
        ->assertSee('Comments title')
        ->assertSee($item->comments[15]->content)
        ->assertDontSee('<span class="badge">16</span>', false)
    ;
});
