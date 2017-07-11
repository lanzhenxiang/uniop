<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'addedit')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>
        </div>
        <div class="pull-right" >
            <a type="button" onclick="clickSearch()" href="javascript:void();" class="btn btn-addition "><i class="icon-search"></i>&nbsp;&nbsp;查询</a>
        </div>
        <div class="pull-right input-group" style="margin-left:30px;">
                <input type="text" id="name" name="name" value="<?=$name?>" placeholder="搜索参数名" class="form-control">
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>数据代码</th>
                <th>数据值</th>
                <th>数据说明</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                            <td><?php echo $value['para_code']; ?></td>
                            <td><?php echo $value['para_value']; ?></td>
                            <td><?php echo $value['para_note']; ?></td>
                            <td>
                                <a  href="<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?><?php if(isset($para_type)){ echo '/'.$para_type;} ?>">修改</a> |
                                <a id="delete" onclick="deletes(<?php echo $value['id']; ?>)" href="#">删除</a>
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
                    <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该系统参数么？<span class="text-primary" id="sure"></span>？
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
        function deletes(id){
            $('#modal-delete').modal("show");
            $('#yes').one('click',function() {
                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'dele')); ?>',
                    data: {id: id},
                    success: function (data) {
                        var data = eval('(' + data + ')');
                        if (data.code == 0) {
                            tentionHide(data.msg, 0);
                            location.href = '<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'index')); ?>/index/<?php echo $name;?>';
                        } else {
                            tentionHide(data.msg, 1);
                        }
                    }
                });
            })
        }

        function clickSearch(){
            var name = $('#name').val();
            location.href = '<?php echo $this->Url->build(array('controller' => 'Systemsetting','action'=>'index')); ?>/index/'+name;
        }

        $('#name').keyup(function(){
            if(event.keyCode == 13){//回车事件
                clickSearch();
            }
        });
    </script>
    <?= $this->end() ?>