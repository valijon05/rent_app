<x-moonshine::form
    :attributes="$attributes"
    :name="$name"
    :precognitive="$precognitive"
    :errorsAbove="$errorsAbove"
>
    <x-moonshine::fields-group
        :components="$fields"
    />

    <x-slot:buttons>
        @if(!($hideSubmit ?? false))
        <x-moonshine::form.button
                :attributes="$submitAttributes->merge([
                'class' => 'form_submit_button',
                'type' => 'submit'
            ])"
        >
            <x-moonshine::spinner
                    color="secondary"
                    class="form_submit_button_loader"
                    style="display: none;"
            />

            {{ $submitLabel }}
        </x-moonshine::form.button>
        @endif

        @if($buttons->isNotEmpty())
            <x-moonshine::action-group
                :actions="$buttons"
            />
        @endif
    </x-slot:buttons>
</x-moonshine::form>
