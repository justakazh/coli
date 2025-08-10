// npm package: dropzone
// github link: https://github.com/dropzone/dropzone

'use strict';

(function () {

  // const myDropzone = new Dropzone("#exampleDropzone", { url: "/file/post"});

  const dropzone = new Dropzone('#exampleDropzone', {
    parallelUploads: 2,
    thumbnailHeight: 120,
    thumbnailWidth: 120,
    maxFilesize: 300000, // in mb
    addRemoveLinks: true,
    dictResponseError: 'Server not Configured',
    // acceptedFiles: ".png,.jpg,.gif,.bmp,.jpeg",
    filesizeBase: 1000,
      init:function(){
        const self = this;
        
        // config
        self.options.addRemoveLinks = true;
        self.options.dictRemoveFile = "Delete";
        
        //New file added
        self.on("addedfile", function (file) {
          console.log('new file added: ', file);
        });

        // Send file starts
        self.on("sending", function (file) {
          console.log('upload started', file);
        });
        
        // File upload Progress
        self.on("totaluploadprogress", function (progress) {
          console.log("progress ", progress);
        });

        self.on("queuecomplete", function (progress) {
          console.log('que completed');
        });
        
        // On removing file
        self.on("removedfile", function (file) {
          console.log('file removed: ', file);
        });
      }

  });


  // Now fake the file upload, since GitHub does not handle file uploads
  // and returns a 404

  const minSteps = 6,
      maxSteps = 60,
      timeBetweenSteps = 100,
      bytesPerStep = 100000;

  dropzone.uploadFiles = function(files) {
    const self = this;

    for (let i = 0; i < files.length; i++) {

      const file = files[i];
      const totalSteps = Math.round(Math.min(maxSteps, Math.max(minSteps, file.size / bytesPerStep)));

      for (let step = 0; step < totalSteps; step++) {
        const duration = timeBetweenSteps * (step + 1);
        setTimeout(function(file, totalSteps, step) {
          return function() {
            file.upload = {
              progress: 100 * (step + 1) / totalSteps,
              total: file.size,
              bytesSent: (step + 1) * file.size / totalSteps
            };

            self.emit('uploadprogress', file, file.upload.progress, file.upload.bytesSent);
            if (file.upload.progress == 100) {
              file.status = Dropzone.SUCCESS;
              self.emit("success", file, 'success', null);
              self.emit("complete", file);
              self.processQueue();
              document.querySelector(".dz-preview.dz-success .dz-success-mark").style.opacity = "1";
            }
          };
        }(file, totalSteps, step), duration);
      }
    }
  }

})();