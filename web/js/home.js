angular.module('securly', ['ngRoute']);
function config($routeProvider, $locationProvider){
    $routeProvider.when('/', {
        templateUrl: 'templates/home.html',
        controller: 'controller'
    })
    .when('/dashboard', {
        templateUrl: 'templates/dashboard.html',
        controller: 'controller'
    }).otherwise({
        redirectTo: '/'
    });
    $locationProvider.html5Mode({
        enabled:true,
        requireBase: false
    });
}

angular.module('securly')
.config(['$routeProvider', '$locationProvider', config])
.controller('controller', controller)
.service('data', data);

data.$inject = ['$http'];
function data($http){
    return {
        login: function(user,password){
            return $http({
                url: '/api/login',
                data: { 
                    user: user,
                    password: password
                },
                method: "POST"
            });
        },
        getChild: function(){
            return $http(
                {
                    url: '/api/get-child',
                    method: "GET",
                }
            );
        },
        import: function(fileData){
            return $http({
                url: '/api/import',
                method: 'POST',
                headers: {'Content-Type': 'application/x-www-form-urlencoded'},
                data: fileData,
            });
        }
    };
}
controller.$inject = ['$scope','data', '$location'];
function controller($scope,data, $location){
    $scope.formData = {};
    $scope.importData = {};
    $scope.login = function(){
        data.login($scope.formData.user,$scope.formData.password)
        .then(function(response){
            console.log(response);
            if(JSON.parse(response.data).status===true){
                $location.path('/dashboard');
            }
        })
        .catch(function(){
            console.log("error");
        });
        return false;
    }
    $scope.import = function(){
        var file = document.getElementById('file').files[0];
        if(!file){
            console.log("file empty");
            return;
        }
        var reader = new FileReader();
        reader.onloadend = function(e){
            data.import(e.target.result)
            .then(function(result){
                console.log(result);
            })
            .catch(function(){
                console.log("error");
            });
        }
        reader.readAsArrayBuffer(file);
    }
    $scope.getChild = function(){
        data.getChild()
        .then(function(response){
            console.log(response);
        })
        .catch(function(){
            console.log("error");
        });
    }
}