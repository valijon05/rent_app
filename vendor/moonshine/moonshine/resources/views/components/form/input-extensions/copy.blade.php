@props(['extension'])
<button @click.prevent="copy('{{ $extension->getValue() }}')"
        {{ $attributes->class(['expansion']) }}
        type="button"
        x-data="tooltip('{{ __('moonshine::ui.copied') }}', {placement: 'top', trigger: 'click', delay: [0, 800]})"
>
    <x-moonshine::icon
        icon="heroicons.outline.clipboard"
        size="4"
    />
</button>
