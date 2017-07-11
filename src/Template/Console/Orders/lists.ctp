<style>
    .content{
        margin-left:0;
    }
</style>

<?= $this->Html->script(['validator.bootstrap.js','jquery.uploadify.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<div class="wrap-nav-right wrap-index-page wrap-manage">

    <div class="order-main  section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                <?=$list_title?>
            </h5>
        </div>
        <div class="section-header clearfix order-manage">
            <!--  <h5 class="pull-left">
                 <?=$list_title?>
             </h5> -->
            <div class="pull-left">
                <button style="margin-left: 20px;" id="batch-submit" auth_action="1" class="btn btn-sm btn-addition" data-toggle="modal" data-target="#order-modal-batch" disabled="disabled">批量通过</button>
            </div>
            <div class="pull-left">
                <button style="margin-left: 20px;" id="batch-reback" class="btn btn-sm btn-addition" data-toggle="modal" data-target="#order-modal-batch-reback" auth_action="-1" disabled="disabled">批量退回</button>
            </div>
            <div class="pull-right">
                <button style="margin-left: 20px;" class="btn btn-addition" onclick="search()">查询</button>
            </div>
            <div class="pull-right order-number">
                订单号:<span class="search"><input type="text" id="txtsearch" value="<?= $search?>" name="search" placeholder="订单号">
                </span>
            </div>
            <div class="pull-right order-number">
                申请人:<span class="search"><input type="text" id="create_by" value="<?= $create_by?>" name="create_by" placeholder="申请人">
                </span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-end" data-date-format="yyyy-mm-dd">
                结束时间:
                <input size="16" type="text" name="time" id="end-time" value="<?php if($end !=-1 ) {echo $end;}?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>
            <div class="pull-right order-number input-append date" id="datetimepicker-start"  data-date-format="yyyy-mm-dd">
                开始时间:
                <input size="16" type="text" name="time" id="start-time" value="<?php if($start !=-1){ echo $start;} ?>" readonly>
                <span class="add-on"><i class="icon-th"></i></span>
            </div>

            <div class="dropdown pull-right">
                租户:
                <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="agent_t" val="<?=$department['id']?>"><?php if(isset($department)){echo $department['name'] ;}else{echo '全部';} ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="agent_two">
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'orders', 'action' => $this->request->params['action'],$start,$end,'all',$search]); ?>?search=<?=$search?>&create_by=<?=$create_by?><?php if(isset($_GET['file_position'])&&$_GET['file_position']=='admin'){echo '&file_position=admin';}?>">全部</a></li>
                    <?php
                        foreach($departments as $value) {
                            ?>
                    <li><a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'orders', 'action' => $this->request->params['action'],$start,$end,$value['id'],$search]); ?>?search=<?=$search?>&create_by=<?=$create_by?><?php if(isset($_GET['file_position'])&&$_GET['file_position']=='admin'){echo '&file_position=admin';}?>" ><?php echo $value['name'] ?></a></li>
                    <?php
                        }?>
                </ul>
            </div>

        </div>
        <div class="bot" id="order-panel">
            <div class="fixed-table-container">
                <div class="fixed-table-body">
                    <table class="table table-striped table-hover vertical-table">
                        <thead>
                        <tr>
                            <th><input type="checkbox"  name="ids[]" id="selectAll"></th>
                            <th>订单号</th>
                            <th>图片</th>
                            <th>商品规格配置</th>
                            <th>计费方式</th>
                            <th>购买量</th>
                            <th>单台原价</th>
                            <th>成交单价</th>
                            <th>操作</th>
                            <th>申请人</th>
                            <th>租户</th>
                            <th>提交时间</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php
                if (isset($data) && count($data)>0) {
                        foreach ($data as $key => $value) { ?>

                        <?php if(!isset($value['orders_goods'][0])) continue;?>
                        <tr>
                            <td><input type="checkbox" name="id" nctype="ids" value="<?=$value['id']?>"></td>
                            <td><?= $value['number']?></td>
                            <td>
                                <div class="order-photo">
                                    <?php
                            $goodInfo = $value['orders_goods'][0]->goodInfo;?>
                                    <?= $this->Html->image($value['orders_goods'][0]->mini_icon); ?>
                                </div>
                            </td>
                            <td>
                                <div class="goods-attribute" >
                                    <?php if(isset($value['orders_goods'][0]['good_type'])):?>
                                    <?=$this->element('order/goods/'.$value['orders_goods'][0]['good_type'],['good_info'=>$goodInfo,'value'=>$value]);?>
                                    <?php endif;?>
                                </div>
                            </td>
                            <td id="charge-mode-txt"><?=$value['orders_goods'][0]->chargeTxt?></td>
                            <?php if($value['orders_goods'][0]['good_type'] == 'mpaas' ||
                            $value['orders_goods'][0]['good_type'] == 'bs'
                        ):?>
                            <td>长期使用服务</td>
                            <?php else:?>
                            <td style="text-align:center"><?php if($value['status'] == 1): ?>
                                <input class="order-num-edit" style="width: 50px;" name="num" id="num" value="<?=$value['orders_goods'][0]['num']?>" min="1" max="9999" order_good_id ="<?=$value['orders_goods'][0]['id']?>" type="number">
                                <?php else:?>
                                <?=$value['orders_goods'][0]['num']?>
                                <?php endif;?></td>
                            <?php endif;?>

                            <td><?= $this->Number->format($value['orders_goods'][0]->price_per, ['places' => 4,'before' => '¥ ']);?>
                                元/<?=$value['orders_goods'][0]->UnitText ?>
                            </td>
                            <td id="tprice">

                                <?= $this->Number->format($value['orders_goods'][0]->transaction_price, ['places' => 4,'before' => '¥ ']);?>
                                元/<?=$value['orders_goods'][0]->UnitText ?>
                            </td>
                            <td>
                                <a  href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'orders', 'action' => 'orderlog',$value['id']])?>" class="btn btn-xs btn-default">订单日志</a><br/><br/>
                                <!-- 审批按钮 -->
                                <?= $this->cell('Workflow::listBtn',[$value['id'],$this->request->session()->read('Auth.User.id'),$value]); ?>
                                <?php if($value['orders_goods'][0]['good_type'] == 'ecs'
                            || $value['orders_goods'][0]['good_type'] == 'citrix_public'
                            || $value['orders_goods'][0]['good_type'] == 'eip'
                            || $value['orders_goods'][0]['good_type'] == 'vfw'
                            || $value['orders_goods'][0]['good_type'] == 'waf'
                            || $value['orders_goods'][0]['good_type'] == 'vpc'
                            || $value['orders_goods'][0]['good_type'] == 'elb'
                            || $value['orders_goods'][0]['good_type'] == 'disks'
                            ): ?>
                                <a
                                        href="<?= $this->Url->build(['prefix'=>'console','controller'=>'orders','action'=>'editOrderGoods','order_good_id'=>$value['orders_goods'][0]['id']]); ?>"
                                        good_id="<?=$value['orders_goods'][0]['id']?>" good-type="<?=$value['orders_goods'][0]['good_type']?>" class="btn btn-xs btn-default">修改配置</a><br/><br/>
                                <?php endif;?>
                                <?php if($value['orders_goods'][0]['good_type'] == 'citrix' || $value['orders_goods'][0]['good_type'] == 'citrix_public'):  ?>
                                <button class="btn btn-xs btn-default" nc_type="charge_mode" order_good_id="<?=$value['orders_goods'][0]->id?>" price="<?=$value['orders_goods'][0]->transaction_price?>">计费模式</button><br/><br/>
                                <?php else:?>
                                <button class="btn btn-xs btn-default" nc_type="price" order_good_id="<?=$value['orders_goods'][0]->id?>" price="<?=$value['orders_goods'][0]->transaction_price?>">修改单价</button><br/><br/>
                                <?php endif;?>
                                <?php if($value['orders_goods'][0]['good_type'] == 'citrix' || $value['orders_goods'][0]['good_type'] == 'citrix_public'):  ?>
                                <button class="btn btn-xs btn-default" nc_type="priority" order_good_id="<?=$value['orders_goods'][0]->id?>" priority_value="<?=$value['orders_goods'][0]->priority?>">修改优先级</button><br/><br/>
                                <?php endif;?>
                            </td>
                            <td><?=$value['account']['username']?></td>
                            <td><?=$value['department']['name']?></td>
                            <td><?=$this->Time->format($value['create_time'],'yyyy-MM-dd HH:mm:ss',false,'PRC')?></td>
                        </tr>
                        <?php }}else{?>
                        <tr>
                            <td colspan="12" style="text-align: center;">没有匹配记录</td>
                        </tr>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <div id="order-pagination">
                <nav class="pull-right">
                    <ul class="pagination">
                        <?php echo $this->Paginator->first('<<');?>
                        <?php echo $this->Paginator->numbers();?>
                        <?php echo $this->Paginator->last('>>');?>
                    </ul>
                </nav>
            </div>
        </div>
    </div>
</div>

</div>


<!-- 弹窗 -->
<div class="modal fade" id="order-modal" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="workflow">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">提示</h4>
                </div>
                <div class="modal-body">
                    <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span id="workflow-detail" class="text-primary">确认通过审核?</span></p>
                    <p style="margin-top:8px;"><textarea id="pass-note" name="auth_note" class="form-control" placeholder="通过备注"></textarea></p>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="order_id" name="order_id" />
                    <input type="hidden" id="auth_action" name="auth_action" />
                    <input type="hidden" id="flow_detail_name" name="flow_detail_name" />
                    <input type="hidden" id="flow_detail_id" name="flow_detail_id" />
                    <button type="button" class="btn btn-primary" id="order-submit">确 认</button>
                    <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!-- 修改单价 -->
<div class="modal fade" id="order-modal-edit" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="priceForm" method="post" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">提示</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-form-group">
                        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认修改单价?</span></p>
                    </div>
                    <div class="form-group">
                        <label class="col-md-2" for="price">成交单价</label>
                        <div class="col-md-5">
                            <input type="text" id="price"  name="price" value="" />
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="order_good_id" name="order_good_id" data-field="order_good_id" value="" >
                    <button type="submit" class="btn btn-primary" id="order-price-edit">确 认</button>
                    <button type="button" class="btn btn-danger" id="order-price-cancel" data-dismiss="modal">取 消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- 修改计费模式 -->
<div class="modal fade" id="charge-modal-edit" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form id="chargeModeForm" method="post" class="form-horizontal">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title" id="myModalLabel">修改计费方式</h4>
                </div>
                <div class="modal-body">
                    <div class="modal-form-group">
                        <!-- <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">修改计费方式</span></p> -->
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
                    <div class="modal-form-group">
                        <label>成交单价:</label>
                        <div class="form-group">
                            <input type="text" id="price"  name="price" value="" /><span id="UnitText" style="padding: 0 10px"></span>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <input type="hidden" id="orderGoodId" name="orderGoodId" data-field="orderGoodId" value="" >
                    <button type="submit" class="btn btn-primary" id="charge_submit">确 认</button>
                    <button type="button" class="btn btn-danger" id="charge_cancel" data-dismiss="modal">取 消</button>
                </div>
            </form>
        </div>
    </div>
</div>
<!--goodstype-->
<!-- 修改优先级 -->
<div class="modal fade" id="modal-priority" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">修改优先级</h5>
            </div>
            <form id="priority-form" action="" method="post">
                <div class="modal-body">
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
                    <input type="hidden" id="priority_id" name="priority_id" data-field="priority_id" value="" >
                    <button id="priority_submit" type="submit" class="btn btn-primary">保存</button>
                    <button id="priority_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="order-modal-batch" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">提示</h4>
            </div>
            <div class="modal-body">
                <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">是否批量通过选中订单?</span></p>
                <p style="margin-top:8px;"><textarea id="pass-notes" name="auth_note" class="form-control"></textarea></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="order-batch-submit">确 认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="order-modal-batch-reback" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">提示</h4>
            </div>
            <div class="modal-body">
                <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">是否批量退回选中订单?</span></p>
                <p style="margin-top:8px;"><textarea id="reback-notes" name="auth_note" class="form-control"></textarea></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="order-batch-reback">确 认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">

    //单选
    $("input[name='id']").click(function(){
        toggleButton();
    });
    //全选
    $('#selectAll').click(function(){
        var isChecked = $(this).prop("checked");
        $("input[name='id']").prop("checked", isChecked);
        toggleButton();
    });

    function toggleButton(){
        var isChecked = $("[nctype='ids']").is(':checked');
        $("#batch-submit").attr('disabled',!isChecked);
        $("#batch-reback").attr('disabled',!isChecked);
    }

    $("[bt='btn-workflow']").click(function(){
        $('#order_id').val($(this).attr('order_id'));
        $('#auth_action').val($(this).attr('auth_action'));
        $('#flow_detail_name').val($(this).attr('flow_detail_name'));
        $('#flow_detail_id').val($(this).attr('to_detail_id'));

        var text = $(this).attr('auth_action') == 1 ? '确认通过审核?' : '确认退回审核?';
        var placeholder = $(this).attr('auth_action') == 1 ? '通过备注' : '退回原因';
        $('#workflow-detail').html(text);
        $('#pass-note').attr("placeholder",placeholder);

        $('#order-modal').modal('show');
    });

    $("input[class='order-num-edit']").click(function(){
        $(this).focus();
    });
    $("input[class='order-num-edit']").blur(function(){
        var num = $(this).val();
        var order_good_id = $(this).attr('order_good_id');
        var _data = {
            'num' : num,
            'order_good_id': order_good_id
        };
        var settings = {
            url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'orders','action'=>'updateNum']); ?>",
            type:"post",
            dataType:"json",
            data:_data,
            success:function(response){
                //if( parseInt(response.code)!= 0 ){
                //TODO 修改样式
                alert(response.msg);
                window.location.href=window.location.href;
                //}
            }
        };
        $.ajax(settings);
    });


    $('#order-submit').click(
            function(){
                $('#order-modal').modal('hide');
                $('#order-modal').one('hidden.bs.modal',function(){
                    /* ajax事件 */
                    var btn = $('#btn-pass');
                    var _data = $('#workflow').serialize();
                    var settings = {
                        url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'workflow','action'=>'auth']); ?>",
                        type:"post",
                        dataType:"json",
                        data:_data,
                        success:function(response){
                            //if( parseInt(response.code)!= 0 ){
                            //TODO 修改样式
                            alert(response.msg);
                            window.location.href=window.location.href;
                            //}
                        }
                    };
                    $.ajax(settings);
                });
            }
    );


    $('#order-batch-reback').click(
            function(){
                $('#order-modal-batch-reback').modal('hide');
                $('#order-modal-batch-reback').one('hidden.bs.modal',function(){
                    /* ajax事件 */
                    var btn = $('#batch-reback');
                    var order_ids = '';
                    $("input[name='id']").each(function(){
                        if($(this).is(":checked")){
                            order_ids += $(this).val()+',';
                        }
                    })
                    var _data = {
                        "order_ids":order_ids,
                        'auth_action':btn.attr('auth_action'),
                        'auth_note':$('#reback-notes').val(),
                    };
                    var settings = {
                        url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'workflow','action'=>'batch_auth']); ?>",
                        type:"post",
                        dataType:"json",
                        data:_data,
                        success:function(response){
                            //if( parseInt(response.code)!= 0 ){
                            //TODO 修改样式
                            alert(response.msg);
                            window.location.href=window.location.href;
                            //}
                        }
                    };
                    $.ajax(settings);
                });
            }
    );

    $('#order-batch-submit').click(
            function(){
                $('#order-modal-batch').modal('hide');
                $('#order-modal-batch').one('hidden.bs.modal',function(){
                    /* ajax事件 */
                    var btn = $('#batch-submit');
                    var order_ids = '';
                    $("input[name='id']").each(function(){
                        if($(this).is(":checked")){
                            order_ids += $(this).val()+',';
                        }
                    })
                    var _data = {
                        "order_ids":order_ids,
                        'auth_action':btn.attr('auth_action'),
                        'auth_note':$('#pass-notes').val(),
                    };
                    var settings = {
                        url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'workflow','action'=>'batch_auth']); ?>",
                        type:"post",
                        dataType:"json",
                        data:_data,
                        success:function(response){
                            //if( parseInt(response.code)!= 0 ){
                            //TODO 修改样式
                            alert(response.msg);
                            window.location.href=window.location.href;
                            //}
                        }
                    };
                    $.ajax(settings);
                });
            }
    );

    //定义修改计费模式的表单验证
    $("#chargeModeForm").bootstrapValidator({
        submitButtons: '#charge_submit',
        submitHandler: function(validator, form, submitButton) {
            // 实用ajax提交表单
            $('#charge-modal-edit').modal('hide');
            $('#charge-modal-edit').one('hidden.bs.modal',function(){
                $.ajax({
                    url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'orders','action'=>'editChargeMode']); ?>",
                    data:$('#chargeModeForm').serialize(),
                    method:'post',
                    dataType:'json',
                    success:function(response){
                        alert(response.msg);
                        window.location.href=window.location.href;
                    }
                });
                $("#chargeModeForm").data('bootstrapValidator').resetForm();
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
        $("#chargeModeForm").data('bootstrapValidator').resetForm();
    });

    //定义修改优先级的表单验证
    $("#priority-form").bootstrapValidator({
        submitButtons: '#priority_submit',
        submitHandler: function(validator, form, submitButton) {
            // 实用ajax提交表单
            $('#modal-priority').modal('hide');
            $('#modal-priority').one('hidden.bs.modal',function(){
                $.ajax({
                    url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'orders','action'=>'editPriority']); ?>",
                    data:$('#priority-form').serialize(),
                    method:'post',
                    dataType:'json',
                    success:function(data){
                        if(data.code==0){
                            alert(data.msg);
                            window.location.reload();
                        }else{
                            alert(data.msg);
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
        switch($(this).val()){

            case "cycle|D":
                $("#UnitText").html("元/天");
                break;
            case "cycle|M":
                $("#UnitText").html("元/月");
                break;
            case "cycle|Y":
                $("#UnitText").html("元/年");
                break;
            case "duration|I":
                $("#UnitText").html("元/分钟");
                break;
            default:
                $("#UnitText").html("");
        }

    });

    //修改ecs配置
    function changeConfigEcs(order_good_id){

        var i = new Array();
        var goods_type = new Array();
        var goods_ids = new Array();
        var order_good_ids = new Array();
        <?php foreach ($data as $key => $value) {?>
        <?php if( isset($value["orders_goods"][0])
        && ($value["orders_goods"][0]["good_type"] =='ecs'
        ||  $value["orders_goods"][0]["good_type"] =='citrix_public'
        ||  $value["orders_goods"][0]["good_type"] =='eip')): ?>

        i[<?= $key?>] = '<?= $value["orders_goods"][0]["instance_conf"]?>';
        order_good_ids[<?= $key?>] = '<?= $value["orders_goods"][0]["id"]?>';
        goods_type[<?= $key?>] = '<?=$value["orders_goods"][0]["good_type"]?>';
        goods_ids[<?= $key?>] = '<?= $value["orders_goods"][0]["good_id"]?>';
        <?php endif;?>
        <?php } ?>
    i[index] = $.parseJSON(i[index]);
    // console.log(i)
    if(goods_type[index] == 'ecs'){
        var url = '/home/selectEcs/'+goods_ids[index];
        i[index].url = "/console/orders/orderGoodsUpdate";
        // i[index].order_good_id = order_good_ids[index];
        // $.StandardPost(url,i[index]);
    }else if(goods_type[index] == 'citrix_public'){
        var url = '/home/selectVpc/'+goods_ids[index]+'/'+i[index].version+'/'+i[index].priceId;
        i[index].url = "/console/orders/orderCitrixGoodsUpdate";
        // i[index].order_good_id = order_good_ids[index];
        // $.StandardPost(url,i[index]);
    }else if(goods_type[index] == 'eip'){
        var url = '/home/selectEip/'+goods_ids[index]+'/'+i[index].version;
        i[index].url = "/console/orders/orderEipGoodsUpdate";
    }
    i[index].order_good_id = order_good_ids[index];
    $.StandardPost(url,i[index]);
    }

    //post 表单 来源json
    $.extend({
        StandardPost:function(url,args){
            var body = $(document.body),
                    form = $("<form method='post'></form>"),
                    input;
            form.attr({"action":url});
            for (arg in args)
            {
                input = $("<input type='hidden'>");
                input.attr({"name":arg});
                input.val(args[arg]);
                form.append(input);
            };

            form.appendTo(document.body);
            form.submit();
            document.body.removeChild(form[0]);
        }
    });

    $(document).ready(function() {
        $('#priceForm').bootstrapValidator({
            message: 'This value is not valid',
            feedbackIcons: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            submitHandler: function(validator, form, submitButton) {
                $('#order-modal-edit').modal('hide');
                $('#order-modal-edit').one('hidden.bs.modal',function(){

                    /* ajax事件 */
                    var _data = {
                        "order_good_id":$("#order_good_id").val(),
                        'transaction_price':$("#price").val()
                    };
                    var settings = {
                        url:"<?= $this->Url->build(['prefix'=>'console','controller'=>'orders','action'=>'editprice']); ?>",
                        type:"post",
                        dataType:"json",
                        data:_data,
                        success:function(response){
                            //if( parseInt(response.code)!= 0 ){
                            //TODO 修改样式
                            alert(response.msg);
                            window.location.reload();
                            //}
                        }
                    };
                    $.ajax(settings);
                });
            },
            fields  :{
                price: {
                    validators: {
                        notEmpty: {
                            message: '价格不能为空'
                        },
                        numeric:{
                            message: '请输入正确的价格'
                        },
                        greaterThan:{
                            value : 0,
                            message: '价格不能小于0'
                        }
                    }
                }
            }
        });

        $("#order-price-cancel").click(function(){
            $('#priceForm').data('bootstrapValidator').resetForm();
        });

        $("[nc_type='price']").click(function(){
            var order_good_id = $(this).attr('order_good_id');
            var price   = $(this).attr('price');

            $('#order_good_id').val(order_good_id);
            $('#price').val(price);
            $('#order-modal-edit').modal('show');
        });
        $("[nc_type='charge_mode']").click(function(){
            var order_good_id = $(this).attr('order_good_id');
            var price   = $(this).attr('price');

            $('#orderGoodId').val(order_good_id);
            $('#price').val(price);
            $('#charge-modal-edit').modal('show');
        });
        $("[nc_type='priority']").click(function(){
            var order_good_id = $(this).attr('order_good_id');
            var priority_value=$(this).attr('priority_value');
            $('#priority_data').val(priority_value);
            $('#priority_id').val(order_good_id);
            $('#modal-priority').modal('show');
        });

    });
</script>

<script type="text/javascript">

    var myDate = new Date();
    var year = myDate.getFullYear();
    var month =myDate.getMonth()+1;
    var day =  myDate.getDate();
    var time =year+'-'+month+'-'+day;
    $('#datetimepicker-end').datetimepicker({
        autoclose:true,
        minView:2,
    <?php if(isset($start) && $start > 0){?>
        startDate:'<?= $start?>',
                <?php }?>
    endDate:time
    }
    ).on('changeDate', function(){
        var end = $('#end-time').val()
        // alert(end);
        location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>$this->request->params['action'],$start]);?>/"+end+'/<?=$department['id']?>?search=<?php echo $search?>&create_by=<?=$create_by?><?php if(isset($_GET['file_position'])&&$_GET['file_position']=='admin'){echo '&file_position=admin';}?>';
    });
    $('#datetimepicker-start').datetimepicker({
        autoclose:true,
        minView:2,
    <?php if(isset($end) && $end > 0){?>
        endDate:'<?= $end?>',
                <?php }else{?>
        endDate:time
        <?php }?>
    }
    ).on('changeDate', function(){
        var start = $('#start-time').val()
        // alert(end);
        location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>$this->request->params['action']]);?>/"+start+'/<?php echo $end?>/<?=$department['id']?>?search=<?php echo $search?>&create_by=<?=$create_by?>'+"<?php if(isset($_GET['file_position'])&&$_GET['file_position']=='admin'){echo '&file_position=admin';}?>";
    });

    function timestrap2date(value){
        var now = new Date(parseInt(value) * 1000);
        return now.toLocaleString();
    }
    $(".section-body").on('click','.order-click',function(){
        if($(this).children().hasClass("icon-angle-down")){
            $(this).parent(".order-tips").nextAll().slideUp();
            $(this).html('展开<i class="icon-angle-up"></i>');
        }else{
            $(this).parent(".order-tips").nextAll().slideDown();
            $(this).html('收起<i class="icon-angle-down"></i>');
        }

    })
    //搜索绑定
    $("#txtsearch").on('keyup',
            function(event) {
                if(event.keyCode == 13){
                    search();
                }
            });
    //搜索绑定
    $("#create_by").on('keyup',
            function(event) {
                if(event.keyCode == 13){
                    search();
                }
            });

    function search(){
        var search = $('#txtsearch').val();
        var create_by = $('#create_by').val();
        location.href="<?= $this ->Url ->build(['prefix' =>'console', 'controller' =>'Orders', 'action' =>$this->request->params['action'],$start,$end]);?>?search="+search+'&create_by='+create_by+"<?php if(isset($_GET['file_position'])&&$_GET['file_position']=='admin'){echo '&file_position=admin';}?>";
    }

</script>