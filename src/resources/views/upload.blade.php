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
    <div class="page-body">
        <div class="container-xl">
            <div class="row justify-content-center">
                <div id="messages-container" class="col-8 alert alert-success d-none"></div>
                <div id="errors-container" class="col-8 alert alert-danger d-none"></div>
                @include('partials.upload.draggable')
                @include('partials.upload.editable')
            </div>
        </div>
    </div>
    <script>
        document.addEventListener("DOMContentLoaded", function () {
            function fillEditableForm(filename) {
                const formattedName = formatFileName(filename);
                $('#upload-title').val(formattedName);
                $('#upload-description').val(formattedName);
            }

            function fillUploadProgress(filesize, progress, bytesSent) {
                $('#total-uploaded').text(Math.floor(bytesSent / 1000));
                $('#upload-total-size').text(Math.floor(filesize / 1000));
                $('#upload-progress').css('width', progress + "%")
            }

            function formatFileName(filename) {
                return filename.replaceAll('-', ' ');
            }

            function showMessage(message) {
                $('#messages-container').html(buildMessageDisplay(message));
                showElement('#messages-container');
            }

            function showErrors(errors) {
                const errorsContainer = document.getElementById('errors-container');
                errors.forEach((error) => {
                    errorsContainer.innerHTML += buildMessageDisplay(error);
                });

                showElement('#errors-container');
            }

            let dropzone = new Dropzone("#dropzone-custom", {
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                },
                maxFilesize: {{ $maxFilesizeInMB }},
                acceptedFiles: "{{ $supportedVideoFormats }}",
                timeout: 50000,
                addedfile: function (file) {
                    hideElement('#draggable-upload');
                    hideElement('#errors-container');
                    hideElement('#messages-container');
                    disableButton('#video-update');
                    showElement('#editable-upload');
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
                        $('#video-update').prop('videoId', response.data.id);
                        hideElement('#progress-section');
                        showMessage(response.message);
                        enableButton('#video-update');
                    }

                    if (response.errors) {
                        showErrors(response.errors);
                    }
                },
                error: function (file, response) {
                    console.log('error', response);
                    showErrors([response]);

                    showElement('#draggable-upload');
                    hideElement('#editable-upload');

                    return false;
                }
            });

            $('#video-update').on('click', function(e) {
                e.preventDefault();
                disableButton('#video-update');

                let url = '{{ route('update_video', ['video' => '__#__']) }}';
                url = url.replace('__#__', $(this).prop('videoId'));

                let data = {
                    'title': $('#upload-title').val(),
                    'description': $('#upload-description').val(),
                    'scope': $('#upload-scope').val(),
                    '_token': '{{ csrf_token() }}',
                }

                $.ajax({
                    type: 'PUT',
                    url: url,
                    contentType: 'application/json',
                    data: JSON.stringify(data), // access in body
                }).done(function (response) {
                    if (response.success && response.message) {
                        showMessage(response.message);
                    }

                    if (response.errors) {
                        showErrors(response.errors);
                    }
                }).fail(function (response) {
                    console.log('FAIL');
                }).always(function (response) {
                    enableButton('#video-update');
                });
            });
        });

    </script>
@endsection
