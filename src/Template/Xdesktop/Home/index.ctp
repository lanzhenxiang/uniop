<!-- 云桌面首页 -->
<style>
	#context-menu{
	    background-color:#fff;
	    border:1px solid #777;
		box-shadow:0px 0px 10px #777;
	}
	#context-menu a{
		color:#555;
		display:block;
		min-width:160px;
		padding:5px 10px;
	}
	#context-menu li{
		position:relative;
		font-size:12px;
	}
	#context-menu li:hover{
		background:#EBEBEB;
	}
	#context-menu li:hover .context-secondary{
		display:block;
	}
</style>
<div class="desktop-main clearfix">
	<!-- <div class="desktop-left pull-left">


		<div class="desktop-section" id="desktop-help" style="margin-top: 20px;">
			<div class="desktop-header">
				<h5>
					媒体桌面控件使用帮助
					<div class="pull-right">
						<i class="icon-refresh"></i>
						<i class="icon-chevron-down"></i>
			       </div>
		       </h5>
			</div>
			<div class="desktop-body">
				<p>
				首次使用媒体桌面前，需要下载安装桌面控件<br />
				下载<br />
				<a version="14.1.200.13" href="/clients/Windows/CitrixReceiver.exe" >[ 下载用于Windows的安装包 ]</a><br />
				<a minimumSupportedOSVersion="10.6" version="11.8.2.255309" href="/clients/Mac/CitrixReceiver.dmg" >[ 下载用于Mac OSX的安装包 ]</a><br />
				<a  href="https://itunes.apple.com/us/app/citrix-receiver/id363501921?mt=8#" >[ 下载用于IPad的安装包 ]</a><br />
				<a  href="/docs/云桌面安装使用手册_Windows.pdf" >[ 下载云桌面使用手册(Windows) ]</a><br />
				<a  href="/docs/云桌面安装使用手册_Mac.pdf" >[ 下载云桌面使用手册(Mac) ]</a><br />
				<a  href="/docs/云桌面安装使用手册_Ipad.pdf" >[ 下载云桌面使用手册(IPad) ]</a><br />
				下载完成后请双击或者右键启动安装。（安装前请先关闭杀毒软件，如360防火墙等）<br />
				安装完控件请重启浏览器。
				</p>

			</div>
		</div>
	</div> -->
	<div class="desktop-panel">
		<div id="desktop-login">
			<div class="desktop-header clearfix">
				<h5 class="pull-left" style="padding:10px 0">
					欢迎使用云桌面
		        </h5>
		        <div class="pull-right">
					<a href="<?= $this->Url->build("/faq/desktop") ?>" target="_blank"><button class="btn btn-primary" >使用帮助</button></a>
		        </div>
			</div>
			<div class="desktop-body">
			    <div class="ng-binding">

			    	<!-- <div class="clearfix">
			    		<h6 class="pull-left">VPC分类</h6>
			    		<div class="bk-form-row-cell">
			    			<ul class="clearfix city" id="vpc-type" >
			    				<li class="active" data-vpc = "all">全 部</li>

			    				<?php
			    				    if (isset($vpc_list)&&!empty($vpc_list)){
			    				        foreach ($vpc_list as  $_vpc => $_hname){
			    				?>
			    				<li data-vpc ="<?= isset($vpcs[$_vpc])?$vpcs[$_vpc]:$_vpc ?>"><?= isset($vpcs[$_vpc])?$vpcs[$_vpc]:$_vpc ?></li>
			    				<?php
			    				        }
			    				    }
			    				?>

			    			</ul>
			    		</div>
			    	</div> -->

					<div class="clearfix">
						<h6 class="pull-left">桌面类型</h6>
					    <div class="bk-form-row-cell">
							<ul class="clearfix city" id="os-type" >
								<li class="active" data-target = "all">全 部</li>
								<?php
								$charts = [];
								$charts['labels']=[];
								$charts['datasets']=[];
								    if (isset($soft_lists)&&!empty($soft_lists)){
								        foreach ($soft_lists as $soft){
								?>
								<li data-target ="<?= $soft['software_code'] ?>"><?= $soft['software_name'] ?></li>
								<?php
								        $charts['labels'][] = $soft['software_code'];
								        $charts['datasets']['used'][$soft['software_code']] = 0;
								        $charts['datasets']['unused'][$soft['software_code']] = 0;
								        }
								    }
								?>
							</ul>
					    </div>
					</div>
					<div class="clearfix">
						<h6 class="pull-left">桌面状态</h6>
					    <div class="bk-form-row-cell" id="os-status">
							<ul class="clearfix city">
								<li class="active" data-state='1'>全 部</li>
								<li data-state='0'>空 闲</li>
								<li data-state='-1'>使用中</li>
							</ul>
					    </div>
					</div>

			    	<!-- <div class="clearfix">
			    		<h6 class="pull-left">证书下载</h6>
			    		<div class="bk-form-row-cell">
			    			<ul class="clearfix city" id="cer-download" >

			    				<?php
			    				    if (isset($vpc_list)&&!empty($vpc_list)){
			    				        foreach ($vpc_list as $_vpc =>$_hname ){
			    				?>
			    				<li data-target ="<?= $_vpc ?>" style="background:#44D2E4">
			    					<a target="lauchFrame" href="<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'home','action'=>'cer',urlencode($_hname),urlencode($_vpc),'_ext'=>'cer']) ?>" style="color:#fff">下载<?= isset($vpcs[$_vpc])?$vpcs[$_vpc]:$_vpc ?>证书</a>
			    				</li>
			    				<?php
			    				        }
			    				    }
			    				?>

			    			</ul>
			    		</div>
			    	</div> -->

				</div>
				<div class="desktop-login-list">
					<?php
					   if (isset($soft_lists)&&!empty($soft_lists)){
					       foreach ($soft_lists as $k => $v){
					?>
					<ul class="clearfix" id="<?= $v['software_code'] ?>">
						<?php
						  if (isset($_useable_lists[$v['software_code']])&&!empty($_useable_lists[$v['software_code']])){
						      foreach ($_useable_lists[$v['software_code']] as $kk =>$vv){
					?>
						<li id="host_name_<?= strtoupper($vv['hostname']) ?>" data-url="<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'home','action'=>'cer','_ext'=>'cer',urlencode($vv['code']),urlencode($vv['vpc'])]); ?>"  data-vpc="<?= isset($vpcs[$vv['vpc']])?$vpcs[$vv['vpc']]:$vv['vpc'] ?>" soft_code="<?= $v['software_code'] ?>" data-status="<?= intval($vv['connect_status']) ?>" class="software-context">
							<div class="desktop-list-info">
								<?php
								if ($vv['status']=='运行中'){
								?>
								<a href="<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'citrix','action'=>'launch','_ext'=>'ica',$vv['hostname']]) ?>" target="lauchFrame">
								<?php
								}else{
								?>
								<a href="#" class="poweroff">
								<?php
								}
								?>
								<div class="pull-left" style="margin-right:5px;">
									<?= $this->Html->image($icons_list[$v['software_code']],['width'=>80,'height'=>80]);  ?>
								</div>
								<div class="pull-left">
								<h5 style="margin-top:20px;padding:5px 0"><?= $vv['dispname'] ?></h5>

									<?php
									 if ($vv['status']=='运行中'){

									     if ($vv['connect_status']==0){
									         $charts['datasets']['unused'][$v['software_code']] += 1;
									 ?>
									 <p class="text-primary" id="desktop-list-status">空闲</p>
									 <?php
									     }else{
									         $charts['datasets']['used'][$v['software_code']] += 1;
									 ?>
									 <p  class="text-assist" id="desktop-list-status"><?= $vv['connect_user'] ?></p>
									 <?php
									     }

									 }else{
									?>
									<p class="text-primary" id="desktop-list-status"><?=$vv['status'] ?></p>
									<?php
									 }
									?>
								</div>
								</a>
							</div>
							<div class="desktop-list-operate" data-toggle="tooltip" data-placement="left" title="下载证书">
								<a download="<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'home','action'=>'cer','_ext'=>'cer',$vv['code'],$vv['vpc']]); ?>" href="<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'home','action'=>'cer','_ext'=>'cer',urlencode($vv['code']),urlencode($vv['vpc'])]); ?>"><i class="icon-download"></i></a>
							</div>
						</li>
					<?php
						      }
						  }
						?>
					</ul>
					<?php
					       }
					   }
					?>

				</div>
			</div>
		</div>
	</div>
</div>


	<div class="modal fade" id="download-modal">
	  <div class="modal-dialog" role="document">
	    <div class="modal-content">
	      <div class="modal-header">
	        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
	        <h4 class="modal-title" id="myModalLabel">下载文件</h4>
	      </div>
	      <div class="modal-body">
	        <a href="" id="download-link" download="" />下载文件</a>
	      </div>
	      <div class="modal-footer">
	        <button type="button" class="btn btn-default" data-dismiss="modal">关闭</button>
	      </div>
	    </div>
	  </div>
	</div>




<div style="width:0;height:0">
    <iframe id="lauchFrame" name="lauchFrame" src="" width=0  height=0 >
    </iframe>
</div>

<?= $this->Html->script('jquery.mouse-content.min.js'); ?>

<script>

	$('.bk-form-row-cell').on('click','li',function(){
		if($(this).siblings().hasClass('active')){
			$(this).siblings().removeClass('active');
		}
		$(this).addClass('active');
	});

	$('#os-type').on('click','li',function(){
		if($(this).attr('data-target')!="all"){
			$('.desktop-login-list ul').addClass('hide');
			var $this =  $("#" + $(this).attr('data-target'));
			$this.removeClass('hide');
			currentStatus($('#os-status li').filter('.active').attr('data-state'));
		}else{
			$('.desktop-login-list ul').removeClass('hide');
			currentStatus($('#os-status li').filter('.active').attr('data-state'));
		}
	});

	$("#os-status").on('click','li',function(){
		currentStatus($(this).attr('data-state'));
	});

	// function setCre(code,vpc){
	// 	$.ajax({
 //            url: "/xdesktop/home/cer/"+code+"/"+vpc+".cer",
 //            method: 'post',
 //            success: function(e) {
 //            	window.open(e);
 //            }
 //        });
	// }

	function currentStatus(state){
		var state = parseInt(state);
		switch(state){
			case 0 : {
				$('.desktop-login-list ul').not('.hide').children('li').addClass('hide');
				$('.desktop-login-list ul').not('.hide').children('li').each(
			    	function(){
			    		if($(this).attr('data-status')=='0'){
			    			$(this).removeClass('hide');
			    		}
			    	}
			    );
			    break;
			}
			case -1 : {
			    $('.desktop-login-list ul').not('.hide').children('li').addClass('hide');
		    	$('.desktop-login-list ul').not('.hide').children('li').each(
		    	function(){
			    		if($(this).attr('data-status')=='-1'){
			    			$(this).removeClass('hide');
			    		}
			    	}
			    );
			    break;
			}
			default : {
				$('.desktop-login-list ul').not('.hide').children('li').removeClass('hide');
			}
		}
	}


	$(function () {
	  $('[data-toggle="tooltip"]').tooltip()
	});


</script>

 <script>
        WEB_SOCKET_SWF_LOCATION = "/js/socket/WebSocketMain.swf";
        WEB_SOCKET_DEBUG = false;

        var tmpTag = 'https:' == document.location.protocol ? false : true;
        if( "https:" == document.location.protocol ){
          pro ="wss://"+window.location.host
        }else{
          pro = "ws://"+window.location.host
        }
      conn = new WebSocket(pro+"/ws?uid=<?=$tempUser?>");

      // Set event handlers.
      conn.onopen = function() {
        //alert("onopen");
      };
      conn.onmessage = function(e) {
        //更改状态
         var response = JSON.parse(e.data);
         response.Data.name = response.Data.name.toLocaleUpperCase();
		 if(response.Data.status != "0" ){
         	$('#host_name_' + response.Data.name ).find('#desktop-list-status').removeClass('text-primary').addClass('text-assist').html(response.Data.username);
		 }else{
			 $('#host_name_' + response.Data.name).find('#desktop-list-status').removeClass('text-assist').addClass('text-primary').html('空闲');
	     }
         //更新左侧统计状态代码
         console.log(response.Data);
         //获取分类
      };
      conn.onclose = function() {
       // alert("onclose");
      };
      conn.onerror = function() {
       // alert("onerror");
      };


    </script>

