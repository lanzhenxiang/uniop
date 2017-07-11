<?php
/** 
* 文件描述文字
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月8日下午4:30:14
* @source constant.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

/**
 * 常量定义
 */

return [
        "virtual_tech"=>[//cmop支持的虚拟化技术
        /**
		 * 子网所支持的虚拟技术 ————阿里云厂商
		 */
	        'aliyun'=>'aliyun',
	    /**
		 * 子网所支持的虚拟技术 ————亚马逊厂商
		 */
	        'aws'=>'aws',
	    /**
		 * 子网所支持的虚拟技术 ————Sobey厂商（Vmware）
		 */
	        'vmware'=>'vmware',
	    /**
		 * 子网所支持的虚拟技术 ————Sobey厂商（OpenStack）
		 */
	        'openstack'=>'openstack'
        ],
        /**
         * 资源类型枚举值，用于新版计费，和版本类型直接关联。
         */
        "resource_type"=>[
        	'ecs'	=>'云主机',
        	'mpaas'	=>'mPaaS',
        	'citrix'=>'Citrix桌面工具',
            'citrix_public'=>'Citrix桌面工具-大众版',
        	'bs'	=>'b/s工具',
		    'eip'    =>'eip',
            //'hive'   =>'HIVE',
            'elb'    =>'elb',
            'disks'    => '存储',
            'vpc'    => 'vpc',
            'mstorage' =>'媒体云存储',
            'vbrI'    =>'边界路由器接口',
            'firewall'=>'防火墙'
        ],
        "service_brand"=>[
            'DaYang'=>'大洋',
            'ArcSoft'=>'ArcSoft',
            'Sobey'=>'sobey'
        ],
        "service_type"=>[
            '转码','审计','迁移','合成'
        ],
        "charge_type"=>[
        	'day'	=>1,
        	'month' =>2,
        	'year'	=>4,
        	'duration'=>5,
        	'order'	=>6
        ],
        /**
         * 商品计费周期
         */
        'charge_interval'=>[
            'Y'=>'年',
            'M'=>'月',
            'D'=>'天',
            'H'=>'小时',
            'I'=>'分钟',
            'S'=>'秒'
        ],
        /**
         * 栏目_部门对应关系
         */
        'column_dept' => [
            'zjxw' => '85'
        ],
    /**
     * 物理专线标识符
     */
        'physicalLineCode'=>[
            "PhysicalLine-zrtgPL01",
            "PhysicalLine-zrtgPL02"
        ]
];
