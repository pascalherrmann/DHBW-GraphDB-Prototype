'use strict';

//Module
var app = angular.module('graphDBPrototypeApp', [
    // App-Module
    'graphDBPrototypeAppControllers',
    'graphDBPrototypeAppFilters',
    'graphDBPrototypeAppServices',
    'graphDBPrototypeAppDirectives',
    // Angular-Erweiterungen/Frameworks
    'ngRoute',
    'ngCookies',
    //weitere
    'ui.bootstrap',
    'pascalprecht.translate',
    'ngSurprise'
]);

// Routing
app.config(['$routeProvider', '$locationProvider',
    function ($routeProvider, $locationProvider) {
        $locationProvider.hashPrefix('');
        $routeProvider.
        when('/', {
            templateUrl: 'html/start.html',
            controller: 'StartController'
        }).
        when('/wiki/:START/:FINISH', {
            templateUrl: 'html/wiki.html',
            controller: 'WikiController'
        }).
        when('/wiki', {
            templateUrl: 'html/wiki.html',
            controller: 'WikiController'
        }).
        when('/info', {
            templateUrl: 'html/info.html',
            controller: 'InfoController'
        }).
        otherwise({
            redirectTo: '/'
        });
    }]);


app.config(['$translateProvider', function ($translateProvider) {

    $translateProvider.useStaticFilesLoader({
        prefix: 'res/locale-',
        suffix: '.json'
    });


    $translateProvider.preferredLanguage('de');
    $translateProvider.fallbackLanguage('de');

    $translateProvider.useSanitizeValueStrategy(null);

}]);
