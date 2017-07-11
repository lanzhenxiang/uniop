<?php if($biz_tid > 0):?>
	<a href="/console/business/lists/hosts">业务系统</a>
<?php else:?>
	<?php if(isset($this->request->params['pass'][2]) && $this->request->params['pass'][2] == "desktop"): ?>
	<a href="/console/desktop/lists/desktop">桌面站点</a>
	<?php else:?>
	<a href="/console/network/lists/hosts">主机系统</a>
	<?php endif;?>
<?php endif;?>>
<a href="javascript:void(0)" style="color: black;"><?=$breadcrumbTitle?></a>