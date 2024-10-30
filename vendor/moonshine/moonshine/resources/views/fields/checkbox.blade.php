<div x-data>
    <x-moonshine::form.input
        type="hidden"
        name="{{ $element->name() }}"
        :attributes="$element->attributes()->except(['class', 'id', 'type', 'checked', 'value'])"
        value="{{ $element->getOffValue() }}"
    />

    <x-moonshine::form.input
        :attributes="$element->attributes()->merge([
                'id' => $element->id(),
                'name' => $element->name(),
                'value' => $element->getOnValue(),
                'checked' => $element->isChecked()
            ])"
        @class(['form-invalid' => formErrors($errors ?? false, $element->getFormName())->has($element->name())])
    />
</div>
