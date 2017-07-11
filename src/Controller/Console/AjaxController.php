<?php
/** 
* 控制台 ajax控制器
* 
* 
* @author XingShanghe<xingshanghe@gmail.com>
* @date  2015年9月24日下午2:39:53
* @source AjaxController.php
* @version 1.0.0 
* @copyright  Copyright 2015 sobey.com 
*/ 

namespace App\Controller\Console;

use App\Controller\Console\ConsoleController;
use Cake\Network\Exception\BadRequestException;
use Cake\Utility\Inflector;

//处理ajax


class AjaxController extends ConsoleController
{
    
    public function initialize()
    {
//         if (!$this->request->is('ajax')){
//             throw new BadRequestException();
//         }
    }
    
    /**
     * 
     * 该方法只支持post和get方式请求
     * 
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function network($type,$action){
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Network\\'.Inflector::camelize($type).'Controller';
        
        $data = call_user_func_array([new $className(),$action],[$request_data]);
        echo json_encode($data,$_options);exit();
    }


    /**
     * 
     * 该方法只支持post和get方式请求
     * 
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function fics($type,$action){
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Fics\\'.Inflector::camelize($type).'Controller';
        
        $data = call_user_func_array([new $className(),ls],[$request_data]);
        echo json_encode($data,$_options);exit();
    }

    /**
     * 
     * 该方法只支持post和get方式请求
     * 
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function business($type,$action){
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Business\\'.Inflector::camelize($type).'Controller';
        
        $data = call_user_func_array([new $className(),$action],[$request_data]);
        echo json_encode($data,$_options);exit();
    }
    
    public function import($type,$action){
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Import\\'.Inflector::camelize($type).'Controller';
        $data = call_user_func_array([new $className(),$action],[$request_data]);
        echo json_encode($data,$_options);exit();
    }
    

    /**
     * 
     * 该方法只支持post和get方式请求
     * 
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function desktop($type,$action){
        // echo 1;exit;
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Desktop\\'.Inflector::camelize($type).'Controller';
        $data = call_user_func_array([new $className(),$action],[$request_data]);
        echo json_encode($data,$_options);exit();
    }

        /**
     * 
     * 该方法只支持post和get方式请求
     * 
     * @param 实例类型 $type
     * @param 请求方法 $action
     */
    public function security($type,$action){
        $this->autoRender = false;
        $this->layout = false;
        $request_data = [];
        if ($this->request->is('post')){
            $request_data = $this->request->data;
        }
        if ($this->request->is('get')){
            $request_data = $this->request->query;
        }
        $_options = isset($request_data['_options'])?$request_data['_options']:0;
        $className = 'App\Controller\Console\Security\\'.Inflector::camelize($type).'Controller';
        $data = call_user_func_array([new $className(),$action],[$request_data]);
        echo json_encode($data,$_options);exit();
    }
}