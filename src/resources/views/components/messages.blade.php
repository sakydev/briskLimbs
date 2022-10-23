@if (!empty($messages))
    <div class="alert alert-{{ $modifier_class ?? 'success' }}">
        @foreach ($messages as $message)
            <div>{{ $message }}</div>
        @endforeach
    </div>
@endif
