<!--  云桌面  列表 -->
<?= $this->Html->script(['validator.bootstrap.js']); ?>
<?= $this->element('desktop/lists/left',['active_action'=>'desktop']); ?>
<div class="wrap-nav-right">

  <div class="wrap-manage">
    <div class="top">
      <span class="title">云桌面列表</span>

      <div id="maindiv-alert"></div>
    </div>

    <div class="center clearfix">
      <button class="btn btn-addition" onclick="refreshTable();">
        <i class="icon-refresh"></i>&nbsp;&nbsp;刷新
      </button>
<!--       <?php if (in_array('ccf_desktop_new', $this->request->session()->read('Auth.User.popedomname'))) { ?>
      <a class="btn btn-addition"
      href="<?= $this->Url->build(['controller'=>'desktop','action'=>'add','desktop']) ?>"><i
      class="icon-plus"></i>&nbsp;&nbsp;新建</a>
      <?php } ?> -->
      <!-- 跨租户新建桌面 -->
          <?=$this->element('switchDepartment',['callback_url' => $this->Url->build(['controller' => 'desktop', 'action' => 'add', 'desktop']),'typeName'=>'云桌面'])?>
      <!-- 跨租户新建桌面 -->
      <?php if (in_array('ccf_desktop_startup', $this->request->session()->read('Auth.User.popedomname'))) { ?>
      <button class="btn btn-default" id="btnStart" disabled>
        <i class="icon-play "></i>&nbsp;&nbsp;启动
      </button>
      <?php } ?>
      <?php if (in_array('ccf_desktop_shutdonw', $this->request->session()->read('Auth.User.popedomname'))) { ?>
      <button class="btn btn-default" id="btnStop" disabled>
        <i class="icon-off "></i>&nbsp;&nbsp;关机
      </button>
      <?php } ?>
        <?php if (in_array('ccf_desktop_shutdonw', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <button class="btn btn-default" id="btnForceStop" disabled>
            <i class="icon-off "></i>&nbsp;&nbsp;强制关机
        </button>
        <?php } ?>
      <button class="btn btn-default" id="btnDel" disabled>
        <i class="icon-remove "></i>&nbsp;&nbsp;删除
      </button>
           <!--  <div class="dropdown">
                <a href="#" class="dropdown-toggle btn btn-addition text-right"
                data-toggle="dropdown" role="button" aria-haspopup="true"
                aria-expanded="false"> <span class="pull-left">更多操作</span> <span
                class="caret"></span>
            </a>
            <ul class="dropdown-menu">
              <li id="btnreboot"><a href="javascript:void(0);"><i class="icon-refresh"></i>&nbsp;&nbsp;重启</a></li> -->
              <!-- <li><a href="#"><i class="icon-tag"></i>&nbsp;&nbsp;绑定标签</a></li> -->
              <!-- <li><a href="#"><i class="icon-inbox"></i>&nbsp;&nbsp;加载硬盘</a></li> -->
              <!-- <li><a href="#"><i class="icon-key"></i>&nbsp;&nbsp;加载SSH密钥</a></li> -->
              <!-- </ul> -->
              <!-- </div> -->


              <div class="pull-right">
              <input type="hidden" id="txtdeparmetId" value="<?= $_default["id"] ?>" />
              <?php if (in_array('ccf_all_select_department', $this->request->session()->read('Auth.User.popedomname'))) { ?>
              <div class="dropdown">
                租户:
                <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button">
                    <span class="pull-left" id="deparmets" val="<?= $_default["id"] ?>"><?= $_default["name"] ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu">
                      <?php foreach($_deparments as $value) { ?>
                         <li><a href="#" onclick="departmentlist(<?php echo $value['id'] ?>,'<?php echo $value['name'] ?>')"><?php echo $value['name'] ?></a></li>
                      <?php }?>
                </ul>
              </div>
              <?php }?>
                <div class="dropdown">
                  厂商:
                  <a href="#" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent" val="">全部</span>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu">
                    <li><a href="javascript:;" onclick="local(0,'','全部')">全部</a></li>
                    <?php
                    if(isset($agent)){
                      foreach($agent as $value) {
                        if ($value['parentid'] == 0) {?>
                        <li><a href="#" onclick="local(<?php echo $value['id'] ?>,'<?php echo $value['class_code'] ?>','<?php echo $value['agent_name'] ?>')"><?php echo $value['agent_name'] ?></a></li>
                        <?php }
                      }
                    } ?>
                  </ul>
                </div>
                <div class="dropdown">
                  地域:
                  <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent_t" val="">全部</span>
                    <span class="caret"></span>
                  </a>
                  <ul class="dropdown-menu" id="agent_two"></ul>
                </div>
                <span class="search"><input type="text" id="txtsearch" name="search" placeholder="搜索">
                  <i class="icon-search"></i>
                </span>
            <!-- <button class="btn btn-addition dropdown" role="presentation">
                <a href="#" class="dropdown-toggle" data-toggle="dropdown"><i class="icon-tag"></i>&nbsp;&nbsp;标签</a>
                <ul class="dropdown-menu">
                    <li><a href="#">测试环境</a></li>
                    <li><a href="#">只是环境</a></li>
                </ul>
              </button> -->
            </div>
          </div>
          <ul id="myTab" class="nav nav-tabs" style="margin-left: 20px;">
                <li class="active" style="margin-right: 10px;">
                  <a href="#desktoplist"  data-toggle="tab">
                  基础信息列表</a>
                </li>
                <li>
                  <a href="#desktop_charge_list" data-toggle="tab">计费与优先级列表</a>
                </li>
          </ul>
          <div id="myTabContent" class="tab-content">
           <div class="tab-pane fade in active" id="desktoplist">
              <div class="bot ">
                <table id="table" data-toggle="table"
                data-pagination="true" 
                data-side-pagination="server"
                data-locale="zh-CN"
                data-click-to-select="true"
                data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists','?'=>['department_id'=>$_default['id']]]); ?>"
                data-unique-id="id">
                <thead>
                  <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id">Id</th>
                    <th data-field="code" data-formatter="formatter_main">云桌面Code</th>
                    <th data-field="code" data-formatter="formatter_code">登录</th>
                    <th data-field="name">桌面名称</th>
                    <th data-field="status" data-formatter="formatter_state">运行状态</th>
                    <th data-field="host_extend" data-formatter="formatter_connect_status">使用状态</th>
                    <th data-field="host_extend" data-formatter="formatter_operateSystem">操作系统</th>
                    <th data-field="location_name">部署区位</th>
                    <th data-field="vpc_name">所属vpc名称</th>
                    <th data-field="hosts_network_card" data-formatter="formatter_vxnets">子网</th>
                    <th data-field="hosts_network_card" data-formatter="formatter_ip">内网IP</th>
                    <th data-field="host_extend" data-formatter="formatter_config">配置</th>
                    <!-- <th data-field="-">警告状态</th> -->
                    <!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
                    <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                    <!-- <th data-field="create_date" >创建时间</th> -->
                  </tr>
                </thead>
              </table>
            </div>
           </div>
           <div class="tab-pane fade" id="desktop_charge_list">
              <div class="bot ">
                <table id="charge-table" data-toggle="table"
                data-pagination="true" 
                data-side-pagination="server"
                data-locale="zh-CN"
                data-click-to-select="true"
                data-url="<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktopCharge','lists','?'=>['department_id'=>$_default['id']]]); ?>"
                data-unique-id="id">
                <thead>
                  <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="id" >Id</th>
                    <th data-field="code" data-formatter="formatter_main">云桌面Code</th>
                    <th data-field="code" data-formatter="formatter_code">登录</th>
                    <th data-field="name">桌面名称</th>
                    <th data-field="status" data-formatter="formatter_state">运行状态</th>
                    <th data-field="host_extend" data-formatter="formatter_connect_status">使用状态</th>
                    <th data-field="location_name">部署区位</th>
                    <th data-field="host_extend" data-formatter="formatter_config">配置</th>
                    <th data-field="instance_charge" data-formatter="formatter_charge_mode">计费模式</th>
                    <th data-field="instance_charge" data-formatter="formatter_charge_price">计费单价</th>
                    <th data-field="priority">优先级</th>
                    <!-- <th data-field="-">警告状态</th> -->
                    <!-- <th data-field="modify_time" data-formatter=timestrap2date>备份于</th> -->
                    <!-- <th data-field="create_date">创建时间</th> -->
                    <th data-field="create_time" data-formatter=timestrap2date>创建时间</th>
                  </tr>
                </thead>
              </table>
            </div>
           </div>
        </div>

          
      </div>
    </div>

    <!-- 右键弹框 -->
    <div class="context-menu" id="context-menu">
      <ul>
        <?php if (in_array('ccf_desktop_startup', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="start" href="#"><i class="icon-play"></i> 启动</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_shutdonw', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="close" href="#"><i class="icon-off"></i> 关机</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_reboot', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="restart" href="#"><i class="icon-refresh"></i> 重启</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_change', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li id="modify"><a href="#"><i class="icon-pencil"></i> 修改</a></li>
        <?php } ?>
        <!-- <li><a href="#"><i class="icon-tag"></i> 标签</a></li> -->
        <?php if (in_array('ccf_desktop_add_disk', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li id="adddisks"><a href="#" > <i class="icon-inbox"></i> 硬盘
        </a>
        <!-- <ul class="context-secondary">
            <li><a href="#"><i class="icon-plus"></i> 加载</a></li>
            <li><a href="#"><i class="icon-minus"></i> 卸载</a></li>
          </ul> --></li>
          <?php } ?>
          <?php if (in_array('ccf_host_system_disk', $this->Session->read('Auth.User.popedomname'))) { ?>
          <li id="sysdisks"><a href="javascript:void(0);"> <i class="icon-inbox"></i> 系统盘扩容</a></li>
          <?php } ?>
        <!-- <li><a href="#"><i class="icon-key"></i> SSH密钥</a></li>
        <li><a href="#"><i class="icon-exchange"></i> 网络</a></li>
        <li><a href="#"><i class="icon-globe"></i> 公网IP</a></li>
        <li><a href="#"><i class="icon-tags"></i> 内网域名别名</a></li>
        <li><a href="#"><i class="icon-paste"></i> 制作成新映象</a></li>
        <li><a href="#"><i class="icon-camera"></i> 创建备份</a></li>
        <li><a href="#" class="context-primary"> <i class="icon-bell"></i>
            告警策略
          </a> -->
        <!-- <ul class="context-secondary">
            <li><a href="#"><i class="icon-plus"></i> 绑定</a></li>
            <li><a href="#"><i class="icon-minus"></i> 解绑</a></li>
        </ul></li>
        <li><a href="#"><i class="icon-th-list"></i> 更多操作 </a></li> -->
        <?php if (in_array('ccf_desktop_delete', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="dele" href="#"><i class="icon-trash"></i> 删除</a></li>
        <?php } ?>
        <?php if (in_array('ccf_host_gen_image', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li id="defined"><a href="javascript:void(0);"> <i class="icon-paste"></i> 自定义镜像</a></li>
        <?php } ?>
        <!--<?php if (in_array('ccm_ps_interface_logs', $this->request->session()->read('Auth.User.popedomname'))) { ?>-->
        <!--<li id="excp"><a  href="javascript:;"><i class="icon-book"></i> 异常信息</a></li>-->
          <!--<?php } ?>-->
          <!--<?php if (in_array('ccm_ps_interface_logs', $this->request->session()->read('Auth.User.popedomname'))) { ?>-->
        <!--<li id="normal"><a  href="javascript:;"><i class="icon-book"></i> 正常信息</a></li>-->
        <!--<?php } ?>-->
          <!--<?php if (in_array('ccm_ps_interface_logs', $this->request->session()->read('Auth.User.popedomname'))) { ?>-->
          <!--<li id="executing"><a  href="javascript:;"><i class="icon-book"></i> 执行中信息</a></li>-->
          <!--<?php } ?>-->
          <?php if (in_array('ccm_ps_interface_logs', $this->Session->read('Auth.User.popedomname'))) { ?>
          <li id=""><a  href="javascript:;"><i class="icon-book"></i> 接口日志</a>
              <ul class="context-secondary">
                  <li id="excp" ><a href="javascript:void(0);">异常日志</a></li>
                  <li id="normal"><a href="javascript:void(0);">正常日志</a></li>
                  <li id="executing"><a href="javascript:void(0);">执行中日志</a></li>
              </ul>
          </li>
          <?php } ?>
          </ul>
    </div>

    <!-- 右键弹框 -->
    <div class="context-menu" id="context-menu2">
      <ul>
        <?php if (in_array('ccf_desktop_startup', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="chargemode" href="#"><i class="icon-pencil"></i> 计费模式</a></li>
        <?php } ?>
        <?php if (in_array('ccf_desktop_shutdonw', $this->request->session()->read('Auth.User.popedomname'))) { ?>
        <li><a id="priority" href="#"><i class="icon-edit"></i> 优先级</a></li>
        <?php } ?>
        </ul>
    </div>

    <!-- 自定义镜像 -->
    <div class="modal fade" id="modal-defined" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
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
                  <input id="" name="name" type="text" value="Win7 64位中文版" />
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
                <p>1.只支持该云桌面所在厂商的云桌面作系统盘的镜像</p>
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
    <!-- 修改计费模式 -->
    <div class="modal fade" id="modal-chargemode" tabindex="-1" role="dialog">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
            <h5 class="modal-title">修改如下桌面的计费方式</h5>
          </div>
          <form id="chargemode-form" action="" method="post">
            <div class="modal-body">
              <div class="modal-form-group">
                <label>桌面名:</label>
                <div>
                  <p id="charge_mode_desktopname"></p>
                </div>
              </div>
              <div class="modal-form-group">
                <label>桌面code:</label>
                <div>
                  <p id="charge_mode_desktopcode"></p>
                </div>
              </div>
              <div class="modal-form-group">
                <label>计费类型:</label>
                <div class="form-group">
                  <select name="charge_mode" id="charge_mode">
                      <option value="">选择计费模式</option>
                      <option value="permanent|P">永久免费</option>
                      <option value="cycle|D">按天计费</option>
                      <option value="cycle|M">按月计费</option>
                      <option value="cycle|Y">按年计费</option>
                      <option value="duration|I">按分钟计费</option>
                  </select>
                </div>
              </div>
              <!-- <div class="form-group">
                  <label class="col-md-2" for="price">成交单价</label>
                  <div class="col-md-5">
                    <input type="text" id="price"  name="price" value="" />
                  </div>
              </div> -->
              <div class="modal-form-group">
                <label for="price">成交价:</label>
                <div class="form-group">
                  <input id="price" name="price" type="text" value="0" />
                </div>
              </div>
            </div>
            <div class="modal-footer">
              <input type="hidden" id="charge_mode_desktopids" name="charge_mode_desktopids" value="" />
              <button id="charge_submit" type="submit" class="btn btn-primary">确认</button>
              <button id="charge_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
            </div>
          </form>
        </div>
      </div>
    </div>
<!-- 修改优先级 -->
<div class="modal fade" id="modal-priority" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">修改如下桌面的优先级</h5>
            </div>
            <form id="priority-form" action="" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label>桌面名:</label>
                        <div>
                            <p id="priority_desktopname"></p>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label>桌面CODE:</label>
                        <div>
                            <p id="priority_desktopcode"></p>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label>分配优先级:</label>
                        <div class="form-group">
                            <input type="text" class="form-control" name="priority_data" id="priority_data" value="">
                        </div>
                    </div>
                    <div class="form-group">
                        <label></label>
                        <div class="col-sm-9">
                            <label class="control-label text-danger"><i class="icon-exclamation-sign"  id="type_note">自动分配桌面和弹性开机时，按优先级从高到低操作<br>弹性关机时，按优先级从低高操作</i></label>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="priority_desktopids" name="priority_desktopids" value="" />
                    <button id="priority_submit" type="submit" class="btn btn-primary">保存</button>
                    <button id="priority_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
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
            <p class="name">
              <span>名称</span><input id="modal-modify-name" name="name"
              type="text" maxlength="15">
            </p>
            <p class="name">
              <span>描述</span>
              <textarea id="modal-modify-description" name="description"></textarea>
              <input id="modal-modify-id" name="id" type="hidden" />
            </p>
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

  <!-- 硬盘 -->
  <div class="modal fade" id="disk-manage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal"
          aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title">云桌面硬盘管理</h5>
      </div>
      <input type="hidden" id="hostsCode"/>
      <input type="hidden" id="hostsId"/>
      <input type="hidden" id="txtvpcCode"/>
      <input type="hidden" id="txtclass_code"/>
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
                        <input type="text" id="amount" placeholder="10"> GB
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
                      <button onclick="btnaddDisks(null)" type="button" class="btn btn-primary">添加</button>
                      <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                  </div>
                  <div class="modal-disk-content" style="display:none">
                    <table id="use_table">
                      <thead>
                        <tr>
                          <th data-field="code" >硬盘ID</th>
                          <th data-field="name">名称</th>
                          <th data-field="capacity" data-formatter="fromatter_Capacity">容量</th>
                          <th data-formatter="operateFormatter">操作</th>
                        </tr>
                      </thead>
                    </table>
                    <div class="modal-footer">
                      <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
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
                      <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                  </div>-->
                </div>
              </div>
            </div>
          </div>
<!-- 系统盘扩容 -->
<div class="modal fade" id="sysdisk-manage" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal"
                        aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">系统盘扩容</h5>
            </div>
            <input type="hidden" id="ecsCode"/>
            <input type="hidden" id="ecsId"/>
            <div class="modal-body">

                <div class="modal-disk-content" style="display:block;">

                    <div class="modal-form-group">
                        <label>容量大小:</label>
                        <div class="slider-area">
                            <div id="slider-sysdisk"></div>
                        </div>
                        <div class="amount pull-left">
                            <input type="text" style="width: 80px;margin: 0 10px 0 0;" id="syssize" placeholder="40" disabled="disabled"> GB
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label></label>
                        <div>
                            <h6 class="warm">当前系统盘大小<span id="sysdisk-size"></span>GB,请输入范围<span id="sysdisk-size2"></span>GB-500GB</h6>
                        </div>
                    </div>

                    <div class="modal-form-point">
                        <!-- <p>总价格: <span class="text-primary">￥0.0115</span> 每小时 X 1 = ￥0.01 每小时 (合 ￥8.28 每月)</p> -->
                    </div>
                    <div class="modal-footer">
                        <button onclick="btnsysDisks(null,this)" type="button" class="btn btn-primary">确认</button>
                        <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

          <div id="maindiv"></div>
          <!-- 删除 -->
          <div class="modal fade" id="modal-dele" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
              <div class="modal-content">
                <div class="modal-header">
                  <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                  <h5 class="modal-title">提示</h5>
                </div>
                <form id="modal-dele-form" action="" method="post">
                  <div class="modal-body">
                    <i class="icon-question-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span id="modal-info"></span><span class=" text-primary" id="modal-dele-name"></span>？<br/> <span id="sapn-delete-hint"></span>

                    <input type="hidden" value="" id="modal-dele-code" name="codes">
                    <input type="hidden" value="" id="modal-dele-id" name="ids">
                    <input type="hidden" value="" id="modal-status" name="status">
                  </div>
                  <div class="modal-footer">
                    <button type="button" id="sumbiter-dele" class="btn btn-primary">确认</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                  </div>
                </form>
              </div>
            </div>
          </div>
          <div style="width:0;height:0">
            <iframe id="lauchFrame" name="lauchFrame" src="" width=0  height=0 >
            </iframe>
          </div>
<?php
$this->start('script_last');
?>
<script>
   $(function(){
      $('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
      // 获取已激活的标签页的名称
      var activeTab = $(e.target).text(); 
      // 获取前一个激活的标签页的名称
      var previousTab = $(e.relatedTarget).text(); 
      $(".active-tab span").html(activeTab);
      $(".previous-tab span").html(previousTab);
   });
});
</script>
<script type="text/javascript">
function fromatter_Capacity(value, row, index) {
    return value + "GB";
}

/* 渲染页面 */
function operateFormatter(value, row, index) {
    return '<a href="javascript:;" data-id="' + row.id + '" data-row="' + row.code + '" class="del-disk"><i class="icon-remove"></i></a>';
}

$(document).on("click", ".del-disk", function() {
    deldisks($(this).data("row"), $(this).data("id"));
});

function deldisks(volumeCode, basicId) {
    $("#disk-manage").modal("hide");
    $.ajax({
        "type":"post",
        "url":"<?= $this -> Url -> build(['prefix' => 'console', 'controller' => 'ajax', 'action' => 'network', 'disks', 'ajaxDisks']); ?>"+"?d="+Date.parse(new Date()),
        "async":true,
        "timeout":9999,
        "data":{
            "basicId":basicId,
            "volumeCode":volumeCode,
            "method":"volume_detach"
        },
        "beforeSend":function() {
            $(document).off("click", ".del-disk");
        },
        //dataType:'json',
        "success":function(data) {
            data = $.parseJSON(data);
            if (data.Code != 0) {
                alert(data.Message);
            } else {
                // showModal('提示', 'icon-exclamation-sign', '解绑硬盘成功','', 'hidenModal()');
                $(document).on("click", ".del-disk", function() {
                    deldisks($(this).data("row"));
                });
            }
        }
    });
}
function queryParams() {
    var params = {};
    $("input[name='search']").each(function () {
    params[$(this).attr('name')] = $(this).val();
    });
    params['order']='asc';
    params['limit']='10';
    params['offset']='0';
    return params;
}
//slide
$("#amount").keyup(function() {
    var a = /(^[1-9]([0-9]*)$|^[0-9]$)/;
    $(this).blur(function() {
        var amountVal = $(this).val();
        if (a.test(amountVal) == false || amountVal < 10 || amountVal > 1e3) {
            $(this).val(10);
        } else {
            return;
        }
    });
    $("#slider").slider({
        "value":$("#amount").val()
    });
});

$("#slider").slider({
    "value":$("#amount").val(),
    "min":10,
    "max":1e3,
    "step":1,
    "orientation":"horizontal",
    "range":"min",
    "animate":true,
    "slide":function(event, ui) {
        $("#amount").val(ui.value);
        $("#bandwidth").html(ui.value);
    }
});

$("#amount").val($("#slider").slider("value"));

$("#bandwidth").html($("#slider").slider("value"));

//弹出框里面的界面切换
$(".modal-title-list").on("click", "li", function() {
    $(".modal-title-list li").removeClass("active");
    $(this).addClass("active");
    $(".modal-disk-content").css("display", "none");
    $(".modal-disk-content").eq($(this).index()).css("display", "block");
    if ($(this).attr("no") == "2") {
        var code = $("#hostsCode").val();
        $("#use_table").bootstrapTable("refresh", {
            "url":"/console/ajax/network/disks/uselist?id=" + code
        });
    } else if ($(this).attr("no") == "3") {
        $("#unuse_table").bootstrapTable("refresh", {
            "url":"/console/ajax/network/disks/unuselist"
        });
    }
});

function modalReturn() {
  $("#disk-manage .modal-title-list li").removeClass("active").eq(0).addClass("active");
  $("#disk-manage .modal-disk-content").css("display", "none").eq(0).css("display", "block");
}
$('#disk-manage').on('hide.bs.modal',function() {
  modalReturn();
});

function formatter_main(value, row, index) {
    if(value!=null&&value!=""){
      return '<a href="/console/network/data/basic_info/' + row.id + '/desktop">' + row.code + '</a>';
        //return value;
    }else{
        return "-";
    }
}



$("#btnStart").on('click',
      function() {
        var id = getRowsID('name');
        var codes = getRowsID('code');
        var names = getRowsID('name');
        codes = codes.substring(0,codes.length-1);
        code = codes.split(",");
        names = names.substring(0,names.length-1);
        f = names.split(",");
        for(var i=0;i<code.length;i++){

          if(code[i] == 'null'){
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
            $("#btnDesktop").html("关闭");
            return false;
          }
        }
        if (id != "") {
          showModal('提示', 'icon-question-sign', '确认要启动机器', id, 'ajaxFun(getRowsID(),\'desktop_start\')',1,'取消');
        } else {
          showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
        }
            //ajaxFun(getRowsID(),'desktop_start');
          });
$("#btnStop").on('click',
      function() {

        var id = getRowsID('name');
        var codes = getRowsID('code');
        var names = getRowsID('name');
        codes = codes.substring(0,codes.length-1);
        code = codes.split(",");
        names = names.substring(0,names.length-1);
        f = names.split(",");
        for(var i=0;i<code.length;i++){

          if(code[i] == 'null'){
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
            $("#btnDesktop").html("关闭");
            return false;
          }
        }
        if (id != "") {
          showModal('提示', 'icon-question-sign', '确认要停止机器', id, 'ajaxFun(getRowsID(),\'desktop_stop\')',1,'取消');
        } else {
          showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
        }
      });

$("#btnForceStop").on('click',
    function() {

        var id = getRowsID('name');
        var codes = getRowsID('code');
        var names = getRowsID('name');
        codes = codes.substring(0,codes.length-1);
        code = codes.split(",");
        names = names.substring(0,names.length-1);
        f = names.split(",");
        for(var i=0;i<code.length;i++){

            if(code[i] == 'null'){
                showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', f[i], '', 0);
                $("#btnDesktop").html("关闭");
                return false;
            }
        }
        if (id != "") {
            showModal('提示', 'icon-question-sign', '确认要强制停止机器', id, 'ajaxFun(getRowsID(),\'desktop_force_stop\')',1,'取消');
        } else {
            showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
        }
    });

$("#btnDel").on('click', function() {
        var names = getRowsID('id');
        //判断机器状态
        var o = true;
    	var rows = $('#table').bootstrapTable('getSelections');
    		rows.every(function(e, i) {
    			if(e.status=="运行中") {
    				showModal('提示', 'icon-exclamation-sign', '请先关机，再删除桌面',e.name, '', 0, '取消');
    				$("#btnEsc").html("关闭");
    				o = false;
    				return false;
    			}
    		});
        
    	if(o==true){
            if (names != "") {
              showModal('提示', 'icon-question-sign', '确认要删除云桌面', names, 'deleteAll()',1,'取消');
            } else {
              showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
            }
    	}
    });

//input 存在一个被选中状态
$("table input").on('click',
  function() {
    if ($("tbody input:checked").length >= 1) {
      $(".center .btn-shutdown").attr('disabled', false);
    } else {
      $(".center .btn-shutdown").attr('disabled', true);
    }
  })

//动态创建modal
function showModal(title, icon, content, content1, method, type, info) {
  $("#maindiv").empty();
  html = "";
  html += '<div class="modal fade" id="modal" tabindex="-1" role="dialog">';
  html += '<div class="modal-dialog" role="document">';
  html += '<div class="modal-content"><div class="modal-header"><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>';
  html += '<h5 class="modal-title">' + title + '</h5>';
  html += '</div><div class="modal-body"><i class="'+icon+' icon-color-gray text-primary"></i>&nbsp;&nbsp;' + content + '<span class="text-primary batch-warning">' + content1 + '</span></div>';
  html += '<div class="modal-footer"><button id="btnMk" onclick="' + method + '" type="button" class="btn btn-primary">确认</button><button type="button" class="btn btn-danger" id="btnDesktop" data-dismiss="modal">'+info+'</button></div></div></div></div>';
  $("#maindiv").append(html);
  if (type == 0) {
    $("#btnMk").remove();
  }
  $('#modal').modal("show");
}

function deleteAll(){
    $('#modal').modal("hide");
    var codes= getRowsID();
    var ids= getRowsID("id");
//    var tot = $('#table').bootstrapTable('getSelections');
//    $.each(tot, function(i,val){
//        $('#table').bootstrapTable('removeByUniqueId',val.id);
//    });
    $.ajax({
        url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','deleteDesktop']); ?>',
        method:'post',
        data: {
              codes:codes,
              ids:ids,
            },
        dataType:'json',
        success:function(e){
            //操作成功
            if(e.code == '0000'){
              refreshTable();
            }else{
                alert(e.msg);
            }
        }
    });
}


$('#table').contextMenu('context-menu', {

  bindings: {
    'start': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!='创建中'&&row.status!='创建失败'){
            $('#modal-info').html('确认要启动云桌面');
            $('#modal-dele-name').html(row.name);
            $('#modal-dele-id').val(row.id);
            $('#modal-dele-code').val(row.code);
            $('#modal-dele').modal("show");
            $('#modal-status').val(0);
            $('#sumbiter-dele').attr('id','sumbiter-stop');
            $('#sumbiter-stop').one('click',function(){
              heartbeat(0, row.id);
              $('#modal-dele').modal("hide");
                //ajax提交页面
                $.ajax({
                  url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>',
                  data:$('#modal-dele-form').serialize(),
                  method:'post',
                  dataType:'json',
                  success:function(e){
                    console.log(e);
                        //操作成功
                        if(e.code == '0000'){
                            //$('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                          }else{
                            //操作失败
                            alert(e.msg);
                          }
                          // refreshTable();
                        }
                      });
                return false;
              });
          }else{
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
            $("#btnDesktop").html("关闭");
          }

          },
          'close': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!='创建中'&&row.status!='创建失败'){

            $('#modal-info').html('确认要关闭云桌面');
            $('#modal-dele-name').html(row.name);
            $('#modal-dele-id').val(row.id);
            $('#modal-dele-code').val(row.code);
            $('#modal-dele').modal("show");
            $('#modal-status').val(1);
            $('#sumbiter-dele').attr('id','sumbiter-stop');
            $('#sumbiter-stop').one('click',function(){
              heartbeat(1, row.id);
              $('#modal-dele').modal("hide");
                //ajax提交页面
                $.ajax({
                  url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>',
                  data:$('#modal-dele-form').serialize(),
                  method:'post',
                  dataType:'json',
                  success:function(e){
                    console.log(e);
                        //操作成功
                        if(e.code == '0000'){
                            //$('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                          }else{
                            //操作失败
                            alert(e.msg);
                          }
                          // refreshTable();
                        }
                      });
                return false;
              });
            }else{
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
            $("#btnDesktop").html("关闭");
          }
          },
          'restart': function(event) {
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');

            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!='创建中'&&row.status!='创建失败'){

            $('#modal-info').html('确认要重启云桌面');
            $('#modal-dele-name').html(row.name);
            $('#modal-dele-id').val(row.id);
            $('#modal-dele-code').val(row.code);
            $('#modal-dele').modal("show");
            $('#modal-status').val(2);
            $('#sumbiter-dele').attr('id','sumbiter-restart');
            $('#sumbiter-restart').one('click',function(){
              heartbeat(2, row.id);
              $('#modal-dele').modal("hide");
                //ajax提交页面
                $.ajax({
                  url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>',
                  data:$('#modal-dele-form').serialize(),
                  method:'post',
                  dataType:'json',
                  success:function(e){
                    console.log(e);
                        //操作成功
                        if(e.code == '0000'){
                            //$('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                          }else{
                            //操作失败
                            alert(e.msg);
                          }
                          refreshTable();
                        }
                      });
                return false;
              });
            }else{
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
            $("#btnDesktop").html("关闭");
          }
          },
          'modify': function(event) {
            //获取数据
            index=$(event).attr('data-index');
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            // if(row.status!='创建中'&&row.status!='创建失败'){

            //填充数据
            //TODO 根据bootstrap方法
            $('#modal-modify-name').val(row.name);
            $('#modal-modify-description').val(row.description);
            $('#modal-modify-id').val(row.id);
            $('#modal-modify').modal("show");

            //填充数据
            $('#sumbiter').one('click',function(){
              $('#modal-modify').modal("hide");
              $.ajax({
                url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','edit']); ?>',
                data:$('#modal-modify-form').serialize(),
                method:'post',
                dataType:'json',
                success:function(e){
                        //操作成功
                        if(e.code == '0000'){
                          $('#table').bootstrapTable('updateRow', {index: index, row: e.data});
                        }else{
                            //操作失败
                            alert(e.msg);
                          }
                        }
                      });
              return false;
            });
          },
          'bound': function(event) {
            $('#modal-bound').modal("show");
            console.log(event.id);
          },
          'built': function(event) {
            $('#modal-built').modal("show");
            console.log(event.id);
          },
          'diskfreash': function(event) {
            $('#modal-diskfreash').modal("show");
            console.log(event.id);
          },
          'diskunload': function(event) {
            $('#modal-diskunload').modal("show");
            console.log(event.id);
          },
          'netadd': function(event) {
            $('#modal-netadd').modal("show");
            console.log(event.id);
          },
          'netremove': function(event) {
            $('#modal-netremove').modal("show");
            console.log(event.id);
          },
          'mapping': function(event) {
            $('#modal-mapping').modal("show");
            console.log(event.id);
          },
          'dele':function(event){
            console.log(event);
            console.log($(event));
            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            console.log(uniqueId);
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if (row.status == '运行中') {
            	showModal('提示', 'icon-exclamation-sign', '请先关机，再删除桌面',row.name, '', 0, '取消');
				$("#btnEsc").html("关闭");
			} else if (row.status == '创建镜像中') {
            	showModal('提示', 'icon-exclamation-sign', '创建镜像中，无法删除桌面',row.name, '', 0, '取消');
            }  else {
			
                $('#modal-info').html('确认要删除云桌面');
                $('#modal-dele-name').html(row.name);
                $('#modal-dele-code').val(row.code);
                $('#modal-dele-id').val(row.id);
                $('#sapn-delete-hint').html('<i class="icon-exclamation-sign icon-color-gray text-primary"></i>&nbsp;&nbsp;<span > 可在回收站找回已删除的云桌面</span><span class=" text-primary" id="modal-dele-name"></span>');
                $('#modal-dele').modal("show");
                $('#sumbiter-dele').attr('id','sumbiter-dele');
                $('#sumbiter-dele').one('click',function(){
                $('#modal-dele').modal("hide");
                //ajax提交页面
                    $.ajax({
                      url:'<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','deleteDesktop']); ?>',
                      data:$('#modal-dele-form').serialize(),
                      method:'post',
                      dataType:'json',
                      success:function(e){
                        console.log(e);
                            //操作成功
                            if(e.code == '0000'){
                                // heartbeat(3, row.id);
                                //$('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                              }else{
                                //操作失败
                                alert(e.msg);
                              }
                              refreshTable();
                            }
                          });
                    return false;
                  });
            }
          },
          'adddisks': function(event) {

            //获取数据
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!='创建中'&&row.status!='创建失败'){
              $('#disk-manage').modal("show");
              $("#txtdisks_name").val('');
              $("#amount").val(10);
              $("#slider").slider({
                value: $("#amount").val()
              })

            $("#hostsCode").val(row.code);
            $("#hostsId").val(uniqueId);
            $("#txtvpcCode").val(row.vpc);
            $("#txtclass_code").val(row.location_code);
            $('#use_table').bootstrapTable({
              url: "/console/ajax/network/disks/uselist?id=" + row.code,
              'data-side-pagination': "server",
                //pagination:true,
                //sidePagination:"server",
                locale: "zh-CN",
                clickToSelect: "true",
                uniqueId: "id",
                pageSize: 7
              });
            }else{
            showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
            $("#btnDesktop").html("关闭");
          }

          },
          'defined':function(event){
            //获取数据

            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            if(row.status!='创建中'&&row.status!='创建失败'){
              $('#modal-defined').modal("show");
            }else{
              showModal('提示','icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
              $("#btnDesktop").html("关闭");
            }

          },
          'excp':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

            var department_id = row.department_id;
            window.location.href = "/console/excp/lists/excp/desktop/"+department_id+'/all/0/0/'+row.id;
          },
          //正常
          'normal':function(event){
            var uniqueId = $(event).attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
            var department_id = row.department_id;
            window.location.href = "/console/excp/lists/normal/desktop/"+department_id+'/all/0/0/'+row.id;
          },
          'sysdisks': function(event) {
              //获取数据
              var uniqueId = $(event).attr('data-uniqueid');

              var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);
              if (row.status != '创建中' && row.status != '创建失败') {
                  $('#sysdisk-manage').modal("show");
                  $("#syssize").val(row.host_extend.sys_disk_size);
//                  $("#slider").slider({
//                      value: $("#slider-sysdisk").val()
//                  })
                  $("#ecsCode").val(row.code);
                  $("#ecsId").val(row.id);
                  var min = parseInt(row.host_extend.sys_disk_size);
                  $("#slider-sysdisk").slider({
                      min: min,
                      max: 500,
                      step: 1,
                      orientation: "horizontal",
                      range: "min",
                      animate: true,
                      slide: function(event, ui) {
                          var val=ui.value-ui.value%10;
                          $("#syssize").val(val);
                      }
                  });

                  $("#sysdisk-size").html(min);
                  $("#sysdisk-size2").html(min);
                  //$("#slider-sysdisk").slider( "option", "min",min);

              } else {
                  showModal('提示', 'icon-exclamation-sign', '当前设备状态无法进行操作', '', '', 0);
                  $("#btnEsc").html("关闭");
              }
          },
      'executing':function(event){
          var uniqueId = $(event).attr('data-uniqueid');
          var row = $('#table').bootstrapTable('getRowByUniqueId', uniqueId);

          var department_id = row.department_id;
          window.location.href = "/console/excp/lists/executing/desktop/"+department_id+'/all/0/0/'+row.id;
      }
        }

      });
//扩容系统盘

function  btnsysDisks(obj) {
    var $obj = $(obj);
    $obj.prop('disabled', true);

    size = $("#syssize").val();
    ecsCode = $("#ecsCode").val();
    ecsId = $("#ecsId").val();

    $.ajax({
            type: "post",
            url:"/console/ajax/network/disks/ajaxSysDisks",
            async: true,
            timeout: 9999999,
            data: {
                size: size,
                ecsCode: ecsCode,
                ecsId:ecsId
            },
            success: function(data) {
                data = $.parseJSON(data);
                if(data.Code != 0){
                    layer.msg(data.Message);
                }
                $('#sysdisk-manage').modal("hide");
                $obj.prop('disabled', false);
            }
    });

}

//判断是否为空对象
function isEmptyObject(obj)
{
   for(var key in obj){
     return false
   };
   return true
};
//计费优先级列表右键菜单
$('#charge-table').contextMenu('context-menu2', {
  bindings: {

          'chargemode': function(event) {

            //支持批量修改，当有批量选择时则用选中数据批量修改
            var selectedList = $('#charge-table').bootstrapTable('getSelections');
            if(isEmptyObject(selectedList)){
                //获取当前行数据
                var uniqueId = $(event).attr('data-uniqueid');
                selectedList[0] = $('#charge-table').bootstrapTable('getRowByUniqueId', uniqueId);
            }
            var ids = names = codes = "";
            for(var key in selectedList){
              ids += selectedList[key].id+",";
              names += selectedList[key].name+",";
              codes += selectedList[key].code+",";
            }
            $('#charge_mode_desktopids').val(ids);
            $("#charge_mode_desktopname").html(names);
            $('#charge_mode_desktopcode').html(codes);
            $('#modal-chargemode').modal("show");
          },
          'priority':function(event){
              var selectedList = $('#charge-table').bootstrapTable('getSelections');
              if(isEmptyObject(selectedList)){
                  //获取当前行数据
                  var uniqueId = $(event).attr('data-uniqueid');
                  selectedList[0] = $('#charge-table').bootstrapTable('getRowByUniqueId', uniqueId);
              }
              var ids = names = codes = "";
              for(var key in selectedList){
                  ids += selectedList[key].id+",";
                  names += selectedList[key].name+",";
                  codes += selectedList[key].code+",";
              }
              $('#priority_desktopids').val(ids);
              $("#priority_desktopname").html(names);
              $('#priority_desktopcode').html(codes);
              $('#modal-priority').modal("show");
          }
        }

      });
//定义修改计费模式的表单验证
  $("#chargemode-form").bootstrapValidator({
        submitButtons: '#charge_submit',
        submitHandler: function(validator, form, submitButton) {
            // 实用ajax提交表单
            $('#modal-chargemode').modal('hide');
            $('#modal-chargemode').one('hidden.bs.modal',function(){
                $.ajax({
                    url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktopCharge','modeEdit']); ?>",
                    data:$('#chargemode-form').serialize(),
                    method:'post',
                    dataType:'json',
                    success:function(e){
                        for(var key in e.data){
                          var str = "tr[data-uniqueid='"+e.data[key].id+"']";
                          index=$(str).attr('data-index');
                          $('#charge-table').bootstrapTable('updateRow', {index: index, row: e.data[key]});
                        }
                    }
                });
                $("#chargemode-form").data('bootstrapValidator').resetForm();
            });
            
        },
        fields: {
          //多个重复
          price: {
              //隐藏或显示 该字段的验证
              enabled: true,
              //错误提示信息
              message: 'This value is not valid',
              /**
              * 定义错误提示位置  值为CSS选择器设置方式
              * 例如：'#firstNameMeg' '.lastNameMeg' '[data-stripe="exp-month"]'
              */
              container: null,
              /**
              * 定义验证的节点，CSS选择器设置方式，可不必须是name值。
              * 若是id，class, name属性，<fieldName>为该属性值
              * 若是其他属性值且有中划线链接，<fieldName>转换为驼峰格式  selector: '[data-stripe="exp-month"]' =>  expMonth
              */
              selector: null,
              /**
              * 定义触发验证方式（也可在fields中为每个字段单独定义），默认是live配置的方式，数据改变就改变
              * 也可以指定一个或多个（多个空格隔开） 'focus blur keyup'
              */
              trigger: null,
              // 定义每个验证规则
              validators: {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric:{
                        message: '请输入正确的价格'
                    },
                    greaterThan:{
                        value : 0,
                        inclusive : true,
                        message: '价格不能小于0'
                    }
              }
          },
          charge_mode: {
              // 定义每个验证规则
              validators: {
                    notEmpty: {
                        message: '请选择计费模式'
                    }
              }
          }

      }
  });
//取消modal，重置表单验证
$("#charge_cancel").click(function(){
  $("#chargemode-form").data('bootstrapValidator').resetForm();
});

//定义修改优先级的表单验证
$("#priority-form").bootstrapValidator({
    submitButtons: '#priority_submit',
    submitHandler: function(validator, form, submitButton) {
        // 实用ajax提交表单
        $('#modal-priority').modal('hide');
        $('#modal-priority').one('hidden.bs.modal',function(){
            $.ajax({
                url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktopCharge','priorityEdit']); ?>",
                data:$('#priority-form').serialize(),
                method:'post',
                dataType:'json',
                success:function(e){
                    if(e.code==0){
                        for(var key in e.data){
                            var str = "tr[data-uniqueid='"+e.data[key].id+"']";
                            index=$(str).attr('data-index');
                            $('#charge-table').bootstrapTable('updateRow', {index: index, row: e.data[key]});
                        }
                        tentionHide(e.msg, 0);
                    }else{
                        tentionHide(e.msg, 1);
                    }

                }
            });
            $("#priority-form").data('bootstrapValidator').resetForm();
        });

    },
    fields: {
        //多个重复
        priority_data: {
            //隐藏或显示 该字段的验证
            enabled: true,
            //错误提示信息
            message: 'This value is not valid',
            /**
             * 定义错误提示位置  值为CSS选择器设置方式
             * 例如：'#firstNameMeg' '.lastNameMeg' '[data-stripe="exp-month"]'
             */
            container: null,
            /**
             * 定义验证的节点，CSS选择器设置方式，可不必须是name值。
             * 若是id，class, name属性，<fieldName>为该属性值
             * 若是其他属性值且有中划线链接，<fieldName>转换为驼峰格式  selector: '[data-stripe="exp-month"]' =>  expMonth
             */
            selector: null,
            /**
             * 定义触发验证方式（也可在fields中为每个字段单独定义），默认是live配置的方式，数据改变就改变
             * 也可以指定一个或多个（多个空格隔开） 'focus blur keyup'
             */
            trigger: null,
            // 定义每个验证规则
            validators: {
                notEmpty: {
                    message: '优先级不能为空'
                },
                numeric:{
                    message: '请输入数字型优先级'
                },
                regexp:{
                    regexp:/^[0-9]+$/ ,
                    message: '请输入正整数'
                },
                greaterThan:{
                    value : 0,
                    inclusive : true,
                    message: '优先级不能小于0'
                },
                lessThan:{
                    value : 10000,
                    inclusive : true,
                    message: '优先级不能大于10000'
                }
            }
        }


    }
});
//取消modal，重置表单验证
$("#priority_cancel").click(function(){
    $("#priority-form").data('bootstrapValidator').resetForm();
});

//永久许可不用设置单价
$("#charge_mode").change(function(){
  //.select(['option:selected'])
  var priceForm = $(this).parent().parent().next();
  if($(this).val() == "permanent|P"){
    priceForm.hide();
  }else{
    priceForm.show();
  }
});
//搜索绑定
$("#txtsearch").on('keyup', function() {
      if (timer != null) {
        clearTimeout(timer);
      }
      
      var timer = setTimeout(function() {
        refreshTable()
      }, 500);
});

 //input 存在一个被选中状态
$("#table").on('all.bs.table.table', function (e, row, $element) {
  if ($("tbody input:checked").length >= 1) {
    $(".center .btn-default").attr('disabled', false);
  } else {
    $(".center .btn-default").attr('disabled', true);
  }
})

//格式化配置
function formatter_config(value, row, index) {
  if(value!=null){
    if (value.cpu != 0) {
      return value.cpu + "核*" + value.memory + "GB*"+value.gpu+"MB(GPU)";
    } else {
      return "-";
    }
  }else{
    return "-";
  }
}
//格式化code
function formatter_code(value, row, index) {
    // if (value!=null) {}else{};
    var html = "";
    var code = row.code;
    if (row.host_extend != null) {
      var name = row.host_extend.name;
      var os = row.host_extend.plat_form;
      var connect_status = row.host_extend.connect_status;
        //TODO 加密解密token
        var url = "<?= $this->Url->build(['prefix'=>'xdesktop','controller'=>'citrix','action'=>'launch']); ?>/"+name+'.ica';
        desktop=row.host_extend
        if (row.status != "创建失败" && row.status != "" &&row.status != "销毁中"&&row.status != "创建中"&&desktop.name!=""&&row.status == "运行中") {
          if (os == "Linux") {
            html += "<a href=" + url + " target='lauchFrame'><i class='icon-laptop'></i></a>";
            return  html;
          } else {
            html += "<a href=" + url + " target='lauchFrame'><i class='icon-desktop'></i></a>";
            return  html;
          }
        } else {
          return "-";
        }
      }else{return "-";}

    }
/**
 * 格式化计费模式
 */
function formatter_charge_mode(value)
{
  if(value != null){
    return value.charge_type_txt;
  }else{
    return '-';
  }
}
/**
 * 格式化计费单价
 */
function formatter_charge_price(value)
{
  if(value != null && value.interval !=null){
    return value.charge_price_txt;
  }else{
    return '-';
  }
}
//格式化优先级
function formatter_priority(value){
    if(value>=0&&value<=30){
        return '低';
    }else if(value>30&&value<=60){
        return '中';
    }else if(value>60&&value<=100){
        return '高';
    }else{
        return '-';
    }
}

//返回状态
function formatter_state(value, row, index) {
  switch (value) {
    case "创建中":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">创建中</span>';
      break;
    }
    case "运行中":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">运行中</span>';
      break;
    }
    case "已停止":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">已停止</span>';
      break;
    }
    case "创建失败":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">创建失败</span>';
      break;
    }
    case "销毁中":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">销毁中</span>';
      break;
    }
    case "销毁失败":
    {
      return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">销毁失败</span>';
      break;
    }
    default:
    {
      return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
    }
  }
  return '<span id="imgState' + row.id + '" class="circle circle-create"></span>-';
}



function formatter_connect_status(value, row, index){
	if(row.status == "运行中") {
        if(value.connect_status == 1){
          return '<span id="imgState' + row.id + '" class="circle circle-stopped"></span><span id="txtState' + row.id + '">使用中（'+value.connect_user+'）</span>';
        }else if(value.connect_status == 99){
          return '<span id="imgState' + row.id + '" class="circle circle-create"></span><span id="txtState' + row.id + '">连接中（'+value.connect_user+'）</span>';
        }else{
          return '<span id="imgState' + row.id + '" class="circle circle-run"></span><span id="txtState' + row.id + '">空闲</span>';
        }
	} else {
		return '-';
	}

}
//返回操作系统
function formatter_operateSystem(value, row, index) {
  if (value != null) {
    return value.os_family;
  } else {
    return "-";
  }
}
//返回网络
function formatter_vxnets(value, row, index) {
  if (value != null) {
    var sub = '';
    $.each(value,function(i,n){
      if(i>0){
        sub += ',';
      }
      sub += n.subnet_code;
    });
    return sub;
  } else {
    return "-";
  }
}
//返回ip
function formatter_ip(value, row, index) {
  if (value != null) {
     var ip = '';
    $.each(value,function(i,n){
      if(i>0){
        ip += ',';
      }
      ip += n.ip;
    });
    return ip;
  } else {
    return "-";
  }
}

//心跳
function heartbeat(type, id) {
  if (id != undefined && id != "") {
    $("#imgState" + id).removeClass('circle-stopped');
    $("#imgState" + id).removeClass('circle-run');
        $("#imgState" + id).addClass('circle-create'); //添加样式，样式名为className
        if (type == 0) {
          $("#txtState" + id).html('正在启动...');
        } else if (type == 1) {
          $("#txtState" + id).html('正在停止...');
        } else if(type==2){
          $("#txtState" + id).html('正在重启...');
        }else{
          $("#txtState" + id).html('正在销毁...');
        }
      } else {
        var ids = getRowsID('id');
        var idList = ids.split(',');
        idList.forEach(function(e) {
          $("#imgState" + e).removeClass('circle-stopped');
          $("#imgState" + e).removeClass('circle-run');
            $("#imgState" + e).addClass('circle-create'); //添加样式，样式名为className
            if (type == 0) {
              $("#txtState" + e).html('正在启动...');
            } else if (type == 1) {
              $("#txtState" + e).html('正在停止...');
            } else if(type==2){
              $("#txtState" + e).html('正在重启...');
            }else{
              $("#txtState" + e).html('正在销毁...');
            }
          });
      }
    }
//获取选中行参数
function getRowsID(type) {
  var idlist = '';
  $("input[name='btSelectItem']:checkbox").each(function() {
    if ($(this)[0].checked == true) {
            //alert($(this).val());
            var id = $(this).parent().parent().attr('data-uniqueid');
            var row = $('#table').bootstrapTable('getRowByUniqueId', id);
            if (row.status != '') {
              if (type == 'name') {
                idlist += row.name + ',';
              } else if (type == "id") {
                idlist += row.id + ',';
              } else {
                idlist += row.code + ',';
              }
            }
          }
        });
  return idlist;
}

function ajaxFun(code, method,id) {
  $('#modal').modal("hide");
  var tot = $('#table').bootstrapTable('getSelections');
  if(method == "desktop_start"){
        // tentionHide('启动云桌面', id);
        heartbeat(0, id);
        status=0;
      }else if(method == "desktop_stop"){
        // tentionHide('停止云桌面', id);
        heartbeat(1, id);
        status=1;
      }else if(method == "desktop_force_stop"){
          // tentionHide('停止云桌面', id);
          heartbeat(1, id);
          status=3;
      }else if(method == "desktop_reboot"){
        // tentionHide('重启云桌面', id);
        heartbeat(2, id);
        status=2;
      }else if(method == "desktop_delete"){
            $.each(tot, function(i,val){
                $('#table').bootstrapTable('removeByUniqueId',val.id);
            });
      }

      $.ajax({
        type: "post",
        url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','stopDesktop']); ?>",
        async: true,
        timeout: 9999999,
        data: {
          codes:code,
          ids:id,
          status:status,
        },
        //dataType:'json',
        success: function(e) {
          e= $.parseJSON(e);
            //操作成功
            if(e.code == '0000'){
                // $('#table').bootstrapTable('removeByUniqueId', $(event).data("uniqueid"));
                $('#modal-dele').modal("hide");
              }else{
                //操作失败
                alert(e.msg);
              }
              if(method == "desktop_reboot"){
                refreshTable();
              }
            }
          });
    }

function refreshTable() {
    var search= $("#txtsearch").val();
    //$('#table').bootstrapTable('showLoading');
    var class_code = $("#agent").attr('val');
    var class_code2 =$("#agent_t").attr('val');
    $('#table').bootstrapTable('refresh', {
      url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
    });
    $('#charge-table').bootstrapTable('refresh', {
      url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktopCharge','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
    });
   
    // $('#table').bootstrapTable('hideLoading');
  }
//提示框消失
function tentionHide(content, state) {
  $("#maindiv-alert").empty();
  var html = "";
  if (state == 0) {
    html += '<div class="point-host-startup "><i></i>' + content + '</div>';
    $("#maindiv-alert").append(html);
    $(".point-host-startup ").slideUp(3000);
  } else {
    html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
    $("#maindiv-alert").append(html);
    $(".point-host-startdown").slideUp(3000);
  }
}

//地域查询
function local(id,class_code,agent_name) {
  if (agent_name) {
    var search= $("#txtsearch").val();
    $('#agent_t').html('全部');
    $('#agent').html(agent_name);
    $('#agent').attr('val', class_code);
    refreshTable()
    var jsondata = <?php echo json_encode($agent); ?>;
    if(id!=0){
      var data='';
      var data='<li><a href="javascript:;" onclick="local_two(\'\',\'全部\',\'' + class_code + '\')">全部</a></li>';
      $.each(jsondata, function (i, n) {
        if(n.parentid == id){
          data += '<li><a href="#" onclick="local_two(\'' + n.class_code + '\',\'' + n.agent_name + '\')">' + n.agent_name + '</a></li>';
        }
      })
      $('#agent_two').html(data);
    }else{
      data='';
      $('#agent_t').attr('val', data);
      $('#agent_two').html(data);
    }

  }
}
function local_two(class_code2,agent_name,class_code){
  var search= $("#txtsearch").val();
  $('#agent_t').html(agent_name);
  $('#agent_t').attr('val',class_code2);
  refreshTable()
}
//重启
$("#btnreboot").on('click',
  function(){
    var names = getRowsID('name');
    if (names != "") {
      showModal('提示', 'icon-question-sign', '确认要重启云桌面', names, 'ajaxFun(getRowsID(),\'desktop_reboot\')',1,'取消');
    } else {
      showModal('提示', 'icon-exclamation-sign', '请选中一台云桌面', '', '', 0,'关闭');
    }
  });


function btnaddDisks(idList) {//挂载硬盘
  var id = $("#hostsId").val();
  var instanceCode = $("#hostsCode").val();
  var name = $("#txtdisks_name").val();
  var size = $("#amount").val();
  var vpcCode = $("#txtvpcCode").val();
  var class_code=$("#txtclass_code").val();
  var volumeCode, method;
  var number = 1;
  $.ajaxSettings.async = false;
  istrue = true

  var a = /^[1-9]\d*0$/;
  if (a.test(size) == false || size < 10 || size > 1000) {
    $("#amount").val(10);
  }
  if (name == "") {
    $('#name-warning').html('硬盘名称不能为空');
    $obj.prop('disabled', false);
    return false;
  }

  $.get("/console/ajax/network/hosts/getDisksCount/"+instanceCode,function(data){
    $.getJSON("/console/ajax/network/hosts/getDisksLimit",function(data2){
      if(Number(data) >= data2){
        alert('主机最多挂载'+data2+'个硬盘')
        istrue = false
      }
    })
  })


  $.getJSON("/console/home/getUserLimit", function(data){
    if(Number(size)+data.disks_used > data.disks_bugedt ){
      alert("配额不足 \r\n 磁盘 配额："+ data.disks_bugedt+" 已使用："+data.disks_used)
      istrue = false
    }else{

    }
  });
  if (!istrue) {
    $obj.prop('disabled', false);
    return false
  }
  $.ajaxSettings.async = true;
  if (idList != null && idList != '') {
        method = "volume_attach"; //挂载
        volumeCode=idList;
      } else {
        method = "volume_add";
      }
      $('#disk-manage').modal("hide");
      $.ajax({
        type: "post",
        url: "<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'ajax', 'action' =>'network', 'disks', 'ajaxDisks']); ?>",
        async: true,
        timeout: 9999999,
        data: {
          method: method,
          id: id,
          name: name,
          size: size,
          instanceCode: instanceCode,
          volumeCode: volumeCode,
          vpcCode: vpcCode,
          class_code:class_code
        },
        success: function(data) {
          data = $.parseJSON(data);
          if (data.Code != 0) {
            alert(data.Message);
          }else{
            if (idList != null && idList != '') {
                    //showModal('提示', 'icon-exclamation-sign', '添加硬盘成功','', 'hidenModal()');
                  } else {
                    //showModal('提示', 'icon-exclamation-sign', '挂载硬盘成功','', 'hidenModal()');
                  }

                }
              }
            });
    }

    function notifyCallBack(value){
//       console.log(value);

      var search = $("#txtsearch").val();
      var department_id = $("#txtdeparmetId").val();
      var class_code = $("#agent").attr('val');
      var class_code2 = $("#agent_t").attr('val');
      var url = "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+department_id;

      if(value.MsgType=="success"||value.MsgType=="error"){
        if(value.Data.method=="desktop_del"||value.Data.method=="desktop_add" || value.Data.method=="desktop"||value.Data.method=="desktop_stop"||value.Data.method=="desktop_start"){
          $('#table').bootstrapTable('refresh', {
            url: url,
            silent: true
          });
        }
      }
    }
    // function departmentlist(id,name){
    //   $("#txtdeparmetId").val(id);
    //   $("#deparmets").html(name);
    //   refreshTable();
    // }
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
      connDesktop = new WebSocket(pro+"/ws?uid=<?= $tempUser ?>");

      // Set event handlers.
      connDesktop.onopen = function() {
        //alert("onopen");
      };
      connDesktop.onmessage = function(e) {
    	  var search= $("#txtsearch").val();
  	    //$('#table').bootstrapTable('showLoading');
  	    console.log($("#agent").attr('val'))
  	    var class_code = $("#agent").attr('val');
  	    var class_code2 =$("#agent_t").attr('val');
  	    $('#table').bootstrapTable('refresh', {
  	      url: "<?= $this->Url->build(['prefix'=>'console','controller'=>'ajax','action'=>'desktop','desktop','lists']); ?>?search=" + search + '&class_code=' + class_code + '&class_code2=' + class_code2+'&department_id='+$("#txtdeparmetId").val()
  	    });
      };
      connDesktop.onclose = function() {
       // alert("onclose");
      };
      connDesktop.onerror = function() {
       // alert("onerror");
      };


    </script>
  <?php
  $this->end();
  ?>
