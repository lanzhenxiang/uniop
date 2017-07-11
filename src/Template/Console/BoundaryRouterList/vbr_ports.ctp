
<style>
    .info{
        margin: 20px 40px;
    }

</style>
<div class="wrap-nav-right wrap-nav-right-left">


    <div class="wrap-manage">
        <!--[if IE]>
        <object id="vmrc" classid="CLSID:4AEA1010-0A0C-405E-9B74-767FC8A998CB"
                style="width: 100%; height: 100%;"></object>
        <![endif] -->
        <!--[if !IE]><!-->

        <!--<![endif]-->
        <div class="top">
            <span class="title">管理VBR接口</span>
            <div class="callback-info pull-right text-success"><i class="icon-ok"></i>&nbsp;操作成功</div>
            <div id="maindiv-alert"></div>
        </div>
        <div>
            <p class="info"> <a class="btn btn-addition" href="<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'vbr']);?>">
                &nbsp;&nbsp;返回边界路由器列表</a></p>
            <p class="info">接入场景:&#160;专线接入阿里云</p>
            <p class="info">路由器:&#160;<?=$vbr_data['name'];?></p>
            <p class="info">部署区位:&#160;<?=$vbr_data['location_name'];?></p>
        </div>
        <hr style="border-color: #ccc">
        <div class="center clearfix">
            <?php if (in_array('ccf_add_vbr_port', $this->Session->read('Auth.User.popedomname'))) { ?>
            <a class="btn btn-addition" href="<?=$this->Url->build(['controller'=>'HighSpeedChannel','action'=>'createRouterInterface','vbr_id'=>$vbr_data['id']]);?>">
                <i class="icon-plus"></i>&nbsp;&nbsp;新建路由器接口</a>
            <?php }?>
            <?php if (in_array('ccf_del_vbr_port', $this->Session->read('Auth.User.popedomname'))) { ?>
            <button class="btn btn-addition" onclick="delPort();">
                <i class="icon-remove"></i>&nbsp;&nbsp;删除路由器接口
            </button>
            <?php }?>

            <div class="pull-right">


    			<!--<span class="search">-->
    			    <!--<input type="text" id="txtsearch" name="search" placeholder="搜索路由器接口名" value="">-->
    		        <!--<i class="icon-search"></i>-->
    		    <!--</span>-->
            </div>
        </div>
        <input type="hidden" id="vbr_id" value="<?=$vbr_data['id'];?>">
        <input type="hidden" id="this_page" value="<?=$page;?>">
        <div class="bot ">
            <form>
            <table id="table" class="table table-striped">
                <thead>
                <tr>
                    <th><input type="checkbox"  id="total_ports"></th>
                    <th>接口</th>
                    <th>接口CODE</th>
                    <th>接口类型</th>
                    <th>规格</th>
                    <th>本端部署区位</th>
                    <th>对接接口</th>
                    <th>对接接口CODE</th>
                    <th>对端部署区位</th>
                    <th>对端VPC</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody id="table_content">
                <?php if(isset($data['ports'])&&!empty($data['ports'])){?>
                <?php foreach($data['ports'] as $key=>$value){?>
                <tr val="<?=$value['id'];?>" name="<?=$value['name'];?>">
                    <td>
                        <input type="checkbox" name="port_id" value="<?=$value['id'];?>" onclick="check(this)">
                    </td>
                    <td>
                        <?=$value['name'];?>
                    </td>
                    <td>
                        <?=$value['initiatingSideRouterInterfaceCode'];?>
                    </td>
                    <td>
                        <?=$value['type'];?>
                    </td>
                    <td>
                        <?=$value['spec'];?>
                    </td>
                    <td>
                        <?=$value['this_location'];?>
                    </td>
                    <td>
                        <?=$value['opposide_name'];?>
                    </td>
                    <td>
                        <?=$value['acceptingSideRouterInterfaceCode'];?>
                    </td>
                    <td>
                        <?=$value['opposide_location'];?>
                    </td>
                    <td>
                        <?=$value['opposide_vpc'];?>
                    </td>
                    <td>
                        <a href="<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'editVbr']);?>?port_id=<?=$value['id']?>&type=<?=$value['type']?>">路由管理</a>
                    </td>
                </tr>
                <?php }?>
                <?php }?>
                </tbody>
            </table>
            <input type="hidden" name="select_id" id="select_id">
            <div class="content-pagination clearfix">
                <nav class="pull-right">
                    <ul id='example' attrs="example">
                    </ul>
                </nav>
            </div>
            </form>
        </div>

    </div>
</div>

<!-- 右键弹框 -->
<div class="context-menu" id="context-menu">
    <ul>

        <li id="rename"><a href="javascript:void(0);"><i class="icon-pencil"></i> 重命名</a></li>


    </ul>
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
                <i class="icon-question-sign text-primary"></i>&nbsp;&nbsp;确认要删除选中路由器接口么？<span class="text-primary"
                                                                                               id="sure"></span>？
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-primary" id="yes_delete">确认</button>
                <button type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
            </div>
        </div>
    </div>
</div>
<!--重命名-->
<div class="modal fade" id="modal-rename" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <h5 class="modal-title">重命名</h5>
            </div>
            <form id="modal-rename-form" action="" method="post">
                <div class="modal-body">
                    <div class="modal-form-group">
                        <label>原名:</label>
                        <div>
                            <span id="modal-old-name"></span>
                        </div>
                    </div>
                    <div class="modal-form-group">
                        <label>新名:</label>
                        <div>
                            <input id="modal-new-name" name="new_name" type="text" maxlength="15" />
                        </div>
                    </div>
                    <input id="modal-rename-id" name="port_id" type="hidden" />
                </div>
                <div class="modal-footer">
                    <button id="sumbiter" type="button" class="btn btn-primary">确认</button>
                    <button id="reseter" type="button" class="btn btn-danger" data-dismiss="modal">取消</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="maindiv"></div>
<?php $this -> start('script_last'); ?>
<!--<?= $this->Html->script(['network/hosts_list']); ?>-->
<?=$this->Html->script(['adminjs.js','validator.bootstrap.js','bootstrap-datetimepicker.js','jquery.cookie.js','bootstrap-paginator.js']); ?>
<script type="text/javascript">
    //搜索绑定
//    $("#txtsearch").on('keyup', function () {
//        if (timer != null) {
//            clearTimeout(timer);
//        }
//        var timer = setTimeout(function () {
//            searchs();
//        }, 500);
//    });
    $('#txtsearch').on('keyup', function (e) {
        var this_page=$('#this_page').val();
        var key = e.which;
        if (key == 13) {
            e.preventDefault();
            searchs(1);
        }
    });
    function searchs(page) {
//        var search = $('#txtsearch').val();
        var search='';
        var vbr_id=$('#vbr_id').val();
        $.ajax({
            type:'get',
            data:{search:search,vbr_id:vbr_id},
            url:"<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'getports']);?>/"+page,
            success:function(data){
             data= $.parseJSON(data);
                if(data.data){
                    $('#this_page').val(data.page);
                    var html='';
                    $.each(data.data,function(i,n){
                        if(n.id){
                            html+='<tr val="'+ n.id+'" name="'+ n.name+'"><td><input name="port_id" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.initiatingSideRouterInterfaceCode+'</td><td>'+n.type+'</td><td>'+n.spec+'</td><td>'+n.this_location+'</td><td>'+n.opposide_name+'</td><td>'+n.acceptingSideRouterInterfaceCode+'</td><td>'+n.opposide_location+'</td><td>'+n.opposide_vpc+'</td><td><a href="<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'editVbr']);?>?port_id='+ n.id+'&type='+ n.type+'">路由管理</a></td></tr>';
                        }
                    });
                    $('#table_content').html(html);
                }
            }
        });

    }



    $('#table').contextMenu('context-menu',{
        bindings:{
            'rename':function(event){
                $('#modal-rename-id').val($(event).attr('val'));
                $('#modal-old-name').html($(event).attr('name').replace('-收','').replace('-发',''));
                $('#modal-rename').one('show.bs.modal',function(){
                    $('#sumbiter').one('click',function(){
                        $.ajax({
                            url:"<?=$this->Url->build(['controller'=>'BoundaryRouterList','action'=>'renamePort']);?>",
                            method:'post',
                            data:$('#modal-rename-form').serialize(),
                            success:function(data){
                                data= $.parseJSON(data);
                                $('#modal-rename').modal('hide');
                                searchs($('#this_page').val());
                                if(data.code==0){
                                    tentionHide(data.msg, 0);
                                }else{
                                    tentionHide(data.msg, 1);
                                }
                            }
                        });
                    });
                });
                $('#modal-rename').modal('show');
            }
        }
    });


    //删除vbr
    function delPort(){
        var select_id=$('#select_id').val();
        if(select_id!=''){
            $('#modal-delete').modal('show');
        }else{
            tentionHide('请选择要删除的路由器接口', 1);
        }
    }
    $('#yes_delete').on('click',function(){
        var select_id=$('#select_id').val();
        $.ajax({
            type:'post',
            url:"<?= $this-> Url->build(['controller'=>'BoundaryRouterList','action'=>'delPort']);?>",
            data:{select_id:select_id},
            success:function(data){
                datas= $.parseJSON(data);
                $('#modal-delete').modal('hide');
                searchs($('#this_page').val());
                if(datas.code==0){
                    tentionHide(datas.msg, 0);
                }else{
                    tentionHide(datas.msg, 1);
                }
            }
        });
    });
    function tentionHide(content, state) {
        $("#maindiv-alert").empty();
        var html = "";
        if (state == 0) {
            html += '<div class="point-host-startup "><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startup ").slideUp(5000);
        } else {
            html += '<div class="point-host-startup point-host-startdown"><i></i>' + content + '</div>';
            $("#maindiv-alert").append(html);
            $(".point-host-startdown").slideUp(5000);
        }
    }

    /**
     * [notifyCallBack 异步消息执行完成回调函数]
     */
    function notifyCallBack(value) {
        window.location.reload();
    }



    //分页
    function paging(page){
        searchs(page);
    }
    var options = {
        alignment:'right',
        bootstrapMajorVersion:10,
        currentPage: <?= $page?>,
        numberOfPages: 8,
                totalPages:<?= $data['total']?>,
        itemTexts: function (type, page, current) {
        switch (type) {
            case "first":
                return "<<";
            case "prev":
                return "<";
            case "next":
                return ">";
            case "last":
                return ">>";
            case "page":
                return page;
        }
    }
    }

    //更新分页
    function pageing(datas){
        var element = $('#example');//对应下面ul的ID
        var options = {
            alignment:'right',
            bootstrapMajorVersion:10,
            currentPage: datas.page,
            numberOfPages: 8,
            totalPages:datas.total,
            itemTexts: function (type, page, current) {
                switch (type) {
                    case "first":
                        return "<<";
                    case "prev":
                        return "<";
                    case "next":
                        return ">";
                    case "last":
                        return ">>";
                    case "page":
                        return page;
                }
            }
        }
        element.bootstrapPaginator(options);
    }
    $('#example').bootstrapPaginator(options);
    //添加cookie
    $(function(){
        var selectID = "";
        $.cookie("portids",selectID);
        str = $.cookie("portids");
        checkCheck();
    })
    //点击桌面是修改cookie
    function check(obj){
        str = $.cookie("portids");
        strs=str.split(",");
        if(obj.checked){
            strs[strs.length]=obj.value;
        }else{
            for(var i=0;i<strs.length;i++){
                if(strs[i]===obj.value){
                    strs.splice(i,1);
                    i--;
                }
            }
        }
        $.cookie("portids",strs);
        str = $.cookie("portids");
        $("#select_id").val(str);

        checkAll();
    }

    //检查是否全选
    function checkAll(){
        var imagelen = $("input:checkbox[name='port_id']:checked").length;
        var imagelens =$("input:checkbox[name='port_id']").length;
        if(imagelen == imagelens){
            $('#total_ports').prop('checked','true');
        }else{
            $('#total_ports').prop('checked','');
        }
    }

    //更具cookie的值添加check
    function checkCheck(){
        str = $.cookie("portids");
        strs=str.split(",");
        $("input:checkbox[name='port_id']").each(function(){
            for(var i=0;i<strs.length;i++){
                if(strs[i]===$(this).val()){
                    $(this).prop('checked','true')
                }
            }
        });
        checkAll();
        $("#select_id").val(strs);
    }

    $('#total_ports').on('click',function(){
        str = $.cookie("portids");
        var checked=true;
        strs=str.split(",");
        if($('#total_ports').is(":checked")){
            $("input:checkbox[name='port_id']").prop('checked','true');
        }else{
            $("input:checkbox[name='port_id']").prop('checked','');
        }
        $("input:checkbox[name='port_id']").each(function(){
            if($(this).prop("checked")==true){
                checked = jQuery.inArray($(this).val(), strs);
                if(checked<0){
                    strs[strs.length]=$(this).val();
                }
            }else{
                for(var i=0;i<strs.length;i++){
                    if(strs[i]==$(this).val()){
                        strs.splice(i,1);
                        i--;
                    }
                }
            }
        });
        $.cookie("portids",strs);
        str = $.cookie("portids");
        $("#select_id").val(str);
    });

    $(function(){
        var imagelen = $("input:checkbox[name='port_id']:checked").length;
        var imagelens =$("input:checkbox[name='port_id']").length;
        if(imagelen == imagelens){
            $('#total_ports').prop('checked','true');
        }else{
            $('#total_ports').prop('checked','');
        }
    })

</script>
<?php  $this->end();?>

