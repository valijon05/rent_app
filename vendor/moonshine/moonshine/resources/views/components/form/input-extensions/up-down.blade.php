<div class="expansion-custom flex">
    <button
        class="px-2"
        type="button"
        @click.prevent="$refs.extensionInput.stepDown()"
        :disabled="$refs.extensionInput.disabled || $refs.extensionInput.readOnly"
    >
        <span>
            <x-moonshine::icon
                icon="heroicons.minus-small"
                size="4"
            />
        </span>
    </button>

    <button
        class="px-2"
        type="button"
        @click.prevent="$refs.extensionInput.stepUp()"
        :disabled="$refs.extensionInput.disabled || $refs.extensionInput.readOnly"
    >
        <span>
            <x-moonshine::icon
                icon="heroicons.plus-small"
                size="4"
            />
        </span>
    </button>
</div>
