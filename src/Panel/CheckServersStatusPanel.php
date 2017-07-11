<?php
/**
 * cmop配置，服务状态检查
 *
 * @author chenqiang<small.jieao@gmail.com>
 * @date  2016年11月24日上午11:25:39
 * @version 1.0.0
 * @copyright  Copyright 2015 sobey.com
 */
namespace App\Panel;
use DebugKit\DebugPanel;
class CheckServersStatusPanel extends DebugPanel {
	public $plugin = 'CheckServersStatus';
	function title(){
		return '状态检查';
	}
	public function data()
    {
        return [];
    }
}

?>