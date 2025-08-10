// npm package: easymde
// github link: https://github.com/Ionaru/easy-markdown-editor

'use strict';

(function () {

  const easyMdeExample = document.querySelector('#easyMdeExample');
  if (easyMdeExample) {
    const easymde = new EasyMDE({
      element: easyMdeExample
    });
  }

})();