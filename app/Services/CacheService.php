<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/10/24
 * Time: 22:57
 */

namespace App\Services;

use App\Libs\Toolkit;

/**
 * 缓存服务类
 *
 * Class CacheService
 * @package App\Services
 */
class CacheService extends BService
{
    private $_cache_key_prex = 'sapi_weikantou_com';

    /**
     * 从服务容器中,取得系统缓存实例
     *
     * TODO 从服务容器中,取得系统缓存实例的2种方式：1.通过门面Illuminate\Support\Facades\Cache静态代理;  2.\App::make('cache');
     *
     * @return mixed
     */
    public function getCache()
    {
        return \App::make('cache');
    }

    /**
     * 根据调用的方法来定义key
     *
     * @param string $className 调用类名称
     * @param string $methodName 调用方法名称
     * @param string|array $params 调用方法参数列表
     * @return string 返回自定的key
     */
    public function getKeyInMethod($className, $methodName, $params = '')
    {
        $className = (isset($className) && Toolkit::is_string($className)) ? trim($className) : null;
        if (!isset($className) || $className == '') {
            return '';
        }

        $methodName = (isset($methodName) && Toolkit::is_string($methodName)) ? trim($methodName) : null;
        if (!isset($methodName) || $methodName == '') {
            return '';
        }

        $params = (isset($params) && (Toolkit::is_string($params) || is_array($params))) ? $params : null;
        if (!isset($params)) {
            return '';
        }

        $key = $this->_cache_key_prex . "." . $className . "." . $methodName;

        if (is_array($params)) {
            sort($params);
            $params = md5(serialize($params));
            $key .= "." . $params;
        } elseif (Toolkit::is_string($params) && trim($params) != '') {
            $key .= "." . trim($params);
        }

        return $key;
    }
}