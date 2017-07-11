<?php
/**
 * ==============================================
 * GoodsCategoryTable.php
 * @author: shrimp
 * @date: 2015年9月10日 下午2:13:07
 * @version: v1.0.0
 * @desc:商品分类table
 * ==============================================
 **/
namespace App\Model\Table;

class GoodsCategoryTable extends SobeyTable{
    
    /**
     * @func: 
     * @param:
     * @date: 2015年9月10日 下午2:11:28
     * @author: shrimp
     * @return: null
     */
    public function initialize(array $config)
    {
        parent::initialize($config);
        
        $this->addBehavior('SobeyTree', [
                'order' => 'sort_order',
        ]);
        $this->belongsTo('GoodsCategorys', [
            //'className' => 'Publishing.Authors',
            'className' => 'GoodsCategory',
            'foreignKey' => 'parent_id',
        ]);

        //$this->hasMany('Goods'); //商品分类->商品（一对多）
        $this->hasMany('Goods', [
            'foreignKey' => 'category_id',

        ]);
        $this->hasMany('GoodsCatAttrsCat', [
            'foreignKey' => 'goods_category_id',
        ]);
        
        
        $this->hasMany('Good', [
            'className' => 'Goods',
            'foreignKey' => 'category_id',
            'conditions' => ['fixed'=>0],
            'sort'=>array('sort'=>'ASC')
        ]);
    }
    
    /**
     * @func: 获取推荐商品分类以及子类
     * @param:null
     * @date: 2015年9月10日 下午2:14:16
     * @author: shrimp
     * @return: null
     */
    public function getHotGoodsByCategory()
    {
        $goodsHotList= $this->find("all")->where(array('is_host'=>1))->order(['sort_order'=>'asc']);
        return $goodsHotList;
    }
}

?>