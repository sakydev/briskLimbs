<textarea name="{{ $name }}" class="form-control {{ $modifier_class ?? null }} @error($name) is-invalid @enderror" placeholder="{{ $placeholder ?? env('text_placeholder') }}" rows="{{ $rows ?? null }}">
    {{ $value ?? null }}
</textarea>
@isset($hint)
    <small class="form-hint">{{ $hint }}</small>
@endisset

@error($name)
<span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror