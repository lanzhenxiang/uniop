<?= $this->element('content_header'); ?>
<style>
    .bold{
        font-weight: bold;
    }
     .point-host-startup{
         margin-right: 150px;
     }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <!--<form class="form-horizontal">-->
        <div>
            <div class="content-operate clearfix">
                <div class="pull-left">
                    <span style="font-size: 20px;">关联管理权限</span>
                </div>

            </div>
            <hr>
            <div class="now-role"><span class="bold">角色名:&#160;</span><span><?php if(isset($rolename)){ echo $rolename;} ?></span></div>
            <div class="now-role"><span class="bold">说明:&#160;</span><span>权限更新后，用户重新登录CMOP后生效。</span></div>
            <br>
            <div class="now-role"><span class="bold">关联CMOP权限 ——勾选关联</span></div>
            <hr>
<div>
    <button  id="all_select" class="btn btn-primary">全选</button>
    <button  id="none_select" class="btn btn-primary">全不选</button>
</div>
            <!--<?php var_dump($data);?>-->
            <!--添加内容-->
            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="home">
                    <div>
                        <ul id="treeDemo" class="ztree"></ul>
                    </div>
                </div>
            </div>
        </div>
    <!--</form>-->
    <div class="col-sm-offset-5">
        <button type="submit" id="popedom_submit" class="btn btn-primary">保存</button>
        <!--<a type="button" href="<?php echo $this->Url->build(array('controller' => 'Role','action'=>'index')); ?>" class="btn btn-danger">返回</a>-->
        <a type="button" onclick="window.history.go(-1)" class="btn btn-danger">返回</a>
    </div>
</div>
<?= $this->Html->css(['zTreeStyle.css']) ?>
<?= $this->Html->script(['jquery.ztree.core-3.5.js']); ?>
<?= $this->Html->script(['jquery.ztree.excheck-3.5.js']); ?>
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    //权限列表
    var data_pope ='<?php echo $data; ?>';
    data_pope  = eval('(' + data_pope + ')');
    var setting_pope = {
        view: {
            showLine:false
        },
        check: {
            enable: true
        },
        data: {
            key:{
                name:"popedomnote"
            },
            simpleData: {
                enable: true,
                idKey:'popedomid',
                pIdKey:'parent_id'
            }
        }
    };
    var zNodes_pope =data_pope;
    var code_pope;
    $.fn.zTree.init($("#treeDemo"), setting_pope, zNodes_pope);
    var zTree_pope = $.fn.zTree.getZTreeObj("treeDemo");
    var type_pope = { "Y":'p' + 's', "N":'p' + 's'};
    zTree_pope.setting.check.chkboxType = type_pope;

    showCode_pope('setting.check.chkboxType = { "Y" : "' + type_pope.Y + '", "N" : "' + type_pope.N + '" };');

    function showCode_pope(str) {
        if (!code_pope) code_pope = $("#code_pope");
        code_pope.empty();
        code_pope.append("<li>"+str+"</li>");
    }

    //全选
    $('#all_select').on('click',function(){
        var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
        treeObj.checkAllNodes(true);
    });

    //全不选
    $('#none_select').on('click',function(){
        var treeObj = $.fn.zTree.getZTreeObj("treeDemo");
        treeObj.checkAllNodes(false);
    });

    //保存权限
    $("#popedom_submit").on('click', function () {
        //获取选中节点的值
        var treeObj=$.fn.zTree.getZTreeObj("treeDemo");
        var nodes=treeObj.getCheckedNodes(true);
        var popeid="";
        for(var i=0;i<nodes.length;i++){
            popeid+=nodes[i].popedomid + ",";
        }
        $.ajax({
            type: "post",
            url: "<?= $this-> Url->build(['controller'=>'Role','action'=>'postpopedom']);?>?id=<?=$id; ?>",
            dataType: "json",
            data: {
                popeid:popeid,
                type:'popedom'
            },
            success: function (data) {
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
//                    location.href="<?= $this->Url->build(array('controller' => 'Role', 'action' => 'index')); ?>";
                    window.location.reload();
                } else {
                    tentionHide(data.msg, 1);
                }

            }

        });
    });





</script>
<?= $this->end() ?>