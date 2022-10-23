@extends('layouts.brisk', [
    'injectStyleSheets' => [
        '_tabler/libs/dropzone/dist/dropzone.css'
    ],
    'injectScripts' => [
        '_tabler/libs/dropzone/dist/dropzone-min.js',
        '_tabler/js/custom.js'
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

        function showMessage(message) {
            const messageContainer = document.getElementById('messages-container');
            messageContainer.innerHTML = buildMessageDisplay(message);
            showElementById('messages-container');
        }

        function showErrors(errors) {
            const errorsContainer = document.getElementById('errors-container');
            errors.forEach((error) => {
                errorsContainer.innerHTML += buildMessageDisplay(error);
            });

            showElementById('errors-container');
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
                    hideElementById('draggable-upload');
                    disableButtonById('video-update');
                    showElementById('editable-upload');
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
                    if (response.success && response.message) {
                        document.getElementById('video-update').setAttribute('videoId', response.data.id);
                        showMessage(response.message);
                        enableButtonById('video-update');
                    }

                    if (response.errors) {
                        showErrors(response.errors);
                    }
                },
                error: function (file, response) {
                    return false;
                }
            });
        })
    </script>
@endsection
