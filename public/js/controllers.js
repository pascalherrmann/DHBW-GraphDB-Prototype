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


    $scope.status = "new"
    var start = $route.current.params.START;

    var finish = $route.current.params.FINISH;

    $scope.start = start
    $scope.finish = finish

    $scope.getPageTitlesForTypeAhead = function (val) {

        var escaped = val.replace(/\//g, '%2F');

        return wikiServices.getTypeAhead(escaped).then(function (response) { //hier das Return nicht vergessen! Sosnt gibt die Methode nix zurück!

            if (response.data.status != "success") {
                $scope.status = "error"
                return []
            } else {
                return response.data.titles
            }

        });
    };


    $scope.getRandom = function (isStart) {

        // Problem: Asynchron. Man kann schwer etwas von der Methode zurückgeben, wenn Zeit unklar.
        // Eher: man kann etwas mit dem Ergebnis machen, sobald es verfügbar ist.

        return wikiServices.getRandom().then(function (response) { //hier das Return nicht vergessen! Sosnt gibt die Methode nix zurück!

            if (response.data.status != "success") {
                $scope.status = "error"
            } else {

                if (isStart) {
                    $scope.start = response.data.randomTitle
                } else {
                    $scope.finish = response.data.randomTitle
                }
            }

        });
    };

    String.prototype.replaceAll = function (search, replacement) {
        var target = this;
        return target.split(search).join(replacement);
    };

    $scope.search = function (a, b) {


        var escapedA = a.replace(/\//g, '%2F');
        var escapedB = b.replace(/\//g, '%2F');

        $scope.status = "loading"

        $http.get("wiki/" + escapedA + "/" + escapedB).then(function (response) { //dann muss auch im Controller then davor

            $scope.loading = false
            $scope.status = response.data.status


            if (response.data.status == "success") {
                $scope.new = false

                var allSteps = response.data.steps
                allSteps.push($scope.finish)

                $scope.steps = allSteps
            } else if (response.data.status == undefined) {
                $scope.status = "error"
            }
        });
    };

    if ($scope.start != undefined && $scope.finish != undefined) {
        $scope.search(start, finish)
    } else {
        $scope.new = true
    }

}]);

/*


So ist Spitze! Man macht nicht isError, isNothingFound und isSuccess, sondern ein Status!!!! Für jede mögliche Antowrt!!!
*/
