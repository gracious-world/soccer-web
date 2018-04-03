<?php
/**
 * 接口工厂类 单例
 * User: damon
 * Date: 1/24/16
 * Time: 6:15 PM
 */
namespace App\Services;
class ServiceFactory{

    /**
     * 获取接口类实例
     *
     * @param $sServiceName StringTool 接口类名称
     * @param bool|false $bInstance 是否单例
     * @return Object 类的实例
     */
    public static function getService($sServiceName,$bInstance = false){
        static $_instance = [];
        $sServiceClassName = 'App\Services\\'.ucfirst($sServiceName).'Service';
        if(!$bInstance){
            return new $sServiceClassName;
        }

        if(!isset($_instance[$sServiceName])){
            $_instance[$sServiceName] = new $sServiceClassName();
        }
        return $_instance[$sServiceName];
    }


}