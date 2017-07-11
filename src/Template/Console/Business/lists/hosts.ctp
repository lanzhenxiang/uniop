
    <div class="wrap-nav-right wrap-nav-right-left">
       <div style="margin:20px;background:#fff;color:#666;border:#d2d2d2;padding:3px 5px;">
    		<!--[if IE]>
    		  <object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
    		  style="width: 100%; height: 100%;">
    			您的浏览器不支持VMRC，请点击<a href="https://my.vmware.com/cn/web/vmware/details?downloadGroup=VMRC70&productId=491">下载</a>。
    		  </object>
    		<![endif] -->
    		<!--[if !IE]><!-->
    		<object id="vmrc" type="application/x-vmware-remote-console-2012" style="width: 100%; height: 100%;">
    		  您的浏览器不支持VMRC，请点击<a href="https://my.vmware.com/cn/web/vmware/details?downloadGroup=VMRC70&productId=491">下载</a>。
    		</object>
    		<!--<![endif]-->

       </div>

       <div class="wrap-manage">
    		 <!--[if IE]>
    		<object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
    		style="width: 100%; height: 100%;"></object>
    		<![endif] -->
    		<!--[if !IE]><!-->

    		<!--<![endif]-->
    		<div class="top">
    			<span class="title">业务系统ECS列表</span>
    			<div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
    			<div id="maindiv-alert"></div>
    		</div>
    		<div class="center clearfix">
    		<button class="btn btn-addition" onclick="refreshTable();">
    		  <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
    		</button>
    			<?php if (in_array('ccf_host_new', $this->Session->read('Auth.User.popedomname'))) { ?>
    		<a class="btn btn-addition" href="<?= $this -> Url -> build(['controller' => 'business', 'action' => 'add', 'hosts']) ?>">
    		<i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
    			<?php } ?>
    			<?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
    		<button class="btn btn-default" id="btnStart" disabled>
    		  <i class="icon-play "></i>&nbsp;&nbsp;启动
    		</button>
    			<?php } ?>
    			<?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
    		<button class="btn btn-default" id="btnStop" disabled>
    		  <i class="icon-off "></i>&nbsp;&nbsp;关机
    		</button>
    			<?php } ?>
                <button class="btn btn-default" id="btnDel" disabled>
              <i class="icon-remove "></i>&nbsp;&nbsp;删除
            </button>
    			<!-- <div class="dropdown">
    				<a href="#" class="dropdown-toggle btn btn-addition text-right"
    					data-toggle="dropdown" role="button" aria-haspopup="true"
    					aria-expanded="false"> <span class="pull-left">更多操作</span> <span
    					class="caret"></span>
    				</a>
    				<ul class="dropdown-menu">
    					<li id="btnreboot"><a href="javascript:void(0);"><i class="icon-refresh"></i>&nbsp;&nbsp;重启</a></li>
    					<li><a href="#"><i class="icon-tag"></i>&nbsp;&nbsp;绑定标签</a></li>
    					<li data-target="#disk-manage" data-toggle="modal"><a href="javascript:void(0);"><i class="icon-inbox"></i>&nbsp;&nbsp;加载硬盘</a></li>
    					<li><a href="#"><i class="icon-key"></i>&nbsp;&nbsp;加载SSH密钥</a></li>
    				</ul>
    			</div> -->
    			<div class="pull-right">
    			<input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
              <?php if (in_array('ccf_all_select_department', $this->Session->read('Auth.User.popedomname'))) { ?>
              <!--
              <div class="dropdown">
                租户
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="deparmets" val="<?= $_default["id"] ?>"><?= $_default["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                      <?php foreach($_deparments as $value) { ?>
                         <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                      <?php }?>
                </ul>
              </div>-->
              <?php }?>
              <!--
    			 	<div class="dropdown">
    			  	厂商
    					<a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
    						<span class="pull-left" id="agent" val="">全部</span>
    					<span class="caret"></span>
    				</a>
    				<ul class="dropdown-menu">
    					<li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
    					<?php if(isset($agent)){
    						foreach($agent as $value) {
    							if ($value['parentid'] == 0) {
    					?>
    						 <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
    					<?php }}} ?>
    				</ul>
    			</div>
    			<div class="dropdown">
    			 地域
    				<a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
    					<span class="pull-left" id="agent_t" val="">全部</span>
    					<span class="caret"></span>
    				</a>
    				<ul class="dropdown-menu" id="agent_two"></ul>
    			</div>-->
                <span class="search">
                    <input type="text" id="txtsearchbiz" name="search_biz" placeholder="搜索业务系统">
                    <i class="icon-search"></i>
                </span>
    			<span class="search">
    			    <input type="text" id="txtsearch" name="search" placeholder="搜索ECS">
    		        <i class="icon-search"></i>
    		    </span>
    				<!-- <button class="btn btn-addition dropdown" role="presentation">
    					<a href="#" class="dropdown-toggle" data-toggle="dropdown"><i
    						class="icon-tag"></i>&nbsp;&nbsp;标签</a>
    					<ul class="dropdown-menu">
    						<li><a href="#">测试环境</a></li>
    						<li><a href="#">只是环境</a></li>
    					</ul>
    				</button> -->
    			</div>
    		</div>
    		<div class="bot ">
    			<table id="table" data-toggle="table"
    	 data-pagination="true" 
    	 data-side-pagination="server"
    	 data-locale="zh-CN"
    	 data-click-to-select="true"
    	 data-url="<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'business', 'hosts', 'lists','?'=>['department_id'=>$_default["id"]]]); ?>"
    	 data-unique-id="H_ID">
    	 <thead>
    	   <tr>
    		<th data-checkbox="true"></th>
    		<th data-field="H_ID" >Id</th>
            <th data-field="T_Name"  >业务系统名称</th>
            <th data-field="T_Version"  >版本</th>
            <th data-field="T_System_level" >系统规模</th>
    		<th data-field="H_Code"  data-formatter="formatter_main">主机Code</th>
    		<th data-field="H_Code" data-formatter="formatter_code">登录</th>
    		<th data-field="" data-formatter="chart_statics">统计</th>
    		<th data-field="H_Name">主机名称</th>
    		<th data-field="H_Status" data-formatter="formatter_state">状态</th>
    		<th data-field="D_Os_Form" data-formatter="formatter_operateSystem">操作系统</th>
    		<th data-field="E_Name">部署区位</th>
    		<th data-field="I_SubnetCode" data-formatter="formatter_vxnets">所属子网</th>
    		<th data-field="I_Ip" data-formatter="formatter_ip">主机IP</th>
    		<th data-field="" data-formatter="formatter_config">配置</th>
    		<th data-field="E_Ip" data-formatter="formatter_eip">公网IP</th>
    		<!-- <th data-field="-">警告状态</th> -->
    		<!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
    		<th data-field="H_time" data-formatter=timestrap2date>创建时间</th>
    	  </tr>
    	</thead>
      </table>
    </div>
    <!-- 图表 -->
    <div class="chart-range">
      <div class="chart-mask" ></div>
    <div class="chart-main">
      <p class="host-title"><span class="host-name">主机CODE：instance-zpSs1Dyq &nbsp;主机名称：nN-MPC-02 </span><span class="pull-right remove">×</span></p>
      <div class=" clearfix">
    	<div  class="chart-box pull-left">
    	  <p>CPU</p>
    	  <div>
    		<canvas id="canvas1" height="450" width="600"></canvas>
    	  </div>
    	  <p class="chart-title"><span></span> CPU使用率(%)</p>
    	</div>
      <!--   <div  class="chart-box pull-left">
    	  <p>内存</p>
    	  <div>
    		<canvas id="canvas2" height="450" width="600"></canvas>
    	  </div>
    	  <p class="chart-title"><span></span> CPU使用率(%)</p>
    	</div> -->
    	<div  class="chart-box pull-left">
    	  <p>磁盘</p>
    	  <div>
    		<canvas id="canvas3" height="450" width="600"></canvas>
    	  </div>
    	  <p class="chart-title"><span ></span> 读&nbsp;KBps &nbsp;<span class="line"></span> 写&nbsp;KBps</p>
    	</div>
    	<div  class="chart-box pull-left">
    	  <p>网络</p>
    	  <div>
    		<canvas id="canvas4" height="450" width="600"></canvas>
    	  </div>
    	   <p class="chart-title"><span ></span>出网&nbsp;KBps &nbsp;<span class="line"></span>入网&nbsp;KBps</p>
    	</div>
      </div>
    </div>
    </div>
    </div>
    </div>

    <!-- 右键弹框 -->
    <div class="context-menu" id="context-menu">
    	 <ul>
    		<?php if (in_array('ccf_host_startup', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="start"><a href="javascript:void(0);"><i class="icon-play"></i> 启动</a></li>
    		<?php } ?>
    		<?php if (in_array('ccf_host_shutdonw', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="close"><a href="javascript:void(0);"><i class="icon-off"></i> 关机</a></li>
    		<?php } ?>
    		<?php if (in_array('ccf_host_reboot', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="restart"><a href="javascript:void(0);"><i class="icon-refresh"></i> 重启</a></li>
    		<?php } ?>
    		<?php if (in_array('ccf_host_change', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="modify"><a href="javascript:void(0);"><i class="icon-pencil"></i> 修改</a></li>
    		<?php } ?>
    	<!-- <li><a href="javascript:void(0);"><i class="icon-tag"></i> 标签</a></li> -->
    		<?php if (in_array('ccf_host_add_disk', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="adddisks"><a href="javascript:void(0);"> <i class="icon-inbox"></i> 硬盘
    	</a>
    <!-- 			<ul class="context-secondary">
    				<li id="adddisks" ><a href="javascript:void(0);"><i class="icon-plus"></i> 加载</a></li>
    				<li><a href="javascript:void(0);"><i class="icon-minus"></i> 卸载</a></li>
    			</ul> -->
    	</li>
    		<?php } ?>
    	<!-- <li><a href="javascript:void(0);"><i class="icon-key"></i> SSH密钥</a></li> -->
    <!--     <li><a href="javascript:void(0);"><i class="icon-exchange"></i> 网络</a></li>
    	<li><a href="javascript:void(0);"><i class="icon-globe"></i> 公网IP</a></li> -->
    	<!-- <li><a href="javascript:void(0);"><i class="icon-tags"></i> 内网域名别名</a></li> -->
    	<!-- <li><a href="javascript:void(0);"><i class="icon-paste"></i> 制作成新映象</a></li> -->
    	<!-- <li><a href="javascript:void(0);"><i class="icon-camera"></i> 创建备份</a></li> -->
    		<!-- <li>
    			<a href="javascript:void(0);" class="context-primary">
    				<i class="icon-bell"></i>
    				告警策略
    		</a>
    			<ul class="context-secondary">
    				<li><a href="javascript:void(0);"><i class="icon-plus"></i> 绑定</a></li>
    				<li><a href="javascript:void(0);"><i class="icon-minus"></i> 解绑</a></li>
    			</ul></li> -->
    	  <!-- <li><a href="javascript:void(0);"><i class="icon-th-list"></i> 更多操作 </a></li> -->
    		<?php if (in_array('ccf_host_delete', $this->Session->read('Auth.User.popedomname'))) { ?>
    	  <li id="del"><a href="javascript:void(0);"><i class="icon-trash"></i> 删除</a></li>
    		<?php } ?>
    		<?php if (in_array('ccf_host_gen_image', $this->Session->read('Auth.User.popedomname'))) { ?>
    	  <li id="defined"><a href="javascript:void(0);"> <i class="icon-paste"></i> 自定义镜像</a></li>
    	<?php } ?>
    	<?php if (in_array('ccf_excp_list', $this->Session->read('Auth.User.popedomname'))) { ?>
    	<li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常日志</a></li>
    	<?php } ?>
    	<?php if (in_array('ccf_normal_list', $this->Session->read('Auth.User.popedomname'))) { ?>
            <li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常日志</a></li>
            <?php } ?>
    	</ul>
      </div>

      <!-- 修改 -->
      <div class="modal fade" id="modal-modify" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
    	<div class="modal-content">
    	 <div class="modal-header">
    	  <button type="button" class="close" data-dismiss="modal"
    	  aria-label="Close">
    	  <span aria-hidden="true">&times;</span>
    	</button>
    	<h5 class="modal-title">修改</h5>
      </div>
      <form id="modal-modify-form" action="" method="post">
    	<div class="modal-body">
    	  <div class="modal-form-group">
    		<label>名称:</label>
    		<div>
    		  <input id="modal-modify-name" name="name" type="text" maxlength="15" />
    		</div>
    	  </div>
    	  <div class="modal-form-group">
    		<label>描述:</label>
    		<div>
    		  <textarea id="modal-modify-description" name="description" rows="5"></textarea>
    		</div>
    	  </div>
    	  <input id="modal-modify-id" name="id" type="hidden" />
    	</div>
    	<div class="modal-footer">
    	 <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
    	 <button id="reseter" type="button" class="btn btn-danger"
    	 data-dismiss="modal">取消</button>
       </div>
     </form>
    </div>
    </div>
    </div>
    <!-- 自定义镜像 -->
      <div class="modal fade" id="modal-defined" tabindex="-1" role="dialog">
       <div class="modal-dialog" role="document">
    	<div class="modal-content">
    	 <div class="modal-header">
    	  <button type="button" class="close" data-dismiss="modal"
    	  aria-label="Close">
    	  <span aria-hidden="true">&times;</span>
    	</button>
    	<h5 class="modal-title">新建自定义镜像</h5>
      </div>
    	<form id="model-image-from" action="" method="post">
    	  <div class="modal-body">
    		<div class="modal-form-group">
    		  <label>镜像名称:</label>
    		  <div>
    			<input id="image_name" name="image_name" type="text" placeholder="请输入镜像名称"/>
    		  </div>
    		</div>
    		<div class="modal-form-group">
    		  <label>操作系统:</label>
    		  <div>
    			<input id="" name="name" type="os_family" value="Win7 64位中文版" />
    		  </div>
    		</div>
    		<div class="modal-form-group">
    		  <label>镜像类型:</label>
    		  <div>
    			<input id="" name="plat_form" type="text" value="收录主机"/>
    		  </div>
    		</div>
    		<div class="modal-form-group">
    		  <label>空间要求:</label>
    		  <div>
    			<input id="" name="smallest_space" type="text" value="20G" />
    		  </div>
    		</div>
    		<div class="modal-form-group">
    		  <label>镜像说明:</label>
    		  <div>
    			<textarea id="" name="image_note" rows="5" placeholder="用户备注信息"></textarea>
    		  </div>
    		</div>
    		<div class="modal-form-group">
    		  <p>温馨提示:</p>
    		  <p>1.dadada</p>
    		  <p>2.asa1</p>
    		</div>


    	  </div>
    	  <div class="modal-footer">
    	   <button id="" type="button" class="btn btn-primary">确认</button>
    	   <button id="" type="button" class="btn btn-danger"
    	   data-dismiss="modal">取消</button>
    	 </div>
       </form>
    </div>
    </div>
    </div>
    <!-- 硬盘 -->
    <div class="modal fade" id="disk-manage" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
    	<div class="modal-content">
    	  <div class="modal-header">
    		<button type="button" class="close" data-dismiss="modal"
    		aria-label="Close">
    		<span aria-hidden="true">&times;</span>
    	  </button>
    	  <h5 class="modal-title">主机硬盘管理</h5>
    	</div>
    	<input type="hidden" id="hostsCode"/>
    	<input type="hidden" id="hostsId"/>
    	<input type="hidden" id="txtvpcCode"/>
    	<input type="hidden" id="txtclass_code"/>
    	<input type="hidden" id="txtisFusion"/>
    	<div class="modal-body">
    	  <div class="modal-title-list">
    		<ul class="clearfix">
    		  <li class="active" no="1">添加硬盘</li>
    		  <li no="2">已用硬盘</li>
    		  <!-- <li no="3">未使用硬盘</li> -->
    		</ul>
    	  </div>
    	  <div class="modal-disk-content" style="display:block;">
    					<!-- <div class="progress">
    						<div class="progress-bar progress-bar-warning" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%">
    						</div>
    					</div>
    					<p class="tips">
    						<i class="icon-info-sign"></i>&nbsp;一台主机最多能添加10块硬盘,该主机已添加7块硬盘,还可以添加3块硬盘
    					  </p> -->
    					  <div class="modal-form-group">
    					   <label>硬盘名称:</label>
    					   <div>
    						<input id="txtdisks_name" type="text" onblur="if($(this).val()!=''){$('#name-warning').html('')}" />
    						<span class="text-danger" id="name-warning" style="font-size:12px;line-height:28px;margin-left:5px;"></span>
    					  </div>
    					</div>
    					<div class="modal-form-group">
    					  <label>容量大小:</label>
    					  <div class="slider-area">
    						<div id="slider"></div>
    					  </div>
    					  <div class="amount pull-left">
    						<input type="text" id="amount" placeholder="10" disabled="disabled"> GB
    					  </div>
    					</div>
    					<div class="modal-form-group">
    					  <label></label>
    					  <div>
    						<h6 class="warm">请输入范围10GB-1000GB</h6>
    					  </div>
    					</div>

    					<div class="modal-form-point">
    					  <!-- <p>总价格: <span class="text-primary">￥0.0115</span> 每小时 X 1 = ￥0.01 每小时 (合 ￥8.28 每月)</p> -->
    					</div>
    					<div class="modal-footer">
    					  <button onclick="btnaddDisks(null,this)" type="button" class="btn btn-primary">确认</button>
    					  <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
    					</div>
    				  </div>
    				  <div class="modal-disk-content" style="display:none">
    					<table id="use_table">
    					  <thead>
    						<tr>
    						  <th data-field="code" >硬盘Code</th>
    						  <th data-field="name">名称</th>
    						  <th data-field="capacity">容量(GB)</th>
    						  <th data-formatter="operateFormatter">操作</th>
    						</tr>
    					  </thead>
    					</table>
    					<div class="modal-footer">
    					  <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
    					</div>
    				  </div>
    				  <!--<div class="modal-disk-content">
    				  <table id="unuse_table" data-toggle="table"     data-pagination="true"
    	 data-side-pagination="server"
    	 data-locale="zh-CN"
    	 data-page-size="7"
    	 data-click-to-select="true"
    	 data-unique-id="id">
    					  <thead>
    						<tr>
    						  <th data-checkbox="true"></th>
    						  <th data-field="name">名称</th>
    						  <th data-field="code" >硬盘ID</th>
    						</tr>
    					  </thead>
    					</table>
    					<div class="modal-footer">
    					  <button type="button" id="btnattach" class="btn btn-primary">使用</button>
    					  <button type="button" class="btn btn-danger" data-dismiss="modal">关闭</button>
    					</div>
    				  </div>-->
    				</div>
    			  </div>
    			</div>
    		  </div>
    <div id="maindiv"></div>
    <!--<?php $this -> start('script_last'); ?>-->
<?= $this->Html->script(['network/hosts_list']); ?>
<?php  $this->end();?>