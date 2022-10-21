@extends('layouts.brisk')

@section('content')
    <div class="page page-center">
        <div class="container container-tight py-4">
            <form class="card card-md" method="POST" action="{{ route('password.email') }}">
            @csrf
            <div class="card-body">
                <h2 class="card-title text-center mb-4">Reset password</h2>
                <div class="mb-3">
                    <label class="form-label">Email address</label>
                    @include('components.fields.general', [
                        'name' => 'email',
                        'type' => 'email',
                        'placeholder' => 'snow@thewall.com',
                        'required' => true,
                    ])
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
