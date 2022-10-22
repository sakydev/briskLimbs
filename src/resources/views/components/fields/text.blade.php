<input type="text" name="{{ $name }}" class="form-control {{ $modifier_class ?? null }} @error($name) is-invalid @enderror" placeholder="{{ $placeholder ?? env('text_placeholder') }}" required="{{ $required ?? false }}">
@isset($hint)
    <small class="form-hint">{{ $hint }}</small>
@endisset

@error($name)
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror