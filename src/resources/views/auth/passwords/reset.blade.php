@extends('layouts.brisk')

@section('content')
<div class="page page-center">
    <div class="container container-tight py-4">
        <form class="card card-md" method="POST" action="{{ route('password.update') }}">
            @csrf
            <input type="hidden" name="token" value="{{ $token }}">
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Reset password</h2>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    <input type="email" name="email" class="form-control @if ($errors->has('email')) is-invalid @endif" placeholder="snow@thewall.com" value="{{ $email ?? old('email')  }}" required>
                    @error('email')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input type="password" name="password" class="form-control @if ($errors->has('password')) is-invalid @endif" placeholder="****" required>
                    @error('password')
                    <div class="invalid-feedback">
                        <strong>{{ $message }}</strong>
                    </div>
                    @enderror
                </div>
                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <input id="password" name="password_confirmation" type="password" class="form-control @error('password_confirmation') is-invalid @enderror" placeholder="****" required autocomplete="new-password">
                    @error('password_confirmation')
                        <span class="invalid-feedback" role="alert">
                            <strong>{{ $message }}</strong>
                        </span>
                    @enderror
                </div>
                <div class="form-footer">
                    @include('components.button', [
                        'modifier_class' => 'btn btn-primary w-100',
                        'text' => __('Reset Password'),
                    ])
                </div>
            </div>
        </form>
    </div>
</div>
@endsection
