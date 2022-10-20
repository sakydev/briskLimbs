<button class="btn {{ $modifier_class ?? null }}">
    @isset($icon)
        @include($icon)
    @endisset
    {{ $text ?? null }}
</button>
