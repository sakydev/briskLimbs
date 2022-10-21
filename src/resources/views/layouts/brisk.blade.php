<!doctype html>
<!--
* Tabler - Premium and Open Source dashboard template with responsive and high quality UI.
* @version 1.0.0-beta13
* @link https://tabler.io
* Copyright 2018-2022 The Tabler Authors
* Copyright 2018-2022 codecalm.net Paweł Kuna
* Licensed under MIT (https://github.com/tabler/tabler/blob/master/LICENSE)
-->
<html lang="en">
    @include('partials.head')
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
<!-- Libs JS -->
<!-- Tabler Core -->
<script src="{{ asset("_tabler/js/tabler.min.js") }}" defer></script>
<script src="{{ asset("_tabler/js/demo.min.js") }}" defer></script>
</body>
</html>
