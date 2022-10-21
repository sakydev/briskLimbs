@extends('layouts.brisk', ['override_default_layout' => true])

@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <div class="text-center mb-4">
                <a href="." class="navbar-brand navbar-brand-autodark"><img src="{{ asset("_tabler/static/logo.svg") }}" height="36" alt=""></a>
            </div>
            @include('components.errors')
            <form class="card card-md" action="{{ route("register") }}" method="POST">
                @csrf
                <div class="card-body">
                    <h2 class="card-title text-center mb-4">Create new account</h2>
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        @include('components.fields.general', [
                            'name' => 'username',
                            'placeholder' => 'Jon Snow',
                            'required' => true,
                        ])
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email address</label>
                        @include('components.fields.general', [
                            'name' => 'email',
                            'type' => 'email',
                            'placeholder' => 'snow@thewall.com',
                            'required' => true,
                        ])
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password</label>
                        <div class="input-group input-group-flat">
                            @include('components.fields.general', [
                                'name' => 'password',
                                'type' => 'password',
                                'placeholder' => '****',
                                'required' => true,
                            ])
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-check">
                            <input type="checkbox" class="form-check-input"/>
                            <span class="form-check-label">Agree the <a href="./terms-of-service.html" tabindex="-1">terms and policy</a>.</span>
                        </label>
                    </div>
                    <div class="form-footer">
                        @include('components.button', [
                            'modifier_class' => 'btn btn-primary w-100',
                            'text' => 'Create new account',
                        ])
                    </div>
                </div>
            </form>
            <div class="text-center text-muted mt-3">
                Already have account? <a href="{{ route("login") }}" tabindex="-1">Login</a>
            </div>
        </div>
    </div>
@endsection
