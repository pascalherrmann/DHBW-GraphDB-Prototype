var services = angular.module('graphDBPrototypeAppServices', []);

var apiPath = "php-api/path/neo"

/*
- bei Nodejs dann einfach /.
- und so problemlos wechslen
- es ergibt schon auch Sinn, wenn man
*/

services.factory("wikiServices", ["$http", function ($http) {
    return {
        getTypeAhead: function (value) {
            return $http.get("wiki/" + value);
        },
        getPath: function (start, finish) {
            return $http.get("wiki/"+start+"/"+finish);
        },
        getRandom: function () {
            return $http.get("random");
        }
    };

}]);
