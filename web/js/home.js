angular.module('securly', ['ngRoute']);
function config($routeProvider, $locationProvider){
    $routeProvider.when('/', {
        templateUrl: 'templates/home.html',
        controller: 'controller'
    })
    .when('/dashboard', {
        templateUrl: 'templates/dashboard.html',
        controller: 'controller',
        resolve: {
            auth: function(data){
                var auth = data.getAuth()
                .then(function(response){
                    if(response.data.status===true){
                        return true;
                    } else {
                        return false;
                    }
                })
                .catch(function(){
                    return false;
                });
                return auth;
            }
        }
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
        getKid: function(email){
            return $http(
                {
                    url: '/api/kid?email='+email,
                    method: "GET",
                }
            );
        },
        getClub: function(name){
            return $http(
                {
                    url: '/api/club?name='+name,
                    method: "GET",
                }
            );
        },
        getPastQueries: function(){
            return $http(
                {
                    url: '/api/queries',
                    method: "GET",
                }
            );
        },
        saveQuery: function(name, value){
            return $http(
                {
                    url: '/api/save',
                    method: "POST",
                    data: {
                        name: name,
                        value: value
                    }
                }
            );
        },
        getAuth: function(){
            return $http(
                {
                    url: '/api/auth',
                    method: "GET",
                }
            );
        },
        getConnected: function(email1, email2){
            return $http(
                {
                    url: '/api/connected?email1='+email1+"&email2="+email2,
                    method: "GET",
                }
            )
        },
        logout: function(){
            return $http(
                {
                    url: '/api/logout',
                    method: "GET",
                }
            )
        }
    };
}
controller.$inject = ['$scope', 'data', '$location', '$route'];
function controller($scope, data, $location, $route){
    if($route.current){
        if($route.current.locals){
            if(!$route.current.locals.auth){
                $location.path('/');
            }
        }
    }
    $scope.banner = "";
    $scope.kid = {};
    $scope.kids = {};
    $scope.select = {};
    $scope.club = {};
    $scope.queries = {};
    $scope.importData = {};
    $scope.logout = function(){
        data.logout()
        .then(function(){
            $location.path('/');
        })
        .catch(function(){
            console.log("error");
        });
    }
    $scope.login = function(){
        data.login($scope.formData.user,$scope.formData.password)
        .then(function(response){
            if(response.data.status===true){
                $location.path('/dashboard');
            } else {            
                $scope.banner = "Access denied, please try again.";
            }
        })
        .catch(function(){
            console.log("error");
        });
        return false;
    }
    $scope.getPastQueries = function(){
        data.getPastQueries()
        .then(function(response){
            $scope.queries = response.data;
        }).catch(function(){
            console.log("error");
        });
    }
    $scope.getPastQueries();
    $scope.rerun = function(){
        value = $scope.select;
        if(value){
            $scope.club.name = "";
            $scope.kid.email = "";
            $scope.kids.email1 = "";
            $scope.kids.email2 = "";
            values = value.split("|");
            operation = values[0];
            value = values[1];
            if(operation=="getKid"){
                $scope.kid.email = value;
            }
            if(operation=="getClub"){
                $scope.club.name = value;
            }
            if(operation=="getConnected"){
                emails = value.split(":");
                $scope.kids.email1 = emails[0];
                $scope.kids.email2 = emails[1];
            }
            $scope.get();
        }
    }
    $scope.get = function(){
        if($scope.club.name){
            data.getClub($scope.club.name)
            .then(function(response){
                if(response.data.status===false){
                    console.log("error");
                    return;
                }
                $scope.club.schools = response.data.schools;
                $scope.club.kids = response.data.kids; 
                data.saveQuery("getClub",$scope.club.name)
                .then(function(){
                    $scope.getPastQueries();

                }).catch(function(){
                    console.log("error");
                });
            })
            .catch(function(){
                console.log("error");
            });
        }
        if($scope.kid.email){
            data.getKid($scope.kid.email)
            .then(function(response){
                if(response.data.status===false){
                    console.log("error");
                    return;
                }
                $scope.kid.clubs = response.data.clubs;
                $scope.kid.schools = response.data.schools;
                data.saveQuery("getKid",$scope.kid.email)
                .then(function(){
                    $scope.getPastQueries();

                }).catch(function(){
                    console.log("error");
                });
            })
            .catch(function(error){
                console.log("error");
            });
        }
        if($scope.kids.email1&&$scope.kids.email2){
            data.getConnected($scope.kids.email1,$scope.kids.email2)
            .then(function(response){
                $scope.kids.connected = response.data.status;
                data.saveQuery("getConnected",$scope.kids.email1+":"+$scope.kids.email2)
                .then(function(){
                    $scope.getPastQueries();
                }).catch(function(){
                    console.log("error");
                });
            }).catch(function(){
                console.log("error");
            });
        }
    }
}