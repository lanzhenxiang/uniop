var adminVersionApp = angular.module("adminVersion", []);

adminVersionApp.controller("version", function($scope,$http) {
	console.log(brands)
    $http.get("/console/ajax/network/hosts/createHostsArray").success(
    	function(data){
    		$scope.hostList = data;
    		$scope.areas = data[0];
    		$scope.area =data[0].area[0]
    		$scope.brands = brands
    		$scope.brand = brands[0].subs
    		$scope.spec= brands[0].subs[0]
    	})
    $scope.changeHost = function(obj) {
    	$scope.areas = obj;
	}

	$scope.changeArea = function(obj){
		console.log(obj)
		$scope.area = obj
	}
	$scope.changeBrands = function(obj){
		$scope.brand = obj.subs
	}
	$scope.selectBrand = function(id){
		$.each( brands, function(index, arr){
      		list =   brands[index].subs;
       			$.each( list, function(i, b){
       				if(b.id == id){
       					$scope.spec = b
       				}
       			})
   		})
   		console.log($scope.spec)
	}
});


