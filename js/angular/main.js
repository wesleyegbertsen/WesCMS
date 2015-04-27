/************************************************/
/** General                                    **/
/************************************************/

var theApp = angular.module("WesCMS", ["ngRoute", "settingsModule", "WesCMS.development"]);


/************************************************/
/** Routes                                     **/
/************************************************/
theApp.config(["$routeProvider", function ($routeProvider) {
    "use strict";
    $routeProvider
        .when("/", {
            templateUrl: "templates/home.html",
            controller: "ctrlHome"
        })
        .when("/about", {
            templateUrl: "templates/about.html",
            controller: "ctrlAbout"
        })
        .otherwise({
            redirectTo : "/"
        });
}]);

/************************************************/
/** Controllers                                **/
/************************************************/
theApp.controller("ctrlMain", ["$scope", "$location", "SettingsService", function ($scope, $location, SettingsService) {
    "use strict";

    //Main option variables
    $scope.main = {
        currentPage: "",
        pageTitle: ""
    };

    $scope.settings = {
        sitename: "",
        navcolor: "",
        footercolor: "",
        androidbar: ""
    };

    /**
     * Loop through all the settings and check if there is a equivalent value in the database for the key
     */
    SettingsService.getSettings(function (settings) {
        if (settings) {
            for (var key in $scope.settings) {
                for(var i = 0; i < settings.length; i++) {
                    if(settings[i].setting_name === key) {
                        $scope.settings[key] = settings[i].setting_value;
                    }
                }
            }
        }
    });

    $scope.setMainOptions = function (currentPage, pageTitle) {
        $scope.main.currentPage = currentPage;
        $scope.main.pageTitle = pageTitle;
    };

}]);

theApp.controller("ctrlHome", ["$scope", "$location", function ($scope, $location) {
    "use strict";

    $scope.setMainOptions("home", "Home");

}]);

theApp.controller("ctrlAbout", ["$scope", "$window", "$http", function ($scope, $window, $http) {
    "use strict";

    $scope.setMainOptions("about", "About");

}]);


theApp.directive("cnav", function () {
    "use strict";
    return {
        restrict: 'E',
        templateUrl: "directives/nav.html"
    };
});

theApp.directive("cfooter", function () {
    "use strict";
    return {
        restrict: 'E',
        templateUrl: "directives/footer.html"
    };
});