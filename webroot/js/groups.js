/**
 * Groups JavaScript
 */
NetCommonsApp.factory('SelectGroup',
    ['NetCommonsModal', function(NetCommonsModal) {
      return function($scope, userId, selectors) {
        return NetCommonsModal.show(
            $scope, 'Group.select',
            $scope.baseUrl + '/groups/groups/select/' + userId + '/',
            {
              backdrop: 'static',
              resolve: {
                options: {
                  userId: userId,
                  //roomId: roomId,
                  selectors: selectors
                }
              }
            }
        );
      }
    }]
);


/**
 * Groups JavaScript
 */
NetCommonsApp.directive('groupsSelectedUsers', function() {
  return {
    restrict: 'EA',
    template: '<div id="groups-selected-user-{{user.id}}"' +
        ' class="col-xs-12 user-selection-list">' +
        '<img class="user-avatar-xs" ng-src="{{user.avatar}}" />' +
        '<span>{{user.handlename}}</span>' +
        '<a id="groups-user-del-link{{user.id}}" href="#" ' +
        'onclick="return false;" ' +
        'ng-click="deleteUser(user.id);">' + '×' + '</a>' +
        '<input type="hidden" name="data[GroupsUser][][user_id]" ' +
        'value="{{user.id}}" />' +
        '</div>',
    transclude: false,
    scope: false,
    replace: true
  };
});


/**
 * Groups Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, SelectUser)} Controller
 */
NetCommonsApp.controller('GroupsSelect', function($scope) {

  /**
   * 会員選択の結果を保持する配列
   *
   * @return {array}
   */
  $scope.users = [];

  /**
   * 会員の選択状態を検知する
   *
   * @return {array}
   */
  $scope.$watch('users', function() {
    return $scope.users;
  }, true);

  /**
   * initialize
   *
   * @return {void}
   */
  $scope.initialize = function(data) {
    angular.forEach(data.users, function(value) {
      $scope.users.push(value);
    });
  };

  $scope.addUsers = function(users) {
    $.each(users, function(index, user) {
      $scope.users.push(user);
    });
  };
  $scope.deleteUser = function(targetUserId) {
    for (var i = 0; i < $scope.users.length; i++) {
      var user = $scope.users[i];
      if (user.id == targetUserId) {
        $scope.users.splice(i, 1);
        break;
      }
    }
  };
});


/**
 * Sample Javascript
 *
 * @param {string} Controller name
 * @param {function($scope, SelectUser)} Controller
 */
NetCommonsApp.controller('GroupsSelectUser',
    function($scope, $controller, SelectUser) {
      $controller('GroupsSelect', {$scope: $scope});

      /**
       * 会員選択ダイアログを表示する
       *
       * @param {number} users.id
       * @return {void}
       */
      $scope.showUserSelectionDialog = function(userId, roomId) {
        SelectUser($scope, userId, roomId).result.then(
            function(result) {
              // 選択したユーザを追加
              $scope.$parent.addUsers(result);
            },
            function() {
            }
        );
      };
    });

NetCommonsApp.controller('GroupsSelectGroup', function($scope, SelectGroup) {

  //$scope.showGroupSelectionDialog = function(userId, roomId) {
  $scope.showGroupSelectionDialog = function(userId) {
    SelectGroup($scope, userId).result.then(
        function(result) {
        },
        function() {
        }
    );
  };
});

NetCommonsApp.controller('Group.select',
    function($scope, $controller, $http, $q,
             $modalInstance, filterFilter, options) {
      $controller('GroupsSelect', {$scope: $scope});

      /**
       * ユーザIDを保持する変数
       */
      $scope.userId = options['userId'];

      /**
       * 検索結果を保持する配列
       */
      $scope.groupList = [];

      /**
       * 選択したユーザを保持する配列
       */
      $scope.selectors = options['selectors'];

      /**
       * Post data
       */
      $scope.data = null;

      /**
       * 初期処理
       *
       * @return {void}
       */
      //$scope.initialize = function(domId, groupList, data, field) {
      $scope.initialize = function(groupList, data) {
        $scope.data = data;
        if (angular.isArray(groupList) && groupList.length > 0) {
          $scope.groupList = groupList;
        }
      };

      /**
       * 選択処理
       *
       * @return {void}
       */
      $scope.select = function(index) {
        var result = filterFilter($scope.groupList,
            $scope.groupList[index]);

        if (!angular.isArray($scope.selectors)) {
          $scope.selectors = [];
        }
        if (!$scope.selected(result[0])) {
          $scope.selectors.push(result[0]);
        }
      };

      /**
       * 選択しているかどうかチェックする
       *
       * @return {bool}
       */
      $scope.selected = function(obj) {
        if (!angular.isArray($scope.selectors)) {
          return false;
        }
        var result = filterFilter($scope.selectors, obj);
        return !(result.length === 0);
      };

      /**
       * 選択の解除処理
       *
       * @return {void}
       */
      $scope.remove = function(index) {
        $scope.selectors.splice(index, 1);
      };

      /**
       * キャンセル処理＆ダイアログ閉じる
       *
       * @return {void}
       */
      $scope.cancel = function() {
        $modalInstance.dismiss('cancel');
      };

      /**
       * 決定処理＆ダイアログ閉じる
       *
       * @return {void}
       */
      $scope.save = function() {
        angular.forEach($scope.selectors, function(selector) {
          this.data.GroupSelect.group_id.push(selector.id);
        }, $scope);

        saveGroupSelect()
            .success(function(data) {
              // 選択したユーザを追加
              $scope.$parent.addUsers(data['users']);
              $modalInstance.close($scope.selectors);
            })
            .error(function(data, status) {
              $modalInstance.dismiss('error');
            });
      };

      /**
       * ユーザ選択したグループ情報更新処理関数
       *
       * @return {Function}
       */
      var saveGroupSelect = function() {
        var deferred = $q.defer();
        var promise = deferred.promise;

        $http.get('/net_commons/net_commons/csrfToken.json')
            .success(function(token) {
              // TODO トークン系見直し
              //$scope.data._Token.key = token.data._Token.key;

              //POSTリクエスト
              $http.post(
                  '/groups/groups/select/' + $scope.userId,
                  $.param({_method: 'POST', data: $scope.data}),
                  {
                    cache: false,
                    headers: {
                      'Content-Type': 'application/x-www-form-urlencoded'
                    }
                  }
              )
                  .success(function(data) {
                    //success condition
                    deferred.resolve(data);
                  })
                  .error(function(data, status) {
                    //error condition
                    deferred.reject(data, status);
                  });
            })
            .error(function(data, status) {
              //Token error condition
              deferred.reject(data, status);
            });

        promise.success = function(fn) {
          promise.then(fn);
          return promise;
        };

        promise.error = function(fn) {
          promise.then(null, fn);
          return promise;
        };

        return promise;
      };
    });
