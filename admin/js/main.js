/************************************************/
/** General                                    **/
/************************************************/

var theApp = angular.module("WesCMS", ["ngRoute", "ngAnimate", "ngCookies", "settingsModule", "WesCMS.development", "userModule"]);


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
theApp.controller("ctrlMain", ["$scope", "$location", "SettingsService", "UserService", function ($scope, $location, SettingsService, UserService) {
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

    $scope.user = {
        id: "",
        isLoggedIn: false,
        username: "",
        name: "",
        profilePic: ""
    };

    UserService.isLoggedIn(function (data) {
        if (data) {
            if(data.success) {
                $scope.user = data.user;
                enableUserDropDown();
            }
        } else {
            //something went wrong
        }
    });

    $scope.logout = function () {
        UserService.logout(function (data) {
            if (data) {
                if (data.success) {
                    $scope.user.isLoggedIn = false;
                    $scope.user.id = "";
                    $scope.user.username = "";
                    $scope.user.name = "";
                    $scope.user.profilePic = "";
                }
            } else {
                //Something went wrong
            }
        });
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

theApp.controller("ctrlHome", ["$scope", "$location", "UserService", "$cookies", function ($scope, $location, UserService, $cookies) {
    "use strict";

    $scope.setMainOptions("home", "Home");

    $scope.userForm = {
        username: "",
        password: ""
    };

    $scope.error = {
        show: false,
        text: "Error"
    };

    $scope.rememberUsername = false;


    if ($cookies.username) {
        $scope.userForm.username = $cookies.username;
        $scope.rememberUsername = true;
    }

    $scope.login = function () {
        $scope.error.show = false;
        UserService.login($scope.userForm, function (data) {
            if(data) {
                if(data.success) {
                    $scope.user.username = data.username;
                    $scope.user.profilePic = data.profilePic;
                    $scope.user.isLoggedIn = true;

                    enableUserDropDown();

                    if($scope.rememberUsername) {
                        $cookies.username = data.username;
                    } else {
                        delete $cookies.username;
                    }

                } else {
                    $scope.error.text = data.message;
                    $scope.error.show = true;
                }
            }
        });
    };

    $scope.formChanged = function () {
        if ($scope.error.show) {
            $scope.error.show = false;
        }
    };

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

/*
 Using timeout, to make sure the dropdown function is called after the ng-if is finished
 As browsers by default keep all events in a queue, therefore, when digest loop is running,
 the callback function from setTimeout will enter the queue and get executed as soon digest loop is over from the ng-if.
 */
function enableUserDropDown () {
    "use strict";

    setTimeout(function() {
        $(".dropdown-button").dropdown();
    }, 0);
}