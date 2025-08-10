// npm package: datatables.net-bs5
// github link: https://github.com/DataTables/Dist-DataTables-Bootstrap5

'use strict';

(function () {

  $('#dataTableExample').DataTable({
    layout: {
      topEnd: {
        search: {
          placeholder: 'Search here'
        }
      }
    },
    "aLengthMenu": [
      [5, 10, 30, 50, -1],
      [5, 10, 30, 50, "All"]
    ],
    "iDisplayLength": 10,
    "language": {
      search: ""
    },
    paginationType: 'simple_numbers'
  });

})();