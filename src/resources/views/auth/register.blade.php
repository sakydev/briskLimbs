@extends('layouts.brisk', ['override_default_layout' => true])

@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ asset("_tabler/static/logo.svg") }}" height="36" alt=""></a>
            </div>
            <form class="card card-md" method="POST" action="{{ route('password.email') }}"">
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Reset password</h2>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        <input type="email" name="email" class="form-control @if ($errors->has('email')) is-invalid @endif" placeholder="snow@thewall.com" value="{{ old('email') }}" required>
                        @error('email')
                        <div class="invalid-feedback">
                            <strong>{{ $message }}</strong>
                        </div>
                        @enderror
                    </div>
                    <div class="form-footer">
                        @include('components.button', [
                            'modifier_class' => 'btn btn-primary w-100',
                            'text' => __('Send Password Reset Link'),
                        ])
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
