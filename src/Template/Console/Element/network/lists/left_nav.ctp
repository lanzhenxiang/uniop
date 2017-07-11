<?= $this->Html->css(['network/hosts']); ?>
<!--left nav-->
<div class="hosts-left pull-left">
    <ol class="nav-left">
        <?php if (($type == 'hosts' && in_array('ccm_ps_hosts', $this->Session->read('Auth.User.popedomname')))||($type == 'desktop' && in_array('ccm_ps_desktop', $this->Session->read('Auth.User.popedomname')))) { ?>
        
        <li <?php if(isset($active_action)&&($active_action=='basic_info')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','basic_info',$id,$type]); ?>"><i class="icon-list-alt"></i>基本信息</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='system_layout')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','system_layout',$id,$type]); ?>"><i class="icon-wrench"></i>系统配置</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='network_card')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','network_card',$id,$type]); ?>"><i class="icon-cog"></i>网卡设置</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='storage')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','storage',$id,$type]); ?>"><i class="icon-tasks"></i>块存储</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='snap')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','snap',$id,$type]); ?>"><i class="icon-camera-retro"></i>快照</a>
        </li>
        <?php if($type == "hosts"){ ?>
        <li <?php if(isset($active_action)&&($active_action=='mirror')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','mirror',$id,$type]); ?>"><i class="icon-road"></i>镜像</a>
        </li>
        <?php }?>
        <li <?php if(isset($active_action)&&($active_action=='imaging')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','imaging',$id,$type]); ?>"><i class="icon-bar-chart"></i>图形化</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='monitor')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','monitor',$id,$type]); ?>"><i class="icon-lightbulb"></i>监控信息</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='action_record')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','action_record',$id,$type]); ?>"><i class="icon-file-alt"></i>操作记录</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='normal_log')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','normal_log',$id,$type]); ?>"><i class="icon-check"></i>正常日志</a>
        </li>
        <li <?php if(isset($active_action)&&($active_action=='abnormal_log')){echo 'class="active-bg"';} ?> >
            <a href="<?= $this->Url->build(['controller'=>'network','action'=>'data','abnormal_log',$id,$type]); ?>"><i class="icon-bolt"></i>异常日志</a>
        </li>
        <?php }?>
    </ol>
</div>
<div id="maindiv"></div>
<?php  $this->start('script_last'); ?>
<?= $this->Html->script(['network/hosts']); ?>
<?php $this -> end() ?>