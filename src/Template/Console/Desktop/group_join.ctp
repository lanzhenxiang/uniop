<?= $this->element('desktop/lists/left',['active_action'=>'desktopGroup','active_group'=>'business_management']); ?>
<style>
    .form-control-blue{
        background-color: #44d2e4;
        cursor: pointer;
        color:white;
    }
    option{
        background-color: white;
        color:#888;
    }
</style>
<div class="wrap-nav-right">
    <div class="wrap-manage">
        <div class="top">
            <span class="title">桌面分组列表</span>
            <div id="maindiv-alert"></div>
        </div>

        <div class="center clearfix">
            <div class="pull-left">
                <button class="btn btn-default" id="add_group">
                    加入分组
                </button>
                <button class="btn btn-default" id="out_group" style="display: none">
                    移出分组
                </button>
            </div>
            <!--筛选-->
            <div class="input-group content-search pull-right col-sm-3">
                <input type="text" class="form-control" id="deptname" placeholder="搜索桌面名、CODE">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search-btn" type="button">搜索</button>
             </span>
            </div>

            <div class="pull-right">
                <span>租户:</span>
                <select class="form-control form-control-blue" id="department" name="department" style="width:150px;"
                        onchange="searchs()">
                    <option value="0">全部租户</option>
                    <?php if(isset($department)){?>
                    <?php foreach($department as $key => $value){?>
                    <option value="<?= $value['id'];?>"><?= $value['name'];?></option>
                    <?php }?>
                    <?php }?>
                </select>


            </div>

            <div style="clear: both;"></div>
        </div>
        <!--表格-->
        <div class="bot">
            <div>
                <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#not" aria-controls="not" role="tab" data-toggle="tab" id="show_not">未分组桌面</a></li>
                    <li role="presentation"><a href="#had" aria-controls="had" role="tab" data-toggle="tab" id="show_had">已分组桌面</a>
                    </li>
                </ul>
                <input type="hidden" value="not" id="type">
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="not">
                        <table class="table table-striped" id="table" data-toggle="table" data-pagination="true"
                               data-side-pagination="server"
                               data-page-list="[20,30]" data-page-size="20"
                               data-locale="zh-CN" data-click-to-select="true"
                               data-url="<?= $this->Url->build(['controller'=>'Desktop','action'=>'groupList']); ?>"
                               data-unique-id="id">
                            <thead>
                            <tr>
                                <th data-checkbox="true"></th>
                                <th data-field="code">桌面code</th>
                                <th data-field="name">桌面名</th>
                                <th data-field="group">所属分组</th>

                            </tr>
                            </thead>
                        </table>
                    </div>
                </div>


            </div>
        </div>
    </div>
</div>
<!--加入分组-->
<div class="modal fade" id="modal-add" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">添加桌面到分组</h5>
            </div>
            <form id="add-form" action="<?php echo $this->Url->build(array('controller' => 'Desktop','action'=>'addGroup')); ?>" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label style="width: 150px;">所选桌面:</label>
                        <div class="col-sm-6">
                            <input type="hidden" class="form-control" name="add_ids" id="add_ids" value="">
                            <span id="selected_desk"></span>
                        </div>
                    </div>
                    <?php if (in_array('cmop_global_sys_admin', $this->Session->read('Auth.User.popedomname'))) { ?>
                    <div class="modal-form-group">
                        <label style="width: 150px;">目标分组所在租户:</label>
                        <div class="col-sm-6">
                            <select name="add_department" id="add_department" class="form-control form-control-blue" onchange="changeGroup()">
                                <option value="0">全部租户</option>
                                <?php if(isset($department)){?>
                                <?php foreach($department as $key => $value){?>
                                <option value="<?= $value['id'];?>"><?= $value['name'];?></option>
                                <?php }?>
                                <?php }?>
                            </select>
                        </div>
                    </div>
                    <?php }?>
                    <div class="modal-form-group">
                        <label style="width: 150px;">目标分组:</label>
                        <div class="col-sm-6">
                            <select name="add_groups" id="add_groups" class="form-control form-control-blue">
                                <option value="">全部分组</option>
                                <?php if(isset($group)){?>
                                <?php foreach($group as $key => $value){?>
                                <option value="<?= $value['id'];?>"><?= $value['software_name'];?></option>
                                <?php }?>
                                <?php }?>
                            </select>
                        </div>
                    </div>


                </div>
                <div class="modal-footer">
                    <button id="yes_add" type="submit" class="btn btn-primary">确认</button>
                    <button id="add_cancel" type="button" class="btn btn-danger"data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>


<!--移出分组-->
<div class="modal fade" id="modal-delete" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span
                        aria-hidden="true">&times;</span></button>
                <h5 class="modal-title">提示</h5>
            </div>
            <div class="modal-body">
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要从分组移出选中桌面么？<span class="text-primary"
                                                                                           id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<div class="modal fade" id="modal-msg" tabindex="-1" role="dialog"></div>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
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
        var type=$('#type').val();
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Desktop','action'=>'groupList']); ?>?search=" + encodeURI(search)+"&department_id="+department_id+"&type="+type
        });
    }
//分页
    $('#show_not').on('click',function(){
        $('#type').val('not');
        $('#add_group').css('display', 'block');
        $('#out_group').css('display', 'none');
        searchs();
    });
    $('#show_had').on('click',function(){
        $('#type').val('had');
        $('#add_group').css('display', 'none');
        $('#out_group').css('display', 'block');
        searchs();
    });

    //加入分组
    $('#add_group').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        var id='';
        var name='';
        if(rows.length==0){
            made_modal('提示', '请先选择要加入分组的桌面');
        }else{
            $.each(rows,function(i,n){
                id+= n.id+',';
                name+= n.name+',';
            });
            $('#selected_desk').html(name);
            $('#add_ids').val(id);

            $('#modal-add').modal('show');
        }
    });
$('#add-form').bootstrapValidator({
    submitButtons:'button[type="submit"]',
    submitHandler: function(validator, form, submitButton) {
        $.post(form.attr('action'), form.serialize(), function(data){
            $('#modal-add').modal('hide');
            var data = eval('(' + data + ')');
            if (data.code == 0) {
                tentionHide(data.msg,0);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            } else {
                tentionHide(data.msg,1);
                setTimeout(function () {
                    window.location.reload();
                }, 500);
            }
        });
    },
    fields:{
        add_groups:{
            validators:{
                notEmpty: {
                    message: '请选择目标分组'
                }
            }
        }
    }
});

    //移出分组
    $('#out_group').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        if(rows.length==0){
            made_modal('提示', '请选择要移出分组的桌面');
        }else{
            $('#modal-delete').modal('show');
        }
    });
    $('#yes_delete').on('click',function(){
        var rows = $('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'Desktop','action'=>'delGroup']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                var search = $('#deptname').val();
                var department_id=$('#department').val();
                var type=$('#type').val();
                $('#table').bootstrapTable('refresh', {
                    url: "<?= $this->Url->build(['controller'=>'Desktop','action'=>'groupList']); ?>?search=" + encodeURI(search)+"&department_id="+department_id+"&type="+type
                });
                if(datas.code==0){
                    tentionHide(datas.msg, 0);
                }else{
                    tentionHide(datas.msg, 1);
                }
            }
        });
    });



//提示信息
function tentionHide(content, state) {
    $("#maindiv-alert").empty();
    var html = "";
    if (state == 0) {
        html += '<div class="point-host-startup "><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startup ").slideUp(3000);
    } else {
        html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
        $("#maindiv-alert").append(html);
        $(".point-host-startdown").slideUp(3000);
    }
}
//弹框
    function made_modal(name, msg) {
        $("#modal-msg").empty();
        var html = '<div class="modal-dialog" role="document"><div class="modal-content"><div class="modal-header">' +
            '<button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>' +
            '<h5 class="modal-title">' + name + '</h5></div><div class="modal-body">' +
            '<i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;' + msg + '<span class="text-primary" ></span>' +
            '</div><div class="modal-footer"><button type="button" class="btn btn-danger" data-dismiss="modal">确认</button></div> </div> </div>';

            $('#modal-msg').append(html);
            $('#modal-msg').modal('show');
    }
</script>
<?= $this->end() ?>