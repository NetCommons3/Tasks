/**
 * Tasks.Content Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, NetCommonsWysiwyg)} Controller
 */
NetCommonsApp.controller('TaskContent',
    function($scope, NetCommonsWysiwyg) {
      /**
       * tinymce
       *
       * @type {object}
       */
      $scope.tinymce = NetCommonsWysiwyg.new();

      /**
       * TaskContent object
       *
       * @type {object}
       */
      $scope.taskContent = [];

      /**
       * Initialize
       *
       * @param {object} TaskContents data
       * @return {void}
       */
      $scope.initialize = function(data) {
        $scope.taskContent = data.TaskContent;
      };

    });


/**
 * Tasks.Charge Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, NetCommonsWysiwyg)} Controller
 */
NetCommonsApp.controller('TaskCharge',
    function($scope) {

      /**
       * tinymce
       *
       * @type {object}
       */
      $scope.target = false;

      /**
       * Initialize
       *
       * @param {object} TaskContents data
       * @return {void}
       */
      $scope.initialize = function(value) {
        $scope.target = value;
      };

      $scope.switchCharge = function($event) {
        $scope.target = $event.target.value;
      };

    });
