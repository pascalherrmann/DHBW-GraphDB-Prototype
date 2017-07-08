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


        $scope.status = "NEW"

        var start = $route.current.params.START;
        var finish = $route.current.params.FINISH;
        $scope.start = start
        $scope.finish = finish

        $scope.getPageTitlesForTypeAhead = function (val) {

            var escaped = val.replace(/\//g, '%2F');

            return wikiServices.getTypeAhead(escaped).then(function (response) { //hier das Return nicht vergessen! Sosnt gibt die Methode nix zurück!

                if (response.data.status != "SUCCESS") {
                    $scope.status = "ERROR"
                    $scope.errorCode = response.data.code
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

                if (response.data.status != "SUCCESS") {
                    $scope.status = "ERROR"
                    $scope.errorCode = response.data.code
                } else {

                    if (isStart) {
                        $scope.start = response.data.randomTitle
                    } else {
                        $scope.finish = response.data.randomTitle
                    }
                }

            }).catch(function (error) {
                console.log(error);
                $scope.status = "ERROR"
                $scope.errorCode = "FRONTEND"
            });
        };

        String.prototype.replaceAll = function (search, replacement) {
            var target = this;
            return target.split(search).join(replacement);
        };

        $scope.search = function (a, b) {

            console.log("joo")


            var escapedA = a.replace(/\//g, '%2F');
            var escapedB = b.replace(/\//g, '%2F');

            $scope.status = "LOADING"

            $http.get("wiki/" + escapedA + "/" + escapedB).then(function (response) { //dann muss auch im Controller then davor

                $scope.status = response.data.status

                if (response.data.status == "SUCCESS") {
                    $scope.steps = response.data.steps
                } else if (response.data.status == undefined) {
                    $scope.status = "ERROR"
                    $scope.errorCode = response.data.code
                }
            }).catch(function (error) {
                console.log(error);
                $scope.status = "ERROR"
                $scope.errorCode = "FRONTEND"
            });

        }

        if ($scope.start != undefined && $scope.finish != undefined) {
            $scope.search(start, finish)
        } else {
            $scope.status = "NEW"
        }

}])
    /*


    So ist Spitze! Man macht nicht isError, isNothingFound und isSuccess, sondern ein Status!!!! Für jede mögliche Antowrt!!!
    */
