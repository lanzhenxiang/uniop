<?php
/**
* 文件用途描述
* 
* @date: 2016年12月6日 下午3:04:19
* @author: lan
*
*/

namespace App\Model\Table;

use App\Model\Table\SobeyTable;
use Cake\Core\Configure;

class BillBaseTable extends SobeyTable
{


    public function initialize(array $config)
    {
    	parent::initialize($config);

    	 $this->belongsTo('BillCitrix', [
            'className' => 'BillCitrix',
            'foreignKey' => false,
            'conditions' =>'BillCitrix.id = BillBase.resource_id'
        ]);
    }

    /**
     * 根据资源类型分组查找账单的总金额，数量
     * @param  [type] $query  [description]
     * @param  [type] $option [description]
     * @return [type]         [description]
     */
    public function findResourceTypeGroup($query,$option){
    	$condition = array();
    	$condition['bill_date >='] 	= $option['start'];
        $condition['bill_date <='] 	= $option['end'];
        $condition['department_id'] = $option['department_id'];

    	return $query->select([
                    'resource_type',
                    'cost'=>$query->func()->sum('amount'),
                    'count'=>$query->func()->count('id')])
            ->where($condition)->group('resource_type');
    }

    public function findBillTotalAmount($query,$option){
    	return $query->select(['total_amount'=>$query->func()->sum('amount')]);
    }

    /**
     * 消费明细获取饼图数据
     * @param  [string]  $start         [账单开始日期]
     * @param  [string]  $end           [账单结束日期]
     * @param  [int]  $department_id 	[租户id]
     * @param  integer $num           	[饼图详细展示项数目]
     * @return [array]                  [饼图数据]
     */
    public function getPieChartData($start,$end,$department_id,$num =5){
    	$resource_type_constant = Configure::read('resource_type');
    	
    	$option = array();
    	$option['start']    = $start;
        $option['end']      = $end;
        $option['department_id']    = $department_id;

        $bill_collection  = $this->find('ResourceTypeGroup',$option)->order('cost desc')->map(
       			function($row) use ($resource_type_constant){
       				$row['name'] = $resource_type_constant[$row['resource_type']];
       				return $row;
       			} 	
        );
        //所有类型消费总金额
        $total 		 	= $bill_collection->sumOf('cost');
        //饼图前几项纪录
        $toplist 		= $bill_collection->sortBy('cost')->take($num);
        //饼图前几项的总金额
        $sub_total		= $toplist->sumOf('cost');
        //计算剩余项的总金额
        $other_total 	= round(($total - $sub_total),4);

        //如果剩余项金额大于0,则全部计为其他
        if($other_total > 0){
        	$item = $this->newEntity();
        	$item['name'] = '其他';
        	$item['cost'] = $other_total;
        	$item['count'] = $bill_collection->sumOf('count') - $toplist->sumOf('count');
        	$toplist = $toplist->append([$num=>$item]);
        }
        return $toplist->toArray();
    }


}