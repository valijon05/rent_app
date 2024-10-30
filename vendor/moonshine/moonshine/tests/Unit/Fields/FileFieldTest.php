<?php

declare(strict_types=1);

use Illuminate\Database\Eloquent\Model;
use MoonShine\Contracts\Fields\Fileable;
use MoonShine\Contracts\Fields\RemovableContract;
use MoonShine\Fields\File;
use MoonShine\Fields\Json;

uses()->group('fields');
uses()->group('file-field');

beforeEach(function (): void {
    $this->field = File::make('File')
        ->disk('public')
        ->dir('files');

    $this->fieldMultiple = File::make('Files')
        ->multiple()
        ->disk('public')
        ->dir('files');

    $this->item = new class () extends Model {
        public string $file = 'files/file.pdf';
        public string $files = '["files/file1.pdf", "files/file2.pdf"]';

        protected $casts = ['files' => 'collection'];
    };

    $this->field->resolveFill(
        ['file' => 'files/file.pdf'],
        $this->item
    );

    $this->fieldMultiple->resolveFill(
        ['files' => ["files/file1.pdf", "files/file2.pdf"]],
        $this->item
    );
});

it('storage methods', function (): void {
    expect($this->field)
        ->getDir()
        ->toBe('files')
        ->getDisk()
        ->toBe('public');
});

it('storage methods with slashes', function (): void {
    expect($this->field->dir('/files/'))
        ->getDir()
        ->toBe('files');
});

it('can be multiple methods', function (): void {
    expect($this->field)
        ->isMultiple()
        ->toBeFalse()
        ->and($this->fieldMultiple)
        ->isMultiple()
        ->toBeTrue();
});

it('removable methods', function (): void {
    expect($this->field)
        ->isRemovable()
        ->toBeFalse()
        ->and($this->field->removable())
        ->isRemovable()
        ->toBeTrue();
});

it('type', function (): void {
    expect($this->field->type())
        ->toBe('file');
});

it('view', function (): void {
    expect($this->field->getView())
        ->toBe('moonshine::fields.file');
});

it('correct interfaces', function (): void {
    expect($this->field)
        ->toBeInstanceOf(Fileable::class)
        ->toBeInstanceOf(RemovableContract::class);
});

it('accept attribute', function (): void {
    $this->field->accept('png');

    expect($this->field->getAttribute('accept'))
        ->toBe('png');
});

it('allowed extensions', function (): void {
    $this->field->allowedExtensions(['gif']);

    expect($this->field->getAllowedExtensions())
        ->toBe(['gif'])
        ->and($this->field->isAllowedExtension('gif'))
        ->toBeTrue()
        ->and($this->field->isAllowedExtension('png'))
        ->toBeFalse();
});

it('can download', function (): void {
    expect($this->field->canDownload())
        ->toBeTrue()
        ->and($this->field->disableDownload()->canDownload())
        ->toBeFalse();
});

it('correct path', function (): void {
    expect($this->field->path(''))
        ->toBe(Storage::disk($this->field->getDisk())->url(''))
        ->and($this->field->path('file.png'))
        ->toBe(Storage::disk($this->field->getDisk())->url('file.png'));
});

it('correct path with dir', function (): void {
    expect($this->field->pathWithDir(''))
        ->toBe(Storage::disk($this->field->getDisk())->url($this->field->getDir() . '/'))
        ->and($this->field->dir('')->pathWithDir('/'))
        ->toBe(Storage::disk($this->field->getDisk())->url('/'));
});

it('preview', function (): void {
    expect((string) $this->field)
        ->toBe(view('moonshine::fields.file', $this->field->toArray())->render());
});

it('preview for multiple', function (): void {
    expect((string) $this->fieldMultiple)
        ->toBe(view('moonshine::fields.file', $this->fieldMultiple->toArray())->render());
});

it('names single', function (): void {
    expect($this->field)
        ->name()
        ->toBe('file')
        ->name('1')
        ->toBe('file');
});

it('names multiple', function (): void {
    expect($this->fieldMultiple)
        ->name()
        ->toBe('files[]')
        ->name('1')
        ->toBe('files[1]');
});

describe('Hidden input for files', function () {
    it('hidden single', function (): void {
        $file = File::make('Thumbnail', 'thumbnail');

        expect($file)
            ->hiddenOldValuesKey()
            ->toBe('hidden_thumbnail')
            ->hiddenOldValuesName()
            ->toBe('hidden_thumbnail');
    });

    it('hidden keys multiple', function (): void {
        $file = File::make('Thumbnail')->multiple();

        expect($file)
            ->hiddenOldValuesKey()
            ->toBe('hidden_thumbnail')
            ->hiddenOldValuesName()
            ->toBe('hidden_thumbnail[]');
    });

    it('hidden keys virtual', function (): void {
        $file = File::make('Thumbnail')->virtualColumn('test');

        expect($file)
            ->hiddenOldValuesKey()
            ->toBe('hidden_test')
            ->hiddenOldValuesName()
            ->toBe('hidden_test');

        $file = File::make('Thumbnail')->multiple()->virtualColumn('test');

        expect($file)
            ->hiddenOldValuesKey()
            ->toBe('hidden_test')
            ->hiddenOldValuesName()
            ->toBe('hidden_test[]');
    });

    it('hidden keys iterable', function (): void {
        $file = File::make('Thumbnail');

        $json = Json::make('Data', 'data')->fields([
            $file,
        ]);

        expect($json->preparedFields()->first())
            ->hiddenOldValuesKey()
            ->toBe('data.${index0}.hidden_thumbnail')
            ->hiddenOldValuesName()
            ->toBe('data[${index0}][hidden_thumbnail]');
    });

    it('hidden keys iterable multiple', function (): void {
        $file = File::make('Thumbnail')->multiple();

        $json = Json::make('Data', 'data')->fields([
            $file,
        ]);

        expect($json->preparedFields()->first())
            ->hiddenOldValuesKey()
            ->toBe('data.${index0}.hidden_thumbnail')
            ->hiddenOldValuesName()
            ->toBe('data[${index0}][hidden_thumbnail][]');
    });

    it('hidden keys iterable virtual', function (): void {
        $file = File::make('Thumbnail')->virtualColumn('test');

        $json = Json::make('Data', 'data')->fields([
            $file,
        ]);

        expect($json->preparedFields()->first())
            ->hiddenOldValuesKey()
            ->toBe('data.${index0}.hidden_test')
            ->hiddenOldValuesName()
            ->toBe('data[${index0}][hidden_test]');

        $file = File::make('Thumbnail')->multiple()->virtualColumn('test');

        $json = Json::make('Data', 'data')->fields([
            $file,
        ]);

        expect($json->preparedFields()->first())
            ->hiddenOldValuesKey()
            ->toBe('data.${index0}.hidden_test')
            ->hiddenOldValuesName()
            ->toBe('data[${index0}][hidden_test][]');
    });
})->group('hidden-file-input');
