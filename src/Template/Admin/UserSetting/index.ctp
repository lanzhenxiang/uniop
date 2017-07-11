<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'add')); ?>" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <!-- <div class="dropdown pull-left" style="margin-left:30px;">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    用户类型
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index')); ?>">全部 </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index','1')); ?>">用户 </a>
                    </li>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index','2')); ?>">租户/租户</a>
                    </li>
                    <li>
                        <a href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index','3')); ?>">机房/区域</a>
                    </li>

                </ul>
            </div> -->
        </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>参数id</th>
                <th>用户名称</th>
                <th>参数类型</th>
                <th>参数代码</th>
                <th>参数值</th>
                <th>参数说明</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?= $value['id']; ?></td>
                        <td><?php echo $value['owner_name']; ?></td>
                        <td><?php
                            switch ($value['owner_type']) {
                                case '1':
                                echo '用户';
                                break;
                                case '2':
                                echo '租户';
                                break;
                                case '3':
                                echo '机房/区域';
                                break;
                                default:
                                echo '未知参数';
                                break;
                            }
                            ?></td>
                            <td><?php echo $value['para_code']; ?></td>
                            <td><?php echo $value['para_value']; ?></td>
                            <td><?php echo $value['para_note']; ?></td>
                            <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该参数么？<span class="text-primary" id="sure"></span>？
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
                    url: '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'dele')); ?>',
                    data: {id: id},
                    success: function (data) {
                        var data = eval('(' + data + ')');
                        if (data.code == 0) {
                            tentionHide(data.msg, 0);
                            location.href = '<?php echo $this->Url->build(array('controller' => 'UserSetting','action'=>'index')); ?>';
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