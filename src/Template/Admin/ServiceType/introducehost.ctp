<?= $this->element('content_header'); ?>
    <div class="content-body clearfix">
        <div id="maindiv-alert"></div>
        <form class="form-horizontal">
            <table class="table table-striped">
                <input name="checkHost" type="hidden" id="checkHost">
                <input name="server_id" type="hidden" value="<?php if(isset($server_id)){ echo $server_id;} ?>">
                <div>
                    <label class="control-label text-danger"><i class="icon-exclamation-sign">保存选择引入的主机后会自动与该服务相关联！</i></label>
                </div>
                <thead>
                <tr>
                    <th><input id="all-host" type="checkbox"/></th>
                    <th>设备名称</th>
                    <th>主机名称</th>
                    <th>主机ip</th>
                </tr>
                </thead>
                <tbody id="desktop-content">
                <?php if(isset($hostdata)){
                    foreach($hostdata['hostdata'] as $value){
                        ?>
                        <tr>
                            <td><input name="host" onclick='check(this)' value="<?php if(isset($value->id)){ echo $value->id;} ?>" type="checkbox"/></td>
                            <td><?php if(isset($value->name)){ echo $value->name;}?></td>
                            <td><?php if(isset($value->title)){ echo $value->title;} ?></td>
                            <td><?php if(isset($value->privateIp)){ echo $value->privateIp;} ?></td>
                        </tr>
                    <?php
                    }
                } ?>
                </tbody>
            </table>
            <div class="content-pagination clearfix">
                <nav class="pull-right">
                    <ul id='hostdata' attrs="hostdata">
                    </ul>
                </nav>
            </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <a type="submit" id="ds" class="btn btn-primary">保存</a>
                <a type="submit" href="<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'index')); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
        </form>
    </div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js','jquery.uploadify.min.js']); ?>
<?= $this->start('script_last'); ?>
    <script type="text/javascript">
        //分页
        function paging(page){
            var search = $("#search").val();
            $.ajax({
                type: "GET",
                url: "<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'introduce')); ?>/"+page+"/"+search,
                dataType:"json",
                success: function(msg){
                    if(msg.hostdata){
                        var type = '';
                        $.each(msg.hostdata, function(i, n){
                            if (n.id) {
                                type+='<tr><td><input name="host" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.name+'</td><td>'+n.title+'</td><td>'+n.privateIp+'</td></tr>';
                            }
                        });
                        $("#desktop-content").html(type);
                        checkCheck();//已选中的打钩
                    }
                }
            });
        }
        //分页
        var options = {
            alignment:'right',
            bootstrapMajorVersion:10,
            currentPage: <?= $page?>,
            numberOfPages: 8,
            totalPages:<?= $hostdata['total']?>,
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
            var element = $('#hostdata');//对应下面ul的ID
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

        $('#hostdata').bootstrapPaginator(options);//填充分页

        //点击是修改cookie
        function check(obj){
            str = $.cookie("checkHost");
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
            $.cookie("checkHost",strs);
            str = $.cookie("checkHost");
            $("#checkHost").val(str);

            checkAll();
        }

        //检查是否全选
        function checkAll(){
            var imagelen = $("input:checkbox[name='host']:checked").length;
            var imagelens =$("input:checkbox[name='host']").length;
            if(imagelen == imagelens){
                $('#all-host').prop('checked','true');
            }else{
                $('#all-host').prop('checked','');
            }
        }

        //更具cookie的值添加check
        function checkCheck(){
            str = $.cookie("checkHost");
            strs=str.split(",");
            $("input:checkbox[name='host']").each(function(){
                for(var i=0;i<strs.length;i++){
                    if(strs[i]===$(this).val()){
                        $(this).prop('checked','true')
                    }
                }
            });
            checkAll();
        }

        $('#all-host').on('click',function(){
            str = $.cookie("checkHost");
            var checked=true;
            strs=str.split(",");
            if($('#all-host').is(":checked")){
                $("input:checkbox[name='host']").prop('checked','true');
            }else{
                $("input:checkbox[name='host']").prop('checked','');
            }
            $("input:checkbox[name='host']").each(function(){
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
            $.cookie("checkHost",strs);
            str = $.cookie("checkHost");
            $("#checkHost").val(str);
        })

        $(function(){
            $.cookie("checkHost",'');
            str = $.cookie("checkHost");
            strs=str.split(",");
            $("input:checkbox[name='host']").each(function(){
                for(var i=0;i<strs.length;i++){
                    if(strs[i]===$(this).val()){
                        $(this).prop('checked','true')
                    }
                }
            });
            checkAll();
            $("#checkHost").val(strs);
        })



        $("#ds").click(function(){
                var validate=true;
                if(validate){
                    $.ajax({
                        type: 'post',
                        url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'editintroduse')); ?>',
                        data: $("form").serialize(),
                        success: function(data) {
                            var data = eval('(' + data + ')');
                            if(data.code==0){
                                tentionHide(data.message,0);
                                $.cookie("checkHost",'');
                                location.href='<?php echo $this->Url->build(array('controller'=>'ServiceType','action'=>'index'));?>';
                            }else{
                                tentionHide(data.message,1);
                            }
                        }
                    });
                }
            }
        );

        $("#search-button").on('click',function(){
            var search = $("#search").val();
            $.ajax({
                url: '<?php echo $this->Url->build(array('controller' => 'ServiceType','action'=>'check_desktop')); ?>/1/'+search,
                success: function(data) {
                    datas = $.parseJSON(data);
                    if(datas){
                        var type = '';
                        $.each(datas.data, function(i, n){
                            if (n.id) {
                                type+='<tr><td><input name="desktop" onclick="check(this)" value="'+n.id+'"  type="checkbox"/></td><td>'+n.id+'</td><td>'+n.name+'</td><td>'+n.department_name+'</td><td>'+n.location_name+'</td></tr>';
                            }
                        });
                    }
                    $("#desktop-content").html(type);
                    checkCheck();//已选中的打钩
                    pageing(datas);
                }
            });
        })
    </script>
<?= $this->end() ?>