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
  angular.module('contactsummary').controller('Contactsummarycontactsummary', function($scope, $timeout, crmApi4, crmStatus, crmUiHelp, data) {
    var ts = $scope.ts = CRM.ts('contactsummary');
    var hs = $scope.hs = crmUiHelp({file: 'CRM/contactsummary/contactsummary'});
    $scope.paletteGroups = {};
    $scope.selectedLayout = null;
    $scope.changesSaved = 1;
    $scope.saving = false;
    $scope.contactTypes = data.contactTypes;
    $scope.layouts = data.layouts;
    var newLayoutCount = 0;
    var allBlocks = loadBlocks(data.blocks);

    // Initialize
    if ($scope.layouts.length) {
      loadLayouts();
    } else {
      $scope.newLayout();
    }

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
      if (layout.contact_sub_type && layout.contact_sub_type.length) {
        return getLabels(layout.contact_sub_type, data.contactTypes).join(', ');
      }
      if (layout.contact_type) {
        return getLabels(layout.contact_type, data.contactTypes);
      }
      return ts('All contact types');
    };

    $scope.clearSubType = function(layout) {
      layout.contact_sub_type = null;
    };

    $scope.showGroups = function(layout) {
      if (layout.groups && layout.groups.length) {
        return getLabels(layout.groups, data.groups).join(', ');
      }
      return ts('All users');
    };

    $scope.selectableSubTypes = function(contactType) {
      typeId = _.where(data.contactTypes, {name: contactType})[0].id;
      return _.where(data.contactTypes, {parent_id: typeId});
    };

    $scope.removeBlock = function(index, blocks) {
      $scope.selectedLayout.palette.push(blocks[index]);
      blocks.splice(index, 1);
    };

    $scope.editBlock = function(block) {
      var edited;
      if (block.group === 'profile') {

      } else {
        CRM.loadForm(CRM.url(block.edit))
          .on('crmFormSuccess', function() {
            edited = true;
          })
          .on('dialogclose', function() {
            if (edited) {
              reloadBlocks();
            }
          });
      }
    };

    $scope.enforceUnique = function(e, ui) {
      if (!ui.item.sortable.received &&
        $(ui.item.sortable.droptarget).is('#cse-palette'))
      {
        ui.item.sortable.cancel();
      }
    };

    $scope.newLayout = function() {
      var newLayout = {
        label: ts('Untitled %1', {1: ++newLayoutCount}),
        blocks: [[],[]],
        palette: _.cloneDeep(allBlocks)
      };
      $scope.layouts.unshift(newLayout);
      $scope.selectedLayout = newLayout;
    };

    $scope.deleteLayout = function(index) {
      if ($scope.selectedLayout === $scope.layouts[index]) {
        $scope.selectedLayout = null;
      }
      $scope.layouts.splice(index, 1);
    };

    $scope.newProfile = function() {

    };

    $scope.save = function() {
      $scope.saving = true;
      var data = [],
        layoutWeight = 0;
      _.each($scope.layouts, function(layout) {
        var item = {
          label: layout.label,
          weight: ++layoutWeight,
          id: layout.id,
          contact_type: layout.contact_type,
          contact_sub_type: layout.contact_sub_type,
          groups: layout.groups,
          blocks: [[],[]]
        };
        _.each(layout.blocks, function(blocks, col) {
          _.each(blocks, function(block) {
            item.blocks[col].push(_.pick(block, 'name'));
          });
        });
        data.push(item);
      });
      CRM.api4('ContactSummary', 'replace', {records: data})
        .done(function() {
          $scope.$apply(function() {
            $scope.saving = false;
            $scope.changesSaved = true;
          });
        });
    };

    function loadBlocks(blockData) {
      allBlocks = [];
      _.each(blockData, function(group) {
        $scope.paletteGroups[group.name] = {title: group.title, icon: group.icon};
        _.each(group.blocks, function(block) {
          block.group = group.name;
          block.icon = group.icon;
          allBlocks.push(block);
        });
      });
      return allBlocks;
    }

    function loadLayouts() {
      _.each($scope.layouts, function(layout) {
        layout.palette = _.cloneDeep(allBlocks);
        _.each(layout.blocks, function(column) {
          _.each(column, function(block) {
            $.extend(block, _.where(layout.palette, {name: block.name})[0]);
            _.remove(layout.palette, {name: block.name});
          });
        });
      });
    }

    function reloadBlocks() {
      CRM.api4('ContactSummary', 'getBlocks')
        .done(function(data) {
          $scope.$apply(function() {
            allBlocks = loadBlocks(data);
            loadLayouts($scope.layouts);
          });
        });
    }

    $scope.$watch('layouts', function(a, b) {$scope.changesSaved = $scope.changesSaved === 1;}, true);

  });

})(angular, CRM.$, CRM._);
