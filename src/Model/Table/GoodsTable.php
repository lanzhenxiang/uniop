<?php
/**
 * Created by PhpStorm.
 * User: Media
 * Date: 2015/9/8
 * Time: 16:08
 */
namespace App\Model\Table;

class GoodsTable extends SobeyTable{
	public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('GoodsCategory', [
            //'className' => 'Publishing.Authors',
            'foreignKey' => 'category_id',
        ]);
        $this->hasMany('GoodsSpec',[
            'foreignKey'=>'goods_id',
            'sort'=>['sort_order' => 'ASC']
        ]);
        
    }
    /**
     * [getGoodsInfo 获取商品详情]
     * @param  [array]  $where [条件数组]
     * @param  array    $field [字段数组]
     * @return [array]         [结果集]
     */
    public function getGoodsInfo($where,$field=[]){
        $good_info = $this->find()->select($field)->where($where)
            ->map(function($row){
                //商品默认图片处理
                $row['picture']= $row['picture'] != "" ? $row['picture'] : "nophoto.jpg";
                $row['mini_icon']= $row['mini_icon'] != "" ? $row['mini_icon'] : "nophoto.jpg";
                return $row;
            })->first();//商品信息\
        return $good_info;
    }
}