var initialUploadForm = $('#upload-section'),
editUploadForm = $('#edit-upload-details');

var uploader = new plupload.Uploader({
  browse_button: 'upload-file', // this can be an id of a DOM element or the DOM element itself
  url: $('#upload-section').attr('uploadUrl'),
  file_data_name : "uploadMedia",
  filters: {
    mime_types : [
    { title : "Video files", extensions : '3gp,avi,flv,m4v,mkv,mov,mp4,mpg,mpeg,mts,webm,wmv,vob' }
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
          description : $('#description').val(),
          filename : objectResponse.filename

        }
      }).done(function(msg) {
        if (msg.id) {
          $('#finish-upload').attr('disabled', false).text('Update');
          $('#finish-upload').attr('videoid', msg.id);
          $('#upload-messages').addClass('alert-success').text('Video has been uploaded successfully. You can edit it in videos manager').fadeIn();
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
  $('#plupload-errors').addClass('alert-danger').text(err.message);
  console.log("\nError #" + err.code + ": " + err.message);
});

$('#finish-upload').on('click', function(e) {
  e.preventDefault();
  var title = $('#title').val(),
  description = $('#description').val(),
  videoId = $('#finish-upload').attr('videoid');

  paramters = {update: videoId, title: title, description: description};

  $('#finish-upload').attr('disabled', true).text('Updating..');
  $.post('upload', paramters, function (response) {
    var response = $.parseJSON(response);
    if (response.status == 'success') {
      var msgClass = 'success';
    } else {
      var msgClass = 'danger';
    }

    $('#finish-upload').attr('disabled', false).text('Update');
    $('#upload-messages').addClass(msgClass).text(response.message).fadeIn();
  });
});