var services = angular.module('graphDBPrototypeAppServices', []);

services.factory("wikiServices", ["$http", function ($http) {
    return {
        getTypeAhead: function (value) {
            return $http.get("wiki/" + value);
        },
        getPath: function (start, finish) {
            return $http.get("wiki/"+start+"/"+finish);
        }
    };

}]);
