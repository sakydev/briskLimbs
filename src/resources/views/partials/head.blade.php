<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover"/>
    <meta http-equiv="X-UA-Compatible" content="ie=edge"/>
    <title>{{ $title ?? config('settings.title') }}</title>
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
