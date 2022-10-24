<input type="text" name="{{ $name }}" id="{{ $modifier_id ?? null }}" class="form-control {{ $modifier_class ?? null }} @error($name) is-invalid @enderror" placeholder="{{ $placeholder ?? config('settings.placeholder_text') }}" required="{{ $required ?? false }}">
@isset($hint)
    <small class="form-hint">{{ $hint }}</small>
@endisset

@error($name)
    <span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
