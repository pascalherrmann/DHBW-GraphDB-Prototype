var controllers = angular.module('graphDBPrototypeAppControllers', []);

controllers.controller('NavigationController', ["$scope", "$location", function ($scope, $location) {

    $scope.isActive = function (viewLocation) {
        return viewLocation === $location.path();
    };

}]);

controllers.controller('StartController', ["$scope", "$http", function ($scope, $http) {

}]);

controllers.controller('InfoController', ["$scope", "$http", function ($scope, $http) {
  $scope.message = '';

    $scope.setMessage = function(){

      $scope.$apply(function(){
        $scope.message = 'woof woof!';
      });
    };

}]);

controllers.controller('WikiController', ["$scope", "$http", "$route", "wikiServices", function ($scope, $http, $route, wikiServices) {


    var start = $route.current.params.START;

    var finish = $route.current.params.FINISH;

    $scope.start = start
    $scope.finish = finish

    $scope.getAutocomplete = function (subString) {
        return $http.get("wiki/" + subString).then(function (response) { //hier das Return nicht vergessen! Sosnt gibt die Methode nix zurück!
                $scope.checkout = response.data
                return response.data
            });
    }

      $scope.getUsernamesForTypeAhead = function (val) {
        return wikiServices.getTypeAhead(val);
    };

    $scope.search = function (a, b) {

        $http.get("wiki/" + a + "/" + b).then(function (status) { //dann muss auch im Controller then davor
            $scope.names = status.data;
        });
    };

    if ($scope.start != undefined && $scope.finish != undefined) {
        $scope.search(start, finish)
    } else {
        $scope.new = true
    }

}]);
