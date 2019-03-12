var initialUploadForm = $('#upload-section'),
editUploadForm = $('#edit-upload-details');

var uploader = new plupload.Uploader({
  browse_button: 'upload-file', // this can be an id of a DOM element or the DOM element itself
  url: $('#upload-section').attr('uploadUrl'),
  file_data_name : "uploadMedia",
  filters: {
    mime_types : [
    { title : "Video files", extensions : 'mkv,mp4' }
    ]
  },
});
 
uploader.init();
 
uploader.bind('FilesAdded', function(up, files) {
  uploader.start();
  initialUploadForm.hide();
  editUploadForm.fadeIn('slow');

  plupload.each(files, function(file) {
    $(window).scrollTop();
    var cleanedFileName = file.name.slice(0, -4);
    $('#title').val(cleanedFileName);
    $('#description').val(cleanedFileName);
  });
});
 
uploader.bind('UploadProgress', function(up, file) {
  var fileProgress = file.percent,
  fileProgressText = fileProgress + "%";
  $('.progress-bar').attr('aria-valuenow', fileProgress).css("width", fileProgressText);
  $('.progress-bar').text(fileProgressText);

});

uploader.bind('FileUploaded', function(uploader, FileUploaded, object) {
  if (object) {
    var objectResponse = $.parseJSON(object.response);
    if (objectResponse.status == 'success') {
      $.ajax({
        url : "upload/",
        type : "post",
        dataType : 'json',
        data : {
          insert : $('#title').val(),
          filename : objectResponse.filename

        }
      }).done(function(msg) {
        if (msg.id) {
          $('#finish-upload').attr('disabled', false).text('Update');
          $('#finish-upload').attr('videoid', msg.id);
          $('#upload-messages').addClass('alert-success').text('Video has been uploaded successfully').fadeIn();
        } else {
          $('#upload-messages').addClass('alert-danger').text('Something went wrong trying to update video');
        }
      }).fail(function(err){
        console.log(err);
      });
    } else {
      $('#upload-messages').addClass('alert-danger').text(objectResponse.message);
    }
  }
})
 
uploader.bind('Error', function(up, err) {
  console.log("\nError #" + err.code + ": " + err.message);
});

$('#finish-upload').on('click', function(e) {
  e.preventDefault();
  var title = $('#title').val(),
  description = $('#description').val(),
  videoId = $('#finish-upload').attr('videoid');

  paramters = {update: videoId, title: title, description: description};

  $.post('upload', paramters, function (response) {
    var response = $.parseJSON(response);
    if (response.status == 'success') {
      var msgClass = 'success';
    } else {
      var msgClass = 'danger';
    }

    $('#upload-messages').addClass(msgClass).text(response.message).fadeIn();
  });
});