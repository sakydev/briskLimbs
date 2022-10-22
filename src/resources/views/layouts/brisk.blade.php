<!doctype html>
<html lang="en">
    @include('partials.head')
    @if(isset($injectStyleSheets))
        @foreach($injectStyleSheets as $styleSheet)
            <link href="{{ asset($styleSheet) }}" rel="stylesheet"/>
        @endforeach
    @endif
<body >
<script src="{{ asset("_tabler/js/demo-theme.min.js") }}"></script>
<div class="page">
    <!-- Navbar -->
    @if(!isset($override_default_layout))
        @include('partials.header-top')
        @include('partials.header-secondary')
    @endif
    <div class="page-wrapper">
        <!-- Page body -->
        @yield('content')

        @if(!isset($override_default_layout))
            @include('partials.footer')
        @endif
    </div>
</div>
<script src="{{ asset("_tabler/js/tabler.min.js") }}" defer></script>
<script src="{{ asset("_tabler/js/demo.min.js") }}" defer></script>

@if(isset($injectScripts))
    @foreach($injectScripts as $script)
        <script src="{{ asset($script) }}" defer></script>
    @endforeach
@endif
</body>
</html>
