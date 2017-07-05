var services = angular.module('graphDBPrototypeAppServices', []);

services.factory("wikiServices", ["$http", function ($http) {
    return {
        getTypeAhead: function (value) {
            return $http.get("wiki/" + value).then(function (response) {
                return response.data
               /* return response.data.map(function (item) {
                    return item.username;
                });*/
            });
        },
        getPath: function (start, finish) {
            return $http.get("wiki/"+start+"/"+finish);
        }
    };

}]);
