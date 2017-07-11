<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="service-form" action="<?php echo $this->Url->build(array('controller'=>'ChargeTemplate','action'=>'addedit'));?>" method="post">
        <div class="form-group">
            <label for="service_name" class="col-sm-2 control-label">模板名称</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="template_name"  id="template_name" placeholder="模板名称" value="<?php if(isset($data['template_name'])){ echo $data['template_name'];}  ?>">
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($data)){ echo $data['id']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按天计费公式</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="charge_expression1"  id="charge_expression1" placeholder="计费公式"  value="<?php if(isset($data['charge_expression1'])){ echo $data['charge_expression1'];}  ?>">
            </div>
            <div class="col-sm-offset-2 col-sm-10" style="margin-top:5px;margin-bottom:-8px">
                <label class="control-label text-danger"><i class="icon-exclamation-sign">计费时长使用 T 表示,eip带宽，存储容量 用 N 表示,计算符号请使用' + (加)',' - (减)',' * (乘)',' / (除)',请使用英文小括号()</i></label>
            </div>
         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按月计费公式</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="charge_expression2"  id="charge_expression2" placeholder="计费公式"  value="<?php if(isset($data['charge_expression2'])){ echo $data['charge_expression2'];}  ?>">
            </div>

         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按年计费公式</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="charge_expression4"  id="charge_expression4" placeholder="计费公式"  value="<?php if(isset($data['charge_expression4'])){ echo $data['charge_expression4'];}  ?>">
            </div>
          
         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">按量计费公式(桌面)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="charge_expression"  id="charge_expression" placeholder="计费公式"  value="<?php if(isset($data['charge_expression'])){ echo $data['charge_expression'];}  ?>">
            </div>
          
         </div>

         <div class="form-group">
            <label for="service_code" class="col-sm-2 control-label">模板类型</label>
            <div class="col-sm-6">
            <select id="tag" name="tag" class="form-control">
                 <option value="">主机/云桌面</option>
                 <option value="eip" <?php if(isset($data['tag']) && $data['tag'] == 'eip'){echo 'selected'; }  ?> >eip</option>
                 <option value="disks" <?php if(isset($data['tag']) && $data['tag'] == 'disks'){echo 'selected'; }  ?>>硬盘</option>
            </select>
             
            </div>
          
         </div>




        <div class="form-group">
            <label for="department" class="col-sm-2 control-label">所属租户</label>
            <div class="col-sm-6">
                <select id="department" name="department_id" class="form-control">
                    <?php if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname')) && in_array('cmop_global_tenant_admin',$this->request->session()->read('Auth.User.popedomname'))){
                        foreach ($dept as $key => $value) {
                            if($value['id'] == $this->request->session()->read('Auth.User.department_id')){
                            ?>
                        <option value="<?php echo $value['id'];?>" <?php if(isset($data['department_id']) && $data['department_id']==$value['id']){ echo 'selected';}elseif($value['id']==$this->request->session()->read('Auth.User.department_id')){ echo 'selected';}  ?> >
                            <span><?php echo $value['name'];?></span>
                        </option>
                       <?php }} }elseif(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){
                        foreach ($dept as $key => $value) {
                        ?>
                        <option value="<?php echo $value['id'];?>" <?php if(isset($data['department_id']) && $data['department_id']==$value['id']){ echo 'selected';}  ?> >
                            <span><?php echo $value['name'];?></span>
                        </option>
                    <?php } }  ?>
                </select>
            </div>
        </div>

        <div class="form-group">
            <label for="min_instance" class="col-sm-2 control-label">计费说明</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="charge_note"  id="charge_note" placeholder="计费说明"  value="<?php if(isset($data['charge_note'])){ echo $data['charge_note'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="service_submit" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'ChargeTemplate','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>

    </form>
</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','validator.bootstrap.js','jquery.uploadify.min.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$('#service-form').bootstrapValidator({
    submitButtons: 'button[type="submit"]',
    submitHandler: function(validator, form, submitButton){

        $.post(form.attr('action'), form.serialize(), function(data){
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                if(data.source==1){
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeTemplate','action'=>'check'));?>/'+data.id;
                }else {
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ChargeTemplate','action'=>'index'));?>/index';
                }
            } else {
                tentionHide(data.msg,1);
            }
        });
    },
    fields : {
        template_name: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '模板名称不能为空'
                },
                stringLength: {
                    min: 2,
                    max: 16,
                    message: '请保持在2-16位'
                },
                regexp: {
                    regexp: /^\S*$/,
                    message: '模板名称中不能有空格'
                }
            }
        },
        charge_expression: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '计费公式不能为空'
                },
                regexp: {
                    regexp: /([T\d,N\d]+(\.[T\d,N\d]+)?([+*\/-][T\d,N\d]+(\.[T\d,N\d]+)?)+)/,
                    message: '请输入正确的计算公式'
                }
            }
        },
        charge_expression1: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '计费公式不能为空'
                },
                regexp: {
                    regexp: /([T\d,N\d]+(\.[T\d,N\d]+)?([+*\/-][T\d,N\d]+(\.[T\d,N\d]+)?)+)/,
                    message: '请输入正确的计算公式'
                }
            }
        },
        charge_expression2: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '计费公式不能为空'
                },
                regexp: {
                   regexp: /([T\d,N\d]+(\.[T\d,N\d]+)?([+*\/-][T\d,N\d]+(\.[T\d,N\d]+)?)+)/,
                    message: '请输入正确的计算公式'
                }
            }
        },
        charge_expression4: {
            group: '.col-sm-6',
            validators: {
                notEmpty: {
                    message: '计费公式不能为空'
                },
                regexp: {
                   regexp: /([T\d,N\d]+(\.[T\d,N\d]+)?([+*\/-][T\d,N\d]+(\.[T\d,N\d]+)?)+)/,
                    message: '请输入正确的计算公式'
                }
            }
        },
        charge_note: {
            group: '.col-sm-6',
            validators: {
                stringLength: {
                    min: 1,
                    max: 30,
                    message: '1-30位'
                }
            }
        }
    }

});
</script>
<?= $this->end() ?>