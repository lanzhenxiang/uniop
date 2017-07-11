<?=$this->element('content_header');?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="update"
               href="<?php echo $this->Url->build(array('controller'=>'Department','action'=>'index')); ?>"
               class="btn btn-addition pull-left">&nbsp;&nbsp;刷新</a>
            <a style="margin-left:10px" type="button" id="add_info"
               href="<?php echo $this->Url->build(array('controller'=>'Department','action'=>'addinfo')); ?>"
               class="btn btn-addition pull-left" <?php if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){echo 'disabled';}?>>&nbsp;&nbsp;新建基本信息</a>&#160;
            <a style="margin-left:10px" type="button" id="delete"
               href="javascript:;" class="btn btn-addition pull-left" <?php if(!in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){echo 'disabled';}?>>&nbsp;&nbsp;删除</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="deptname" placeholder="搜索租户名、租户code、租户AK">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
             </span>
        </div>
    </div>
    <!--表格-->
    <div class="bot">
        <div>
            <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                   data-side-pagination="server"
                   data-page-list="[20,30]" data-page-size="20"
                   data-locale="zh-CN" data-click-to-select="true" data-sortable="false"
                   
                   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="name" data-sortable="true">租户名</th>
                    <th data-field="type">租户类型</th>
                    <th data-field="identifier">租户CODE</th>
                    <th data-field="access_key">租户AK</th>
                    <th data-field="id" data-formatter="operation">操作</th>
                    <th data-field="note">备注</th>
                    <th data-field="username">创建人</th>
                    <th data-field="create_time">创建时间</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

<!--删除-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该租户么？<span class="text-primary"
                                                                                           id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $(function(){
      searchs();
    });
    //搜索
    $('#deptname').on('keyup', function (e) {
        var key = e.which;
        if (key == 13) {
            e.preventDefault();
            searchs();
        }
    });
    $('#search-btn').on('click', function () {
        searchs();
    });

    function searchs() {
        var search = $('#deptname').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Department','action'=>'lists']); ?>?search=" + encodeURI(search)
        });
    }

  
    //删除
    $('#delete').on('click', function () {
        var rows=$('#table').bootstrapTable('getSelections');
        if(rows.length>0){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择租户', 1);
        }
    });
    $('#yes_delete').on('click',function(){
        var rows=$('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'Department','action'=>'delete']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                var search = $('#deptname').val();
                $('#table').bootstrapTable('refresh',{ url: "<?= $this->Url->build(['controller'=>'Department','action'=>'lists']); ?>?search=" + encodeURI(search)
                });
                if(datas.code==0){
                    tentionHide(datas.msg, 0);
                }else{
                    tentionHide(datas.msg, 1);
                }
            }
        });
    });
    //操作
    function operation(value) {
        return "<a href='javascript::' onclick='edit(this)'>修改基本信息</a> <a href='javascript::' onclick='management(this)'>配额管理</a><a href='javascript::' onclick='subnetSet(this)'>扩展子网配置</a>";
    }
    function edit(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Department','action'=>'edit']);?>?id="+uniqueId;
    }
    function management(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Department','action'=>'management']);?>?id="+uniqueId;
    }
    function subnetSet(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Department','action'=>'subnetSet']);?>?id="+uniqueId;
    }
    

</script>
<?= $this->end() ?>








