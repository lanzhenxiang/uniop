<!-- 主页模板 -->
<?= $this->element('content_header'); ?>
<div class="content-body clearfix">
	<div class="content-operate clearfix">	
	<div class="dropdown pull-left" style="margin-left:30px;" id="selects">
                <button class="btn btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                    租户
                    <span class="caret"></span>
                </button>
                <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                <?php if(in_array('cmop_global_sys_admin',$this->request->session()->read('Auth.User.popedomname'))){ ?>
                    <li>
                        <a href="javascript:;" onclick="local(0)">全部</a>
                    </li>
                   
                    <?php } ?>
                    <?php foreach ($dept_grout as $key => $value){?>
                        <li>
                            <a href="javascript:;" onclick="local(<?= $value['id']?>)">
                                <?php echo $value['name'];?>
                            </a>
                        </li>
                    <?php }?>
                </ul>
            </div>

            <div class="pull-left" style="margin-left:30px;margin-top: 6px">
                <span>当前租户：</span><span id="depart_name"></span>
               
            </div>
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<!-- <th>序号</th> -->
				<th>租户名称</th>
				<th>部署区位</th>
				<th>类型</th>
				<th>已使用数量</th>
			</tr>
		</thead>
		<tbody>
			<?php $type = array('hosts'=>'主机','router'=>'路由器','subnet'=>'子网','desktop'=>'云桌面','vpc'=>'VPC',
			'firewall'=>'防火墙','eip'=>'公网IP','fimas'=>'FIMAS','disks'=>'硬盘','lbs'=>'负载均衡','nbl'=>'nbl')?>
			<?php foreach ($data['rows'] as $key => $value){ ?>
			<tr>
				<!-- <td><?= $key+1?></td> -->
				<td><?= $value['name']?></td> 
				<td><?= $value['location_name']?></td>
				<td><?php if (!empty($type[$value['type']])) {
					echo $type[$value['type']];
				}else{
					echo $value['type'];
				}?></td>
				<td><?= $value['count(a.id)']?></td>
			</tr><?php  }?>


		</tbody>
	</table>
	<div class="content-pagination clearfix">
		<nav class="pull-right">
			<ul id='example'>

			</ul>
		</nav>
	</div>
</div>
<?=$this->Html->script(['adminjs.js','jquery.cookie.js']); ?>
<?= $this->start('script_last'); ?>
<script type="text/javascript">
	$(function() {		
		var depart_name ="<?php echo $department_name; ?>";	
     	$('#depart_name').html(depart_name);	
	})	
	
	function paging(datas){}
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
        },
        pageUrl: function(type, page, current){
        	var department_id=<?php echo isset($department_id)?$department_id:0; ?>;
        	return "<?= $this -> Url -> build(['prefix' => 'admin', 'controller' => 'home', 'action' => 'index']); ?>/index/"+department_id+'/'+page; 
        }
    }

    $('#example').bootstrapPaginator(options);


	  function local(id){
	
	        location.href = "<?= $this -> Url -> build(['prefix' => 'admin', 'controller' => 'home', 'action' => 'index']); ?>/index/"+id;
	    }
</script>
<?= $this->end() ?>