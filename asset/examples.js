var app = angular.module("angApp", ["ngRoute"]);
app.config(function($routeProvider) {
    $routeProvider
        .when("/", {
            templateUrl : "task_1.htm",
            controller : "uploadsCtrl"
        })
        .when("/tickets", {
            templateUrl : "task_2.htm",
            controller : "ticketsCtrl"
        })
});
app.controller("uploadsCtrl", function ($scope) {});

app.controller("ticketsCtrl", function($scope, $http) {
    $http({
        method: 'GET',
        url: 'http://localhost/test/basic/tickets'
    }).then(function successCallback(response) {
        $scope.posts = response.data.tickets.tickets;
    });
});




app.controller('UploadController',
    function UploadController($scope, $http) {
        //By default used blank image.
        $scope.uploadme = 'http://1x1px.me/FF4D00-0.png';
        $scope.serverUrl = 'http://1x1px.me/FF4D00-0.png';

        $scope.uploadImage = function() {
            $http({
                    method: 'POST',
                    url: 'http://localhost/test/basic/uploadimage',
                    headers: {
                        'Content-Type': undefined
                    },
                    data: { image: $scope.uploadme }
                }).then(function successCallback(response) {
                    if (response.data.status === 200) {
                        $scope.serverUrl = response.data.url;
                    } else {
                        alert('Something goes wrong:(')
                    }

            }, function errorCallback(response) {
                console.log('Server error');
            });
        }
    }
);


//directive
app.directive("fileread", [
    function() {
        return {
            scope: {
                fileread: "="
            },
            link: function(scope, element, attributes) {
                element.bind("change", function(changeEvent) {
                    var reader = new FileReader();
                    reader.onload = function(loadEvent) {
                        scope.$apply(function() {
                            scope.fileread = loadEvent.target.result;
                        });
                    };
                    reader.readAsDataURL(changeEvent.target.files[0]);
                });
            }
        }
    }
]);





