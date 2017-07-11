<?= $this->Html->script(['validator.bootstrap.js','jquery.uploadify.min.js','bootstrap-datetimepicker.js','jquery.infinitescroll.min.js']); ?>
<?= $this->Html->css(['bootstrap-datetimepicker.min.css']); ?>
<?= $this->Html->css(['style.css']); ?>
<?= $this->Html->css(['normalize.css']); ?>
<?= $this->Html->css(['jquery-ui-1.10.0.custom.css']); ?>
<?= $this->Html->css(['swiper.css']); ?>
<?= $this->Html->css(['pnotify.core.min.css']); ?>
<?= $this->Html->css(['styleBack.css']); ?>
<style>
    .wrap-index-page{
        margin-left: 0;
    }
    .table-striped a{
        margin:0;
    }
</style>
<div class="wrap-nav-right wrap-index-page wrap-manage">
    <div class="order-main section">
        <div class="section-header clearfix order-manage">
            <h5 class="pull-left">
                <?=$list_title?>
            </h5>
            <div class="pull-right">
                <button style="margin-left: 20px;" class="btn btn-addition" onclick="search()">查询</button>
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
            <div class="pull-right order-number">
                订单号:<span class="search"><input type="text" id="txtsearch" value="<?= $search?>" name="search" placeholder="订单号">
                </span>
            </div>
            <div class="dropdown pull-right">
                处理状态:
                <a  href="javascript:;" class="dropdown-toggle btn btn-addition text-right" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
                    <span class="pull-left" id="process_status" val="<?=$process_status?>"><?php echo $process_status == 0 ? '全部':($process_status == -1 ? '未完结' : '已完结'); ?></span>
                    <span class="caret"></span>
                </a>
                <ul class="dropdown-menu" id="agent_two">
                        <li><a href="<?= $this->Url->build(['prefix' => 'admin', 'controller' => 'orders', 'action' => $this->request->params['action'],$start,$end,0,$search]); ?>?search=<?=$search?>">全部</a></li>
                        
                        <li><a href="<?= $this->Url->build(['prefix' => 'admin', 'controller' => 'orders', 'action' => $this->request->params['action'],$start,$end,-1,$search]); ?>?search=<?=$search?>" >未完结</a></li>

                        <li><a href="<?= $this->Url->build(['prefix' => 'admin', 'controller' => 'orders', 'action' => $this->request->params['action'],$start,$end,1,$search]); ?>?search=<?=$search?>" >已完结</a></li>
                </ul>
            </div>

        </div>
        <div class="bot" id="order-panel">
            <table class="table table-striped  vertical-table">
                <thead>
                    <tr>
                        <th>订单号</th>
                        <th>图片</th>
                        <th>商品规格配置</th>
                        <th>费用说明</th>
                        <th>购买量</th>
                        <th>状态</th>
                        <th>操作</th>
                        <th>申请人</th>
                        <th>租户</th>
                        <th>提交时间</th>
                    </tr>
                </thead>
                <tbody>
                <?php 
                if (isset($data) && count($data) >0) {
                    foreach ($data as $key => $value) { ?>

                    <?php if(!isset($value['orders_goods'][0])) continue;?>

                    <tr>
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
                            <?=$this->element('order/goods/'.$value['orders_goods'][0]['good_type'],['good_info'=>$goodInfo]);?>
                        <?php endif;?>
                        </div>
                        </td>
                        <td>
                            <div class="goods-attribute">
                            <dl>
                                <dt>计费方式:</dt>
                                <dd><?=$value['orders_goods'][0]->chargeTxt?></dd>
                            </dl>
                            <dl>
                                <dt>单台费用:</dt>
                                <dd><?= $this->Number->format($value['orders_goods'][0]->transaction_price, ['places' => 4,'before' => '¥ '])?>元/<?=$value['orders_goods'][0]->UnitText ?></dd>
                            </dl>
                            </div>
                        </td>
                        <?php if($value['orders_goods'][0]['good_type'] == 'mpaas' ||
                            $value['orders_goods'][0]['good_type'] == 'bs'
                        ):?>
                        <td>长期使用服务</td>
                        <?php else:?>
                        <td style="text-align: center;"><?=$value['orders_goods'][0]['num']?></td>
                        <?php endif;?>
                        <td>
                            <?php if($value['status'] == -2){
                              echo "已退回";
                              }else{
                            ?>
                            <?=$value['workflow_detail']['step_name']?></td>
                        <?php }?>
                        <td>
                            <a href="<?= $this->Url->build(['prefix' => 'console', 'controller' => 'orders', 'action' => 'orderlog',$value['id']])?>" class="btn btn-xs btn-default">订单日志</a><br/><br/>
                        </td>
                        <td><?=$value['account']['username']?></td>
                        <td><?=$value['department']['name']?></td>
                        <td><?=$this->Time->format($value['create_time'],'yyyy-MM-dd HH:mm:ss',false,'PRC')?></td>
                    </tr>
                <?php }}else{?>
                    <tr>
                        <td colspan="10" style="text-align: center;">没有匹配记录</td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
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
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">提示</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认通过审核?</span></p>
        <p style="margin-top:8px;"><textarea id="pass-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-submit">确 认</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>  
<div class="modal fade" id="order-modal-reback" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">提示</h4>
      </div>
      <div class="modal-body">
        <p><i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;<span class="text-primary">确认退回?</span></p>
        <p style="margin-top:8px;"><textarea id="reback-note" name="auth_note" class="form-control"></textarea></p>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="order-reback-submit">确 认</button>
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
    </div>
  </div>
</div>
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
        <button type="button" class="btn btn-danger" data-dismiss="modal">取 消</button>
      </div>
      </form>
    </div>
  </div>
</div>


<script type="text/javascript">
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
                    url:"<?= $this->Url->build(['prefix'=>'admin','controller'=>'orders','action'=>'updateNum']); ?>",
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
        var _data = {
                    "order_id":btn.attr('order_id'),
                    'auth_action':btn.attr('auth_action'),
                    'auth_note':$('#pass-note').val(),
                    'flow_detail_name':btn.attr('flow_detail_name'),
                    'flow_detail_id':btn.attr('to_detail_id')
                };  
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
  $('#order-reback-submit').click(
    function(){
      $('#order-modal-reback').modal('hide');
      $('#order-modal-reback').one('hidden.bs.modal',function(){
        /* ajax事件 */
          var btn = $('#btn-reback');
          var _data = {
                    "order_id":btn.attr('order_id'),
                    'auth_action':btn.attr('auth_action'),
                    'auth_note':$('#reback-note').val(),
                    'flow_detail_name':btn.attr('flow_detail_name'),
                    'flow_detail_id':btn.attr('to_detail_id')
                  };  
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

    $("[nc_type='price']").click(function(){
        var order_good_id = $(this).attr('order_good_id');
        var price   = $(this).attr('price');

        $('#order_good_id').val(order_good_id);
        $('#price').val(price);
        $('#order-modal-edit').modal('show');
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
                        url:"<?= $this->Url->build(['prefix'=>'admin','controller'=>'orders','action'=>'editprice']); ?>",
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
            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Orders', 'action' =>$this->request->params['action'],$start]);?>/"+end+'/<?=$process_status?>?search=<?php echo $search?>';
        });
        $('#datetimepicker-start').datetimepicker({
            autoclose:true,
            minView:2,
            <?php if(isset($end) && intval($end) > 0){?>
            endDate:'<?= $end?>',
            <?php }else{?>
            endDate:time
            <?php }?>
        }
        ).on('changeDate', function(){
            var start = $('#start-time').val()
            // alert(end);
            location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Orders', 'action' =>$this->request->params['action']]);?>/"+start+'/<?php echo $end?>/<?=$process_status?>?search=<?php echo $search?>';
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

    function search(){
        var search = $('#txtsearch').val();
        location.href="<?= $this ->Url ->build(['prefix' =>'admin', 'controller' =>'Orders', 'action' =>$this->request->params['action'],$start,$end,$process_status]);?>?search="+search;
    }

</script>