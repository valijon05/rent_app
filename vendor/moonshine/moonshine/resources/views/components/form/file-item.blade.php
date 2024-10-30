@props([
    'file' => null,
    'raw' => null,
    'filename' => null,
    'download' => false,
    'removable' => true,
    'removableAttributes' => null,
    'hiddenAttributes' => null,
    'imageable' => true,
    'itemAttributes',
])
<div
    {{ $itemAttributes?->class(['x-removeable dropzone-item zoom-in', 'dropzone-item-file' => !$imageable]) }}
    @if(is_null($itemAttributes))
    class="x-removeable dropzone-item zoom-in @if(!$imageable) dropzone-item-file @endif"
    @endif
>
    <x-moonshine::form.input
        type="hidden"
        :attributes="$hiddenAttributes"
        :value="$raw"
    />

    @if(!$imageable)
        @include('moonshine::ui.file', [
            'value' => $file,
            'filename' => $filename ?? $file,
            'download' => $download
        ])
    @endif

    @if($removable)
        <button
            {{ $removableAttributes?->merge([
                '@click.prevent' => '$event.target.closest(".x-removeable").remove()',
                'type' => 'button',
                'class' => 'dropzone-remove',
            ]) }}
        >
            <x-moonshine::icon
                icon="heroicons.x-mark"
            />
        </button>
    @endif

    @if($imageable)
        <img src="{{ $file }}"
             @click.stop="$dispatch('img-popup', {open: true, src: '{{ $file }}' })"
             alt="{{ $filename ?? '' }}"
        />
    @endif
</div>
