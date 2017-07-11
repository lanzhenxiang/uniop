<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" action="<?php echo $this->Url->build(array('controller' => 'SetHardware','action'=>'addedit')); ?>" method="post">
        <div class="form-group">
            <label for="set_name" class="col-sm-2 control-label">计算能力</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="set_name"  id="set_name" placeholder="计算能力" value="<?php if(isset($department_data['set_name'])){ echo $department_data['set_name'];}  ?>">
                <?php if(isset($department_data)){ ?>
                <input type="hidden" class="form-control" name="set_id"  id="set_id" value="<?php if(isset($department_data)){ echo $department_data['set_id']; } ?>">
                <input type="hidden" class="form-control" name="set_code"  id="set_code" value="<?php if(isset($department_data['set_code'])){ echo $department_data['set_code']; } ?>">
                <?php } ?>
            </div>
        </div>
        <div class="form-group">
            <label for="set_code" class="col-sm-2 control-label">计算能力CODE
</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  placeholder="计算能力CODE"  value="<?php if(isset($department_data['set_code'])){ echo $department_data['set_code'];}  ?>" <?php if(isset($department_data['set_code'])){echo "disabled";}else{echo 'name="set_code"  id="set_code"';}?>>
                <p><i class="icon-info-sign"></i>&nbsp;请按CMDB规划填写</p>
            </div>
        </div>
        
        <div class="form-group">
            <label for="cpu_number" class="col-sm-2 control-label">CPU数量(核)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="cpu_number"  id="cpu_number" placeholder="cpu数量"  value="<?php if(isset($department_data['cpu_number'])){ echo $department_data['cpu_number'];}  ?>" >
            </div>
        </div>
        <div class="form-group">
            <label for="memory_gb" class="col-sm-2 control-label">内存大小(GB)</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="memory_gb"  id="memory_gb" placeholder="内存大小"  value="<?php if(isset($department_data['memory_gb'])){ echo $department_data['memory_gb'];}  ?>">
            </div>
        </div>
        <div class="form-group">
            <label for="gpu_gb" class="col-sm-2 control-label">GPU大小(MB)</label>
            <div class="col-sm-6">
                <select id="gpu_gb" name="gpu_gb" class="form-control" onchange="change()">
                    <option value="0" <?php if(isset($department_data['gpu_gb']) && $department_data['gpu_gb']==0){ echo 'selected';}  ?> >
                        <span>0</span>
                    </option>
                    <option value="512" <?php if(isset($department_data['gpu_gb']) && $department_data['gpu_gb']==512){ echo 'selected';}  ?> >
                        <span>512</span>
                    </option>
                    <option value="1024" <?php if(isset($department_data['gpu_gb']) && $department_data['gpu_gb']==1024){ echo 'selected';}  ?> >
                        <span>1024</span>
                    </option>
                    <option value="2048" <?php if(isset($department_data['gpu_gb']) && $department_data['gpu_gb']==2048){ echo 'selected';}  ?> >
                        <span>2048</span>
                    </option>
                    <option value="4096" <?php if(isset($department_data['gpu_gb']) && $department_data['gpu_gb']==4096){ echo 'selected';}  ?> >
                        <span>4096</span>
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="gpu_mode" class="col-sm-2 control-label">GPU模式</label>
            <div class="col-sm-6">
                <select id="gpu_mode" name="gpu_mode" class="form-control" onchange="change()">
                    <option value="0" <?php if(isset($department_data['gpu_mode']) && $department_data['gpu_mode']==0){ echo 'selected';}  ?> >
                        <span>无</span>
                    </option>
                    <option value="1" <?php if(isset($department_data['gpu_mode']) && $department_data['gpu_mode']==1){ echo 'selected';}  ?> >
                        <span>直通</span>
                    </option>
                    <option value="2" <?php if(isset($department_data['gpu_mode']) && $department_data['gpu_mode']==2){ echo 'selected';}  ?> >
                        <span>共享</span>
                    </option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="set_type" class="col-sm-2 control-label">适用范围</label>
            <div class="col-sm-6">
                <select id="set_type" name="set_type" class="form-control" onchange="change()">
                    <option value="云主机" <?php if(isset($department_data['set_type']) && $department_data['set_type']=='云主机'){ echo 'selected';}  ?>>
                        <span>云主机</span>
                    </option>
                    <option value="云桌面" <?php if(isset($department_data['set_type']) && $department_data['set_type']=='云桌面'){ echo 'selected';}  ?>>
                        <span>云桌面</span>
                    </option>
                    <option value="云主机与云桌面" <?php if(isset($department_data['set_type']) && $department_data['set_type']=='云主机与云桌面'){ echo 'selected';}  ?>>
                    <span>云主机与云桌面</span>
                    </option>
                </select>
            </div>
            <!-- <label class="control-label text-danger"><i class=" icon-asterisk" id="leixin"></i></label> -->
        </div>

        <div class="form-group ecs-form">
            <label for="price_day" class="col-sm-2 control-label">按日计费单价（元/日）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_day"  id="price_day" placeholder="按日计费单价（元/日）"  value="<?php if(isset($department_data['price_day'])){ echo $department_data['price_day'];}  ?>">
            </div>
        </div>
        <div class="form-group ecs-form">
            <label for="price_month" class="col-sm-2 control-label">按月计费单价（元/月）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_month"  id="price_month" placeholder="按月计费单价（元/月）"  value="<?php if(isset($department_data['price_month'])){ echo $department_data['price_month'];}  ?>">
            </div>
        </div>
        <div class="form-group ecs-form">
            <label for="price_year" class="col-sm-2 control-label">按年计费单价（元/年）</label>
            <div class="col-sm-6">
                <input type="text" class="form-control" name="price_year"  id="price_year" placeholder="按年计费单价（元/年）"  value="<?php if(isset($department_data['price_year'])){ echo $department_data['price_year'];}  ?>">
            </div>
        </div>

        <div class="form-group">
            <label for="set_note" class="col-sm-2 control-label">备注</label>
            <div class="col-sm-6">
                <textarea rows="4" type="text" class="form-control" name="set_note"  id="set_note" placeholder="备注"><?php if(isset($department_data['set_note'])){ echo $department_data['set_note'];}  ?></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">保存</button>
                <!--<a type="submit" href="<?php echo $this->Url->build(array('controller' => 'SetHardware','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>
    </form>
</div>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>

<?= $this->start('script_last'); ?>
<script type="text/javascript">

    function showPrice(){
        var set_type = $('#set_type').val();
        if(set_type == "云主机" || set_type=='云主机与云桌面'){
            $('.ecs-form').show();
        }else{
            $('.ecs-form').hide();
        }
    }
    showPrice();
    $('#set_type').change(showPrice);


    $('form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        feedbackIcons: {
            valid: 'glyphicon glyphicon-ok',
            invalid: 'glyphicon glyphicon-remove',
            validating: 'glyphicon glyphicon-refresh'
        },
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg,0);
                    location.href='<?php echo $this->Url->build(array('controller'=>'SetHardware','action'=>'index'));?>';
                }else{
                    tentionHide(data.msg,1);
                }
            });
        },
        fields : {
            set_name : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '套餐名称不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 16,
                        message: '请保持在2-16位'
                    },
                    regexp: {
                        regexp: /^\S*$/,
                        message: '套餐名称中不能有空格'
                    }
                }
            },
            set_code : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '套餐Code不能为空'
                    },
                    stringLength: {
                        min: 2,
                        max: 30,
                        message: '请保持在2-30位'
                    },
                    regexp: {
                        regexp: /^[^\u4e00-\u9fa5\s]{0,}$/,
                        message: '套餐Code不能有中文和空格'
                    }
                }
            },
            cpu_number: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: 'CPU不能为空'
                    },
                    between: {
                        min: 1,
                        max: 64,
                        message: 'CPU只能是1或2-64之间的偶数'
                    },
                    regexp: {
                        regexp: /^(([1-9][0-9]*)?[02468]|1)$/,
                        message: 'CPU只能是1或2-64之间的偶数'
                    }
                }
            },
            memory_gb: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '内存不能为空'
                    },
                    between: {
                        min: 1,
                        max: 64,
                        message: '内存只能是1或2-64之间的偶数'
                    },
                    regexp: {
                        regexp: /^(([1-9][0-9]*)?[02468]|1)$/,
                        message: '内存只能是1或2-64之间的偶数'
                    }
                }
            },
            provider : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '供应商不能为空'
                    }
                } 
            },
            price_day : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            },
            price_month : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            },
            price_year : {
                group: '.col-sm-6',
                validators : {
                    notEmpty: {
                        message: '价格不能为空'
                    },
                    numeric: {
                        message: '请填写正确的价格'
                    },
                    greaterThan :{
                        value :0,
                        inclusive : true,
                        message: '价格必须大于0'
                    }
                } 
            }
//            2017-3-13,不进行判断
//            set_type :{
//                group: '.col-sm-6',
//                validators : {
//                    remote: {//ajax验证。server result:{"valid",true or false} 向服务发送当前input name值，获得一个json数据。例表示正确：{"valid",true}
//                         url: '/admin/SetHardware/isAllowEdit',//验证地址
//                         message: '计算能力已经绑定厂商地区或云桌面规格，请先解除绑定再修改类型,',//提示消息
//                         delay :  1000,//每输入一个字符，就发ajax请求，服务器压力还是太大，设置2秒发送一次ajax（默认输入一个字符，提交一次，服务器压力太大）
//                         type: 'POST',//请求方式
//                         data: function(validator) {
//                            return {
//                                   set_id: $('#set_id').val(),
//                                   set_type: $('#set_type').val(),
//                                   set_code: $('#set_code').val()
//                            };
//                        }
//                     }
//                }
//            }
        }
    });

    function change(){
        $("button[type='submit']").attr({disabled:false});
    }
    function back(){

    }


</script>
<?= $this->end() ?>