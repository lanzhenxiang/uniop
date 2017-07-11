<div class="wrap-nav-center">
    <div>
        <div class="title"><span>媒体存储</span><i class="icon-angle-down"></i></div>
        <ul class="center-nav total">

            <li <?php if(isset($active_action)&&($active_action=='fics')){echo 'class="active"';} ?>><a href="<?= $this->Url->build(['controller'=>'fics','action'=>'index','hosts']); ?>"><span>Fics</span></a></li>
            
        </ul>
        <span class="iconpic iconpic-spread"></span>
    </div>
</div>