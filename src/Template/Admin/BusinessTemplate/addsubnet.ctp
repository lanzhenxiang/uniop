<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <form class="form-horizontal" id="aduser-form" action="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'addsubnetpost')); ?>" method="post">

        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">子网名</label>
            <div class="col-sm-6">
                <?php if(isset($data)){ ?>
                    <input type="hidden" value="<?php echo  $data['id']; ?>" name="id" id="id" >
                <?php } ?>
                <input type="hidden" value="<?php echo  $vpc_id; ?>" name="vpc_id" id="vpc_id" >
                <input type="text" class="form-control" value="<?php if(isset($data)){ echo $data['tagname'];} ?>" name="tagname" id="tagname" >
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">子网cidr</label>
            <div class="col-sm-6">
                172.16.
                <select onchange="cidr(this)" id="cidrs">
                    <?php for($i=0;$i<15;$i++){ ?><option value="<?php echo $i; ?>" <?php if(isset($data)){
                        $cidr =  explode('.',$data['subnet_cidr']);
                        if($cidr[2] == $i){
                          echo 'selected';
                        }
                    } ?>><?php echo $i; ?></option><?php } ?>
                </select>
                .0/24
                <input type="hidden"  id="subnet_cidr"  name="subnet_cidr" value="172.16.<?php if(isset($data)){
                    $cidr =  explode('.',$data['subnet_cidr']);
                    echo $cidr[2];
                }else{ echo 0;} ?>.0/24">
            </div>
        </div>
        <div class="form-group">
            <label for="inputEmail3" class="col-sm-2 control-label">虚拟化技术指定</label>
            <div class="col-sm-6">
                <select  name="is_fusion">
                    <option value="">不指定</option>
                    <option value="false" <?php if(isset($data)){ if($data['is_fusion']== 'false'){ echo 'selected';}} ?> >vmWare</option>
                    <option value="true" <?php if(isset($data)){ if($data['is_fusion']== 'true'){ echo 'selected';}} ?> >openStack</option>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">排序</label>
            <div class="col-sm-6">
                <input type="text" class="form-control"  value="<?php if(isset($data)){ echo $data['sort_order'];} ?>" name="sort_order" id="sort_order" >
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" id="ds" class="btn btn-primary">保存</button>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'GoodsVpc','action'=>'configure',$vpc_id)); ?>" class="btn btn-danger">返回</a>
            </div>
        </div>
    </form>
</div>

<?=$this->Html->script(['adminjs.js','validator.bootstrap.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">

    $('#aduser-form').bootstrapValidator({
        submitButtons: 'button[type="submit"]',
        submitHandler: function(validator, form, submitButton){
            $.post(form.attr('action'), form.serialize(), function(data){
                var data = eval('(' + data + ')');
                if (data.code == 0) {
                    tentionHide(data.msg, 0);
                    location.href = '<?php echo $this->Url->build(array('controller'=>'GoodsVpc','action'=>'configure'));?>/'+data.vpc_id;
                } else {
                    tentionHide(data.msg, 1);
                }
            });
        },
        fields : {
            tagname: {
                group: '.col-sm-6',
                validators: {
                    notEmpty: {
                        message: '子网名称不能为空'
                    }
                }
            }
            // ,
            // is_fusion: {
            //     group: '.col-sm-6',
            //     validators: {
            //         notEmpty: {
            //             message: '请选择一个虚拟化技术'
            //         }
            //     }
            // }
        }
    });


    function cidr(e){
        var cidr = $(e).val();
        cidr = '172.16.'+cidr+'.0/24';
        var id = <?= $vpc_id ?>;
        $.ajax({
            type: "post",
            url: "<?= $this -> Url -> build(['controller' => 'GoodsVpc', 'action' => 'cidr']); ?>",
            data: {cidr: cidr,vpc_id:id},
            success: function (data) {
                datas = $.parseJSON(data);
                if (datas.code == 0) {
                    $('#subnet_cidr').val(cidr);
                    $('#ds').removeAttr('disabled');
                } else {
                    $('#subnet_cidr').val('');
                    $('#ds').attr('disabled','');
                    tentionHide('该cidr地址已经被使用，请重新选择', 1);
                }
            }
        });

    }
</script>
<?= $this->end() ?>

