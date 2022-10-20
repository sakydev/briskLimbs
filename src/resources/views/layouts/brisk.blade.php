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
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>Blank page - Tabler - Premium and Open Source dashboard template with responsive and high quality UI.</title>
    <!-- CSS files -->
    <link href="{{ asset("_tabler/css/tabler.min.css") }}" rel="stylesheet"/>
    <link href="{{ asset("_tabler/css/tabler-flags.min.css") }}" rel="stylesheet"/>
    <link href="{{ asset("_tabler/css/tabler-payments.min.css") }}" rel="stylesheet"/>
    <link href="{{ asset("_tabler/css/tabler-vendors.min.css") }}" rel="stylesheet"/>
    <link href="{{ asset("_tabler/css/demo.min.css") }}" rel="stylesheet"/>
    <link href="{{ asset("_tabler/css/custom.css") }}" rel="stylesheet"/>
    <style>
        @import url('https://rsms.me/inter/inter.css');
        :root {
            --tblr-font-sans-serif: Inter, -apple-system, BlinkMacSystemFont, San Francisco, Segoe UI, Roboto, Helvetica Neue, sans-serif;
        }
    </style>
</head>
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
