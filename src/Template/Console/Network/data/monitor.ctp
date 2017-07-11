<!--运行状态-->
<?= $this->Html->css(['network/hosts']); ?>
<?php if($type == "desktop"){ ?>
    <?= $this -> element('desktop/lists/left', ['active_action' => 'desktop']); ?>
<?php }else{?>
<?= $this -> element('network/lists/left', ['active_action' => 'hosts']); ?>
<?php }?>
<div class="wrap-nav-right hosts-content">
    <?= $this -> element('network/lists/left_nav', ['active_action' => 'monitor','id'=>$_data['0']['H_ID']]); ?>
    <div class="status-con hosts-right clearfix host-static">
        <div class="static-detailInfo">
            <h5>  
                <?= $this -> element('network/data/breadcrumb', ['breadcrumbTitle' => '监控信息','biz_tid' => $_data['0']['Biz_tid']]); ?>
            </h5>
            <div  class="chart-box">
                <p>CPU</p>
                <div>
                    <canvas id="canvas1"></canvas>
                </div>
                <p class="chart-title"><span></span> CPU使用率(%)</p>
            </div>
            <div  class="chart-box">
                <p>网络</p>
                <div>
                    <canvas id="canvas4"></canvas>
                </div>
                <p class="chart-title"><span ></span>出网&nbsp;KBps &nbsp;<span class="line"></span>入网&nbsp;KBps</p>
            </div>
        </div>
    </div>
</div>
<script type="text/javascript">
    $(function(){
        chart('<?= $_data[0]['H_Code'] ?>');
    });
</script>