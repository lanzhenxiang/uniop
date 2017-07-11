<style>
    .flow-list-heading{padding:5px 0;background:#373D3D;color:#fff;border:1px solid #d2d2d2;}
    .flow-list-heading li{float:left;width:150px;text-align:center;padding:0 2px;}
    .flow-list-heading li:first-child{width:35px;}
    .flow-list-heading li:last-child{width:180px;}
    #flow-list .ui-state-default {padding:5px 0;border:1px solid #d2d2d2;}
    #flow-list .ui-state-default > span {display:inline-block; width:150px;text-align: center}
    #flow-list .ui-state-default > span:first-child{width:30px;}
    #flow-list .ui-state-default > span:last-child{width:180px;}
</style>
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="work-flow-form" action="<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'flow',$flow_id)); ?>" method="post">
        <div style="width:75%;margin:0 auto;">
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#basic" aria-controls="home" role="tab" data-toggle="tab" aria-expanded="false">基本信息</a></li>
                <li role="presentation"><a href="#flow" aria-controls="profile" role="tab" data-toggle="tab" aria-expanded="true">流程步骤</a></li>
            </ul>

            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="basic">
                    <div class="form-horizontal">
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                流程编码 :
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="flow_code" class="form-control" value="<?php if(isset($data)){ echo $data['flow_code'];}  ?>" />
                                <!-- <button class="btn btn-primary" style="margin-left:5px;vertical-align:top;">生 成</button> -->
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                流程名称 :
                            </label>
                            <div class="col-sm-8">
                                <input type="text" name="flow_name" class="form-control" value="<?php if(isset($data)){ echo $data['flow_name'];}  ?>" />
                                <input type="hidden" class="form-control" name="flow_order" id="flow_order">
                                <?php if(isset($data)){ ?>
                                <input type="hidden" class="form-control" name="flow_id" value="<?php if(isset($data)){ echo $data['flow_id']; } ?>">
                                <?php } ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-2 control-label">
                                备注 :
                            </label>
                            <div class="col-sm-8">
                                <textarea name="flow_note" class="form-control"><?php if(isset($data)){ echo $data['flow_note'];}  ?></textarea>
                            </div>
                        </div>
                    </div>

                </div>
                <div role="tabpanel" class="tab-pane" id="flow">
                    <div>
                        <button class="btn btn-addition" type="button" onclick="addForm()"><i class="icon-plus"></i> 新建</button>
                        <button class="btn btn-addition" type="button" onclick="refresh()"><i class="icon-refresh"></i> 刷新</button>
                        <!-- <button class="btn btn-danger" type="button"><i class="icon-remove"></i> 删除</button> -->
                    </div>
                    <div style="margin-top: 15px;margin-bottom:20px;">
                        <ul class="flow-list-heading clearfix">
                            <li>序号</li>
                            <li>名称</li>
                            <li>权限</li>
                            <li>处理方式</li>
                            <li>操作</li>
                        </ul>
                        <ul id="flow-list" class="clearfix">
                            <?php if(isset($data_detail)&&!empty($data_detail)){?>
                            <?php foreach ($data_detail as  $value) {?>
                            <li class="ui-state-default">
                                <span><?= $value['id']?></span>
                                <span><?= $value['step_name']?></span>
                                <span><?= $value['step_popedom_code']?></span>
  								<span><?php switch ($value['action_type']) {
                    case 1:
                      echo '自动';
                      break;
                    case 0:
                      echo '手动';
                      break;
                    default:
                      echo '未知';
                      break;
                  }?></span>
  								<span>
  									<a href="javascript:;" onclick="editAddForm(<?php if(isset($value['id'])){ echo $value['id'];} ?>)" >修改</a> | <a href="javascript:;" onclick="deletes(<?php echo $value['id']; ?>)">删除</a>
  								</span>
                            </li>
                            <?php }?>
                            <?php }?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <div class="text-center">
            <button type="submit" class="btn btn-primary" style="margin-left:25px;">提 交</button>
            <!-- <button class="btn btn-negative" type="button" onclick="location.href='lists'">回列表</button> -->
            <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'workflow','action'=>'lists')); ?>" class="btn btn-danger">返回</a>-->
            <a type="button" id="cancel_submit" class="btn btn-danger">返回</a>
        </div>
    </form>
</div>
<div class="modal fade" id="add-flow" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">新建流程</h4>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" id="add-form" action="<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'addeditDetail')); ?>" method="post">
                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">
                            上一步 :
                        </label>
                        <div class="col-sm-8">
                            <select name="step_order" id="step_order" class="form-control">
                                <option>无</option>
                                <?php foreach ($data_detail as  $detail) {?>
                                <option value="<?= $detail['step_order']?>"><?= $detail['step_name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div> -->
                    <!-- <div class="form-group">
                            <label class="col-sm-2 control-label">
                                下一步 :
                            </label>
                            <div class="col-sm-8">
                                <select class="form-control">
                                    <option>无</option>
                                    <?php foreach ($data_detail as  $detail) {?>
                                        <option><?= $detail['step_name']?></option>
                                    <?php }?>
                                </select>
                            </div>
                        </div> -->
                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">
                            回退到 :
                        </label>
                        <div class="col-sm-8">
                            <select name="back_step_code" id="back_step_code" class="form-control">
                                <option value="">无</option>
                                <?php foreach ($data_detail as  $detail) {?>
                                <option value="<?= $detail['step_code']?>"><?= $detail['step_name']?></option>
                                <?php }?>
                            </select>
                        </div>
                    </div> -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            名称 :
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="step_name" name="step_name" />
                            <input type="hidden" class="form-control" id="form_flow_id" name="flow_id" value="<?php if(isset($data)){ echo $data['flow_id']; } ?>" />
                            <input type="hidden" class="form-control" id="form_id" name="id" value="" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            编码 :
                        </label>
                        <div class="col-sm-8">
                            <input type="text" id="step_code" name="step_code" class="form-control" />
                            <!-- <button class="btn btn-primary" style="vertical-align:top;margin-left:10px;">生成</button> -->
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="step_popedom_code" class="col-sm-2 control-label">权限名称</label>
                        <div class="col-sm-8">
                            <select class="form-control" id="step_popedom_code" name="step_popedom_code">
                                <option value=""><span>请选择对应的权限</span></option>
                                <?php foreach ($popedomlist_info as $popedomlist) {   ?>
                                <option value="<?php echo $popedomlist['popedomname'];?>" <?php if(isset($department_data) && $department_data['step_popedom_code']==$popedomlist['popedomname']){ echo 'selected';}  ?>>
                                <span><?php echo $popedomlist['popedomnote'];?></span>
                                </option>
                                <?php }   ?>
                            </select>
                        </div>

                    </div>

                    <!-- <div class="form-group">
                        <label class="col-sm-2 control-label">
                            外部URL :
                        </label>
                        <div class="col-sm-8">
                            <input type="text"  class="form-control" id="step_req_url" name="step_req_url" />
                        </div>
                    </div> -->
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            参数 :
                        </label>
                        <div class="col-sm-8">
                            <input type="text" class="form-control" id="step_bizinfo" name="step_bizinfo" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            处理方式 :
                        </label>
                        <div class="col-sm-8">
                            <select name="action_type" id="action_type" class="form-control">
                                <option value="1" >自动</option>
                                <option value="0">手动</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">
                            发送邮件 :
                        </label>
                        <div class="col-sm-8">
                            <select name="send_email" id="send_email" class="form-control">
                                <option value="0" >不发送</option>
                                <option value="1">发送给客户</option>
                                <option value="2">发送给管理员</option>
                                <option value="3">发送给客户和管理员</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-sm-offset-2">
                            <button type="submit" class="btn btn-primary" style="margin-left:25px;">提 交</button>
                            <button type="button" class="btn btn-danger" data-dismiss="modal">返 回</button>
                            <input type="reset" name="reset" id="add-form-reset" style="display: none;" />
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该步骤么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','jquery-ui-1.10.0.custom.min.js']); ?>
<?= $this->start('script_last'); ?>
<script>

    $('#work-flow-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            var array = new Array();
            $(".ui-state-default").each(
                    function(){
                        array.push($(this).children(0).html());
                    }
            );
            $("#flow_order").val(array);
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href='<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'lists')); ?>';
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            flow_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '流程名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                }
            },
            flow_note: {
                group: '.col-sm-6',
                validators: {
                    stringLength: {
                        min: 0,
                        max: 30,
                        message: '请保持在1-30位'
                    },
                }
            },
            flow_code: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '流程编码不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '流程编码不能有空格'
                    }
                }
            }
        }
    });

//取消修改基本信息
    $('#cancel_submit').on('click',function(){
        $.ajax({
            url:"<?=$this->Url->build(['controller'=>'Workflow','action'=>'cancelAddeditDetail']);?>",
            success:function(data){
//                location.href="<?php echo $this->Url->build(array('controller' => 'workflow','action'=>'lists')); ?>";
                window.history.go(-1)
            }

        });
    });

    $('#add-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){

            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    $('#add-flow').modal("hide");
                    refresh();
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            // step_name: {
            // 	group: '.col-sm-6',
            // 	validators: {
            // 		notEmpty: {
            // 			message: '步骤名称不能为空'
            // 		},
            // 		stringLength: {
            // 			min: 1,
            // 			max: 16,
            // 			message: '请保持在1-16位'
            // 		},
            // 	}
            // },
            step_name: {
                trigger: 'submit',
                validators: {
                    notEmpty: {
                        message: '步骤名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                }
            },
            step_code: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '步骤编码不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '步骤编码不能有空格'
                    }
                }
            }
        }
    });

    function editAddForm(flowId){
        $.ajax({
            url:'<?= $this->Url->build(['controller'=>'Workflow','action'=>'getDetail']); ?>/'+flowId,
            dataType:'json',
            success:function(e){
                // $('#step_order').val(e.per_order);
                // $('#back_step_code').val(e.back_step_code);
                $('#step_name').val(e.step_name);
                $('#step_code').val(e.step_code);
                $('#step_bizinfo').val(e.step_bizinfo);
                $('#action_type').val(e.action_type);
                $('#send_email').val(e.send_email);
                $('#form_flow_id').val(e.flow_id);
                $('#form_id').val(e.id);
                $('#step_popedom_code').val(e.step_popedom_code);
                $('#add-flow').modal("show");
                if($('#add-flow').find('.btn-primary').attr("disabled",'disabled')){
                    $('#add-flow').find('.btn-primary').removeAttr('disabled').blur();
                }
            }
        });

    }

    function addForm(){
        $("#add-form-reset").trigger("click");
        $('#form_id').val('');
        $('#form_flow_id').val("<?php if(isset($data)){ echo $data['flow_id']; } ?>");
        $('#add-flow').modal("show");
    }

    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: "<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'deteltDetail')); ?>",
                dataType: "json",
                data: {'id': id},
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        $('#modal-delete').modal("hide");
                        refresh();
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }

                }
            });
        })


    }


    function refresh(){
        flowId = "<?php if(isset($data)){ echo $data['flow_id']; } ?>";
        $.ajax({
            type: 'get',
            url: "<?php echo $this->Url->build(array('controller' => 'Workflow','action'=>'detailLists')); ?>/"+flowId,
            dataType: "json",
            success: function (data) {
                html = '';
                if (data != '') {
                    $.each(data, function (i, n) {
                        html +='<li class="ui-state-default"><span>'+n.id+'</span><span>'+n.step_name+'</span><span>';
                        if(n.step_popedom_code){
                            html+=n.step_popedom_code;
                        }
                        html+='</span><span>';
                        switch (n.action_type) {
                            case 1:
                                html += '自动';
                                break;
                            case 0:
                                html += '手动';
                                break;
                            default:
                                html += '未知';
                                break;
                        }
                        html += '</span><span><a href="javascript:;" onclick="editAddForm('+n.id+')" >修改</a> | <a href="javascript:;"  onclick="deletes('+n.id+')">删除</a></span></li>'
                        ;
                    })

                }
                $('#flow-list').html(html);
            }
        });
    }


    $( "#flow-list" ).sortable();
    $( "#flow-list" ).disableSelection();
</script>
<?= $this->end() ?>