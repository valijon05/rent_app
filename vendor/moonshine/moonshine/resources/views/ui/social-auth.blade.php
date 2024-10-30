@if(config('moonshine.socialite'))
    <div class="social">
        <div class="social-divider">{{ $title ?? '' }}</div>
        <div class="social-list">
            @foreach(config('moonshine.socialite') as $driver => $src)
                <a href="{{ route('moonshine.socialite.redirect', $driver) }}" class="social-item">
                    <img class="h-6 w-6"
                         src="{{ asset($src) }}"
                         alt="{{ $driver }}"
                    >
                </a>
            @endforeach
        </div>
    </div>

    @if($attached ?? false)
        @if(auth()->user()?->moonshineSocialites?->isNotEmpty())
        <div class="social">
            <div class="social-divider">@lang('moonshine::ui.resource.linked_socialite')</div>
            <div class="social-list">
                @foreach(auth()->user()->moonshineSocialites as $socials)
                    {{ $socials->driver }} - {{ $socials->identity }}
                @endforeach
            </div>
        </div>
        @endif
    @endif
@endif
