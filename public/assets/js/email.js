'use strict';

(function () {

  // select2
  if (document.querySelector(".compose-multiple-select")) {
    $(".compose-multiple-select").select2();
  }

  // easymde editor
  const easyMdeEditorEl = document.querySelector('#easyMdeEditor');
  if (easyMdeEditorEl) {
    var easymde = new EasyMDE({
      element: easyMdeEditorEl
    });
  }

  // Check/uncheck all email-list-item checkboxes on main checkbox click/change 
  const inboxCheckAll = document.querySelector('#inboxCheckAll');
  const inboxListItemChecks = document.querySelectorAll('.email-list-item .form-check-input');
  if (inboxCheckAll) {
    inboxCheckAll.addEventListener('change', function (event) {
      inboxListItemChecks.forEach(function (checkItem) {
        if (inboxCheckAll.checked === true) { 
          checkItem.checked = true;
        } else if (inboxCheckAll.checked === false) {
          checkItem.checked = false;
        }
      });
    });
  }

})();