<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" enctype="multipart/form-data" id="console-categroy-form" action="<?php echo $this->Url->build(array('controller' => 'ConsoleCategory','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="name" class="col-sm-2 control-label">菜单名称(后台区分)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="name"  id="name" placeholder="菜单名称" value="<?php if(isset($department_data['name'])){ echo $department_data['name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="id"  id="id" value="<?php if(isset($department_data)){ echo $department_data['id']; } ?>">
                <?php } ?>
            </div>
            
        </div>
        <div class="form-group">
            <label for="label" class="col-sm-2 control-label">标签（用于展示）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="label"  id="label" placeholder="标签"  value="<?php if(isset($department_data['label'])){ echo $department_data['label'];}  ?>">
            </div>
            
        </div>
        <div class="form-group">
            <label for="parent_id" class="col-sm-2 control-label">上级菜单</label>
            <div class="col-sm-6">
                <select class="form-control" id="parent_id" name="parent_id">
                    <option value="0"><span>顶级菜单</span></option>
                    <?php foreach ($query as $key => $value) {   ?>
                    <option value="<?php echo $key;?>" <?php if(isset($department_data) && $department_data['parent_id']==$key){ echo 'selected';}  ?>>
                        <span><?php echo $value;?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            
        </div>
        <div class="form-group">
            <label for="icon" class="col-sm-2 control-label">图标</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="icon"  id="icon" placeholder="图标"  value="<?php if(isset($department_data['icon'])){ echo $department_data['icon'];}  ?>">
            </div>
            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="tubiao"></i></label> -->
        </div>
        <div class="form-group">
            <label for="url" class="col-sm-2 control-label">地址</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="url"  id="url" placeholder="地址"  value="<?php if(isset($department_data['url'])){ echo $department_data['url'];}  ?>">
            </div>
            <!-- <label class="control-label text-danger"><i class="icon-asterisk" id="dizhi"></i></label> -->
        </div>
        <div class="form-group">
            <label for="popedom_code" class="col-sm-2 control-label">权限名称</label>
            <div class="col-sm-6">
                <select class="form-control" id="popedom_code" name="popedom_code">
                    <option value=""><span>请选择菜单对应的权限</span></option>
                    <?php foreach ($popedomlist_info as $popedomlist) {   ?>
                    <option value="<?php echo $popedomlist['popedomname'];?>" <?php if(isset($department_data) && $department_data['popedom_code']==$popedomlist['popedomname']){ echo 'selected';}  ?>>
                        <span><?php echo $popedomlist['popedomnote'];?></span>
                    </option>
                    <?php }   ?>
                </select>
            </div>
            
        </div>
        <div class="form-group">
            <label for="sort_order" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="sort_order"  id="sort_order" placeholder="排序"  value="<?php if(isset($department_data['sort_order'])){ echo $department_data['sort_order'];}  ?>">
            </div>
            
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'ConsoleCategory','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<?php echo $this->Html->script('jquery.uploadify.min.js'); ?>
<script type="text/javascript">
    $('#console-categroy-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href="<?php echo $this->Url->build(array('controller'=>'ConsoleCategory','action'=>'index'));?>";
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            name: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '菜单名不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 30,
                        message: '请保持在1-30位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '菜单名不能有空格'
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
            label: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '标签不能为空'
                    },
                    stringLength: {
                        min: 1,
                        max: 16,
                        message: '请保持在1-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '标签不能有空格'
                    }
                }
            },
            popedom_code: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '权限名称不能为空'
                    }
                }
            }
        }
    }); 
</script>
<?= $this->end() ?>