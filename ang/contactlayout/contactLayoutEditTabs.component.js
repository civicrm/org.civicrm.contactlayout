(function(angular, $, _) {

  angular.module('contactlayout').component('contactLayoutEditTabs', {
    bindings:  {
      defaults: '=',
      layout: '<',
      contactType: '<',
    },
    templateUrl: '~/contactlayout/contactLayoutEditTabs.html',
    controller: function($scope) {
      var ts = $scope.ts = CRM.ts('contactlayout'),
        ctrl = this;

      // Settings for ui-sortable
      this.sortableOptions = {
        containment: '#cse-tabs-container',
        cancel: 'input,textarea,button,select,option,a,.crm-editable-enabled,[contenteditable]'
      };

      // Toggle between using defaults & custom tabs
      this.toggleTabs = function() {
        if (ctrl.layout.tabs) {
          ctrl.layout.tabs = null;
        } else {
          ctrl.layout.tabs = angular.copy(ctrl.defaults);
        }
      };
    }
  });

})(angular, CRM.$, CRM._);
