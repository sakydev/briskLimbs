@extends('layouts.brisk', ['override_default_layout' => true])

@section('content')
    <div class="page page-center">
        <div class="container container-normal py-4">
            <div class="row align-items-center g-4">
                <div class="col-lg">
                    <div class="container-tight">
                        <div class="text-center mb-4">
                            <a href="." class="navbar-brand navbar-brand-autodark"><img
                                    src="{{ asset('_tabler/static/logo.svg') }}" height="36" alt=""></a>
                        </div>
                        @include('components.errors')
                        <div class="card card-md">
                            <div class="card-body">
                                <h2 class="h2 text-center mb-4">Login to your account</h2>
                                <form method="POST" action="{{ route('login') }}" autocomplete="off">
                                    @csrf
                                    <div class="mb-3">
                                        <label class="form-label">Username</label>
                                        <input type="text" name="username" class="form-control" placeholder="Jon"
                                               required>
                                        @error('username')
                                        <span class="invalid-feedback" role="alert">
                                                <strong>{{ $message }}</strong>
                                            </span>
                                        @enderror
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-label">
                                            Password
                                            <span class="form-label-description">
                                                <a href="./forgot-password.html">I forgot password</a>
                                            </span>
                                        </label>
                                        <div class="input-group input-group-flat">
                                            <input type="password" name="password" class="form-control"
                                                   placeholder="Your password" autocomplete="off" required>
                                            <span class="input-group-text">
                                                <a href="#" class="link-secondary" title="Show password" data-bs-toggle="tooltip">
                                                    @include('svg.icons.eye')
                                                </a>
                                            </span>
                                            @error('password')
                                            <span class="invalid-feedback" role="alert">
                                                    <strong>{{ $message }}</strong>
                                                </span>
                                            @enderror
                                        </div>
                                    </div>
                                    <div class="mb-2">
                                        <label class="form-check">
                                            <input type="checkbox" class="form-check-input"/>
                                            <span class="form-check-label">Remember me on this device</span>
                                        </label>
                                    </div>
                                    <div class="form-footer">
                                        @include('components.button', [
                                            'modifier_class' => 'btn-primary w-100',
                                            'type' => 'submit',
                                            'text' => 'Login'
                                        ])
                                    </div>
                                </form>
                            </div>
                            <div class="hr-text">or</div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col">
                                        @include('components.button', [
                                            'modifier_class' => 'btn-outline-default',
                                            'icon' => 'svg.icons.github',
                                            'text' => 'Login with Github',
                                        ])
                                    </div>
                                    <div class="col">
                                        @include('components.button', [
                                            'modifier_class' => 'btn-outline-default',
                                            'icon' => 'svg.icons.twitter',
                                            'text' => 'Login with Twitter',
                                        ])
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="text-center text-muted mt-3">
                            Don't have account yet? <a href="{{ route('register') }}" tabindex="-1">Register</a>
                        </div>
                    </div>
                </div>
                <div class="col-lg d-none d-lg-block">
                    <img src="{{ asset('_tabler/static/illustrations/undraw_secure_login_pdn4.svg') }}" height="300"
                         class="d-block mx-auto" alt="">
                </div>
            </div>
        </div>
    </div>
@endsection
