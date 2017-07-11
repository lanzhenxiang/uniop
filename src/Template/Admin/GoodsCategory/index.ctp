<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="add"  href="" class="btn btn-addition pull-left"><i class="icon-plus"></i>&nbsp;&nbsp;新建</a>

            <div class="dropdown pull-left" style="margin-left:30px;">
                <a type="button" id="edit"  href="" class="btn btn-addition pull-left"><i  class="icon-plus"></i>&nbsp;&nbsp;修改</a>
            </div>
            <div class="dropdown pull-left" style="margin-left:30px;">
                <a type="button" id='delete' onclick='' class="btn btn-addition pull-left"><i  class="icon-plus"></i>&nbsp;&nbsp;删除</a>
            </div>
            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前分类：</span><span id="depart_name">全部</span>
            </div>
        </div>

    </div>
    <div>
        <label class="control-label text-danger"><i class="icon-exclamation-sign">修改或删除之前，请先选中分类。</i></label>
    </div>
    <div>
        <ul id="treeDemo" class="ztree"></ul>
    </div>
    <script>
        var data ='<?php echo $data; ?>';
        data  = eval('(' + data + ')');
        var setting = {
            view: {
                showLine:false
            },
            data: {
                key:{
                    name: 'name'
                },
                simpleData: {
                    enable: true,
                    pIdKey:'parent_id'
                }
            },
            callback:{
                onClick:select
            }
        };
        var zNodes =data;
        $.fn.zTree.init($("#treeDemo"), setting, zNodes);
        function select(){
            var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
            var nodes = treeObj.getSelectedNodes();
            $('#depart_name').html(nodes[0].name);

            $('#edit').attr('disabled',false);
            $('#delete').attr('disabled',false);

            $('#edit').attr('href','<?php echo $this->Url->build(array('controller'=>'GoodsCategory','action'=>'addedit')); ?>/'+nodes[0].id);
            $('#delete').attr('onclick','deletes('+nodes[0].id+')');

        }
        $('#add').attr('href','<?php echo $this->Url->build(array('controller'=>'GoodsCategory','action'=>'addedit')); ?>/');
    </script>


</div>

<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该商品分类么？<span class="text-primary" id="sure"></span>？
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
    $(function(){
        var treedata=$("#treeDemo").html();
        if($('#edit').attr('href')==''){
            $('#edit').attr('disabled',true);
        }

        if($('#delete').attr('onclick')==''){
            $('#delete').attr('disabled',true);
        }
    })

    function deletes(id){
        $('#modal-delete').modal("show");
        $('#yes').one('click',function() {
            $.ajax({
                type: 'post',
                url: '<?php echo $this->Url->build(array('controller' => 'GoodsCategory','action'=>'dele')); ?>',
                data: {id: id},
                success: function (data) {
                    var data = eval('(' + data + ')');
                    if (data.code == 0) {
                        tentionHide(data.msg, 0);
                        location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsCategory','action'=>'index'));?>';
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