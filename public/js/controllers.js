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

    $scope.setMessage = function () {

        $scope.$apply(function () {
            $scope.message = 'woof woof!';
        });
    };

}]);

controllers.controller('WikiController', ["$scope", "$http", "$route", "wikiServices", function ($scope, $http, $route, wikiServices) {


    var start = $route.current.params.START;

    var finish = $route.current.params.FINISH;

    $scope.start = start
    $scope.finish = finish

    $scope.getPageTitlesForTypeAhead = function (val) {
        return wikiServices.getTypeAhead(val).then(function (response) { //hier das Return nicht vergessen! Sosnt gibt die Methode nix zurück!
            $scope.checkout = response.data

            if (response.data.status == "error") {
                $scope.error = true
            }

            return response.data
        });
    };

    $scope.search = function (a, b) {

        $scope.loading = true

        $http.get("wiki/" + a + "/" + b).then(function (response) { //dann muss auch im Controller then davor
            $scope.loading = false
            $scope.names = response.data;
            if (response.data.status == "error") {
                $scope.error = true
            } else {
                $scope.new = false
            }
        });
    };

    if ($scope.start != undefined && $scope.finish != undefined) {
        $scope.search(start, finish)
    } else {
        $scope.new = true
    }

}]);
