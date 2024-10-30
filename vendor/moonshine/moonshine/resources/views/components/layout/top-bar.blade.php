@props([
    'components' => [],
    'actions' => $_actions ?? [],
    'home_route' => null,
    'hideLogo' => false,
    'hideSwitcher' => false,
    'logo',
    'profile',
])
<!-- Menu horizontal -->
<aside {{ $attributes->merge(['class' => 'layout-menu-horizontal']) }}
       :class="asideMenuOpen && '_is-opened'"
       x-data="{minimizedMenu: false}"
>
    @if(!$hideLogo)
        <div class="menu-logo">
            @if($logo ?? false)
                {{ $logo }}
            @else
                @include('moonshine::layouts.shared.logo', ['home_route' => $home_route])
            @endif
        </div>
    @endif

    <nav class="menu-navigation">
        <x-moonshine::components
            :components="$components"
        />

        {{ $slot ?? '' }}
    </nav>

    <div class="menu-actions">
        <x-moonshine::components
            :components="$actions"
        />

        {{ $profile ?? '' }}

        @if(!$hideSwitcher && config('moonshine.use_theme_switcher', true))
            <div class="menu-inner-divider"></div>

            <div class="menu-mode">
                <x-moonshine::layout.theme-switcher :top="true" />
            </div>
        @endif

        <div class="menu-burger">
            @include('moonshine::layouts.shared.burger')
        </div>
    </div>
</aside>
<!-- END: Menu horizontal -->
