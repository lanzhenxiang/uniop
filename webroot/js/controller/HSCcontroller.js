function HSCrouterListService($scope, $http) {
	$http.get("/console/highSpeedChannel/createHSCArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(response) {
			var data = response.data;
			$scope.unit = "元/天";
			$scope.routerList = data;

            $scope.currentConnectScene = data[0].connectscene;
            $scope.currentConnectSceneCode = data[0].connectscene.code;


            //本地端配置
			$scope.currentCompany = data[0].localconfig[0];
			$scope.currentCompanyCode = data[0].localconfig[0].company.code;

			$scope.currentArea = data[0].localconfig[0].area[0];
			$scope.currentAreaCode = data[0].localconfig[0].area[0].code;

            $scope.currentVpc = data[0].localconfig[0].area[0].vpc[0];
            $scope.currentVpcCode = data[0].localconfig[0].area[0].vpc[0].code;

            if(data[0].localconfig[0].area[0].vpc[0].subnet[0]){
                $scope.currentSubnet = data[0].localconfig[0].area[0].vpc[0].subnet[0];
                $scope.currentSubnetCode = data[0].localconfig[0].area[0].vpc[0].subnet[0].code;
            }
            //对端配置
            $scope.currentRemoteCompany = data[0].remoteconfig[0];
            $scope.currentRemoteCompanyCode = data[0].remoteconfig[0].company.code;

            $scope.currentRemoteArea = data[0].remoteconfig[0].area[0];
            $scope.currentRemoteAreaCode = data[0].remoteconfig[0].area[0].code;
            if(data[0].remoteconfig[0].area[0].vpc[0]){
                $scope.currentRemoteVpc = data[0].remoteconfig[0].area[0].vpc[0];
                $scope.currentRemoteVpcCode = data[0].remoteconfig[0].area[0].vpc[0].code;
            }

            $scope.routerStatusLabel = '启用';
            $scope.routerStatus = 1;

		}
	);

    /**
	 * 选择连接场景
     * @param obj
     */
	$scope.changeConnectScene = function(obj)
	{
		$scope.currentConnectScene = obj.connectscene;
		$scope.currentConnectSceneCode = obj.connectscene.code;

		$scope.currentCompany = obj.localconfig[0];
		$scope.currentCompanyCode = obj.localconfig[0].company.code;

        $scope.currentArea = obj.localconfig[0].area[0];
        $scope.currentAreaCode = obj.localconfig[0].area[0].code;

        $scope.currentVpc = obj.localconfig[0].area[0].vpc[0];
        $scope.currentVpcCode = obj.localconfig[0].area[0].vpc[0].code;

        $scope.currentSubnet = obj.localconfig[0].area[0].vpc[0].subnet;
        $scope.currentSubnetCode = obj.localconfig[0].area[0].vpc[0].subnet[0].code;

	}
	
	$scope.changeCompany = function (obj) {
        $scope.currentCompany = obj;
        $scope.currentCompanyCode = obj.company.code;

        $scope.currentArea = obj.area[0];
        $scope.currentAreaCode = obj.area[0].code;

        $scope.currentVpc = obj.area[0].vpc[0];
        $scope.currentVpcCode = obj.area[0].vpc[0].code;

        $scope.currentSubnet = obj.area[0].vpc[0].subnet;
        $scope.currentSubnetCode = obj.area[0].vpc[0].subnet[0].code;
    }

	$scope.changeArea = function(obj) {

        $scope.currentArea = obj;
        $scope.currentAreaCode = obj.code;

        $scope.currentVpc = obj.vpc[0];
        $scope.currentVpcCode = obj.vpc[0].code;

        $scope.currentSubnet = obj.vpc[0].subnet[0];
        $scope.currentSubnetCode = obj.vpc[0].subnet[0].code;
	}

	$scope.changeVpc = function(obj) {
        $scope.currentVpc = obj;
        $scope.currentVpcCode = obj.code;

        $scope.currentSubnet = obj.subnet[0];
        $scope.currentSubnetCode = obj.subnet[0].code;
	}

	$scope.changeSubnetNet = function(obj) {
		$scope.currentSubnet = obj;
        $scope.currentSubnetCode = obj.code;
	}

    //对端
    $scope.changeRemoteCompany = function (obj) {
        $scope.currentRemoteCompany = obj;
        $scope.currentRemoteCompanyCode = obj.company.code;

        $scope.currentRemoteArea = obj.area[0];
        $scope.currentRemoteAreaCode = obj.area[0].code;

        $scope.currentRemoteVpc = obj.area[0].vpc[0];
        $scope.currentRemoteVpcCode = obj.area[0].vpc[0].code;

    }

    $scope.changeRemoteArea = function(obj) {

        $scope.currentRemoteArea = obj;
        $scope.currentRemoteAreaCode = obj.code;

        $scope.currentRemoteVpc = obj.vpc[0];
        $scope.currentRemoteVpcCode = obj.vpc[0].code;

    }

    $scope.changeRemoteVpc = function(obj) {
        $scope.currentRemoteVpc = obj;
        $scope.currentRemoteVpcCode = obj.code;
    }

    //边界路由器名称
    $scope.changeRouterName = function (val) {
	    $scope.routerName = val;
    }

    $scope.changeRouterStatus = function (val) {
        $scope.routerStatusLabel = '启用';
        $scope.routerStatus = 1;
        if(val == 0){
            $scope.routerStatusLabel = '不启用';
            $scope.routerStatus = 0;
        }
    }
}


function HSCrouterInterfaceService($scope, $http) {
    $http.get("/console/highSpeedChannel/createRouterInterfaceArray.json").success(
        // $http.get("/json/data-new.json").success(
        function(response) {
            var data = response.data;
            $scope.unit = "元/天";
            $scope.routerList = data;

            $scope.currentChargeType = data.priceList[0];
            $scope.currentChargeInterval = data.priceList[0].interval[0];
            //本地端配置

            $scope.currentSpec = data.spec[0];

            //对端配置
            $scope.currentRemoteCompany = data.remoteconfig[0];
            $scope.currentRemoteCompanyCode = data.remoteconfig[0].company.code;

            $scope.currentRemoteArea = data.remoteconfig[0].area[0];
            $scope.currentRemoteAreaCode = data.remoteconfig[0].area[0].code;
            if(data.remoteconfig[0].area[0].vpc[0]){
                $scope.currentRemoteVpc = data.remoteconfig[0].area[0].vpc[0];
                $scope.currentRemoteVpcCode = data.remoteconfig[0].area[0].vpc[0].code;
            }
            getRouterInterfacePrice($scope.currentChargeInterval.code,$scope.currentSpec.number);
        }
    );
    /**
     * 选择计费方式
     * @param obj
     */
    $scope.changeChargeType = function (obj) {
        $scope.currentChargeType = obj;
        $scope.currentChargeInterval = obj.interval[0];
        getRouterInterfacePrice($scope.currentChargeInterval.code,$scope.currentSpec.number);
    }
    $scope.changeCycle = function (obj) {
        $scope.currentChargeInterval = obj;
        getRouterInterfacePrice($scope.currentChargeInterval.code,$scope.currentSpec.number);
    }
    
    $scope.changeSpec = function (obj) {
        $scope.currentSpec = obj;
        getRouterInterfacePrice($scope.currentChargeInterval.code,$scope.currentSpec.number);
    }

    //对端
    $scope.changeRemoteCompany = function (obj) {
        $scope.currentRemoteCompany = obj;
        $scope.currentRemoteCompanyCode = obj.company.code;

        $scope.currentRemoteArea = obj.area[0];
        $scope.currentRemoteAreaCode = obj.area[0].code;

        $scope.currentRemoteVpc = obj.area[0].vpc[0];
        $scope.currentRemoteVpcCode = obj.area[0].vpc[0].code;

    }

    $scope.changeRemoteArea = function(obj) {

        $scope.currentRemoteArea = obj;
        $scope.currentRemoteAreaCode = obj.code;

        $scope.currentRemoteVpc = obj.vpc[0];
        $scope.currentRemoteVpcCode = obj.vpc[0].code;

    }

    $scope.changeRemoteVpc = function(obj) {
        $scope.currentRemoteVpc = obj;
        $scope.currentRemoteVpcCode = obj.code;
    }

    //边界路由器名称
    $scope.changeRouterInterfaceName = function (val) {
        $scope.routerInterfaceName = val;
    }

}

