<?php
/**
 * Created by PhpStorm.
 * User: kelly
 * Date: 2017/5/16
 * Time: 10:29
 */

namespace App\Model\Table;

use Cake\Cache\Cache;
use Cake\Core\Configure;
use Cake\ORM\Query;

class SecurityGroupExtendsTable extends SobeyTable
{
    public function initialize(array $config)
    {
        parent::initialize($config);
    }
}