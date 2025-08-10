// npm package: flatpickr
// github link: https://github.com/flatpickr/flatpickr

'use strict';

(function () {

  // date picker 
  const flatpickrDateEl = document.querySelector('#flatpickr-date');
  if(flatpickrDateEl) {
    flatpickr("#flatpickr-date", {
      wrap: true,
      dateFormat: "Y-m-d",
    });
  }


  // time picker
  const flatpickrTimeEl = document.querySelector('#flatpickr-time');
  if(flatpickrTimeEl) {
    flatpickr("#flatpickr-time", {
      wrap: true,
      enableTime: true,
      noCalendar: true,
      dateFormat: "H:i",
    });
  }

})();