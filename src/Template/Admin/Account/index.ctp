<?=$this->element('content_header');?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="update"
               href="<?php echo $this->Url->build(array('controller'=>'Account','action'=>'index')); ?>"
               class="btn btn-addition pull-left">&nbsp;&nbsp;刷新</a>
            <a style="margin-left:10px" type="button" id="add_info"
               href="<?php echo $this->Url->build(array('controller'=>'Account','action'=>'addaccount')); ?>"
               class="btn btn-addition pull-left">&nbsp;&nbsp;新建</a>&#160;
            <a style="margin-left:10px" type="button" id="delete"
               href="javascript:;" class="btn btn-addition pull-left">&nbsp;&nbsp;删除</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="deptname" placeholder="搜索登录名、用户名、手机号">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
             </span>
        </div>
        <div class="pull-right form-group">
            <span>租户:</span>
            <select class="form-control" id="department" name="department" style="width:150px;" onchange="changedepart()">
                <option value="0">全部租户</option>
                <?php foreach($department as $key => $value){?>
                <option value="<?= $value['id'];?>"><?= $value['name'];?></option>
                <?php }?>
            </select>
        </div>
        <div style="clear: both;"></div>
    </div>

    <!--表格-->
    <div class="bot">
        <div>
            <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                   data-side-pagination="server" data-sortable="false"
                   data-page-list="[20,30]" data-page-size="20"
                   data-locale="zh-CN" data-click-to-select="true"

                   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="loginname">登录名</th>
                    <th data-field="username">姓名</th>
                    <th data-field="mobile">手机</th>
                    <th data-field="email">邮箱</th>
                    <th data-field="address">联系地址</th>
                    <th data-field="department_name">租户</th>
                    <th data-field="expire">有效期</th>
                    <th data-field="id" data-formatter="operation">操作</th>
                    <th data-field="note">备注</th>
                    <th data-field="create_name">创建人</th>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该人员么？<span class="text-primary"
                                                                                           id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>

<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
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
        var department_id=$('#department').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Account','action'=>'lists']); ?>?search=" + encodeURI(search)+"&department_id="+department_id
        });
    }

    function changedepart(){
        var search = $('#deptname').val();
        var department_id=$('#department').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Account','action'=>'lists']); ?>?search=" + encodeURI(search)+"&department_id="+department_id
        });
    }
    //删除
    $('#delete').on('click', function () {
        var rows=$('#table').bootstrapTable('getSelections');
        if(rows.length>0){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择人员', 1);
        }
    });
    $('#yes_delete').on('click',function(){
        var rows=$('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'Account','action'=>'delete']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                var search = $('#deptname').val();
                $('#table').bootstrapTable('refresh',{ url: "<?= $this->Url->build(['controller'=>'Account','action'=>'lists']); ?>?search=" + encodeURI(search)
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
        return "<a href='javascript::' onclick='editinfo(this)'>修改信息</a> <a href='javascript::' onclick='editpassword(this)'>修改密码</a> <a href='javascript::' onclick='connectroles(this)'>关联角色</a>";
    }
    function editinfo(event){
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Account','action'=>'editinfo']);?>?id="+uniqueId;
    }
    function editpassword(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Account','action'=>'editpassword']);?>?id="+uniqueId;
    }
    function connectroles(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Account','action'=>'connectroles']);?>?id="+uniqueId;
    }

</script>
<?= $this->end() ?>