@extends('layouts.brisk')

@section('content')
    <link href="{{ asset("_tabler/libs/dropzone/dist/dropzone.css") }}" rel="stylesheet"/>
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                @include('partials.upload.draggable')
            </div>
        </div>
    </div>
    <script src="{{ asset("_tabler/libs/dropzone/dist/dropzone-min.js") }}" defer></script>
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
