<?php
/**
 * 权限配置
 */

return array(
    'lists' => array( //列表页权限
        'hosts' => 'ccm_ps_hosts',
        'disks' => 'ccm_ps_disks',
        'images' => 'ccm_ps_images',
        'router' => 'ccm_ps_routers',
        'subnet' => 'ccm_ps_subnets',
        'elb' => 'ccm_ps_load_banlance',
        'eip' => 'ccm_ps_eip',
        'vpc' => 'ccm_ps_vpc',
        'vpx' => 'cmop_vpx',
        'server' => 'ccm_sm_MPC_Dispatch',
        'EipbHosts' => 'ccf_eip_alloc_hosts',
        'EipbElb'=>'ccf_eip_alloc_banlance',
        'Elblisten'=>'ccf_load_banlance_configure',
        'fics' => 'ccm_ps_fics',
        'settinglist' => 'ccm_ps_fics_settinglist',
        'ficsHosts' => 'ccm_ps_fics_hosts',
        'hostsGroup' => 'ccm_ps_fics_hosts'
    ),
    'add_lists' => array( //新建页权限
        'hosts' => 'ccf_host_new',
        'hostDetail' => 'ccm_ps_hosts',
        'router' => 'ccf_router_new',
        'subnet' => 'ccf_subnet_new',
        'elb' => 'ccf_load_banlance_new',
        'eip' => 'ccf_eip_new',
        'fics' =>'ccf_fics_new'
    )
);