// npm package: sortablejs
// github link: https://github.com/SortableJS/Sortable

'use strict';

(function () {

  // Simple list example
  const simpleList = document.querySelector("#simple-list");
  if (simpleList) {
    new Sortable(simpleList, {
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }



  // Handle example
  const handleExample = document.querySelector("#handle-example");
  if (handleExample) {
    new Sortable(handleExample, {
      handle: '.handle', // handle's class
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }



  // Shared lists example
  const sharedListLeft = document.querySelector("#shared-list-left");
  if (sharedListLeft) {
    new Sortable(sharedListLeft, {
      group: 'shared', // set both lists to same group
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }

  const sharedListRight = document.querySelector("#shared-list-right");
  if (sharedListRight) {
    new Sortable(sharedListRight, {
      group: 'shared', // set both lists to same group
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }



  // Cloning example
  const sharedList2Left = document.querySelector("#shared-list-2-left");
  if (sharedList2Left) {
    new Sortable(sharedList2Left, {
      group: {
        name: 'shared2',
        pull: 'clone' // To clone: set pull to 'clone'
      },
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }

  const sharedList2Right = document.querySelector("#shared-list-2-right");
  if (sharedList2Right) {
    new Sortable(sharedList2Right, {
      group: {
        name: 'shared2',
        pull: 'clone' // To clone: set pull to 'clone'
      },
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }



  // Disabling sorting example
  const sharedList3Left = document.querySelector("#shared-list-3-left");
  if (sharedList3Left) {
    new Sortable(sharedList3Left, {
      group: {
        name: 'shared3',
        pull: 'clone',
        put: false // Do not allow items to be put into this list
      },
      animation: 150,
      ghostClass: 'bg-secondary',
      sort: false // To disable sorting: set sort to false
    });
  }

  const sharedList3Right = document.querySelector("#shared-list-3-right");
  if (sharedList3Right) {
    new Sortable(sharedList3Right, {
      group: {
        name: 'shared3',
      },
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }


  
  // Filter example
  const filterExample = document.querySelector("#filter-example");
  if (filterExample) {
    new Sortable(filterExample, {
      filter: '.filtered', // 'filtered' class is not draggable
      animation: 150,
      ghostClass: 'bg-secondary'
    });
  }



  // Grid example
  const gridExample = document.querySelector("#grid-example");
  if (gridExample) {
    new Sortable(gridExample, {
      animation: 150,
      ghostClass: 'border-warning'
    });
  }



  // Nested example
  const nestedSortables = [].slice.call(document.querySelectorAll('.nested-sortable'));
  if (nestedSortables) {

    // Loop through each nested sortable element
    for (let i = 0; i < nestedSortables.length; i++) {
      new Sortable(nestedSortables[i], {
        group: 'nested',
        animation: 150,
        fallbackOnBody: true,
        swapThreshold: 0.65
      });
    }
  }


})();