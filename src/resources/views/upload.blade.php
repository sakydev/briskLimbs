@extends('layouts.brisk', [
    'injectStyleSheets' => [
        '_tabler/libs/dropzone/dist/dropzone.css'
    ],
    'injectScripts' => [
        '_tabler/libs/dropzone/dist/dropzone-min.js'
    ],
])

@section('content')
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                @include('partials.upload.draggable')
            </div>
        </div>
    </div>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            new Dropzone("#dropzone-default")
        })
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            new Dropzone("#dropzone-multiple")
        })
    </script>
    <script>
        // @formatter:off
        document.addEventListener("DOMContentLoaded", function() {
            new Dropzone("#dropzone-custom")
        })
    </script>
@endsection
