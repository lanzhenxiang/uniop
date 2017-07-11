<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月21日下午4:02:09
* @source SobeyCachedTable.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 


namespace App\Model\Table;

use App\Model\Table\SobeyTable;

class SobeyCachedTable extends SobeyTable
{

    /**
     * 重写find,增加缓存
     * (non-PHPdoc)
     * @see \Cake\ORM\Table::find()
     */
    public function find($type = 'all', $options = [])
    {
        $query = $this->query();
        $query->select();
        strval($query);
        //增加缓存
        $query->cache(function ($q) {
            $key = serialize($q->clause('select'));
            $key .= serialize($q->clause('where'));
            return md5($key);
        },'cmop');
        return $this->callFinder($type, $query, $options);
    }
}