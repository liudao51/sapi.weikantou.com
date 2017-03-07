<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2017/3/9
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\OauthAuthCode;

/**
 * 授权码操作类
 *
 * Class OauthAuthCodeDao
 * @package App\Daos
 */
class OauthAuthCodeDao extends BDao
{
    private $_oauthAuthCode;

    public function __construct()
    {
        $this->_oauthAuthCode = new OauthAuthCode();
    }

    /**
     * 保存授权码信息
     *
     * @param string $auth_code
     * @param string $session_id
     *
     * @return bool $result_save
     */
    public function createOauthAuthCode($auth_code, $session_id)
    {
        $now_time = TIME_BJ;
        $save_oauthAuthCode = new OauthAuthCode();
        $save_oauthAuthCode->auth_code = $auth_code;
        $save_oauthAuthCode->session_id = $session_id;
        $save_oauthAuthCode->expire_time = $now_time + config('oauth2.grant_types.authorization_code.auth_token_ttl', 3600);
        $save_oauthAuthCode->create_time = $now_time;

        $save_oauthAuthCode->exists = false;
        $result_save = $save_oauthAuthCode->save();

        unset($save_oauthAuthCode);

        return $result_save;
    }

    /**
     * 取得授权码的信息
     *
     * @param int $oauthAuthCode_id
     * @return object
     */
    public function getOauthAuthCodeById($oauthAuthCode_id)
    {
        $oauthAuthCode_id = Toolkit::is_integer($oauthAuthCode_id) ? trim($oauthAuthCode_id) : null;
        if (!isset($oauthAuthCode_id) || intval($oauthAuthCode_id, 10) < 1) {
            return null;
        }

        $oauthAuthCode = $this->_oauthAuthCode->where(array('id' => $oauthAuthCode_id))->select('id', 'auth_code', 'session_id', 'expire_time', 'create_time')->first();

        return $oauthAuthCode;
    }


}