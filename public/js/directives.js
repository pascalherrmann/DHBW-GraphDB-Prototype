var directives = angular.module('graphDBPrototypeAppDirectives', []);

directives.directive('navigation', function () {
    return {
        restrict: "E",
        templateUrl: "html/navigation-directive.tpl.html"
    };
});
