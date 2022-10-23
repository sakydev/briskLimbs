@extends('layouts.brisk', [
    'injectStyleSheets' => [
        '_tabler/libs/dropzone/dist/dropzone.css'
    ],
    'injectScripts' => [
        '_tabler/libs/dropzone/dist/dropzone-min.js'
    ],
])

@section('content')
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                @include('partials.upload.draggable')
                @include('partials.upload.editable')
            </div>
        </div>
    </div>
    <script>
        function fillEditableForm(filename) {
            const formattedName = formatFileName(filename);
            document.getElementById('upload-title').value = formattedName;
            document.getElementById('upload-description').innerText = formattedName;
        }

        function fillUploadProgress(filesize, progress, bytesSent) {
            let uploadedSpan = document.getElementById('total-uploaded');
            let totalSpan = document.getElementById('upload-total-size');
            let progressElement = document.getElementById("upload-progress");

            uploadedSpan.textContent = Math.floor(bytesSent / 1000);
            totalSpan.textContent = Math.floor(filesize / 1000);
            progressElement.style.width = progress + "%";
        }

        function formatFileName(filename) {
            return filename.replaceAll('-', ' ');
        }

        function hideForm(formId) {
            document.getElementById(formId).classList.add('d-none');
        }

        function showForm(formId) {
            document.getElementById(formId).classList.remove('d-none');
        }

        function disableButton(buttonId) {
            document.getElementById(buttonId).setAttribute('disabled', '');
        }

        function enableButton(buttonId) {
            document.getElementById(buttonId).removeAttribute('disabled');
        }

        document.addEventListener("DOMContentLoaded", function () {
            let dropzone = new Dropzone("#dropzone-custom", {
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute("content"),
                },
                maxFilesize: {{ config('settings.max_filesize_video') }},
                acceptedFiles: "{{ config('settings.supported_formats_video') }}",
                timeout: 50000,
                addedfile: function (file) {
                    hideForm('draggable-upload');
                    disableButton('video-update');
                    showForm('editable-upload');
                    fillEditableForm(file.name);
                },
                sending: function(file, xhr, formData) {
                    formattedFileName = formatFileName(file.name);
                    formData.append('title', formattedFileName);
                    formData.append('description', formattedFileName);
                },
                uploadprogress: function (file, progress, bytesSent) {
                    fillUploadProgress(file.size, progress, bytesSent);
                },
                success: function (file, response) {
                    console.log('success', response);
                },
                error: function (file, response) {
                    return false;
                }
            });
        })
    </script>
@endsection
