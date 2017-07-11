<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="agent-form" action="<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'areaedit')); ?>/<?= $_agent["id"] ?>/0" method="post">
        <div>
            <!-- Nav tabs -->
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <?php if(isset($department_data)){ $agentinfo=$department_data['agent'][0];}?>
                    <div class="form-group">
                    <label for="agent_name" class="col-sm-2 control-label">云厂商名称</label>
                        <div class="col-sm-6">
                            <label class="col-sm-2 control-label"><?= $_agent["agent_name"] ?></label>
                        </div>
                        </div>
                    <div class="form-group">

                        <label for="agent_name" class="col-sm-2 control-label">地域名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="agent_name"  id="agent_name" placeholder="地域名称" value="<?php if(isset($agentinfo['agent_name'])){ echo $agentinfo['agent_name'];}  ?>">
                            <?php if(isset($agentinfo)){ ?>
                            <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($agentinfo)){ echo $agentinfo['id']; } ?>">
                            <?php } ?>
                            <input type="hidden" class="form-control" name="display_name"  id="display_name" value="<?= $_agent["agent_name"] ?>">
                            <input type="hidden" class="form-control" name="parentid"  id="parentid" value="<?= $_agent["id"] ?>">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="region_code" class="col-sm-2 control-label">地域CODE</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="region_code"  id="region_code" placeholder="地域CODE,请按CMDB规划填写"  value="<?php if(isset($agentinfo['region_code'])){ echo $agentinfo['region_code'];}  ?>">
                        </div>
                    </div>
                    <input type="hidden" name="class_code"  id="class_code" value="<?php if(isset($agentinfo['class_code'])){ echo $agentinfo['class_code'];}  ?>">
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">通用IaaS</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="is_enabled" <?php if(isset($agentinfo['is_enabled'])){ if($agentinfo['is_enabled']==1){ echo 'checked'; } }else{ echo 'checked';} ?> value="1"> 开放
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_enabled" <?php if(isset($agentinfo['is_enabled'])){ if($agentinfo['is_enabled']==0){ echo 'checked'; } } ?> value="0"> 不开放
                            </label>
                            <p>新建通用IaaS资源（如新建VPC、子网、主机等）时，在设置部署区时，厂商位选项组中是否出现此厂商</p>
                        </div>

                    </div>

                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">云桌面</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="is_desktop" <?php if(isset($agentinfo['is_desktop'])){ if($agentinfo['is_desktop']==1){ echo 'checked'; } } ?> value="1"> 开放
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_desktop" <?php if(isset($agentinfo['is_desktop'])){ if($agentinfo['is_desktop']==0){ echo 'checked'; } }else{ echo 'checked';} ?> value="0"> 不开放
                            </label>
                            <p>新建云桌面时，在设置部署区位时，厂商选项组中，是否出现此厂商</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label">虚拟化技术</label>
                        <div class="col-sm-6">
                            <?php foreach($_query['virtual'] as $key => $value){ ?>
                            <label class="checkbox-inline">
                                <input type="checkbox" data-bv-field="virtual_technology[]" name="virtual_technology[]" value="<?php echo $value['para_value'];?>"  <?php if(isset($agentinfo['virtual_technology'])){$arr=explode(',',$agentinfo['virtual_technology']);$condition="$value->para_value";if(in_array($condition,$arr)){echo 'checked';}}?>><?php echo $value['para_note'];?>
                            </label>
                            <?php }?>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sort_order" class="col-sm-2 control-label">排序</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($agentinfo['sort_order'])){ echo $agentinfo['sort_order'];}else{echo "100";}  ?>">
                            <p>该排序用于新建主机或桌面页面中，部署区位中厂商的排序；</p>
                            <p>请填正整数，排序数字相同时，按厂商名升序排序；</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="sort_order" class="col-sm-2 control-label">备注</label>
                        <div class="col-sm-6">
                            <textarea class="form-control" name="remark"  id="remark" placeholder="备注"  value=""><?php if(isset($agentinfo['remark'])){ echo $agentinfo['remark'];}  ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" id="ds" class="btn btn-primary">保存</button>
                        <!--<a type="button" href="<?php echo $this->Url->build(array('controller'=>'agent','action'=>'arealist')); ?>/<?= $_agent["id"] ?>" class="btn btn-danger">返回</a>-->
                        <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
                    </div>
                </div>
            </div>

        </div>

    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#all-imagelist').on('click',function(){
        if($('#all-imagelist').is(":checked")){
            $("input:checkbox[name='imagelist']").prop('checked','true')
        }else{
            $("input:checkbox[name='imagelist']").prop('checked','');
        }
    })

    $("input:checkbox[name='imagelist']").on('click',function(){
        var imagelen = $("input:checkbox[name='imagelist']:checked").length;
        var imagelens =$("input:checkbox[name='imagelist']").length;
        if(imagelen == imagelens){
            $('#all-imagelist').prop('checked','true');
        }else{
            $('#all-imagelist').prop('checked','');
        }
    })
    $(function(){
        var imagelen = $("input:checkbox[name='imagelist']:checked").length;
        var imagelens =$("input:checkbox[name='imagelist']").length;
        if(imagelen == imagelens){
            $('#all-imagelist').prop('checked','true');
        }else{
            $('#all-imagelist').prop('checked','');
        }
    })

    $('#all-hardware').on('click',function(){
        if($('#all-hardware').is(":checked")){
            $("input:checkbox[name='hardware']").prop('checked','true')
        }else{
            $("input:checkbox[name='hardware']").prop('checked','');
        }
    })

    $("input:checkbox[name='hardware']").on('click',function(){
        var len = $("input:checkbox[name='hardware']:checked").length;
        var lens =$("input:checkbox[name='hardware']").length;
        if(len == lens){
            $('#all-hardware').prop('checked','true');
        }else{
            $('#all-hardware').prop('checked','');
        }
    })
    $(function(){
        var len = $("input:checkbox[name='hardware']:checked").length;
        var lens =$("input:checkbox[name='hardware']").length;
        if(len == lens){
            $('#all-hardware').prop('checked','true');
        }else{
            $('#all-hardware').prop('checked','');
        }
    })

    $(function(){
        var validator = $('#agent-form').bootstrapValidator().data('bootstrapValidator');
        $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
            validator.validate();
            if (!validator.isValid()) {
                return false;
            }else{
                $('#ds').removeAttr('disabled');
            }
        })
    });

$('#agent-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        $('#name').html('');
        $('#daima').html('');
        $('#paixu').html('');
        var classCode=$('#class_code').val();
        var imageID="";
        $("[name='imagelist']:checked").each(function(){
            imageID+=$(this).val()+",";
        })
        $("#check-iamge").val(imageID);
        var setID="";
        $("[name='hardware']:checked").each(function(){
            setID+=$(this).val()+",";
        })
        $("#check-hardware").val(setID);
        var parentName = $("#display_name").val();
        var childName = $("#agent_name").val();
        $('#display_name').val(parentName+"-"+childName);
        $.post(form.attr('action'), form.serialize(), function(data){
             var data = eval('(' + data + ')');
                    if(data.code==0){
                        tentionHide(data.msg,0);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'arealist'));?>/<?= $_agent["id"] ?>/0';
                    }else{
                        tentionHide(data.msg,1);
                    }
        });
    },
     fields : {
            agent_name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '厂商名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '厂商名称不能有空格'
                    }
                }
            },
            region_code: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '地域CODE不能为空'
                    }
                }
            },
            sort_order: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '排序不能为空'
                    },
                    between: {
                        min: 0,
                        max: 1000,
                        message: '排序只能在0-1000之间'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            agent_code: {
                group: '.col-sm-6',
                validators: {
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '厂商代码不能有空格'
                    }
                }
            },
            'virtual_technology[]': {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '请选择至少一项虚拟化技术'
                    }
                }
            }
        }
});
</script>
<?= $this->end() ?>