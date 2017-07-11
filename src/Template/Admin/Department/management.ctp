<?= $this->element('content_header'); ?>

<div class="content-body clearfix">
    <div id="maindiv-alert"></div>
    <div>
        <div class="content-operate clearfix">
            <div class="form-group clearfix">
                <span>租户名:</span>

                <select class="form-control" id="department" name="department" style="width:10%;" onchange="changeinfo()">
                    <?php foreach($depart_data as $key => $value){?>
                    <option value="<?php echo $value['id'];?>"
                    <?php if($value['id']==$select_id){echo 'selected';}?>><?php echo $value['name'];?></option>
                    <?php }?>

                </select>

            </div>
            <div class="pull-left">
                <span style="font-size: 20px;">资源配额明细</span>
            </div>

        </div>
        <hr>

        <!--配额明细-->
        <!--table-->
        <div class="bot">
            <div>
                <div role="tabpanel" class="tab-pane" id="profile">
                    <table class="table table-striped">
                        <thead>
                        <tr>
                            <!--<th><input  id="all-roles" type="checkbox"></th>-->
                            <th style="width:20%">科目(单位)</th>
                            <th>配额</th>
                            <th>用量</th>
                            <th>余量</th>
                            <th>使用水位</th>
                        </tr>
                        </thead>
                        <tbody id="roles-content">



                        <?php if(isset($quota)){?>
                        <?php foreach($quota as $key => $value){?>
                        <tr>
                            <td><?=$value['name']?></td>
                            <td><?=$value['budget']?></td>
                            <td><?=$value['used'];?></td>
                            <td><?=$value['can_use'];?></td>
                            <td class="pro-parent">
                                <div class="quota">

                                    <div class="progress" style="margin-bottom:0;">
                                        <div class="progress-bar progress-bar-info" role="progressbar"
                                             aria-valuenow="60" aria-valuemin="0" aria-valuemax="100"
                                             style="width:<?=$value['percent'];?>">

                                        </div>
                                    </div>

                                </div>
                                <span class="pro-num"> <?=$value['percent'];?></span>
                            </td>
                        </tr>
                        <?php }?>
                        <?php }?>


                        </tbody>
                    </table>
                    <div class="content-pagination clearfix">
                        <nav class="pull-right">
                            <ul id='example' attrs="example">
                            </ul>
                        </nav>
                    </div>
                    <input type="hidden" name="role_id"  id="role_id">
                </div>
            </div>
            <div class="col-sm-offset-5">
                <a type="button"
                   href="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'adjust')); ?>?id=<?=$select_id?>"
                   class="btn btn-primary">调整配额</a>
                <a type="button" href="<?php echo $this->Url->build(array('controller' => 'Department','action'=>'index')); ?>" class="btn btn-danger">返回</a>
                <!--<a type="button" id="back" class="btn btn-danger" onclick="window.history.go(-1)">返回</a>-->
            </div>
        </div>

    </div>

</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
    function changeinfo(){
        var department_id=$('#department').val();
        location.href="<?=$this->Url->build(['controller'=>'Department','action'=>'management']);?>?id="+department_id;
    }
</script>
<?= $this->end() ?>

