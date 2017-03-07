<?php
/**
 * Created by PhpStorm.
 * User: liuzw
 * Date: 2016/11/10
 * Time: 10:51
 */

namespace App\Models;

/**
 * Oauth会话模型类
 *
 * Class OauthSession
 * @package App\Models
 */
class OauthSession extends BModel
{
    protected $table = 'oauth_session';
    protected $primaryKey = 'id';

}