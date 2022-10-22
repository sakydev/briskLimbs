<select name="{{ $name }}" class="form-select {{ $modifier_class ?? null }} @error($name) is-invalid @enderror">
    @foreach($options as $value => $option)
        <option value="{{ $value }}" @if(!empty($selected) && $value === $selected) selected="selected" @endif>
            {{ $option }}
        </option>
    @endforeach
</select>

@isset($hint)
    <small class="form-hint">{{ $hint }}</small>
@endisset

@error($name)
<span class="invalid-feedback" role="alert">
        <strong>{{ $message }}</strong>
    </span>
@enderror
