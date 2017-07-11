function hostListService($scope, $http) {
	$http.get("/console/ajax/network/hosts/createHostsArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(data) {
			$scope.unit = "元/天";
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.currentDisksPrice = data[0].company.disksPrice[0].price;

			if (data[0].area[0].set.length != 0) {
				angular.element('#set-warning').html('');
				$scope.currentSet = data[0].area[0].set[0];
				if (data[0].area[0].set[0].rom.length != 0) {
					$scope.currentRom = data[0].area[0].set[0].rom[0];
					$scope.currentSetCode = data[0].area[0].set[0].rom[0].setCode;
					$scope.instancePrice=data[0].area[0].set[0].rom[0].priceDay;//设置默认天费
				}
			} else {
				$scope.currentSet = "";
				$scope.currentRom = "";
				$scope.currentSetCode = "";
				angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
			}

			if (data[0].area[0].vpc.length != 0) {
				$scope.currentVpc = data[0].area[0].vpc[0];
				//loadSubnetPublic(data[0].area[0].vpc[0].vpCode,$scope.currentCompanyCode);
				if (data[0].area[0].vpc[0].net.length != 0) {
					initSubnetExtends(data[0].area[0].vpc[0].net[0]);
					$scope.currentNet = data[0].area[0].vpc[0].net[0];
				}
			}

			// if (data[0].area[0].cluster.length != 0) {
			// 	$scope.currentCluster = data[0].area[0].cluster[0];
			// }

			if (data[0].area[0].imageType.length != 0) {
				$scope.currentImageType = data[0].area[0].imageType[0];
				if(data[0].area[0].imageType[0].Os.length != 0){
					$scope.currentOs = data[0].area[0].imageType[0].Os[0];
					if (data[0].area[0].imageType[0].Os[0].types.length != 0) {
						$scope.currentOsTypes = data[0].area[0].imageType[0].Os[0].types[0];
						$scope.imagePrice=data[0].area[0].imageType[0].Os[0].types[0].priceDay;//设置默认天费
					}
				}
			}

		}
	);


	$scope.changeHost = function(obj) {
		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentDisksPrice = $scope.currentCompany.disksPrice[0].price;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		if (obj.area[0].set.length != 0) {
			angular.element('#set-warning').html('');
			$scope.currentSet = obj.area[0].set[0];
			if (obj.area[0].set[0].rom.length != 0) {
				$scope.currentRom = obj.area[0].set[0].rom[0];
				$scope.currentSetCode = obj.area[0].set[0].rom[0].setCode;
				$scope.changeCharging($scope.buyTyle);
			}
		} else {
			$scope.currentSet = "";
			$scope.currentRom = "";
			$scope.currentSetCode = "";
			angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
		}


		if (obj.area[0].vpc.length != 0) {
			$scope.currentVpc = obj.area[0].vpc[0];
			if (obj.area[0].vpc[0].net.length != 0) {
				initSubnetExtends(obj.area[0].vpc[0].net[0]);
				$scope.currentNet = obj.area[0].vpc[0].net[0];
			}
		}

		if (obj.area[0].cluster.length != 0) {
			$scope.currentCluster = obj.area[0].cluster[0];
		}
		if(obj.area[0].imageType.length!=0){
			if (obj.area[0].imageType[0].Os.length != 0) {
				$scope.currentOs = obj.area[0].imageType[0].Os[0];
				if (obj.area[0].imageType[0].Os[0].types.length != 0) {
					$scope.currentOsTypes = obj.area[0].imageType[0].Os[0].types[0];
					$scope.changeCharging($scope.buyTyle);
				}
			}
		}

		//console.log(obj.area[0].vpc[0]);
		//loadSubnetPublic(obj.area[0].vpc[0].vpCode,$scope.currentCode);
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		if (obj.set.length != 0) {
			angular.element('#set-warning').html('');
			$scope.currentSet = obj.set[0];
			if (obj.set[0].rom.length != 0) {
				$scope.currentRom = obj.set[0].rom[0];
				$scope.currentSetCode = obj.set[0].rom[0].setCode;
				$scope.changeCharging($scope.buyTyle);
			}
		} else {
			$scope.currentSet = "";
			$scope.currentRom = "";
			$scope.currentSetCode = "";
			angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
		}

		if (obj.vpc.length != 0) {
			$scope.currentVpc = obj.vpc[0];
			if (obj.vpc[0].net.length != 0) {
				initSubnetExtends(obj.vpc[0].net[0]);
				$scope.currentNet = obj.vpc[0].net[0];
			}
		}

		if (obj.cluster.length != 0) {
			
			$scope.currentCluster = obj.cluster[0];
		}

		if (obj.imageType[0].Os.length != 0) {
			$scope.currentOs = obj.imageType[0].Os[0];
			if (obj.imageType[0].Os[0].types.length != 0) {
				$scope.currentOsTypes = obj.imageType[0].Os[0].types[0];
				$scope.changeCharging($scope.buyTyle);
			}
		}
		//loadSubnetPublic(obj.vpc[0].vpCode);
	}

	$scope.changeSet = function(obj) {

		$scope.currentSet = obj;

		if (obj.rom.length != 0) {
			$scope.currentRom = obj.rom[0];
			$scope.currentSetCode = obj.rom[0].setCode;
			$scope.changeCharging($scope.buyTyle);
		}
	}

	$scope.changeRom = function(obj) {

		$scope.currentRom = obj;
		$scope.currentSetCode = obj.setCode;
		$scope.changeCharging($scope.buyTyle);

	}

	$scope.changeVpc = function(obj) {
        clearEipInput();
		$scope.currentVpc = obj;
		if (obj.net.length != 0) {
			initSubnetExtends(obj.net[0]);
			$scope.currentNet = obj.net[0];
		}
		// console.log(obj);
		//loadSubnetPublic(obj.vpCode);
	}

	$scope.changeCluster = function(obj) {
        $scope.currentCluster = obj;
	}

	$scope.changeNet = function(obj) {
        clearEipInput();
		initSubnetExtends(obj);
		$scope.currentNet = obj;
	}

	$scope.changeImageType = function(obj){
		$scope.currentImageType = obj;
		if(obj.Os.length != 0){
			$scope.currentOs = obj.Os[0];
			if (obj.Os[0].types.length != 0) {
				$scope.currentOsTypes = obj.Os[0].types[0];
				$scope.changeCharging($scope.buyTyle);

			}
		}
	}

	$scope.changeOs = function(obj) {
		$scope.currentOs = obj;
		if (obj.types.length != 0) {
			$scope.currentOsTypes = obj.types[0];
			$scope.changeCharging($scope.buyTyle);
		}
	}
	$scope.changeOsTypes = function(obj) {

		$scope.currentOsTypes = obj;
		$scope.changeCharging($scope.buyTyle);
	}

	$scope.cpuFilter = function (item) {
      return item.cpu === $scope.currentSet.cpu;
  	};

  	$scope.changeCharging = function(obj) {
		$scope.buyTyle= obj;
		if(obj==1){
			$scope.unit = "元/天";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceDay;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceDay;
			}
			if($scope.currentCompany !=""){
				$scope.currentDisksPrice = $scope.currentCompany.disksPrice[0].price;
			}
		}else if(obj==2){
			$scope.unit = "元/月";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceMonth;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceMonth;
			}
			if($scope.currentCompany !=""){
				$scope.currentDisksPrice = $scope.currentCompany.disksPrice[1].price;
			}
		}else if(obj==4){
			$scope.unit = "元/年";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceYear;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceYear;
			}
			if($scope.currentCompany !=""){
				$scope.currentDisksPrice = $scope.currentCompany.disksPrice[2].price;
			}
		}

	}
}

function vpnListService($scope, $http) {
    $http.get("/console/vpn/createVpnArray.json").success(
        // $http.get("/json/data-new.json").success(
        function(response) {
        	data = response.vpn;
            $scope.vpnList = data;

            $scope.currentCompany = data[0].company;
            $scope.currentCompanyCode = data[0].company.companyCode;

            $scope.currentArea = data[0].area[0];
            $scope.currentAreaCode = data[0].area[0].areaCode;

            $scope.currentVpc = data[0].area[0].vpc[0];

            $scope.currentFirewall = data[0].area[0].vpc[0].firewall[0];

            $scope.currentRange = data[0].area[0].range[0];
        }
    );
    $scope.changeHost = function(obj) {
        $scope.currentCompany = obj.company;
        $scope.currentCode = obj.company.companyCode;

        $scope.currentArea = obj.area[0];
        $scope.currentAreaCode = obj.area[0].areaCode;

        $scope.currentVpc = obj.area[0].vpc[0];
        $scope.currentFirewall = obj.area[0].vpc[0] ? obj.area[0].vpc[0].firewall[0] : '';
        $scope.currentRange = obj.area[0].range[0];
        if(!$scope.currentVpc){
            $('#all').siblings().addClass('hide');
		}
    }

    $scope.changeArea = function(obj) {

        $scope.currentArea = obj;
        $scope.currentAreaCode = obj.areaCode;

        $scope.currentVpc = obj.vpc[0];
        $scope.currentFirewall = obj.vpc[0].firewall[0];
        $scope.currentRange = obj.range[0];
    }

    $scope.changeRange = function (obj) {
    	if($scope.currentVpc){
            $scope.currentRange = obj;
            if(obj.code == 'cidr'){
                subnetRefreshTable($scope.currentVpc.vpCode);
            }else if(obj.code == 'instance'){
                instanceRefreshTable($scope.currentVpc.vpCode);
            }else{
                $("#range-dashboard").addClass('hide');
                $("#range").val('0.0.0.0');
                $('#all').siblings().addClass('hide');
            }
		}else {
            $('#all').siblings().addClass('hide');
		}
    }
}

//2017-3-13防火墙
function firewallListService($scope, $http) {
	$http.get("/console/ajax/network/hosts/createHostsFireArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(data) {
			$scope.unit = "元/天";
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentPriceId = 1;

			if (data[0].area[0].set.length != 0) {
				angular.element('#set-warning').html('');
				$scope.currentSet = data[0].area[0].set[0];
				if (data[0].area[0].set[0].rom.length != 0) {
					$scope.currentRom = data[0].area[0].set[0].rom[0];
					$scope.currentSetCode = data[0].area[0].set[0].rom[0].setCode;
					$scope.instancePrice=data[0].area[0].set[0].rom[0].priceDay;//设置默认天费
				}
			} else {
				$scope.currentSet = "";
				$scope.currentRom = "";
				$scope.currentSetCode = "";
				angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
			}

			if (data[0].area[0].vpc.length != 0) {
				$scope.currentVpc = data[0].area[0].vpc[0];
				//loadSubnetPublic(data[0].area[0].vpc[0].vpCode,$scope.currentCompanyCode);
				if (data[0].area[0].vpc[0].net.length != 0) {
					initSubnetExtends(data[0].area[0].vpc[0].net[0]);
					$scope.currentNet = data[0].area[0].vpc[0].net[0];
				}
			}

			if (data[0].area[0].imageType.length != 0) {
				$scope.currentImageType = data[0].area[0].imageType[0];
				if(data[0].area[0].imageType[0].Os.length != 0){
					$scope.currentOs = data[0].area[0].imageType[0].Os[0];
					if (data[0].area[0].imageType[0].Os[0].types.length != 0) {
						$scope.currentOsTypes = data[0].area[0].imageType[0].Os[0].types[0];
						$scope.imagePrice=data[0].area[0].imageType[0].Os[0].types[0].priceDay;//设置默认天费
					}
				}
			}

		}
	);


	$scope.changeHost = function(obj) {
		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];

		if (obj.area[0].set.length != 0) {
			angular.element('#set-warning').html('');
			$scope.currentSet = obj.area[0].set[0];
			if (obj.area[0].set[0].rom.length != 0) {
				$scope.currentRom = obj.area[0].set[0].rom[0];
				$scope.currentSetCode = obj.area[0].set[0].rom[0].setCode;
				$scope.changeCharging($scope.buyTyle);
			}
		} else {
			$scope.currentSet = "";
			$scope.currentRom = "";
			$scope.currentSetCode = "";
			angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
		}


		if (obj.area[0].vpc.length != 0) {
			$scope.currentVpc = obj.area[0].vpc[0];
			if (obj.area[0].vpc[0].net.length != 0) {
				initSubnetExtends(obj.area[0].vpc[0].net[0]);
				$scope.currentNet = obj.area[0].vpc[0].net[0];
			}
		}
		if(obj.area[0].imageType.length!=0){
			if (obj.area[0].imageType[0].Os.length != 0) {
				$scope.currentOs = obj.area[0].imageType[0].Os[0];
				if (obj.area[0].imageType[0].Os[0].types.length != 0) {
					$scope.currentOsTypes = obj.area[0].imageType[0].Os[0].types[0];
					$scope.changeCharging($scope.buyTyle);
				}
			}
		}

		//console.log(obj.area[0].vpc[0]);
		//loadSubnetPublic(obj.area[0].vpc[0].vpCode,$scope.currentCode);
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		if (obj.set.length != 0) {
			angular.element('#set-warning').html('');
			$scope.currentSet = obj.set[0];
			if (obj.set[0].rom.length != 0) {
				$scope.currentRom = obj.set[0].rom[0];
				$scope.currentSetCode = obj.set[0].rom[0].setCode;
				$scope.changeCharging($scope.buyTyle);
			}
		} else {
			$scope.currentSet = "";
			$scope.currentRom = "";
			$scope.currentSetCode = "";
			angular.element('#set-warning').html('<i class="icon-info-sign"></i>&nbsp;套餐信息不存在，请联系管理员');
		}

		if (obj.vpc.length != 0) {
			$scope.currentVpc = obj.vpc[0];
			if (obj.vpc[0].net.length != 0) {
				initSubnetExtends(obj.vpc[0].net[0]);
				$scope.currentNet = obj.vpc[0].net[0];
			}
		}

		if (obj.imageType[0].Os.length != 0) {
			$scope.currentOs = obj.imageType[0].Os[0];
			if (obj.imageType[0].Os[0].types.length != 0) {
				$scope.currentOsTypes = obj.imageType[0].Os[0].types[0];
				$scope.changeCharging($scope.buyTyle);
			}
		}
		//loadSubnetPublic(obj.vpc[0].vpCode);
	}

	$scope.changeSet = function(obj) {

		$scope.currentSet = obj;

		if (obj.rom.length != 0) {
			$scope.currentRom = obj.rom[0];
			$scope.currentSetCode = obj.rom[0].setCode;
			$scope.changeCharging($scope.buyTyle);
		}
	}

	$scope.changeRom = function(obj) {

		$scope.currentRom = obj;
		$scope.currentSetCode = obj.setCode;
		$scope.changeCharging($scope.buyTyle);

	}

	$scope.changeVpc = function(obj) {

		$scope.currentVpc = obj;
		if (obj.net.length != 0) {
			initSubnetExtends(obj.net[0]);
			$scope.currentNet = obj.net[0];
		}
		// console.log(obj);
		//loadSubnetPublic(obj.vpCode);
	}

	$scope.changeNet = function(obj) {
		initSubnetExtends(obj);
		$scope.currentNet = obj;
	}

	$scope.changeImageType = function(obj){
		$scope.currentImageType = obj;
		if(obj.Os.length != 0){
			$scope.currentOs = obj.Os[0];
			if (obj.Os[0].types.length != 0) {
				$scope.currentOsTypes = obj.Os[0].types[0];
				$scope.changeCharging($scope.buyTyle);

			}
		}
	}

	$scope.changeOs = function(obj) {
		$scope.currentOs = obj;
		if (obj.types.length != 0) {
			$scope.currentOsTypes = obj.types[0];
			$scope.changeCharging($scope.buyTyle);
		}
	}
	$scope.changeOsTypes = function(obj) {

		$scope.currentOsTypes = obj;
		$scope.changeCharging($scope.buyTyle);
	}

	$scope.cpuFilter = function (item) {
		return item.cpu === $scope.currentSet.cpu;
	};

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}

	$scope.changeCharging = function(obj) {
		$scope.buyTyle= obj;
		if(obj==1){
			$scope.unit = "元/天";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceDay;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceDay;
			}

			console.log($scope.currentRom);
		}else if(obj==2){
			$scope.unit = "元/月";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceMonth;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceMonth;
			}

		}else if(obj==4){
			$scope.unit = "元/年";
			if($scope.currentRom!=""){
				$scope.instancePrice=$scope.currentRom.priceYear;
			}
			if($scope.currentOsTypes!=""){
				$scope.imagePrice=$scope.currentOsTypes.priceYear;
			}

		}

	}
}

function securityGroupService($scope,$http){
	$http.get("/console/SecurityGroup/createSecurityGroupArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;
			$scope.virtuals = data[0].area[0].virtual;
			if(data[0].area[0].virtual.length > 0){
				$scope.currentVirtual = data[0].area[0].virtual[0];
			}
			if (data[0].area[0].vpc.length != 0) {
				$scope.currentVpc = data[0].area[0].vpc[0];
				
			}

		}
	);

	$scope.changeVpc = function(obj) {

		$scope.currentVpc = obj;
		if (obj.net.length != 0) {
			initSubnetExtends(obj.net[0]);
			$scope.currentNet = obj.net[0];
		}

	}
	$scope.changeHost = function(obj) {
		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.routers = obj.area[0].router;
		$scope.virtuals = obj.area[0].virtual;


	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		if(obj.virtual.length > 0){
			$scope.currentVirtual = obj.virtual[0];
		}

		$scope.routers = obj.router;
		$scope.virtuals = obj.virtual;

	}
	$scope.changeVirtual = function(obj) {
		$scope.currentVirtual = obj;
	}

	$scope.$watch('currentCompany', function(newValue, oldValue, scope) {
		if (newValue != null && newValue.name == '索贝') {
			angular.element('#virtual').css('display', 'block');
		} else {
			angular.element('#virtual').css('display', 'none');
		}
	})
}
function subnetListService($scope, $http) {
	$http.get("/console/ajax/network/subnet/createSubnetArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.routers = data[0].area[0].router;
			$scope.virtuals = data[0].area[0].virtual;

			if(data[0].area[0].virtual.length > 0){
				$scope.currentVirtual = data[0].area[0].virtual[0];
			}
		}
	);
	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		if(obj.area[0].virtual.length > 0){
			$scope.currentVirtual = obj.area[0].virtual[0];
		}

		$scope.routers = obj.area[0].router;
		$scope.virtuals = obj.area[0].virtual;
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;
		if(obj.virtual.length > 0){
			$scope.currentVirtual = obj.virtual[0];
		}

		$scope.routers = obj.router;
		$scope.virtuals = obj.virtual;

	}
	$scope.changeRouter = function(obj) {

		$scope.routerName = obj.name;
		$scope.routerId = obj.id;
		if (obj.cidr != "") {
			var array = split(obj.cidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];
		} else {
			$scope.ip0 = "";
			$scope.ip1 = "";
			$scope.ip2 = "";
			$scope.ip3 = "";
		}
	}
	$scope.changeVirtual = function(obj) {
		$scope.currentVirtual = obj;
	}

	$scope.$watch('currentCompany', function(newValue, oldValue, scope) {
		if (newValue != null && newValue.name == '索贝') {
			angular.element('#virtual').css('display', 'block');
		} else {
			angular.element('#virtual').css('display', 'none');
		}
	})
}


function storeListService($scope, $http) {
	$http.get("/console/ajax/fics/fics/createStroArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;
			$scope.storeType = data[0].area[0].storeType;
			if (data[0].area[0].storeType.length != 0) {
				$scope.currentStoreType = data[0].area[0].storeType[0];
				$scope.currentStore = data[0].area[0].storeType[0].store[0];
				$scope.store = data[0].area[0].storeType[0].store;
			} else {
				$scope.store = "";
			}
		}
	);
	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;
		$scope.storeType = obj.area[0].storeType;
		if (obj.area[0].storeType.length != 0) {
			$scope.currentStoreType = obj.area[0].storeType[0];
			$scope.store = obj.area[0].storeType[0].store;
			$scope.currentStore = obj.area[0].storeType[0].store[0];
		} else {
			$scope.store = "";
		}
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;
		$scope.storeType = obj.storeType;
		if (obj.storeType.length != 0) {
			$scope.currentStoreType = obj.storeType[0];
			$scope.store = obj.storeType[0].store;
			$scope.currentStore = obj.storeType[0].store[0];
		} else {
			$scope.store = "";
		}
	}

	$scope.changeStoreType = function(obj) {
		$scope.store = obj.store;
		$scope.currentStore = obj.store[0];
		$scope.currentStoreType = obj;
	}
	$scope.changeStore = function(obj) {
		$scope.currentStore = obj;
	}
}

function eipListService($scope, $http) {
	$http.get("/console/ajax/network/eip/createEIPArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			if (data[0].area[0].vpc.length != 0) {
				$scope.currentVpc = data[0].area[0].vpc[0];
			}

			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentPriceId = 1;
			
			$scope.currentTotalPrice = $scope.currentPrice.price;
		}
	);
	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		if (obj.area[0].vpc.length != 0) {
			$scope.currentVpc = obj.area[0].vpc[0];
		}

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];
		var size = $("#amount").val();
	    var totalPrice = $scope.currentPrice.price * size;
        $('#totalPrice').html(totalPrice);
        $('#txtprice_total').val(totalPrice);
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		if (obj.vpc.length != 0) {
			$scope.currentVpc = obj.vpc[0];
		}
	}

	$scope.changeVpc = function(obj) {
		$scope.currentVpc = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
		var size = $("#amount").val();
	    var totalPrice = $scope.currentPrice.price * size;
        $('#totalPrice').html(totalPrice);
        $('#txtprice_total').val(totalPrice);
	}
}

function routerListService($scope, $http) {
	$http.get("/console/ajax/network/router/createRouterArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentPriceId = 1;
		}
	);

	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCompanyCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];

	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		// $scope.currentFireWall = obj.firewall[0];
		// $scope.currentFireWallId = obj.firewall[0].id;

	}

	$scope.changeFirewallTemplateInfo = function(obj) {
		$scope.currentTemplateInfo = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}

}

function desktopListService($scope, $http) {
	$http.get("/console/ajax/desktop/desktop/createDesktopArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.currentSoftware = data[0].area[0].setsoftwareInfo[0];
			$scope.currentSoftwareInfo = data[0].area[0].setsoftwareInfo[0].softwareInfo[0];

			$scope.currentStyle = data[0].area[0].setsoftwareInfo[0].softwareInfo[0].pricelist[0];
			$scope.currentInterval = data[0].area[0].setsoftwareInfo[0].softwareInfo[0].pricelist[0].list[0];

			$scope.currentVpc = data[0].area[0].vpc[0];
			$scope.currentNet = data[0].area[0].vpc[0].net[0];
			initSubnetExtends($scope.currentNet);

			var array = split(data[0].area[0].vpc[0].net[0].netcidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];

			// console.log(data[0].area[0].vpc[0]);
			loadSubnetPublic(data[0].area[0].vpc[0].vpcCode);
		}
	);
	$scope.changeHost = function(obj) {
		$scope.currentCompany = obj.company;
		$scope.currentCompanyCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.currentSoftware = obj.area[0].setsoftwareInfo[0];
		$scope.currentSoftwareInfo = obj.area[0].setsoftwareInfo[0].softwareInfo[0];

		$scope.currentVpc = obj.area[0].vpc[0];
		if(obj.area[0].vpc[0]!=undefined){
			$scope.currentNet = obj.area[0].vpc[0].net[0];
			initSubnetExtends($scope.currentNet);
			var array = split(obj.area[0].vpc[0].net[0].netcidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];

			loadSubnetPublic(obj.area[0].vpc[0].vpcCode);
		}

	}
	$scope.changeArea = function(obj) {
		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		$scope.currentSoftware = obj.setsoftwareInfo[0];
		$scope.currentSoftwareInfo = obj.setsoftwareInfo[0].softwareInfo[0];

		$scope.currentVpc = obj.vpc[0];
		if(obj.vpc[0]!=undefined){
			$scope.currentNet = obj.vpc[0].net[0];
			initSubnetExtends($scope.currentNet);
			var array = split(obj.vpc[0].net[0].netcidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];
			loadSubnetPublic(obj.vpc[0].vpcCode);
		}
	}

	$scope.changeSoftware = function(obj) {
		$scope.currentSoftware = obj;
		$scope.currentSoftwareInfo = obj.softwareInfo[0];
	}

	$scope.changeSoftwareInfo = function(obj) {
		$scope.currentSoftwareInfo = obj;
	}

	$scope.changeVpc = function(obj) {

		$scope.currentVpc = obj;
		$scope.currentNet = obj.net[0];
		initSubnetExtends($scope.currentNet);
		if (obj.net[0] != undefined) {
			var array = split(obj.net[0].netcidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];
		}

		loadSubnetPublic(obj.vpcCode);
	}

	$scope.changeNet = function(obj) {
		$scope.currentNet = obj;
		initSubnetExtends($scope.currentNet);
		var array = split(obj.netcidr);
		$scope.ip0 = array[0];
		$scope.ip1 = array[1];
		$scope.ip2 = array[2];
		$scope.ip3 = array[3];
	}
	
	$scope.changeStyle = function(obj){
		$scope.currentStyle = obj;
		$scope.currentInterval = obj.list[0];
	}
	$scope.changeInterval = function(obj){
		$scope.currentInterval = obj;
	}

}

function desktopInitListService($scope, $http) {
    $http.get("/console/ajax/desktop/desktop/createDesktopInitArray.json").success(
        function(data) {
            $scope.hostList = data;

            $scope.currentCompany = data[0].company;
            $scope.currentCompanyCode = data[0].company.companyCode;

            $scope.currentArea = data[0].area[0];
            $scope.currentAreaCode = data[0].area[0].areaCode;

            $scope.currentSoftware = data[0].area[0].setsoftwareInfo[0];
            $scope.currentSoftwareInfo = data[0].area[0].setsoftwareInfo[0].softwareInfo[0];

            $scope.currentStyle = data[0].area[0].setsoftwareInfo[0].softwareInfo[0].pricelist[0];
            $scope.currentInterval = data[0].area[0].setsoftwareInfo[0].softwareInfo[0].pricelist[0].list[0];

            $scope.currentVpc = data[0].area[0].vpc[0];
            $scope.currentNet = data[0].area[0].vpc[0].net[0];
            initSubnetExtends($scope.currentNet);

            var array = split(data[0].area[0].vpc[0].net[0].netcidr);
            $scope.ip0 = array[0];
            $scope.ip1 = array[1];
            $scope.ip2 = array[2];
            $scope.ip3 = array[3];

            // console.log(data[0].area[0].vpc[0]);
            loadSubnetPublic(data[0].area[0].vpc[0].vpcCode);
        }
    );
    $scope.changeHost = function(obj) {
        $scope.currentCompany = obj.company;
        $scope.currentCompanyCode = obj.company.companyCode;

        $scope.currentArea = obj.area[0];
        $scope.currentAreaCode = obj.area[0].areaCode;

        $scope.currentSoftware = obj.area[0].setsoftwareInfo[0];
        $scope.currentSoftwareInfo = obj.area[0].setsoftwareInfo[0].softwareInfo[0];

        $scope.currentVpc = obj.area[0].vpc[0];
        if(obj.area[0].vpc[0]!=undefined){
            $scope.currentNet = obj.area[0].vpc[0].net[0];
            initSubnetExtends($scope.currentNet);
            var array = split(obj.area[0].vpc[0].net[0].netcidr);
            $scope.ip0 = array[0];
            $scope.ip1 = array[1];
            $scope.ip2 = array[2];
            $scope.ip3 = array[3];

            loadSubnetPublic(obj.area[0].vpc[0].vpcCode);
        }

    }
    $scope.changeArea = function(obj) {
        $scope.currentArea = obj;
        $scope.currentAreaCode = obj.areaCode;

        $scope.currentSoftware = obj.setsoftwareInfo[0];
        $scope.currentSoftwareInfo = obj.setsoftwareInfo[0].softwareInfo[0];

        $scope.currentVpc = obj.vpc[0];
        if(obj.vpc[0]!=undefined){
            $scope.currentNet = obj.vpc[0].net[0];
            initSubnetExtends($scope.currentNet);
            var array = split(obj.vpc[0].net[0].netcidr);
            $scope.ip0 = array[0];
            $scope.ip1 = array[1];
            $scope.ip2 = array[2];
            $scope.ip3 = array[3];
            loadSubnetPublic(obj.vpc[0].vpcCode);
        }
    }

    $scope.changeSoftware = function(obj) {
        $scope.currentSoftware = obj;
        $scope.currentSoftwareInfo = obj.softwareInfo[0];
    }

    $scope.changeSoftwareInfo = function(obj) {
        $scope.currentSoftwareInfo = obj;
    }

    $scope.changeVpc = function(obj) {

        $scope.currentVpc = obj;
        $scope.currentNet = obj.net[0];
        initSubnetExtends($scope.currentNet);
        if (obj.net[0] != undefined) {
            var array = split(obj.net[0].netcidr);
            $scope.ip0 = array[0];
            $scope.ip1 = array[1];
            $scope.ip2 = array[2];
            $scope.ip3 = array[3];
        }

        loadSubnetPublic(obj.vpcCode);
    }

    $scope.changeNet = function(obj) {
        $scope.currentNet = obj;
        initSubnetExtends($scope.currentNet);
        var array = split(obj.netcidr);
        $scope.ip0 = array[0];
        $scope.ip1 = array[1];
        $scope.ip2 = array[2];
        $scope.ip3 = array[3];
    }

    $scope.changeStyle = function(obj){
        $scope.currentStyle = obj;
        $scope.currentInterval = obj.list[0];
    }
    $scope.changeInterval = function(obj){
        $scope.currentInterval = obj;
    }

}

function elbService($scope, $http) {
	$http.get("/console/ajax/network/hosts/createHostsArray.json").success(
		// $http.get("/json/data-new.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;

			$scope.currentVpc = data[0].area[0].vpc[0];
			$scope.currentNet = data[0].area[0].vpc[0].net[0];

			$scope.currentEip = data[0].area[0].eip[0];

			$scope.priceList =  data[0].company.price;
			$scope.currentPrice = data[0].company.price[0];
			$scope.currentPriceId = 1;
		}
	);

	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;

		$scope.priceList = obj.company.price;
		$scope.currentPrice = obj.company.price[0];

		$scope.currentVpc = obj.area[0].vpc[0];
		$scope.currentNet = obj.area[0].vpc[0].net[0];

		$scope.currentEip = obj.area[0].eip[0];
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;

		$scope.currentVpc = obj.vpc[0];
		$scope.currentNet = obj.vpc[0].net[0];

		$scope.currentEip = obj.eip[0];
	}

	$scope.changeVpc = function(obj) {

		$scope.currentVpc = obj;
		$scope.currentNet = obj.net[0];

	}

	$scope.changeNet = function(obj) {

		$scope.currentNet = obj;
	}

	$scope.changeEip = function(obj) {

		$scope.currentEip = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}
}

function storeListService($scope, $http) {
	$http.get("/console/ajax/fics/fics/createStroArray.json").success(
		function(data) {
			$scope.hostList = data;

			$scope.currentCompany = data[0].company;
			$scope.currentCompanyCode = data[0].company.companyCode;

			$scope.currentArea = data[0].area[0];
			$scope.currentAreaCode = data[0].area[0].areaCode;
			$scope.storeType = data[0].area[0].storeType;

			$scope.priceList =  data[0].area[0].storeType[0].price;
			$scope.currentPrice = data[0].area[0].storeType[0].price[0];
			$scope.currentPriceId = 1;

			if (data[0].area[0].storeType.length != 0) {
				$scope.currentStoreType = data[0].area[0].storeType[0];
				$scope.currentStore = data[0].area[0].storeType[0].store[0];
				$scope.store = data[0].area[0].storeType[0].store;
			} else {
				$scope.store = "";
			}
		}
	);
	$scope.changeHost = function(obj) {

		$scope.currentCompany = obj.company;
		$scope.currentCode = obj.company.companyCode;

		$scope.currentArea = obj.area[0];
		$scope.currentAreaCode = obj.area[0].areaCode;
		$scope.storeType = obj.area[0].storeType;
		if (obj.area[0].storeType.length != 0) {
			$scope.currentStoreType = obj.area[0].storeType[0];
			$scope.store = obj.area[0].storeType[0].store;
			$scope.currentStore = obj.area[0].storeType[0].store[0];

			$scope.priceList =  obj.area[0].storeType[0].price;
			$scope.currentPrice = obj.area[0].storeType[0].price[0];
			$scope.currentPriceId = 1;
		} else {
			$scope.store = "";
		}
	}

	$scope.changeArea = function(obj) {

		$scope.currentArea = obj;
		$scope.currentAreaCode = obj.areaCode;
		$scope.storeType = obj.storeType;
		if (obj.storeType.length != 0) {
			$scope.currentStoreType = obj.storeType[0];
			$scope.store = obj.storeType[0].store;
			$scope.currentStore = obj.storeType[0].store[0];

			$scope.priceList =  obj.storeType[0].price;
			$scope.currentPrice = obj.storeType[0].price[0];
			$scope.currentPriceId = 1;
		} else {
			$scope.store = "";
		}
	}

	$scope.changeStoreType = function(obj) {
		$scope.store = obj.store;
		$scope.currentStore = obj.store[0];
		$scope.currentStoreType = obj;

		$scope.priceList =  obj.price;
		$scope.currentPrice = obj.price[0];
		$scope.currentPriceId = 1;
	}
	$scope.changeStore = function(obj) {
		$scope.currentStore = obj;
	}

	$scope.changePrice = function(obj) {
		$scope.currentPrice=obj;
	}
}

function split(str) {
	var array = str.split(".");
	var index = array[array.length - 1].indexOf("/");
	array[array.length - 1] = array[array.length - 1].substring(0, index);
	return array;
}