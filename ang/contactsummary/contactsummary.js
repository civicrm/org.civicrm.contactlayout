(function(angular, $, _) {

  angular.module('contactsummary').config(function($routeProvider) {
      $routeProvider.when('/contact-summary-editor', {
        controller: 'Contactsummarycontactsummary',
        templateUrl: '~/contactsummary/contactsummary.html',

        // If you need to look up data when opening the page, list it out
        // under 'resolve'.
        resolve: {
          data: function(crmApi4) {
            return crmApi4({
              layouts: ['ContactSummary', 'get', {orderBy: {weight: 'ASC'}}],
              blocks:  ['ContactSummary', 'getBlocks'],
              contactTypes: ['ContactType', 'get'],
              groups: ['Group', 'get', {
                select: ['name','title','description'],
                where: [['is_hidden','=','0'],['is_active','=','1'],['saved_search_id','IS NULL','']]
              }]
            });
          }
        }
      });
    }
  );

  // The controller uses *injection*. This default injects a few things:
  //   $scope -- This is the set of variables shared between JS and HTML.
  //   crmApi, crmStatus, crmUiHelp -- These are services provided by civicrm-core.
  //   data -- defined above in config().
  angular.module('contactsummary').controller('Contactsummarycontactsummary', function($scope, crmApi, crmStatus, crmUiHelp, data) {
    // The ts() and hs() functions help load strings for this module.
    var ts = $scope.ts = CRM.ts('contactsummary');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/contactsummary/contactsummary'});

    loadLayouts(data.layouts);
    $scope.selectedLayout = null;
    $scope.changesSaved = 1;

    $scope.selectLayout = function(layout) {
      $scope.selectedLayout = layout;
    };

    function getLabels(name, data) {
      if (_.isArray(name)) {
        var ret = [];
        _.each(name, function(n) {
          ret.push(getLabels(n, data));
        });
        return ret;
      }
      var values = _.where(data, {name: name})[0];
      return values.label || values.title;
    }

    $scope.showContactTypes = function(layout) {
      if (layout.contact_sub_type) {
        return getLabels(layout.contact_sub_type, data.contactTypes).join(', ');
      }
      if (layout.contact_type) {
        return getLabels(layout.contact_type, data.contactTypes);
      }
      return ts('All contact types');
    };

    $scope.showGroups = function(layout) {
      if (layout.groups) {
        return getLabels(layout.groups, data.groups).join(', ');
      }
      return ts('All users');
    };

    function loadLayouts(layouts) {
      $scope.layouts = _.cloneDeep(layouts);
      _.each($scope.layouts, function(layout) {
        _.each(layout.blocks, function(column) {
          _.each(column, function(block) {
            $.extend(block, findBlock(block.name));
          });
        });
      });
    }

    function findBlock(blockName) {
      var dot = blockName.indexOf('.'),
        groupName = blockName.substr(0, dot);
      var group = _.where(data.blocks, {name: groupName})[0];
      return _.where(group.blocks, {name: blockName})[0];
    }

    $scope.$watch('layouts', function(a, b) {$scope.changesSaved = $scope.changesSaved === 1;}, true);

  });

})(angular, CRM.$, CRM._);
