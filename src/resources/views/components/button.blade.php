<button class="btn {{ $modifier_class ?? null }}" type="{{ $type ?? null }}">
    @isset($icon)
        @include($icon)
    @endisset
    {{ $text ?? null }}
</button>
