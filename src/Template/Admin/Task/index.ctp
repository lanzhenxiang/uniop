<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="request" href="<?php echo $this->Url->build(array('controller' => 'Task','action'=>'index')); ?>" class="btn btn-addition"><i class="icon-refresh"></i>&nbsp;&nbsp;重新请求</a>
        </div>
    </div>
    <table class="table table-striped">
        <thead>
        <tr>
            <th><input type="checkbox"  id="total"></th>
            <th>任务id</th>
            <th>任务类型</th>
            <th>用户名称</th>
            <th>创建时间</th>
            <th>开始时间</th>
            <!--<th>结束时间</th>-->
            <th>api名称</th>
           <!-- <th>请求数据</th>
            <th>同步返回数据</th>
            <th>异步返回数据</th>-->
            <th>状态</th>
            <th>操作</th>
        </tr>
        </thead>
        <tbody>
        <?php if(isset($data)){
            foreach($data as $value){
                ?>
                <tr>
                    <td><input type="checkbox" name="id" value="<?php echo $value['task_id']; ?>"></td>
                    <td><?php echo $value['task_id']; ?></td>
                    <td><?php echo $value['task_type']; ?></td>
                    <td><?php echo $value['account']['loginname']; ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['create_time']); ?></td>
                    <td><?php echo date('Y-m-d H:i:s',$value['begin_time']); ?></td>
                   <!-- <td><?php /*echo date('Y-m-d H:i:s',$value['end_time']); */?></td>-->
                    <td><?php echo $value['api_url']; ?></td>
                    <!--<td><?php /*echo $value['request_data']; */?></td>
                    <td><?php /*echo $value['response_syn_data']; */?></td>
                    <td><?php /*echo $value['response_asyn_data']; */?></td>-->
                    <td><?php  switch ($value['status']) {
                            case 0:
                            case "0":
                            {
                               echo '等待执行';
                                break;
                            }
                            case 1:
                            case "1":
                            {
                                echo '执行中';
                                break;
                            }
                            case 2:
                            case "2":
                            {
                                echo '完成';
                                break;
                            }
                            case 3:
                            case "3":
                            {
                                echo '错误不可重试';
                                break;
                            }
                            case 4:
                            case "4":
                            {
                                echo '可以重试';
                                break;
                            }
                            default:
                            {
                                echo '-';
                            }
                        } ?></td>
                    <td>
                        <a id="delete" onclick="deletes(<?php echo $value['task_id']; ?>)" href="#">删除</a>
                    </td>
                </tr>
            <?php
            }
        }
        ?>
        </tbody>
    </table>
    <div class="content-pagination clearfix">
        <nav class="pull-right">
            <ul class="pagination">

                <?php echo $this->Paginator->first('<<');?>
                <?php echo $this->Paginator->prev(' < '); ?>
                <?php echo $this->Paginator->numbers();?>
                <?php echo $this->Paginator->next(' > '); ?>
                <?php echo $this->Paginator->last('>>');?>

            </ul>
        </nav>
    </div>
</div>
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该任务么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#total').on('click',function(){
        if($('#total').is(":checked")){
            $("input:checkbox[name='id']").prop('checked','true')
        }else{
            $("input:checkbox[name='id']").prop('checked','');
        }
    })

    $("input:checkbox[name='id']").on('click',function(){
        var len = $("input:checkbox[name='id']:checked").length;
        var lens =$("input:checkbox[name='id']").length;
        if(len == lens){
            $('#total').prop('checked','true');
        }else{
            $('#total').prop('checked','');
        }
    })

    $("#request").on('click',function(){
        var arr = new Array();
        var ids = $("input:checkbox[name='id']:checked");
        $.each(ids, function (i) {
           arr[i] =ids[i].value;
        })
        //console.debug(arr);
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'Task','action'=>'request')); ?>',
            data: {id:arr},
            success: function(data) {
                var data = eval('(' + data + ')');
                if(data.code==0){
                    tentionHide(data.msg, 0);
                    location.href='<?php echo $this->Url->build(array('controller' => 'Task','action'=>'index')); ?>';
                }else{
                    tentionHide(data.msg, 1);
                }
            }
        });
    });
    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'Task','action'=>'delete')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller' => 'Task','action'=>'index')); ?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg, 1);
                    }
                }
            });
        })
    }
</script>
<?= $this->end() ?>