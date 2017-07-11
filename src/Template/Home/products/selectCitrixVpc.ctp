<!-- 云桌面 新增 -->
<div class="index-breadcrumb">
    <ol class="breadcrumb">
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'category'])?>">商品列表</a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'goods'])?><?php if(isset($this_good_cate)){echo '/',$this_good_cate['id'];}?>"><?php if(isset($this_good_cate)){echo $this_good_cate['name'];}?></a></li>
        <li><a href="<?= $this->Url->build(['controller'=>'home','action'=>'products'])?><?php if(isset($this_good_info)){echo '/',$this_good_info['id'];}?>"><?php if(isset($this_good_info)){echo $this_good_info['name'];}?></a></li>
        <li class="active">商品配置</li>
    </ol>
</div>
<div class="wrap-nav-right " ng-app>
	<div class="container-wrap  wrap-buy">
		
		<div class="clearfix buy-theme" ng-controller="desktopListService">
			<div class="pull-left theme-left">
				<div class="">
					<table>
						<input type="hidden" value='<?= $goods_id ?>' id="txtgoods_id"></input>
						
						<tr>
							<td class="row-message-left">基本设置</td>
							<td class="row-message-right" colspan="3">
								<div class="ng-binding">
									<div class="clearfix">
										<label for="" class="pull-left ">实例名称：</label>
										<div class="bk-form-row-cell">
											<input ng-model="firstName" type="text" ng-init="firstName=''" id="name" maxlength="15" />
											<span class="text-danger txtname"></span>
											<p><i class="icon-info-sign"></i>&nbsp;实例名称由主名、短横线、数字序号构成，其中，主名只能由8个英文字母构成，序号范围从1到999;例：abcdefgh-12</p>
										</div>
									</div>

								</div>
							</td>
						</tr>
						
						<tr >
                            <td class="row-message-left">网络设置</td>
                            <td class="row-message-right">
                                <div class="ng-binding">

                                    <div class="clearfix network-tab">
                                        <label for="" class="pull-left"></label>
                                        <div class="bk-form-row-cell">
                                            <ul class="clearfix city">
                                                <li id="subnet_default" class="active">默认网络 </li>
                                                <li id="subnet_extend_menu" class="hide">扩展网络</li>
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="network-box">
                                        <!--默认-->
                                        <div class="">
                                            <div class="clearfix">
                                                <label style="width:20%" for="" class="pull-left ">VPC:</label>
                                                <div style="width:80%" class="bk-form-row-cell">
                                                    <select ng-options="vpc.name for vpc in vpcList" ng-init="vpc=currentVpc" ng-model="currentVpc"  ng-change="changeVpc(currentVpc)" id="vpc">
                                                    </select>
                                                    <span class="text-danger" id="vpc-warning"></span>
                                                </div>
                                            </div>
                                            <div class="clearfix">
												<label for="" class="pull-left ">选择网络:</label>
												<div class="bk-form-row-cell">
										    		<div ng-repeat="vpc in vpcList | filter: {vpcCode : currentVpc.vpcCode}">
														<select ng-options="net.name for net in vpc.net" ng-model="net" ng-init="net = currentNet" ng-change="changeNet(net)"></select>
														<p ng-repeat="net in vpc.net | filter: net.netCode : currentNet.netCode"><i class="icon-info-sign"></i>&nbsp;该子网使用{{net.isFusion}}技术</p>
													</div>
												</div>
											</div>
											
                                            <!--<div class="clearfix">-->
												<!--<label for="" class="pull-left ">IP分配方式:</label>-->
												<!--<div class="bk-form-row-cell bk-form-row-small" >-->
													<!--<ul class="clearfix city" style="margin-bottom:10px;" id="netIp">-->
														<!--<li class="active">自动分配</li>-->
														<!--&lt;!&ndash;<li>自定义分配</li>&ndash;&gt;-->
													<!--</ul>-->
													<!--<div id="netIpInfo" style="display: none;">-->
														<!--<input type="text" ng-model="ip0" disabled="disabled" />-->
														<!--&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;-->
														<!--<input type="text" ng-model="ip1" disabled="disabled" />-->
														<!--&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;-->
														<!--<input type="text" ng-model="ip2" disabled="disabled" />-->
														<!--&nbsp;&nbsp;<strong>·</strong>&nbsp;&nbsp;-->
														<!--<input type="text" ng-model="ip3" disabled="true" id="ip3" />-->
													<!--</div>-->
												<!--</div>-->
											<!--</div>-->
                                        </div>
                                        <!--扩展-->
                                        <div class="bk-form-row-cell " style="display: none;">
                                            <table id="subnet-extend-table" data-toggle="table"
										        data-pagination="true"
										        data-side-pagination="server"
										        data-locale="zh-CN"
										        data-click-to-select="true"
										        data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'getPublicSubnetExtend']); ?>"
										        data-unique-id="id"  class="network-table">
                                                <thead>
                                                <tr>
                                                    <th data-checkbox="true"></th>
                                                    <th data-field="vpc_name">VPC</th>
                                                    <th data-field="subnet_name">子网</th>
                                                </tr>
                                                </thead>
                                            </table>
                                            <p><i class="icon-info-sign"></i>&nbsp;通过勾选设置主机要连接的网络。</p>
                                        </div>
                                    </div>
                                </div>
                            </td>
                    	</tr>
					</table>
				</div>
			</div>


			<div class="pull-right theme-right">
				<div class="theme-right-mian ">
					<p class="theme-buy">当前配置 </p>
					<ul class="goods-detail">
                        <li>
                            <span class="deleft">部署区位：</span>
                            <span class="deright"><?= $version['region']['name']?></span>
                            <input type="hidden" id="version" value="{{version}}" name="">
                            <input type="hidden" id="priceId" value="{{priceId}}" name="">
                        </li>
                        <li>
                            <span class="deleft">所在VPC：</span>
                            <span class="deright">{{currentVpc.name}}</span>
                            <input id="txtvpc" type="hidden" value="{{currentVpc.name}}" code="{{currentVpc.vpcCode}}" />
                        </li>
                        <li>
                            <span class="deleft">所在子网：</span>
                            <span class="deright">{{currentNet.name}}</span>
                            <input id="txtnet" type="hidden" value="{{currentNet.name}}" code="{{currentNet.netCode}}" />
                        </li>
                        <li>
                            <span class="deleft">桌面名称：</span>
                            <span class="deright" id = "firstName">{{firstName}}</span>
                        </li>
                        <li>
							<span class="deleft">品牌：</span>
							<span class="deright"><?= $version['name']?></span>
						</li>
						<li>
							<span class="deleft" style="">规格：</span>
							<span class="deright"><?= $version['spec']['name']?></span>
						</li>
                        <li>
                            <span class="deleft">计算能力：</span>
                            <span class="deright">----------------------</span>
                        </li>
                        <li>
                            <span class="deleft">CPU：</span>
                            <span class="deright"><?= $version['spec']['instancetype']['cpu']?>核</span>
                        </li>
                        <li>
                            <span class="deleft">内存：</span>
                            <span class="deright"><?= $version['spec']['instancetype']['memory']?>GB</span>
                        </li>
                        <li>
                            <span class="deleft">GPU：</span>
                            <span class="deright"><?= $version['spec']['instancetype']['gpu']?>GB</span>
                        </li>
                        <li>
                            <span class="deleft">系统镜像：</span>
                            <span class="deright"><?= $version['spec']['image']['name']?></span>
                        </li>
                        <li>
                            <span class="deleft">添加网络：</span>
                            <span id="txtnetn22" class="deright" ></span>
                            <input id="txtnet22" type="hidden" value="" code="" />
                        </li>
                        <li>
                            <span class="deleft">单台原价：</span>
                            <span class="deright" id="imagePay"><?= $price['price']?><?= $price['unit']?></span>
                        </li>
                        <li>
                            
                            <span class="deright"></span>
                        </li>
					</ul>
					<button id="btnBuy"  class="btn btn-oriange">确认</button>
				</div>
			</div>
		</div>
	</div>
<?= $this->Html->css(['bootstrap-table.css']); ?>
<?php  $this->start('script_last'); ?>

<?=$this->Html->script(['bootstrap.js' , 'bootstrap-table.js', 'jquery-ui-1.10.0.custom.min.js']); ?>
	<script type="text/javascript">
function desktopListService($scope, $http) {
	$http.get("/console/ajax/desktop/desktop/createCitrixPublicArray?region=<?= $detail_data['value']?>&dept_id=<?php if(isset($config['dept_id'])){echo $config['dept_id'];}?>").success(
		function(data) {
			$scope.vpcList = data;
			$scope.currentVpc = data[0];
			$scope.currentNet = data[0].net[0];
			$scope.currentVpcCode = <?php if(isset($config['vpcCode'])) {echo "'".$config['vpcCode']."'";} else { ?> data[0].vpcCode <?php }?>;//赋值vpcCode
			$scope.currentNetCode = <?php if(isset($config['subnetCode'])) {echo "'".$config['subnetCode']."'";} else { ?> data[0].net[0].netCode <?php }?>;//赋值厂商
			$scope.firstName = <?php if(isset($config['ecsName'])) {echo "'".$config['ecsName']."'";} else { echo "''"; }?>;//赋值名称
			$scope.version = <?php if(isset($config['version'])) {echo "'".$config['version']."'";} else { echo "''"; }?>;//赋值版本
			$scope.priceId = <?php if(isset($config['priceId'])) {echo "'".$config['priceId']."'";} else { echo "''"; }?>;//赋值收费
			for (var v in data) {

				if (data[v].vpcCode == $scope.currentVpcCode) {
					$scope.currentVpc = data[v];
					for (var n in data[v].net) {
						if (data[v].net[n].netCode == $scope.currentNetCode) {
							$scope.currentNet = data[v].net[n];
						}
					}
				}

			}
			initSubnetExtends($scope.currentNet);
			var array = split(data[0].net[0].netcidr);
			$scope.ip0 = array[0];
			$scope.ip1 = array[1];
			$scope.ip2 = array[2];
			$scope.ip3 = array[3];
			loadSubnetPublic(data[0].vpcCode);
		}
	);


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
}			

function setSubnet(){
    $("#txtnet2").html($("#net2").find("option:selected").text());
    $("#txtnet22").val($("#net2").val());
}
function initVpc2(){
    $('#vpc2').html("<option value=''>不扩展</option>");
    $('#net2').html("");
}
function loadSubnetPublic(vpc){
    var h="";
    var h2="";
	vpc_txt = $("#vpc2  option:selected").text();
    if(vpc==''||vpc==null){
        vpc='';
    }
    // var date = new Date();
    $.ajax({
        type:"post",
        url:"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'hosts', 'loadSubnetPublic']); ?>"+"?d="+Date.parse(new Date()),
        data:{vpc:vpc},
        async:false,
        cache: false,
        success:function(data){
            data= $.parseJSON(data);
             h += "<option value=''  >不扩展</option>";
             $.each(data.vpc,function(i,n){
                if(i==0 && vpc !=''){
                    //<option value="4">$vpc$_822</option><option value="5">vpc[822]_fox</option><
                    h += "<option value='"+n.code+"' selected=\"selected\">"+n.name+"</option>";
                }else{
                    h += "<option value='"+n.code+"'>"+n.name+"</option>";
                }
             });

             $("#vpc2").html(h);
             vpc = $("#vpc2").val();
             if(vpc_txt!="不扩展"){
                $.each(data.vpc,function(i,n){
                if(n.code==vpc){
                        $.each(n.subnet,function(x,y){
                            if(i==x){
                                h2 += "<option value='"+y.code+"' selected=\"selected\">"+y.name+"</option>";
                            }else{
                                h2 += "<option value='"+y.code+"'>"+y.name+"</option>";
                            }
                        })
                    };
                 });

				$("#net2").html(h2);
             }else{
				h2 += "<option value=''></option>";
				$("#net2").html(h2);
			 }
            // alert(data)
        }
    });
    
}
		$(".pwd-tab").on("click","li",function(){
			$(".pwd-content").css("display","none");
			$(".pwd-content").eq($(this).index()).css("display","block");
			switch($(this).index()){
				case 0:{
					$("#ad").val(1);
					break;
				}
				case 1:{
					$("#ad").val(2);
					break;
				}
				default:{
					$("#ad").val("");
				}
			}
		});

		$("#netIp").on("click","li",function(){
			var state = !$("#ip3").prop("disabled");
			if(state){
				$("#netIpInfo").css("display","none");
			}else{
				$("#netIpInfo").css("display","block");
			}
			$("#ip3").prop("disabled",state);
		});


		$('.ng-tie').on('click','li',function(){
			if($(this).siblings().hasClass('active')){
				$(this).siblings().removeClass('active');
			}
			$(this).addClass('active');
		});




		$('#btnBuy').on('click',function(){
			
				addCar(false);
			

		});


//添加清单
function addCar(type){
	var value1 = true;

	if($('#name').val()==''||$('#name').val()==null){
		$(".txtname").html('请输入名称');
		$('#btnBuy').prop('disabled',false);
		return false;
	}

	$.ajax({
		type:"post",
		url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'Ajax','action'=>'desktop','desktop','checkDesktopName']); ?>",
		async:true,
		data:{
			name:$('#name').val(),
			num:1,
		},
		async:false,
	}).done(function(data){
		result = $.parseJSON(data);
		if(result==1){
			$(".txtname").html('已存在的桌面名称，请重新输入');
			$('#btnBuy').prop('disabled',false);
			value1 = false;
			return false;
		}
	});
	var goods_id=$("#txtgoods_id").val(); //id


	if(type){
		type=1;
	}else{
		type=0;
	}
	if(value1){
		var parames = new Array();
        parames['csid']             = $("#txtcsid").val();
        parames['ecsName']          = $("#name").val();
        parames['vpcCode']          = $("#txtvpc").attr("code");
        parames['vpcName']          = $("#txtvpc").val();
        parames['subnetCode']       = $("#txtnet").attr("code");
        parames['netName']          = $("#txtnet").val();
        parames['subnetCode2']      = $("#txtnet22").val();
        parames['netName2']          = $("#txtnetn22").html();
        parames['version']      	= $("#version").val();
        parames['priceId']      	= $("#priceId").val();

        <?php if(isset($config['order_good_id'])): ?>
        parames['order_good_id']           = '<?=$config['order_good_id']?>';
        <?php endif;?>

        var url;
        url = '<?= $url?>/'+<?= $goods_id?>;

        $.StandardPost(url,parames);

	}else{
		$('#btnBuy').prop('disabled',false);
	}

}

//post 表单
$.extend({
    StandardPost:function(url,args){
        var body = $(document.body),
            form = $("<form method='post'></form>"),
            input;
        form.attr({"action":url});
        for (arg in args)
        {
            input = $("<input type='hidden'>");
            input.attr({"name":arg});
            input.val(args[arg]);
            form.append(input);
        };

        form.appendTo(document.body);
        form.submit();
        document.body.removeChild(form[0]);
    }
});

$('.bk-form-row-cell').on('blur','input',function(){
	if($(this).val()!=''){
		$(this).next().html('');
	}
})

$("#subnet-extend-table").on('all.bs.table', function(e, row, $element) {
    var subnet_name = getRowId('name');
    var subnet_code = getRowId('code');
    $("#txtnet22").val(subnet_code);
    $("#txtnetn22").html(subnet_name);
})

function getRowId(type){
    var idlist = '';
    $("input[name='btSelectItem']:checkbox").each(function() {
        if ($(this)[0].checked == true) {
            //alert($(this).val());
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#subnet-extend-table').bootstrapTable('getRowByUniqueId', id);
            var delimiter = idlist =="" ? '':',';
            if (type == 'code') {
                idlist += delimiter + row.subnet_code;
            } else if (type == "name") {
                idlist += delimiter + row.subnet_name;
            } else {
                idlist += delimiter + row.id;
            }
        }
    });
    return idlist;
}
//        网络设置tab
$(".network-tab").on('click', 'li', function() {
    $(this).addClass("active");
    $(this).siblings().removeClass("active");
    var $tabIndex = $(this).index();
    var $table = $(".network-box>div").eq($tabIndex);
    $table.show();
    $table.siblings().hide();
});

function initSubnetExtends(obj){
    var subnet_code = obj.netCode;
    $.ajax({
        type:"post",
        url:"/console/ajax/network/hosts/ajaxExtendNetCardAllow",
        async:true,
        data:{
            subnet_code:subnet_code,
        },
        success: function (data) {
            data= $.parseJSON(data);
            $("input[name='btSelectItem']:checkbox").each(function() {
                if ($(this)[0].checked == true) {
                    $(this)[0].checked = false;
                }
            });
            // $("input[name='btSelectAll']:checkbox")[0].checked = false;
            $('#subnet_default').click();
            $("#txtnet22").val('');
            $("#txtnetn22").html('');
            if(data.allow == true){
                $('#subnet_extend_menu').removeClass('hide');
            }else{
                $('#subnet_extend_menu').addClass('hide');
            }

            <?php if (!empty($config['subnetCode2'])) {?>
            //设置初始化选中
            $("#subnet-extend-table").bootstrapTable("checkBy", {field:"subnet_code", values:["<?= $config['subnetCode2']?>"]})
            <?php }?>
        
        }
    });
}

</script>

<?php $this->end(); ?>