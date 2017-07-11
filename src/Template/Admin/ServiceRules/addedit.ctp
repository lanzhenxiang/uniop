<?= $this->element('content_header'); ?>
<style>
    #calculate-tips span{
        color:#999;
        cursor:pointer;
        margin-right: 5px;
    }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'ServiceRules','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="type_id" class="col-sm-2 control-label">服务名称</label>
            <div class="col-sm-6">
                <select class="form-control" id="type_id" name="type_id">
                    <?php foreach ($service_type_data as $service_type) {   ?>
                    <option value="<?= $service_type['type_id']?>" <?php if(isset($data) && $data['type_id']==$service_type['type_id']){ echo 'selected';}  ?>>
                        <span><?php echo $service_type['service_name'];?></span>
                    </option>
                    <?php }   ?>
                </select>
                <?php if(isset($data)){ ?>
                <input type="hidden" class="form-control" name="id"  value="<?php echo $data['id'];  ?>">
                <?php } ?>
            </div>
        </div>

        <div class="form-group">
            <label for="action_type" class="col-sm-2 control-label">弹性类型</label>
            <div class="col-sm-6"> 
             <select class="form-control" id="action_type" name="action_type">
                <option value="1" <?php if(isset($data) && $data['action_type']==1){ echo 'selected';}  ?>>
                    <span>关闭现有服务</span>
                </option>
                <option value="2" <?php if(isset($data) && $data['action_type']==2){ echo 'selected';}  ?>>
                    <span>开启新服务</span>
                </option>
            </select>
        </div>
    </div>
    <div class="form-group">
        <label for="inputEmail3" class="col-sm-2 control-label">计算规则</label>
        <div class="col-sm-6">
            <input type="text" class="form-control" name="rule_expression"  id="rule_expression" value="<?php if(isset($data['rule_expression'])){ echo $data['rule_expression'];}  ?>" />
            <!-- <button class="btn btn-primary" id="verify" style="margin-left:15px;vertical-align:top;">验  证</button> -->
            <p class="col-sm-10" id="calculate-tips">可选参数 : 
                <span value = "@busy_instance">忙碌实例数量</span>&nbsp;&nbsp; 
                <span value = "@free_instance">空闲实例数量</span>&nbsp;&nbsp; 
                <span value = "@wait_job">排队任务数</span>&nbsp;&nbsp; 
                <span value = "@min_instance">最小实例数量</span>&nbsp;&nbsp; 
                <span value = "@max_instance">最大实例数量</span>&nbsp;&nbsp; 
                <span value = "@process_efficiency">处理效率</span>&nbsp;&nbsp; 
                <span value = "@current_instance">当前实例数量</span>
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">权重</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="rule_weight" id="rule_weight" value="<?php if(isset($data['rule_weight'])){ echo $data['rule_weight'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">规则说明</label>
            <div class="col-sm-6">
                <textarea  class="form-control" name="rule_note" id="rule_note"><?php if(isset($data['rule_note'])){ echo $data['rule_note'];}  ?></textarea> 
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'ServiceRules','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    
    $('#aduser-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'ServiceRules','action'=>'index'));?>';
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields : {
            rule_weight: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '权重不能为空'
                    },
                    between: {
                        min: 1,
                        max: 100,
                        message: '权重只能在1-100之间'
                    },
                    regexp: {
                        regexp: /^([1-9]\d*|0)$/,
                        message: '请输入整数'
                    }
                }
            },
            rule_expression: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '计算规则不能为空'
                    },
                    // regexp: {
                    //     regexp: /^([1-9]\d*|0)$/,
                    //     message: '请输入整数'
                    // }
                }
            },
        }
    });

    $('#calculate-tips').on('click','span',function(){
        var text = $(this).attr('value');
        var dom = $('#rule_expression').get(0);
        $('#rule_expression').val(getCursor(dom,text));
    });


    function getCursor(obj,str){
        //非IE浏览器
        cursorPosition= obj.selectionStart;
        str = obj.value.substr(0,cursorPosition) + str + obj.value.substr(cursorPosition,obj.value.length-1);

        return str;
    }

    
</script>
<?= $this->end() ?>

