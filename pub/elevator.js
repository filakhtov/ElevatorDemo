var module = angular.module("lender411", []);

module.factory("elevatorService", ["$http", function ($http) {
        var elevatorService = {

        "getStatus": function (callback) {
            return $http.get("/api.php/elevator").then(function (response) {
                callback(response.data);
            });
        },

        "addRequest": function (from, to, error) {
            return $http.post("/api.php/elevator", {"from": from, "to": to}).then(null, error);
        },

        "sendSignal": function (signal, error) {
            return $http.post("/api.php/signal", {"signal": signal}).then(null, error);
        },

        "getFloors": function (callback) {
            $http.get("/api.php/floors").then(function (response) {
                var floorsStatus = response.data.floorsStatus;
                var floors = [];

                for (var i in floorsStatus) {
                    floors.push({"number": parseInt(i), "status": floorsStatus[i]});
                }

                callback(floors);
            });
        },

        "resetState": function () {
            return $http.post("/api.php/reset", {"reset": true});
        }
    };

    return elevatorService;
}]);

module.controller("elevatorController", ["$scope", "elevatorService", "$interval", function ($scope, elevatorService, $interval) {

    var getStatus = function () {
        elevatorService.getStatus(function (elevator) {
            $scope.elevator = elevator;
        });
    };

    var errorHandler = function (response) {
        if ("data" in response && "error" in response.data) {
            $scope.error = response.data.error;
        }
    };

    $scope.request = {};
    $scope.requests = [];
    $scope.error = null;

    elevatorService.getFloors(function (floors) {
        $scope.floors = floors;
    });

    getStatus();

    $interval(getStatus, 1500);

    $scope.openDoor = function () {
        $scope.error = null;
        $scope.requests.push("Open door request");
        elevatorService.sendSignal(2, errorHandler);
        };

        $scope.closeDoor = function () {
        $scope.error = null;
        $scope.requests.push("Close door request");
        elevatorService.sendSignal(4, errorHandler);
    };

    $scope.alarm = function () {
        $scope.error = null;
        $scope.requests.push("Triggered alarm");
        elevatorService.sendSignal(3, errorHandler);
    };

    $scope.reset = function () {
        $scope.error = null;
        $scope.requests = [];
        elevatorService.resetState();
    };

    $scope.sendRequest = function () {
        $scope.error = null;

        if (!$scope.request.from || !$scope.request.to) {
            $scope.error = "Please select source and target floors first!";
            return;
        }

        if ($scope.request.from === $scope.request.to) {
            $scope.error = "Please, select different floor.";
            return;
        }

        $scope.requests.push("Move request: " + $scope.request.from.number + "->" + $scope.request.to.number);

        elevatorService.addRequest($scope.request.from.number, $scope.request.to.number, errorHandler);
    };

}]);
