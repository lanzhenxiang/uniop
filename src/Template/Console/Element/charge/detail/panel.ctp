<div class="panel panel-default">
				<div class="panel-body">
					<ul class="clearfix">
						<li>
							租户
							&nbsp;&nbsp;
							<span class="text-bold"><?= $department_name?></span>
						</li>
						<li>
							资源类型
							&nbsp;&nbsp;
							<span class="text-bold"><?= $resource_type_data[$type]?></span>
						</li>
						<li>
							计费周期
							&nbsp;&nbsp;
							<span class="text-bold"><?= $start?> 至 <?= $end?> </span>
						</li>
						<li>
							消费金额
							&nbsp;&nbsp;
							<span class="text-bold">￥<?= $total_amount?> </span>
						</li>	
					</ul>
				</div>
			</div>