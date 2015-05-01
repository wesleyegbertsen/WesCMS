angular.module('WesCMS.development', [])
    .constant('config', {
        backend: '../api/'
    });

angular.module('WesCMS.live', [])
    .constant('config', {
        backend: 'api/'
    });
