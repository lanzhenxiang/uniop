<?=$this->element('content_header');?>
<div class="content-body clearfix">
    <div id="maindiv-alert" class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="pull-left">
            <a type="button" id="update"
               href="<?php echo $this->Url->build(array('controller'=>'Role','action'=>'index')); ?>"
               class="btn btn-addition pull-left">&nbsp;&nbsp;刷新</a>
            <a style="margin-left:10px" type="button" id="add_info"
               href="<?php echo $this->Url->build(array('controller'=>'Role','action'=>'addrole')); ?>"
               class="btn btn-addition pull-left">&nbsp;&nbsp;新建</a>&#160;
            <a style="margin-left:10px" type="button" id="delete"
               href="javascript:;" class="btn btn-addition pull-left">&nbsp;&nbsp;删除</a>
        </div>
        <div class="input-group content-search pull-right">
            <input type="text" class="form-control" id="deptname" placeholder="搜索角色名">
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
                   data-locale="zh-CN" data-click-to-select="true"

                   data-unique-id="id">
                <thead>
                <tr>
                    <th data-checkbox="true"></th>
                    <th data-field="name">角色名</th>
                    <th data-field="id" data-formatter="operation">操作</th>
                    <th data-field="note">备注</th>
                    <th data-field="create_name">创建人</th>
                    <th data-field="created" data-formatter="formatter_time">创建时间</th>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该角色么？<span class="text-primary"
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
        $('#table').bootstrapTable('refresh', {
            url: "<?= $this->Url->build(['controller'=>'Role','action'=>'lists']); ?>?search=" + encodeURI(search)
        });
    }
    //删除
    $('#delete').on('click', function () {
        var rows=$('#table').bootstrapTable('getSelections');
        if(rows.length>0){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择juese', 1);
        }
    });
    $('#yes_delete').on('click',function(){
        var rows=$('#table').bootstrapTable('getSelections');
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'Role','action'=>'delete']);?>",
            data:{rows:rows},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                var search = $('#deptname').val();
                $('#table').bootstrapTable('refresh',{ url: "<?= $this->Url->build(['controller'=>'Role','action'=>'lists']); ?>?search=" + encodeURI(search)
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
        return "<a href='javascript::' onclick='editrole(this)'>修改信息</a> <a href='javascript::' onclick='popedom(this)'>关联管理权限</a> <a href='javascript::' onclick='software(this)'>关联工具分类</a> <a href='javascript::' onclick='accounts(this)'>关联用户</a>";
    }
    function editrole(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Role','action'=>'editrole']);?>?id="+uniqueId;
    }
    function popedom(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Role','action'=>'popedom']);?>?id="+uniqueId;
    }
    function software(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Role','action'=>'software']);?>?id="+uniqueId;
    }
    function accounts(event) {
        var uniqueId = $(event).parent().parent().attr('data-uniqueid');
        location.href="<?=$this->Url->build(['controller'=>'Role','action'=>'accounts']);?>?id="+uniqueId;
    }


//修改时间格式
    function formatter_time(value) {
        if(value==null){
            return '-';
        }else {
            if (!!window.ActiveXObject || "ActiveXObject" in window){
                var now = new Date(value.replace(/-/g,"/"));
            }else{
                var now = new Date(value);
            }

            now = Date.parse(now);
            var now = new Date(parseInt(now));
            return now.pattern("yyyy-MM-dd HH:mm:ss");
        }
    }

    Date.prototype.pattern = function (fmt) {
        var o = {
            "M+" : this.getMonth() + 1, //月份
            "d+" : this.getDate(), //日
            "h+" : this.getHours() % 12 == 0 ? 12 : this.getHours() % 12, //小时
            "H+" : this.getHours(), //小时
            "m+" : this.getMinutes(), //分
            "s+" : this.getSeconds(), //秒
            "q+" : Math.floor((this.getMonth() + 3) / 3), //季度
            "S" : this.getMilliseconds() //毫秒
        };
        var week = {
            "0" : "/u65e5",
            "1" : "/u4e00",
            "2" : "/u4e8c",
            "3" : "/u4e09",
            "4" : "/u56db",
            "5" : "/u4e94",
            "6" : "/u516d"
        };
        if (/(y+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, (this.getFullYear() + "").substr(4 - RegExp.$1.length));
        }
        if (/(E+)/.test(fmt)) {
            fmt = fmt.replace(RegExp.$1, ((RegExp.$1.length > 1) ? (RegExp.$1.length > 2 ? "/u661f/u671f" : "/u5468") : "") + week[this.getDay() + ""]);
        }
        for (var k in o) {
            if (new RegExp("(" + k + ")").test(fmt)) {
                fmt = fmt.replace(RegExp.$1, (RegExp.$1.length == 1) ? (o[k]) : (("00" + o[k]).substr(("" + o[k]).length)));
            }
        }
        return fmt;
    }
</script>
<?= $this->end() ?>