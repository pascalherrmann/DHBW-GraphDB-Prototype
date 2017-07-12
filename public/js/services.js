var services = angular.module('graphDBPrototypeAppServices', []);


/*
- bei Nodejs dann einfach /.
- und so problemlos wechslen
- es ergibt schon auch Sinn, wenn man
*/

services.factory("wikiServices", ["$http", function ($http) {

    var apiPath = "php-api/neo/"

    return {
        getAPIPath: function () {
            return apiPath;
        },

        setAPIPath: function (path) {
            apiPath = path;
        },

        getTypeAhead: function (value) {
            return $http.get(apiPath+"autocomplete/" + value);
        },
        getPath: function (start, finish) {
            return $http.get(apiPath+"path/"+start+"/"+finish);
        },
        getRandom: function () {
            return $http.get(apiPath+"random");
        }
    };

}]);
