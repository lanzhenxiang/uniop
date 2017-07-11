<?= $this->element('content_header'); ?>

<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal"  id="department-form" action="<?php echo $this->Url->build(array('controller' => 'Departments','action'=>'postedit')); ?>" method="post">
        <div>
            <ul class="nav nav-tabs" role="tablist">
                <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">基础信息</a></li>
                <li role="presentation"><a href="#profile" aria-controls="profile" role="tab" data-toggle="tab">配额管理</a></li>
            </ul>
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">租户名称</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="name"  id="name" placeholder="租户名称" value="<?php if(isset($parent_depart)){ echo $parent_depart['name'];} ?>">
                            <input type="hidden" name="id"  id="id" value="<?php if(isset($parent_depart)){ echo $parent_depart['id'];} ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">租户code</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="dept_code"  id="dept_code" placeholder="租户code" value="<?php if(isset($parent_depart)){ echo $parent_depart['dept_code'];} ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">邮箱</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="email"  id="email" placeholder="邮箱" value="<?php if(isset($parent_depart)){ echo $parent_depart['email'];} ?>">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="type" class="col-sm-2 control-label">租户类型</label>
                        <div class="col-sm-6">
                            <select class="form-control" id="type" name="type" disabled>
                                <option value="normal" data-name="普通租户" <?php if(isset($parent_depart) && $parent_depart['type'] == 'normal'){echo "selected";}?>><span>普通租户</span></option>
                                <option value="platform" data-name="平台租户" <?php if(isset($parent_depart) && $parent_depart['type'] == 'platform'){echo "selected";}?>><span>平台租户</span></option>
                            </select>
                        </div>
                    </div>
                   <!-- <div class="form-group">
                        <label for="inputPassword3" class="col-sm-2 control-label">上级租户</label>
                        <div class="col-sm-6">
                            <input type="text" disabled="disabled" class="form-control" value="<?php /*if(isset($parent_depart)){ echo $parent_depart['department']['name'];} */?>">
                            <input type="hidden" class="form-control" name="parent_id"  id="parent_id"  value="<?php /*if(isset($parent_depart)){ echo $parent_depart['department']['id'];} */?>">
                        </div>
                    </div>-->
                    <!-- <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">排序</label>
                        <div class="col-sm-6">
                            <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($parent_depart)){ echo $parent_depart['sort_order'];} ?>">
                        </div>
                    </div> -->
                </div>
                <div role="tabpanel" class="tab-pane clearfix" id="profile">
                    <div style="width: 50%;float: left">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th><input type="checkbox"  id="total"></th>
                                    <th>参数代码</th>
                                    <th>参数值</th>
                                    <th>参数说明</th>
                                </tr>
                            </thead>
                            <?php if(isset($para)){
                                foreach($para as $value){
                                    ?>
                                    <tr>
                                        <td><input type="checkbox"  name="para_id" data-role="<?php echo $value['id']; ?>" <?php if(isset($para_code)){
                                            if(in_array($value['para_code'], $para_code)){echo 'checked';}
                                        } ?>></td>
                                        <td><?php echo $value['para_code']; ?></td>
                                        <td><?php echo $value['para_value']; ?></td>
                                        <td><?php echo $value['para_note']; ?></td>
                                    </tr>
                                    <?php
                                }
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
                <div class="department-add department-add pull-left">
                    <ul>
                        <?php if(isset($user_data)){
                            foreach ($user_data as $key=>$value) { ?>
                                <?php if(!($value['para_code']=="host_max_disk"||$value["para_code"]=="subnet_bugedt")){ ?>
                                <li class="add<?php echo $value['para_code'] ?>"><label><?php echo $value['para_note'] ?></label>
                                <!-- <input type="text" name="para_value[<?php /*echo $value['id'] */?>]" value="<?php /*echo $value['para_value'] */?>" />-->
                                <input type="text" value="<?php echo $value['para_value'] ?>" name="<?php echo $value['para_code'] ?>[para_value]">
                                <input type="hidden" value="<?php echo $value['para_note'] ?>" name="<?php echo $value['para_code'] ?>[para_note]">
                                <?php } ?>

                                <?php } } ?>
                            </ul>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="submit" id="ds" class="btn btn-primary">保存</button>
                        <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Departments','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                    </div>
                </div>
            </div>
        </form>
    </div>


    <?= $this->start('script_last'); ?>
    <?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
    <script type="text/javascript">
        $('#total').on('click',function(){
            if($('#total').is(":checked")){
                $("input:checkbox[name='para_id']").prop('checked','true');
                $('.department-add ul').empty();
                $("input:checkbox[name='para_id']").each(
                    function(){
                        var index = $(this).parent().siblings().eq(0).html();
                        var content = '<li class="add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><input type="text" name="' + $(this).parent().siblings().eq(0).html() + '[para_value]" value="' + $(this).parent().siblings().eq(1).html() + '" /><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
                        $('.department-add ul').append(content);
                    }
                    );
            }else{
                $("input:checkbox[name='para_id']").prop('checked','');
                $("input:checkbox[name='para_id']").each(
                    function(){
                        var index = ".add" + $(this).parent().siblings().eq(0).html();
                        $('.department-add li').remove(index);
                    }
                    );
            }
        });

$("input:checkbox[name='para_id']").on('click',function(){
    var len = $("input:checkbox[name='para_id']:checked").length;
    var lens =$("input:checkbox[name='para_id']").length;
    if($(this).is(':checked')){
        var index = $(this).parent().siblings().eq(0).html();
        var content = '<li class="add' + index + '"><label>' + $(this).parent().siblings().eq(2).html() + '</label><input type="text" name="' + $(this).parent().siblings().eq(0).html() + '[para_value]" value="' + $(this).parent().siblings().eq(1).html() + '" /><input type="hidden" name="' + $(this).parent().siblings().eq(0).html() + '[para_note]" value="' + $(this).parent().siblings().eq(2).html() + '" /></li>';
        $('.department-add ul').append(content);
    }else{
        var index = ".add" + $(this).parent().siblings().eq(0).html();
        $('.department-add li').remove(index);
    }
    if(len == lens){
        $('#total').prop('checked','true');
    }else{
        $('#total').prop('checked','');
    }
});

        $(function(){
            var validator = $('#department-form').bootstrapValidator().data('bootstrapValidator');
            $('a[data-toggle="tab"]').on('hide.bs.tab', function (e) {
                validator.validate();
                if (!validator.isValid()) {
                    return false;
                }else{
                    $('#ds').removeAttr('disabled');
                }
            })
        });


        $('#department-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){
        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg, 0);
                location.href = '<?php echo $this->Url->build(array('controller'=>'Departments','action'=>'index'));?>';
            } else {
                tentionHide(data.msg, 1);
            }
        });
    },
    fields : {
        name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '租户名不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '租户名中不能有空格'
                }
            }
        },
        dept_code: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '租户code不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                    message: '租户code中不能有空格和中文'
                }
            }
        },
        email: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '邮箱不能为空'
                },
                emailAddress: {
                    message: '邮箱格式不对'
                }
            }
        }
    }
});

</script>
<?= $this->end() ?>

