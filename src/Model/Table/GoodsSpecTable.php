<?php
/**
 * ==============================================
 * GoodsSpecTable.php
 * @author: shrimp liao
 * @date: 2015年10月9日 下午3:25:07
 * @version: v1.0.0
 * @desc:商品规格表
 * ==============================================
 **/
namespace App\Model\Table;

class GoodsSpecTable extends SobeyTable{
    public function initialize(array $config)
    {
        parent::initialize($config);
        $this->belongsTo('Goods', [
            //'className' => 'Publishing.Authors',
            'foreignKey' => 'goods_id',
        ]);

    }
}

?>