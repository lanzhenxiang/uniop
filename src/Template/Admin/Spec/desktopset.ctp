<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"  class="content-list-page"></div>
    <?= $this->Html->css(['zTreeStyle.css']) ?>
    <?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
    <div class="content-operate clearfix">
        <div class="clearfix">
            <div class="pull-left">
                <a type="button" id="save" class="btn btn-addition"><i class="icon-save"></i>&ensp;保存</a>&nbsp;
                <a type="button" id="search_d_t" href="javascript:;" class="btn btn-addition"><i class="icon-refresh"></i>&ensp;刷新</a>
                <!--<a type="button" id="btnBack" onclick="return delSession('desktopIDList')" href="<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'arealist'));?>/<?= $_agent["id"] ?>" class="btn btn-addition"><i class="icon-arrow-left"></i>&ensp;返回</a>-->
                <a type="button" id="btnBack" onclick="return delSession('desktopIDList')"  class="btn btn-addition"><i class="icon-arrow-left"></i>&ensp;返回</a>
            </div>
            <div class="input-group content-search pull-right">
                <input type="text" class="form-control" id="searchtext" placeholder="搜索桌面规格...">
             <span class="input-group-btn">
                 <button class="btn btn-primary" id="search" type="button">搜索</button>
             </span>
            </div>
        </div>
        <div class="margint20">
            <span>云厂商:<?= $_agent["agent_name"] ?></span>
            <span class="marginl20">地域:<?= $_area["agent_name"] ?></span>
            <span class="marginl20">勾选定义选项组</span>
        </div>
    </div>
    <table id="mainTable" class="table table-striped">
</table>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除该厂商吗？<span class="text-primary" id="sure">删除后关联地域也会删除</span>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<?= $this->Html->script(['adminjs.js']); ?>
<?= $this->Html->script(['jQuery.session']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
$(function(){
        initTable();
        $("#search_d_t").bind("click", function(){
            $.session.remove('desktopIDList');
            refresh();
          });
});
function initTable() {
    //初始化表格,动态从服务器加载数据
    $("#mainTable").bootstrapTable({
        method: "get",  //使用get请求到服务器获取数据
        url: "/admin/spec/lists", //获取数据的Servlet地址
        striped: true,  //表格显示条纹  http://cmop.com/admin/goods
        pagination: true, //启动分页
        paginationVAlign:'bottom',
        paginationHAlign:'left',
        pageSize: 15,  //每页显示的记录数
        pageNumber:1, //当前第几页
        pageList: [10, 15, 20, 25,50,100],  //记录数可选列表
        search: false,  //是否启用查询
        showColumns: false,  //显示下拉框勾选要显示的列
        showRefresh: false,  //显示刷新按钮
        sidePagination: "server", //表示服务端请求
        queryParamsType : "undefined",
        clickToSelect: true,
        singleSelect: true,
        columns: [
            {field: 'id',title: '',formatter:function(i,row){
                return '<input name="ids" id="ckID_'+row.id+'" onchange="checkId(\''+row.id+'\')" value="'+row.id+'" type="checkbox">';
            }},
            {field: 'id',title: 'id'},
            {field: 'brand',title: '品牌'},
            {field: 'name',title: '规格'},
            {field: 'set_name',title: '计算能力'},
            {field: 'image_name',title: '镜像'},
            {field: '',title: '按秒计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'S')}},
            {field: '',title: '按分钟计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'I')}},
            {field: '',title: '按小时计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'H')}},
            {field: '',title: '按天计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'D')}},
            {field: '',title: '按月计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'M')}},
            {field: '',title: '按年计费单价/元',formatter:function(value,row,i){return initPrice(value,row,i,'Y')}},
            // {field: '',title: '操作',formatter:function(i,row){
            //     str =  '<a href="/admin/spec/add?id='+row.id+'">修改</a> | ';
            //     str+=  '<a href="/admin/spec/clone?id='+row.id+'">复制</a> ';
            //     return str;

            // }}
            ],
        queryParams: function queryParams(params) {   //设置查询参数
          var param = {
              pageNumber: params.pageNumber,
              pageSize: params.pageSize,
              goodType:$("#goodType").val(),
              goodStatus:$("#goodStatus").val(),
              name:$("#searchtext").val()
          };
          return param;
        },
        onLoadSuccess: function(){  //加载成功时执行
          //layer.msg("加载成功");
            getCheckIds();
        },
        onLoadError: function(){  //加载失败时执行
         // layer.msg("加载数据失败", {time : 1500, icon : 2});
        },
        formatLoadingMessage: function () {
            return "";
        },
        formatNoMatches: function () {  //没有匹配的结果
            return '无符合条件的记录';
        }
    });
}
function initPrice(value,row,i,field){
    str = '-';
    if( row.price==null || row.price ==""){
        return '-';
    }
    arr=row.price.split(" ");
    for (x in arr)
    {
        if(arr[x].indexOf(field) >0){
            str = arr[x].replace(field,"");
        }
    }
    return str
}
function deletes(id){
    $('#modal-delete').modal("show");
    $('#yes').one('click',function() {
        $.ajax({
            type: 'post',
            url: '<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'dele')); ?>',
            data: {id: id},
            success: function (data) {
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'index'));?>';
                } else {
                    $('#modal-delete').modal("hide");
                    tentionHide(data.msg, 1);
                }
            }
        });
    })
}
$('#save').on('click',function(){
    var str = $.session.get('desktopIDList');
    var name = $('#searchtext').val();
    //获取所有选中的id
    $.ajax({
        type:'post',
        url:'<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'saveDesktopPower')); ?>',
        data:{req:str,agentid:"<?= $_area["id"] ?>"},
        success:function(data){
            tentionHide("操作成功", 0);
            refresh();
        }
    });
});
$('#search').on('click',function(){
    $.session.remove('desktopIDList');
    refresh();
});
function getCheckIds(){
    var bbb = $.session.get('desktopIDList');
    var str_array;
    if(bbb!=undefined){//如果session为空则取数据库选择记录,不为空则取session记录
        str_array = bbb.split(',');
    }else{
        $.ajax({
            type:"get",
            url:"<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'getCheckIds')); ?>/<?= $_area["id"] ?>",
            async:false,
            success:function(str){
                var idlist = str;
                str_array = idlist.split(',');
            }
        });
    }
    $.each(str_array,function(n,value) {
        $("#ckID_"+value).attr("checked","checked");
    });
    var strn = str_array.join();
    $.session.set('desktopIDList',strn);
}
function checkId(id){
    var status = $("#ckID_"+id).is(':checked');
    //true 选中
    var str = $.session.get('desktopIDList');
    var str_array = str.split(',');
    if(status==true){
        var isHave = $.inArray(id,str_array);//如果id不在在session中则添加到session中，否则不处理
        if(isHave!=-1){
        }else{
            var a = str_array.push(id);
            var strn = str_array.join();
            $.session.set('desktopIDList',strn);
        }
    }else{
        var isHave = $.inArray(id,str_array);//如果存在删除session
        if(isHave != -1){
            var ccc = str_array.splice(isHave,1);
            var strn = str_array.join();
            $.session.set('desktopIDList',strn);
        }
    }
    console.log($.session.get('desktopIDList'));
}
function refresh(){
            $("#mainTable").bootstrapTable('refresh')
}
function delSession(name){
    $.session.remove(name);
    window.history.go(-1);
    return true;
}
</script>
<?= $this->end() ?>