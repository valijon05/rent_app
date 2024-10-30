@props([
    'persist' => false,
    'open' => false,
    'button' => null,
    'icon' => null,
    'title',
])
<div
    {{ $attributes->class(['accordion']) }}
    x-data="
        @if($persist)
            collapse($persist({{ $open ? 'true' : 'false' }}).as($id('collapse')))
        @else
            collapse({{ $open ? 'true' : 'false' }})
        @endif
    "
>
    <div
        class="accordion-item"
        :class="open ? 'mt-5' : 'my-5'"
    >
        <h2 class="accordion-header">
            <button type="button" @click.prevent="toggle()" :class="{ '_is-active': open }"
                    class="accordion-btn btn"
                    type="button"
            >
                <div class="flex gap-2">
                    @if($icon)
                        <x-moonshine::icon :icon="$icon" />
                    @endif

                    {!! $title !!}
                </div>

                @if($button ?? false)
                    {!! $button !!}
                @else
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="h-6 w-6">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                    </svg>
                @endif
            </button>
        </h2>
        <div
            x-cloak
            :class="open ? 'block' : 'hidden'"
             class="accordion-body"
        >
            <div class="accordion-content">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

