<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/25
 * Time: 18:28
 */

namespace App\Daos;

use App\Libs\Toolkit;
use App\Models\OauthScope;

/**
 * 数据操作类: OAuth2.0权限类型
 *
 * Class OauthClientDa
 * @package App\Daos
 */
class OauthScopeDao extends BDao
{
    private $_oauthScope;

    public function __construct()
    {
        $this->_oauthScope = new OauthScope();
    }

    /**
     * 获得用户权限的基本信息（通过Id）
     *
     * @param string $oauth_scope_id
     *
     * @return object|null $oauthScope
     */
    public function getOauthScopeById($oauth_scope_id)
    {
        $oauth_scope_id = Toolkit::is_integer($oauth_scope_id) ? trim($oauth_scope_id) : null;
        if (!isset($oauth_scope_id) || intval($oauth_scope_id, 10) < 1) {
            return null;
        }

        $oauthScope = $this->_oauthScope->where(array('id' => $oauth_scope_id))->select('id', 'name', 'description')->first();

        return $oauthScope;
    }


    /**
     * 获得用户权限列表（通过names）
     *
     * @param array $names
     *
     * @return object list|null $oauthScopes
     */
    public function getOauthScopesByNames($names)
    {
        if (!isset($names) || !is_array($names) || count($names) < 1) {
            return null;
        }

        $oauthScopes = $this->_oauthScope->whereIn('name', $names)->select('id', 'name', 'description')->get();

        return $oauthScopes;

    }

}