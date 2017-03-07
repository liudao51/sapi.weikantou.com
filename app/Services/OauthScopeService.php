<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\OauthScopeDao;
use App\Libs\Toolkit;

/**
 * 服务类: OAuth2.0权限类型
 *
 * Class OauthClientService
 * @package App\Services
 */
class OauthScopeService extends BService
{
    private $_oauthScopeDao;

    public function __construct()
    {

        $this->_oauthScopeDao = new OauthScopeDao();
    }

    /**
     * 获取权限列表信息
     *
     * 必选参数：$names
     * @param array $names 必须参数，权限名称数组
     *
     * @return array|null
     * @throws \Exception
     */
    public function getOauthScopesByNames($names)
    {
        $oauthScopes = $this->_oauthScopeDao->getOauthScopesByNames($names);

        return $oauthScopes;
    }
}