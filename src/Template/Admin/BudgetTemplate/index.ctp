<?= $this->element('content_header'); ?>
<style>
    
    .single{
        width:45%;
        border: 1px solid lightgray;
        padding: 10px 20px;
        margin-right: 30px;
        margin-bottom: 30px;
    }
    .left{
        float:left;
    }
    .clear{
        clear:both;
    }
</style>
<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div>
        <div class="content-operate clearfix">

            <div class="pull-left">
                <span style="font-size: 20px;">资源配额明细</span>
            </div>
            <div class="pull-right form-group">
                <span>租户类型:</span>
                <select class="form-control" id="department" name="department" style="width:150px;" onchange="changeType()">
                    <option value="" <?php if(!isset($type)||empty($type)){echo 'selected';}?>>全部</option>
                    <option value="platform" <?php if(isset($type)&& $type=='platform'){echo 'selected';}?>>平台租户</option>
                    <option value="normal_inner" <?php if(isset($type)&& $type=='normal_inner'){echo 'selected';}?>>内部租户</option>
                    <option value="normal_outer" <?php if(isset($type)&& $type=='normal_outer'){echo 'selected';}?>>外部租户</option>
                </select>
            </div>
        </div>
        <hr>

        <!--配额明细-->
        <!--table-->
        <div class="bot">
            <?php if(isset($data)){?>
            <?php foreach($data as $key => $value){?>
            <div class="single left">
                <span class="pull-left"><?php if(isset($value['title_name']) && !empty($value['title_name'])){echo $value['title_name'];}else{echo '';}?></span>
                <div class="pull-right"> <a type="button" href="<?php echo $this->Url->build(array('controller' => 'BudgetTemplate','action'=>'adjust')); ?>?depart_type=<?php if(isset($value['depart_type'])){echo $value['depart_type'];}?>" class="btn btn-primary">修改模板</a></div>
                <div class="clear"></div>
                <hr>
                <div role="tabpanel" class="tab-pane">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <th style="width:20%">科目(单位)</th>
                            <th>默认配额</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php unset($value['title_name']);unset($value['depart_type']);?>
                        <?php if(is_array($value)){?>
                        <?php foreach($value as $keys => $values){?>
                        <tr>
                            <td><?=$values['title']?></td>
                            <td><?=$values['para_value']?></td>
                        </tr>
                        <?php }?>
                        <?php }?>
                        </tbody>
                    </table>
                </div>
            </div>
            <?php }?>
            <?php }?>


            <div class="clear"></div>
        </div>

    </div>

</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    function changeType(){
        var department=$('#department').val();
        location.href="<?=$this->Url->build(['controller'=>'BudgetTemplate','action'=>'index']);?>?type="+department;
    }
</script>
<?= $this->end() ?>

