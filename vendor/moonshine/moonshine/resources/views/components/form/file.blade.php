@props([
    'files' => [],
    'raw' => [],
    'download' => false,
    'removable' => true,
    'removableAttributes' => null,
    'imageable' => true,
    'names' => null,
    'itemAttributes' => null,
    'hiddenAttributes' => null,
])
<div class="form-group form-group-dropzone">
    <x-moonshine::form.input
        type="file"
        {{ $attributes->merge(['class' => 'form-file-upload'])->except(['id'])}}
    />

    @if(is_array($files) ? array_filter($files) : $files->isNotEmpty())
        <div class="dropzone">
            <div class="dropzone-items"
                 x-data="sortable"
                 data-handle=".dropzone-item"
            >
                @foreach($files as $index => $file)
                    <x-moonshine::form.file-item
                        :attributes="$attributes"
                        :itemAttributes="value($itemAttributes, $file, $index)"
                        :filename="is_null($names) ? $file : value($names, $file, $index)"
                        :raw="$raw[$index]"
                        :file="$file"
                        :download="$download"
                        :removable="$removable"
                        :removableAttributes="$removableAttributes"
                        :hiddenAttributes="$hiddenAttributes"
                        :imageable="$imageable"
                    />
                @endforeach
            </div>
        </div>
    @endif
</div>
