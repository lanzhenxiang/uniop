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
                <!--<a type="button" id="btnBack" onclick="return delSession('setIDList')" href="<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'arealist'));?>/<?= $_agent["id"] ?>" class="btn btn-addition"><i class="icon-arrow-left"></i>&ensp;返回</a>-->
                <a type="button" id="btnBack" onclick="return delSession('setIDList')"  class="btn btn-addition"><i class="icon-arrow-left"></i>&ensp;返回</a>
            </div>
            <div class="input-group content-search pull-right">
                <input type="text" class="form-control" id="searchtext" placeholder="搜索计算能力名、CODE...">
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
    <table class="table table-striped">
<thead>
<tr>
    <th>选择</th>
	<th>计算能力名</th>
	<th>计算能力CODE</th>
	<th>CPU(核)</th>
	<th>内存(GB)</th>
	<th>GPU(MB)</th>
	<th>单价(日/元)</th>
	<th>单价(月/元)</th>
	<th>单价(年/元)</th>
</tr>
</thead>
<tbody>
<?php
	if(!empty($_data)){
	   foreach($_data as $value){ ?>
        <tr>
            <td>
            <input type="checkbox" id="ckID_<?= $value["set_id"] ?>" onchange="checkId('<?= $value["set_id"] ?>')" name="ckSetId">
            </td>
            <td><?= $value["set_name"] ?></td>
            <td><?= $value["set_code"] ?></td>
            <td><?= $value["cpu_number"] ?></td>
            <td><?= $value["memory_gb"] ?></td>
            <td><?= $value["gpu_gb"] ?></td>
            <td><?= $value["price_day"] ?></td>
            <td><?= $value["price_month"] ?></td>
            <td><?= $value["price_year"] ?></td>
        </tr>
	   <?php }
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
        getCheckIds();
        var name ="<?php echo $name ?>";
        $('#searchtext').val(name);
        $("#search_d_t").bind("click", function(){
            $.session.remove('setIDList');
                refresh()
          });
});
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
    var str = $.session.get('setIDList');
    var name = $('#searchtext').val();
    //获取所有选中的id
    $.ajax({
        type:'post',
        url:'<?php echo $this->Url->build(array('controller' => 'Agent','action'=>'saveAgentPower')); ?>',
        data:{req:str,agentid:"<?= $_area["id"] ?>"},
        success:function(data){
            $.session.remove('setIDList');
            tentionHide("操作成功", 0);
            location.href = "<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'agentset'));?>/<?= $_agent["id"] ?>/<?= $_area["id"] ?>/"+name;
        }
    });
});
$('#search').on('click',function(){
    $.session.remove('setIDList');
    var name = $('#searchtext').val();
    location.href = "<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'agentset'));?>/<?= $_agent["id"] ?>/<?= $_area["id"] ?>/"+name;
});
function getCheckIds(){
    var bbb = $.session.get('setIDList');
    var str_array;
    if(bbb!=undefined){//如果session为空则取数据库选择记录,不为空则取session记录
        str_array = bbb.split(',');
    }else{
        var idlist = "<?= $_setIdList ?>";
        str_array = idlist.split(',');
    }
    $.each(str_array,function(n,value) {
        $("#ckID_"+value).attr("checked","checked");
    });
    var strn = str_array.join();
    $.session.set('setIDList',strn);
}
function checkId(id){
    var status = $("#ckID_"+id).is(':checked');
    //true 选中
    var str = $.session.get('setIDList');

    var str_array = str.split(',');
    if(status==true){
        var isHave = $.inArray(id,str_array);//如果id不在在session中则添加到session中，否则不处理
        if(isHave!=-1){
        }else{
            var a = str_array.push(id);
            var strn = str_array.join();
            $.session.set('setIDList',strn);
        }
    }else{
        var isHave = $.inArray(id,str_array);//如果存在删除session
        if(isHave != -1){
            var ccc = str_array.splice(isHave,1);
            var strn = str_array.join();
            $.session.set('setIDList',strn);
        }
    }
}
function refresh(){
    var name = $('#searchtext').val();
    location.href = "<?php echo $this->Url->build(array('controller'=>'Agent','action'=>'agentset'));?>/<?= $_agent["id"] ?>/<?= $_area["id"] ?>/"+name;
}
function delSession(name){
    $.session.remove(name);
    window.history.go(-1);
    return true;
}
</script>
<?= $this->end() ?>