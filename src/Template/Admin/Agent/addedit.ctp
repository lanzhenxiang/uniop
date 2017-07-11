<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="agent-form" action="<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'addedit')); ?>" method="post">
        <div>
            <!-- Nav tabs -->
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基本信息</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">机房镜像信息</a></li>
                <li role="presentation"><a href="#messages" aria-controls="messages" role="tab" data-toggle="tab">机房套餐信息</a></li>
            </ul>
            <!-- Tab panes -->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <?php if(isset($department_data)){ $agentinfo=$department_data['agent'][0];}?>
                    <div class="form-group">
                        <label for="agent_name" class="col-sm-2 control-label">厂商或者地域名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="agent_name"  id="agent_name" placeholder="厂商或者地域名称" value="<?php if(isset($agentinfo['agent_name'])){ echo $agentinfo['agent_name'];}  ?>">
                            <?php if(isset($agentinfo)){ ?>
                            <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($agentinfo)){ echo $agentinfo['id']; } ?>">
                            <?php } ?>
                            <input type="hidden" class="form-control" name="display_name"  id="display_name" value="">
                        </div>

                    </div>
                    <div class="form-group">
                        <label for="region_code" class="col-sm-2 control-label">区域代码-与底层交互用</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="region_code"  id="region_code" placeholder="区域代码-与底层交互用"  value="<?php if(isset($agentinfo['region_code'])){ echo $agentinfo['region_code'];}  ?>">
                        </div>
                        <!-- <label id="" style="color:#ac2925;margin-top: 5px;">* 必填项</label> -->
                    </div>
                    <div class="form-group">
                        <label for="agent_code" class="col-sm-2 control-label">厂商或者地域代码</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="agent_code"  id="agent_code" placeholder="厂商或者地域代码"  value="<?php if(isset($agentinfo['agent_code'])){ echo $agentinfo['agent_code'];}  ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="parentid" class="col-sm-2 control-label">选择厂商</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="parentid" name="parentid">
                                <option value="0" data-name="顶级厂商"><span>顶级厂商</span></option>
                                <?php foreach ($query['agent'] as $key => $value) {   ?>
                                <option data-name ="<?php echo $value->agent_name;?>" value="<?php echo $value->id;?>" <?php if(isset($agentinfo) && $agentinfo['parentid']==$value->id){ echo 'selected';}  ?>>
                                    <span><?php echo $value->agent_name;?></span>
                                </option>
                                <?php }   ?>
                            </select>
                        </div>

                    </div>
                    <div class="col-sm-offset-2 col-sm-10" style="margin-top:-10px;margin-bottom:10px">
                        <label class="control-label text-danger"><i class="icon-exclamation-sign">创建厂商时选择顶级厂商，创建地域时选择对应的厂商</i></label>
                    </div>
                    <input type="hidden" name="class_code"  id="class_code" value="<?php if(isset($agentinfo['class_code'])){ echo $agentinfo['class_code'];}  ?>">
                   <!--  <div class="form-group">
                        <label for="class_code" class="col-sm-2 control-label">分类代码</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="class_code"  id="class_code" placeholder="分类代码"  value="<?php if(isset($agentinfo['class_code'])){ echo $agentinfo['class_code'];}  ?>">
                        </div>
                        <label class="control-label text-danger"><i class=" icon-asterisk" id="fenlei"></i></label>
                    </div> -->
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">是否提供云桌面</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="is_desktop" <?php if(isset($agentinfo['is_desktop'])){ if($agentinfo['is_desktop']==1){ echo 'checked'; } } ?> value="1"> 提供
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_desktop" <?php if(isset($agentinfo['is_desktop'])){ if($agentinfo['is_desktop']==0){ echo 'checked'; } }else{ echo 'checked';} ?> value="0"> 不提供
                            </label>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">是否可用</label>
                        <div class="col-sm-6">
                            <label class="radio-inline">
                                <input type="radio" name="is_enabled" <?php if(isset($agentinfo['is_enabled'])){ if($agentinfo['is_enabled']==1){ echo 'checked'; } }else{ echo 'checked';} ?> value="1"> 可用
                            </label>
                            <label class="radio-inline">
                                <input type="radio" name="is_enabled" <?php if(isset($agentinfo['is_enabled'])){ if($agentinfo['is_enabled']==0){ echo 'checked'; } } ?> value="0"> 不可用
                            </label>
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-2 control-label">虚拟化技术</label>
                        <div class="col-sm-6">
                            <?php foreach($query['virtual'] as $key => $value){ ?>
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
                        </div>

                    </div>
                </div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input id="all-imagelist" type="checkbox"/></th>
                                <th>id</th>
                                <th>镜像名称</th>
                                <th>镜像代码</th>
                                <th>操作系统</th>
                                <th>镜像分类（业务）</th>
                            </tr>
                        </thead>
                        <tbody>
                            <input name="check-image" type="hidden" id="check-iamge">
                            <?php if(isset($query)){
                                foreach($query['imagelist'] as $value){
                                    ?>
                                    <tr>
                                        <td><input name="imagelist" value="<?php if(isset($value['id'])){ echo $value['id'];} ?>" <?php if(isset($department_data['image_id'])){ if (in_array($value['id'],$department_data['image_id'])){echo 'checked';}}?> type="checkbox"/></td>
                                        <td><?php if(isset($value['id'])){ echo $value['id'];} ?></td>
                                        <td><?php if(isset($value['image_name'])){ echo $value['image_name'];}?></td>
                                        <td><?php if(isset($value['image_code'])){ echo $value['image_code'];} ?></td>
                                        <td><?php if(isset($value['os_family'])){ echo $value['os_family'];} ?></td>
                                        <td><?php if(isset($value['plat_form'])){ echo $value['plat_form'];} ?></td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div role="tabpanel" class="tab-pane" id="messages">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th><input id="all-hardware" type="checkbox"/></th>
                                <th>id</th>
                                <th>套餐名称</th>
                                <th>套餐代码</th>
                                <th>CPU</th>
                                <th>内存</th>
                                <th>GPU</th>
                                <th>套餐说明</th>
                                <th>供应商</th>
                            </tr>
                        </thead>
                        <tbody>
                            <input name="check-hardware" type="hidden" id="check-hardware">
                            <?php if(isset($query)){
                                foreach($query['sethardware'] as $value){
                                    ?>
                                    <tr>
                                        <td><input name="hardware" <?php if(isset($department_data['set_id'])){ if (in_array($value['set_id'],$department_data['set_id'])){echo "checked";}}?> type="checkbox" value="<?php if(isset($value['set_id'])){ echo $value['set_id'];} ?>"/></td>
                                        <td><?php if(isset($value['set_id'])){ echo $value['set_id'];} ?></td>
                                        <td><?php if(isset($value['set_name'])){ echo $value['set_name'];} ?></td>
                                        <td><?php if(isset($value['set_code'])){ echo $value['set_code'];} ?></td>
                                        <td><?php if(isset($value['cpu_number'])){ echo $value['cpu_number'];} ?></td>
                                        <td><?php if(isset($value['memory_gb'])){ echo $value['memory_gb'];} ?></td>
                                        <td><?php if(isset($value['gpu_gb'])){ echo $value['gpu_gb'];} ?></td>
                                        <td><?php if(isset($value['set_note'])){ echo $value['set_note'];} ?></td>
                                        <td><?php if(isset($value['provider'])){ echo $value['provider'];} ?></td>
                                    </tr>
                                    <?php
                                }
                            } ?>
                        </tbody>
                    </table>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" id="ds" class="btn btn-primary">保存</button>
                        <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'index')); ?>" class="btn btn-danger">返回</a>
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
        all=$('#parentid').find("option:selected").attr('data-name');
        all=all.replace(/\s+/g,"");
        if(all!='顶级供应商'){
            $('#display_name').val(all+"-"+name);
        }else{
            $('#display_name').val(name);
        }

        $.post(form.attr('action'), form.serialize(), function(data){
             var data = eval('(' + data + ')');
                    if(data.code==0){
                        tentionHide(data.msg,0);
                        location.href='<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'index'));?>';
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
                        message: '厂商或地域名称不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '厂商或地域名称不能有空格'
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
                        message: '厂商或地域代码不能有空格'
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