// npm package: ace-builds (Ajax.org Cloud9 Editor)
// github link: https://github.com/ajaxorg/ace-builds

'use strict';

(function () {

  // Example 1
  if (document.querySelector('#ace_html')) {
    const editor = ace.edit("ace_html");
    editor.setTheme("ace/theme/dracula");
    editor.getSession().setMode("ace/mode/html");
    editor.setOption("showPrintMargin", false)
  }

  // Example 2
  if (document.querySelector('#ace_scss')) {
    const editor = ace.edit("ace_scss");
    editor.setTheme("ace/theme/dracula");
    editor.getSession().setMode("ace/mode/scss");
    editor.setOption("showPrintMargin", false)
  }

  // Example 3
  if (document.querySelector('#ace_javaScript')) {
    const editor = ace.edit("ace_javaScript");
    editor.setTheme("ace/theme/dracula");
    editor.getSession().setMode("ace/mode/javascript");
    editor.setOption("showPrintMargin", false)
  }

})();