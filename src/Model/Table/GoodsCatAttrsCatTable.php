<?php

/**
 * ==============================================
 * GoodsCatAttrsCatTable.php
 * @author: shrimp liao
 * @date: 2015年9月11日 下午2:24:35
 * @version: v1.0.0
 * @desc:
 * ==============================================
 **/

namespace App\Model\Table;

class GoodsCatAttrsCatTable extends SobeyTable
{
    /**
     * @func: 
     * @param:
     * @date: 2015年9月11日 下午2:38:03
     * @author: shrimp liao
     * @return: null
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
    
        $this->belongsTo('GoodsCategory', [
            //'className' => 'Publishing.Authors',
            'className' => 'GoodsCategory',
            'foreignKey' => 'goods_category_id',
        ]);

        $this->addBehavior('SobeyTree', [
                'order' => 'sort_order',
        ]);

        //$this->hasMany('GoodsCategoryAttrs'); //商品属性类型->商品属性（一对多）
        
        $this->hasMany('GoodsCatAttrs', [
            'foreignKey' => 'goods_cat_attrs_cat_id',
        ]);

    }
}
