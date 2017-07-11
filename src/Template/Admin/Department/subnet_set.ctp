<?= $this->element('content_header'); ?>

<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div>
        <div class="content-operate clearfix">
            <div class="form-group clearfix">
                <span>租户名:</span>

                <select class="form-control" id="department" name="department" style="width:10%;" onchange="changeinfo()">
                    <?php foreach($depart_data as $key => $value){?>
                    <option value="<?php echo $value['id'];?>"
                    <?php if($value['id']==$select_id){echo 'selected';}?>><?php echo $value['name'];?></option>
                    <?php }?>

                </select>

            </div>
            <div class="pull-left">
                <span style="font-size: 20px;">租户可选公共子网</span>
            </div>

        </div>
        <hr>

        <!--配额明细-->
        <!--table-->
        <div class="bot">
            <div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th><input  id="all-subnets" type="checkbox"></th>
                            <th style="width:20%">子网名称</th>
                            <th>子网code</th>
                            <!-- <th></th>
                            <th>余量</th>
                            <th>使用水位</th> -->
                        </tr>
                        </thead>
                        <tbody id="roles-content">



                        <?php if(isset($lists)){?>
                        <?php foreach($lists as $key => $value){?>
                        <tr style="text-align: center;">
                            <td><input  name="subnet_id" type="checkbox" <?php if(isset($selected_lists[$value['id']]) && $selected_lists[$value['id']] >0){?> checked="checked" <?php }?> value="<?=$value['id']?>"></td>
                            <td><?=$value['name']?></td>
                            <td><?=$value['code']?></td>
                            <!-- <td><?=$value['used'];?></td>
                            <td><?=$value['can_use'];?></td>
                            <td class="pro-parent">
                                <div class="quota">

                                    <div class="progress" style="margin-bottom:0;">
                                        <div class="progress-bar progress-bar-info" role="progressbar"
                                             aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                             style="width:<?=$value['percent'];?>">

                                        </div>
                                    </div>

                                </div>
                                <span class="pro-num"> <?=$value['percent'];?></span>
                            </td> -->
                        </tr>
                        <?php }?>
                        <?php }?>


                        </tbody>
                    </table>
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example' attrs="example">
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="role_id"  id="role_id">
                </div>
            </div>
            <div class="col-sm-offset-5">
                <a type="button"
                   href="javascript:void(0)"
                   onclick="saveSubnetConfig(<?=$select_id?>)"
                   class="btn btn-primary">保存配置</a>
                <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
                <a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>
            </div>
        </div>

    </div>

</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','layer/layer.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    function changeinfo(){
        var department_id=$('#department').val();
        location.href="<?=$this->Url->build(['controller'=>'Department','action'=>'subnetSet']);?>?id="+department_id;
    }

    function saveSubnetConfig(department_id)
    {
        var subnet_ids = "";
        $("input[name='subnet_id']:checkbox").each(function(){
            if($(this)[0].checked == true){
                subnet_ids += $(this).val() + ",";
            }
            
        });

        $.ajax({
                type: "post",
                url: "<?=$this->Url->build(['controller'=>'Department','action'=>'saveSubnetSet']);?>",
                async: true,
                timeout: 9999,
                data: {
                    subnet_ids: subnet_ids,
                    department_id: department_id
                },
                //dataType:'json',
                success: function(data) {
                    if(data.code == 0){
                        layer.alert(data.msg);
                    }else{
                        layer.msg(data.msg, {icon: 5});
                    }
                }
        });
    }

    $("#all-subnets").click(function(){
        var isChecked = $(this).prop("checked");
        $("input[name='subnet_id']").prop("checked", isChecked);
    });
</script>
<?= $this->end() ?>

