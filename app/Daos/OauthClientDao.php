<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\OauthClient;

/**
 * 数据操作类: OAuth2.0客户端
 *
 * Class OauthClientDa
 * @package App\Daos
 */
class OauthClientDao extends BDao
{
    private $_oauthClient;

    public function __construct()
    {
        $this->_oauthClient = new OauthClient();
    }

    /**
     * 获得用户的基本信息（通过Id）
     *
     * @param string $oauth_client_id
     * @return object|null $oautClient
     */
    public function getOauthClientById($oauth_client_id)
    {
        $oauth_client_id = Toolkit::is_integer($oauth_client_id) ? trim($oauth_client_id) : null;
        if (!isset($oauth_client_id) || intval($oauth_client_id, 10) < 1) {
            return null;
        }

        $oauthClient = $this->_oauthClient->where(array('id' => $oauth_client_id))->select('id', 'appid', 'appsecret', 'scope_ids')->first();

        return $oauthClient;
    }

    /**
     * 获得用户的基本信息（通过app_id）
     *
     * @param string $app_id
     * @return object|null $oautClient
     */
    public function getOauthClientByAppid($app_id)
    {
        $app_id = Toolkit::is_string($app_id) ? trim($app_id) : null;
        if (!isset($app_id) || $app_id == '') {
            return null;
        }

        $oauthClient = $this->_oauthClient->where(array('app_id' => $app_id))->select('id', 'app_id', 'app_secret', 'scope_ids')->first();

        return $oauthClient;
    }

}