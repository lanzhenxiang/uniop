<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div id="maindiv-alert"></div>
<div class="content-body clearfix" >
    <div id="maindiv-alert"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addedit')); ?>" class="btn btn-addition"><i class="icon-plus"></i>&nbsp;&nbsp;新建规格</a>
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addspec')); ?>" class="btn btn-addition"><i class="icon-plus"></i>&nbsp;&nbsp;新建分组</a>
            <!--  <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'editspec')); ?>" class="btn btn-addition"><i class="icon-edit"></i>&nbsp;&nbsp;修改分组</a> -->
            <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index'));?>/<?php echo 'index';?>/<?php echo '-1';?>" class="btn btn-addition">显示规格分组</a>

        </div>

        <div class="dropdown pull-left" style="margin-left:30px;">
            <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                规格分组
                <span class="caret"></span>
            </button>
            <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <li>
                    <a href="#" onclick="local(0)">全部</a>
                </li>
                <?php foreach ($group as $key => $value){?>
                <li>
                    <a href="#" onclick="local(<?= $value['group_id']?>)">
                        <?php echo $value['group_name'];?>
                    </a>
                </li>
                <?php }?>
            </ul>
        </div>
        <?php if(isset($spec_data)){?>
        <div class="pull-left" style="margin-left:30px;margin-top: 6px">
            <span>当前规格：</span>
            <span id="order_state" value = "<?= $group_id?>"><?= $group_name?></span>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="searchtext" placeholder="搜索规格名称或代码...">
            <span class="input-group-btn">
                <button class="btn btn-primary" id="search" type="button">搜索</button>
            </span>
        </div>
        <?php }?>
    </div>
    <?php if(isset($groups)){ ?>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>分组id</th>
                <th>分组名</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($groups as $value){
                ?>
                <tr>
                    <td><?php echo $value['group_id']; ?></td>
                    <td><?php echo $value['group_name']; ?></td>
                    <td>
                        <a href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addspec')); ?>/<?php if(isset($value['group_id'])){ echo $value['group_id'];} ?>">修改</a> |
                        <a id="delete" onclick="specdel(<?php echo $value['group_id']; ?>)" href="#">删除</a>
                    </td>
                </tr>
                <?php } ?>
            </tbody>
        </table>
        <?php }elseif(isset($spec_data)){ ?>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>id</th>
                    <th>分组名</th>
                    <th>规格名称</th>
                    <th>规格代码</th>
                    <th>排序</th>
                    <th>操作</th>
                </tr>
            </thead>
            <tbody>
                <?php if(isset($spec_data)){
                    foreach($spec_data as $value){
                        ?>
                        <tr>
                            <td><?php echo $value['id']; ?></td>
                            <td><?php echo $value['group_name']; ?></td>
                            <td><?php echo $value['spec_name']; ?></td>
                            <td><?php echo $value['spec_code']; ?></td>
                            <td><?php echo $value['sort_order']; ?></td>
                            <td>
                                <a  href="<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'addedit')); ?>/<?php if(isset($value['id'])){ echo $value['id'];} ?>">修改</a> |
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
        <?php } ?>
    </div>


    <div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h5 class="modal-title">提示</h5>
                </div>
                <div class="modal-body">
                    <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该规格/规格分组么？<span class="text-primary" id="sure"></span>？
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

        $('#search').on('click',function(){
        var group_id = <?php if(isset($group_id)){ echo $group_id; }else{ echo 0;}?>;
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'GoodsSpecDefine','action'=>'index'));?>/index/"+group_id+"/"+name;
        })
        $(function() {
            var name ="<?php echo $name ?>";
            $('#searchtext').val(name);
        })

        function deletes(id){
            $('#modal-delete').modal("show");
            $('#yes').one('click',function() {
                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'delete')); ?>',
                    data: {id: id},
                    success: function (data) {
                        var data = eval('(' + data + ')');
                        if (data.code == 0) {
                            tentionHide(data.msg, 0);
                            location.href = '<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index')); ?>';
                        } else {
                            tentionHide(data.msg, 1);
                        }
                    }
                });
            })
        }
        function specdel(group_id){
            $('#modal-delete').modal("show");
            $('#yes').one('click',function() {
                $.ajax({
                    type: 'post',
                    url: '<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'specdel')); ?>',
                    data: {group_id: group_id},
                    success: function (data) {
                        var data = eval('(' + data + ')');
                        if (data.code == 0) {
                            tentionHide(data.msg, 0);
                            location.href = '<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index')); ?>' + '/index/-1';
                        } else {
                            $('#modal-delete').modal("hide");
                            tentionHide(data.msg, 1);
                        }
                    }
                });
            })
        }
        function local(group_id){
            var name = $('#searchtext').val();
            if(!name){
                name='';
            }
            location.href = "<?php echo $this->Url->build(array('controller' => 'GoodsSpecDefine','action'=>'index')); ?>/<?php echo 'index';?>/"+group_id+"/"+name;
        }

    </script>
    <?= $this->end() ?>