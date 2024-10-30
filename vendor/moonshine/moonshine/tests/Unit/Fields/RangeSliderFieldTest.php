<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Fields\Range;
use MoonShine\Fields\RangeSlider;

uses()->group('fields');

beforeEach(function (): void {
    $this->field = RangeSlider::make('Slide')
    ;
    $this->item = new class () extends Model {
        public int $from = 10;
        public int $to = 20;
    };

    $this->field->resolveFill(['slide' => ['from' => 10, 'to' => 20]]);
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('number');
});

it('number is parent', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Range::class);
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.slide');
});

it('preview with stars', function (): void {
    $from = view('moonshine::ui.rating', [
        'value' => $this->item->from,
    ])->render();

    $to = view('moonshine::ui.rating', [
        'value' => $this->item->to,
    ])->render();

    expect($this->field->stars()->preview())
        ->toBe("$from - $to");
});

it('preview', function (): void {
    expect($this->field->preview())
        ->toBe("{$this->item->from} - {$this->item->to}");
});

it('apply', function (): void {
    fakeRequest(parameters: [
        'slide' => [
            'from' => 100,
            'to' => 200,
        ],
    ]);

    expect(
        $this->field->apply(
            fn () => null,
            new class () extends Model {
                protected $fillable = [
                    'from',
                    'to',
                ];
            }
        )
    )
        ->from
        ->toBe(100)
        ->to
        ->toBe(200)
    ;
});
