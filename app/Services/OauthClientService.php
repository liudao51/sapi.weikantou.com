<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:20
 */

namespace App\Services;

use App\Daos\OauthClientDao;
use App\Libs\Toolkit;

/**
 * 服务类: OAuth2.0客户端
 *
 * Class OauthClientService
 * @package App\Services
 */
class OauthClientService extends BService
{
    private $_oauthClientDao;

    public function __construct()
    {

        $this->_oauthClientDao = new OauthClientDao();
    }

    /**
     * 获取客户端信息
     *
     * 必选参数：$app_id
     * @param string $app_id 必须参数，注册应用时获得的API Key
     *
     * @return object|null
     * @throws \Exception
     */
    public function getOauthClientByAppid($app_id)
    {
        $app_id = Toolkit::is_string($app_id) ? trim($app_id) : null;
        if (!isset($app_id) || $app_id == '') {
            return null;
        }

        $oauthClient = $this->_oauthClientDao->getOauthClientByAppid($app_id);

        return $oauthClient;
    }
}