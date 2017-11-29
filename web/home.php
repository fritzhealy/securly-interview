<!doctype html>
<html ng-app="securly">
    <head>
        <title>Securly App</title>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.6/angular.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/angularjs/1.6.6/angular-route.min.js"></script>
        <script src="js/home.js"></script>
        <link rel="stylesheet" type="text/css" href="css/home.css">
    </head>
    <body ng-controller="controller">
        <div id="masthead">Securly App - Built by Frederick Healy</div><!--#masthead-->
        <div class="view" ng-view>
        </div>
    </body>
</html>

