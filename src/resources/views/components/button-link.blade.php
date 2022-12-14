<a class="btn {{ $modifier_class ?? null }}" href="{{ $link ?? '#' }}" type="{{ $type ?? null }}">
    @isset($icon)
        @include($icon)
    @endisset
    {{ $text ?? null }}
</a>
