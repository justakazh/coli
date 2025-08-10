// npm package: jquery-steps
// github link: https://github.com/rstaib/jquery-steps/

'use strict';

(function () {

  const wizard = document.querySelector('#wizard');
  if (wizard) {
    $("#wizard").steps({
      headerTag: "h2",
      bodyTag: "section",
      transitionEffect: "slideLeft"
    });
  }

  const wizardVertical = document.querySelector('#wizardVertical');
  if (wizardVertical) {
    $("#wizardVertical").steps({
      headerTag: "h2",
      bodyTag: "section",
      transitionEffect: "slideLeft",
      stepsOrientation: 'vertical'
    });
  }

})();