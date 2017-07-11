<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="add"  href="<?php echo $this->Url->build(array('controller' => 'Popedomlist','action'=>'addedit')); ?>" class="btn btn-addition pull-left"><i
        class="icon-plus"></i>&nbsp;&nbsp;新建</a>

        </div>

         <div class="input-group content-search pull-right">
             <input type="text" class="form-control" id="searchtext" placeholder="搜索权限名称或说明...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
         </div>
    </div>
    <table class="table table-striped">
        <thead>
            <tr>
                <th>权限id</th>
                <th>所属系统</th>
                <th>权限名称</th>
                <th>权限说明</th>
                <th>权限类型</th>
                <th>序列号</th>
                <th>操作</th>
            </tr>
        </thead>
        <tbody>
            <?php if(isset($data)){
                foreach($data as $value){
                    ?>
                    <tr>
                        <td><?php echo $value['popedomid']; ?></td>
                        <td><?php echo $value['popedomtype']; ?></td>
                        <td><?php echo $value['popedomname']; ?></td>
                        <td><?php echo $value['popedomnote']; ?></td>
                        <td><?php echo $value['popedomsubtype']; ?></td>
                        <td><?php echo $value['serinalno']; ?></td>
                        <td>
                            <a  href="<?php echo $this->Url->build(array('controller' => 'Popedomlist','action'=>'addedit')); ?>/<?php if(isset($value['popedomid'])){ echo $value['popedomid'];} ?>">修改</a> |
                            <a value="<?php echo $value['id']; ?>" href="javascript:;" onclick="deletes(<?php echo $value['popedomid']; ?>)">删除</a>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该权限么？<span class="text-primary" id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    $('#search').on('click',function(){
        var name = $('#searchtext').val();
        location.href = "<?php echo $this->Url->build(array('controller'=>'Popedomlist','action'=>'index'));?>/index/"+name;
    })

    $(function() {
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
    })

    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: "POST",
                url: '<?php echo $this->Url->build(array('controller'=>'Popedomlist','action'=>'delete'));?>',
                dataType: "json",
                data: {'id': id},
                success: function (data) {
                    if (data.code == 0) {
                        tentionHide(data.msg,0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'Popedomlist','action'=>'index'));?>';
                    } else {
                        $('#modal-delete').modal("hide");
                        tentionHide(data.msg,1);
                    }
                }
            });
        })
    }

</script>
<?= $this->end() ?>