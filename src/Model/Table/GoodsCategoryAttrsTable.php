<?php
/**
 * ==============================================
 * GoodsCategoryAttrsTable.php
 * @author: shrimp liao
 * @date: 2015年9月11日 上午11:30:31
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Model\Table;

class GoodsCategoryAttrsTable extends SobeyTable
{
    /**
     * @func: 
     * @param:
     * @date: 2015年9月11日 上午11:30:55
     * @author: shrimp liao
     * @return: null
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    
        $this->belongsTo('GoodsCatAttrsCat'); //商品属性类型->商品属性（多对一）
    }
}

?>