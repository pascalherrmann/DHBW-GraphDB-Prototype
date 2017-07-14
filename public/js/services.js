var services = angular.module('graphDBPrototypeAppServices', []);

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
            return $http.get(apiPath + "autocomplete/" + value);
        },
        getPath: function (start, finish) {
            return $http.get(apiPath + "path/" + start + "/" + finish);
        },
        getRandom: function () {
            return $http.get(apiPath + "random");
        }
    };

}]);
