<input type="{{ $type ?? 'text' }}" name="{{ $name }}" class="form-control {{ $modifier_class ?? null }} @error($name) is-invalid @enderror" placeholder="{{ $placeholder ?? null }}" required="{{ $required ?? false }}">
@if(!isset($skip_validation))
    @error($name)
        <span class="invalid-feedback" role="alert">
            <strong>{{ $message }}</strong>
        </span>
    @enderror
@endif
