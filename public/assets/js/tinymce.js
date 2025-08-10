// npm package: tinymce
// github link: https://github.com/tinymce/tinymce

'use strict';

(function () {

  const tinymceExample = document.querySelector('#tinymceExample');

  if (tinymceExample) {
    const options = {
      selector: '#tinymceExample',
      min_height: 350,
      default_text_color: 'red',
      plugins: [
        'advlist', 'autoresize', 'autolink', 'lists', 'link', 'image', 'charmap', 'preview', 'anchor', 'pagebreak',
        'searchreplace', 'wordcount', 'visualblocks', 'visualchars', 'code', 'fullscreen', 'paste'
      ],
      toolbar1: 'undo redo | insert | styleselect | bold italic | alignleft aligncenter alignright alignjustify | bullist numlist outdent indent | link image | print preview media | forecolor backcolor emoticons | codesample help',
      image_advtab: true,
      templates: [{
          title: 'Test template 1',
          content: 'Test 1'
        },
        {
          title: 'Test template 2',
          content: 'Test 2'
        }
      ],
      promotion: false,
      paste_data_images: true, // Enable image file upload from paste
      images_upload_url: '/api/upload-image', // Endpoint for image upload
      automatic_uploads: true,
      file_picker_types: 'image',
      images_upload_handler: function (blobInfo, success, failure) {
        const xhr = new XMLHttpRequest();
        xhr.withCredentials = false;
        xhr.open('POST', '/api/upload-image');

        xhr.onload = function() {
          if (xhr.status < 200 || xhr.status >= 300) {
            failure('HTTP Error: ' + xhr.status);
            return;
          }
          const json = JSON.parse(xhr.responseText);
          success(json.fileName); // Assuming the response contains the file name
        };

        const formData = new FormData();
        formData.append('upload', blobInfo.blob(), blobInfo.filename());

        xhr.send(formData);
      }
    };

    const theme = localStorage.getItem('theme');
    if (theme === 'dark') {
      options["content_css"] = "dark";
      options["content_style"] = `body{background: ${getComputedStyle(document.documentElement).getPropertyValue('--bs-body-bg')}}`
    } else if (theme === 'light') {
      options["content_css"] = "default";
    }

    tinymce.init(options);
  }

})();